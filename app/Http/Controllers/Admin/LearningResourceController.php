<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AgreementTemplate;
use App\Models\Country;
use App\Models\LearningResource;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Shared\Converter;
use PhpOffice\PhpWord\Shared\Html;
use PhpOffice\PhpWord\TemplateProcessor;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class LearningResourceController extends Controller
{
    /**
     * Display the Learning Resources dashboard with all 4 tabs.
     */
    public function index(Request $request)
    {
        $activeTab = $request->get('tab', 'agreements');
        $countryFilter = $request->get('country_id');

        $countries = Country::orderBy('name')->get();

        // 1. Agreement Templates
        $agreementTemplates = AgreementTemplate::agreements()
            ->active()
            ->ordered()
            ->with('country')
            ->get();

        // 2. Clearance Templates
        $clearanceTemplates = AgreementTemplate::clearances()
            ->active()
            ->ordered()
            ->get();

        // 3. Country Based Documents
        $docQuery = LearningResource::category('country_document')
            ->with(['country', 'creator'])
            ->ordered();

        if ($countryFilter) {
            $docQuery->where('country_id', $countryFilter);
        }
        $countryDocuments = $docQuery->get();

        // 4. Interview Preparation Resources
        $interviewQuery = LearningResource::category('interview_prep')
            ->with(['country', 'creator'])
            ->ordered();

        if ($countryFilter) {
            $interviewQuery->where('country_id', $countryFilter);
        }
        $interviewPreps = $interviewQuery->get();

        return view('admin.learning-resources.index', compact(
            'activeTab',
            'countryFilter',
            'countries',
            'agreementTemplates',
            'clearanceTemplates',
            'countryDocuments',
            'interviewPreps'
        ));
    }

    /**
     * Update an agreement or clearance master template.
     */
    public function updateTemplate(Request $request, AgreementTemplate $template): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'header_space_mm' => 'nullable|integer|min:0|max:200',
            'content' => 'required|string',
            'is_active' => 'nullable|boolean',
            'docx_template' => 'nullable|file|mimes:docx|max:20480',
        ]);

        $template->update([
            'title' => $validated['title'],
            'header_space_mm' => $validated['header_space_mm'] ?? 0,
            'content' => $validated['content'],
            'is_active' => $request->has('is_active') ? true : false,
        ]);

        // If user uploaded a customized master DOCX file directly in the CRM
        if ($request->hasFile('docx_template')) {
            $masterPath = $this->getMasterDocxTemplatePath($template);
            if ($masterPath) {
                $dir = dirname($masterPath);
                if (!is_dir($dir)) {
                    mkdir($dir, 0755, true);
                }
                $request->file('docx_template')->move($dir, basename($masterPath));
            }
        }

        $tab = in_array($template->type, ['clearance_withdrawal', 'clearance_full']) ? 'clearances' : 'agreements';

        return redirect()->route('admin.learning-resources.index', ['tab' => $tab])
            ->with('success', "Template '{$template->title}' updated successfully.");
    }

    /**
     * Preview template in browser / print preview.
     */
    public function previewTemplate(AgreementTemplate $template)
    {
        return view('admin.learning-resources.preview', compact('template'));
    }

    /**
     * Download template as formatted PDF (Pad format or Stamp Paper format).
     */
    public function downloadPdf(AgreementTemplate $template)
    {
        $cleanTitle = Str::slug($template->title);
        $filename = "{$cleanTitle}.pdf";

        $pdf = Pdf::loadView('admin.learning-resources.pdf', compact('template'))
            ->setPaper('a4', 'portrait')
            ->setOption([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'sans-serif',
            ]);

        return $pdf->download($filename);
    }

    /**
     * Resolve pre-built master DOCX template path if available.
     */
    protected function getMasterDocxTemplatePath(AgreementTemplate $template): ?string
    {
        $countryName = strtolower($template->country->name ?? '');
        $layout = $template->layout_type === 'stamp' ? 'stamp' : 'pad';

        if (str_contains($countryName, 'malta')) {
            $path = storage_path("app/templates/malta_agreement_{$layout}.docx");
            if (file_exists($path)) {
                return $path;
            }
        }

        if (str_contains($countryName, 'korea')) {
            $path = storage_path("app/templates/korea_agreement_{$layout}.docx");
            if (file_exists($path)) {
                return $path;
            }
        }

        // Generic fallback template for other countries / general agreements
        $defaultPath = storage_path("app/templates/default_agreement_{$layout}.docx");
        if (file_exists($defaultPath)) {
            return $defaultPath;
        }

        return null;
    }

    /**
     * Download template as editable Microsoft Word DOCX.
     */
    public function downloadDocx(AgreementTemplate $template): BinaryFileResponse
    {
        $cleanTitle = Str::slug($template->title);
        $masterPath = $this->getMasterDocxTemplatePath($template);

        if ($masterPath && file_exists($masterPath)) {
            $tempPath = tempnam(sys_get_temp_dir(), 'docx_') . '.docx';
            $processor = new TemplateProcessor($masterPath);

            $countryName = $template->country->name ?? 'Destined Country';
            $isKorea = str_contains(strtolower($countryName), 'korea');

            $processor->setValue('DATE', now()->format('d F Y'));
            $processor->setValue('APPLICANT_NAME', '________________________');
            $processor->setValue('FATHER_NAME', '________________________');
            $processor->setValue('MOTHER_NAME', '________________________');
            $processor->setValue('PASSPORT_NO', '________________________');
            $processor->setValue('ADDRESS', '________________________');
            $processor->setValue('PHONE', '________________________');
            $processor->setValue('SERVICE_CHARGE', $isKorea ? 'BDT 120,000' : 'BDT 50,000');
            $processor->setValue('SERVICE_CHARGE_WORDS', $isKorea ? 'One Lac Twenty Thousand Taka only' : 'Fifty Thousand Taka only');
            $processor->setValue('COUNTRY_NAME', $countryName);
            $processor->setValue('COUNTRY_UPPER', strtoupper($countryName));
            $processor->saveAs($tempPath);

            return response()->download($tempPath, "{$cleanTitle}.docx", [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            ])->deleteFileAfterSend(true);
        }

        $phpWord = new PhpWord();
        $phpWord->setDefaultFontName('Arial');
        $phpWord->setDefaultFontSize(10.5);

        // Stamp paper top margin clearance (~90mm / 5100 twips) vs standard Pad (~20mm / 1134 twips)
        $topMarginTwips = ($template->layout_type === 'stamp' && $template->header_space_mm > 0)
            ? Converter::pixelToTwip(intval($template->header_space_mm * 3.7795))
            : 1134;

        $section = $phpWord->addSection([
            'marginTop' => $topMarginTwips,
            'marginBottom' => 1134,
            'marginLeft' => 1134,
            'marginRight' => 1134,
            'paperSize' => 'A4',
            'orientation' => 'portrait',
        ]);

        // If Pad format, add agency header block in Word
        if ($template->layout_type === 'pad') {
            $header = $section->addHeader();
            $header->addText(
                'INSAF IMMIGRATION CONSULTANCY',
                ['bold' => true, 'size' => 14, 'color' => '1E293B'],
                ['alignment' => 'center']
            );
            $header->addText(
                'Official Student Agreement & Clearance Service Hub | Dhaka, Bangladesh',
                ['italic' => true, 'size' => 8.5, 'color' => '64748B'],
                ['alignment' => 'center']
            );
            $header->addLine(['weight' => 1, 'width' => 450, 'height' => 0, 'color' => 'CBD5E1']);
        }

        // Clean and prepare HTML content for PhpWord parser
        $cleanHtml = $this->prepareHtmlForPhpWord($template->content);

        try {
            Html::addHtml($section, $cleanHtml, false, false);
        } catch (\Throwable $e) {
            // Fallback plain paragraph if complex inline HTML styles conflict
            $plainText = strip_tags(str_replace(['<br>', '</tr>', '</p>'], "\n", $template->content));
            $section->addText($plainText);
        }

        $cleanTitle = Str::slug($template->title);
        $tempPath = tempnam(sys_get_temp_dir(), 'docx_') . '.docx';

        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save($tempPath);

        return response()->download($tempPath, "{$cleanTitle}.docx", [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Upload and store a country-based document.
     */
    public function storeDocument(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'country_id' => 'nullable|exists:countries,id',
            'description' => 'nullable|string|max:1000',
            'file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,webp|max:25600', // 25MB max
        ]);

        $file = $request->file('file');
        $path = $file->store('learning-resources/documents', 'public');

        LearningResource::create([
            'category' => 'country_document',
            'country_id' => $validated['country_id'] ?? null,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'file_type' => strtolower($file->getClientOriginalExtension()),
            'file_size' => $file->getSize(),
            'created_by' => auth()->id(),
            'is_active' => true,
        ]);

        return redirect()->route('admin.learning-resources.index', ['tab' => 'documents'])
            ->with('success', 'Document uploaded successfully.');
    }

    /**
     * Delete an uploaded country document.
     */
    public function destroyDocument(LearningResource $resource): RedirectResponse
    {
        if ($resource->file_path && Storage::disk('public')->exists($resource->file_path)) {
            Storage::disk('public')->delete($resource->file_path);
        }

        $resource->delete();

        return redirect()->route('admin.learning-resources.index', ['tab' => 'documents'])
            ->with('success', 'Document deleted successfully.');
    }

    /**
     * Store interview preparation video link, Drive link, or guide.
     */
    public function storeInterviewPrep(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'country_id' => 'nullable|exists:countries,id',
            'description' => 'nullable|string|max:2000',
            'video_url' => 'nullable|url|max:1000',
            'external_link' => 'nullable|url|max:1000',
            'file' => 'nullable|file|mimes:pdf,doc,docx|max:25600',
        ]);

        $filePath = null;
        $fileName = null;
        $fileType = null;
        $fileSize = null;

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filePath = $file->store('learning-resources/interview', 'public');
            $fileName = $file->getClientOriginalName();
            $fileType = strtolower($file->getClientOriginalExtension());
            $fileSize = $file->getSize();
        }

        LearningResource::create([
            'category' => 'interview_prep',
            'country_id' => $validated['country_id'] ?? null,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'video_url' => $validated['video_url'] ?? null,
            'external_link' => $validated['external_link'] ?? null,
            'file_path' => $filePath,
            'file_name' => $fileName,
            'file_type' => $fileType,
            'file_size' => $fileSize,
            'created_by' => auth()->id(),
            'is_active' => true,
        ]);

        return redirect()->route('admin.learning-resources.index', ['tab' => 'interview'])
            ->with('success', 'Interview preparation resource added successfully.');
    }

    /**
     * Delete an interview preparation resource.
     */
    public function destroyInterviewPrep(LearningResource $resource): RedirectResponse
    {
        if ($resource->file_path && Storage::disk('public')->exists($resource->file_path)) {
            Storage::disk('public')->delete($resource->file_path);
        }

        $resource->delete();

        return redirect()->route('admin.learning-resources.index', ['tab' => 'interview'])
            ->with('success', 'Interview resource removed successfully.');
    }

    /**
     * Generate populated agreement (Preview, PDF, or DOCX) for a specific student.
     */
    public function generateAgreement(Request $request)
    {
        $validated = $request->validate([
            'template_id' => 'required|exists:agreement_clearance_templates,id',
            'action_type' => 'required|in:preview,pdf,docx',
            'date' => 'nullable|string',
            'applicant_name' => 'required|string|max:255',
            'father_name' => 'nullable|string|max:255',
            'mother_name' => 'nullable|string|max:255',
            'passport_no' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:100',
            'service_charge' => 'nullable|string|max:100',
            'service_charge_words' => 'nullable|string|max:255',
            'university_name' => 'nullable|string|max:255',
            'course_name' => 'nullable|string|max:255',
            'intake_name' => 'nullable|string|max:255',
        ]);

        $template = AgreementTemplate::findOrFail($validated['template_id']);

        // Token replacements
        $replacements = [
            '[DATE]' => !empty($validated['date']) ? $validated['date'] : now()->format('d F Y'),
            '[APPLICANT_NAME]' => $validated['applicant_name'],
            '[FATHER_NAME]' => !empty($validated['father_name']) ? $validated['father_name'] : '________________________',
            '[MOTHER_NAME]' => !empty($validated['mother_name']) ? $validated['mother_name'] : '________________________',
            '[PASSPORT_NO]' => !empty($validated['passport_no']) ? $validated['passport_no'] : '________________________',
            '[ADDRESS]' => !empty($validated['address']) ? $validated['address'] : '________________________',
            '[PHONE]' => !empty($validated['phone']) ? $validated['phone'] : '________________________',
            '[SERVICE_CHARGE]' => !empty($validated['service_charge']) ? $validated['service_charge'] : 'BDT 50,000',
            '[SERVICE_CHARGE_WORDS]' => !empty($validated['service_charge_words']) ? $validated['service_charge_words'] : 'Fifty Thousand Taka only',
            '[UNIVERSITY_NAME]' => !empty($validated['university_name']) ? $validated['university_name'] : '________________________',
            '[COURSE_NAME]' => !empty($validated['course_name']) ? $validated['course_name'] : '________________________',
            '[INTAKE_NAME]' => !empty($validated['intake_name']) ? $validated['intake_name'] : '________________________',
        ];

        $renderedContent = str_replace(array_keys($replacements), array_values($replacements), $template->content);

        // Create a clone object for view rendering without modifying DB
        $populatedTemplate = clone $template;
        $populatedTemplate->content = $renderedContent;
        $populatedTemplate->title = "{$template->title} - {$validated['applicant_name']}";

        $cleanTitle = Str::slug($populatedTemplate->title);

        if ($validated['action_type'] === 'preview') {
            return view('admin.learning-resources.preview', ['template' => $populatedTemplate]);
        }

        if ($validated['action_type'] === 'pdf') {
            $pdf = Pdf::loadView('admin.learning-resources.pdf', ['template' => $populatedTemplate])
                ->setPaper('a4', 'portrait')
                ->setOption([
                    'isHtml5ParserEnabled' => true,
                    'isRemoteEnabled' => true,
                    'defaultFont' => 'sans-serif',
                ]);

            return $pdf->download("{$cleanTitle}.pdf");
        }

        if ($validated['action_type'] === 'docx') {
            $masterPath = $this->getMasterDocxTemplatePath($template);

            // Use 1:1 master DOCX template preserving pad watermark logo and official layout
            if ($masterPath && file_exists($masterPath)) {
                $processor = new TemplateProcessor($masterPath);

                $countryName = $template->country->name ?? 'Destined Country';
                $isKorea = str_contains(strtolower($countryName), 'korea');

                $processor->setValue('DATE', !empty($validated['date']) ? $validated['date'] : now()->format('d F Y'));
                $processor->setValue('APPLICANT_NAME', $validated['applicant_name']);
                $processor->setValue('FATHER_NAME', !empty($validated['father_name']) ? $validated['father_name'] : '________________________');
                $processor->setValue('MOTHER_NAME', !empty($validated['mother_name']) ? $validated['mother_name'] : '________________________');
                $processor->setValue('PASSPORT_NO', !empty($validated['passport_no']) ? $validated['passport_no'] : '________________________');
                $processor->setValue('ADDRESS', !empty($validated['address']) ? $validated['address'] : '________________________');
                $processor->setValue('PHONE', !empty($validated['phone']) ? $validated['phone'] : '________________________');
                $processor->setValue('SERVICE_CHARGE', !empty($validated['service_charge']) ? $validated['service_charge'] : ($isKorea ? 'BDT 120,000' : 'BDT 50,000'));
                $processor->setValue('SERVICE_CHARGE_WORDS', !empty($validated['service_charge_words']) ? $validated['service_charge_words'] : ($isKorea ? 'One Lac Twenty Thousand Taka only' : 'Fifty Thousand Taka only'));
                $processor->setValue('COUNTRY_NAME', $countryName);
                $processor->setValue('COUNTRY_UPPER', strtoupper($countryName));
                $processor->setValue('UNIVERSITY_NAME', !empty($validated['university_name']) ? $validated['university_name'] : '');
                $processor->setValue('COURSE_NAME', !empty($validated['course_name']) ? $validated['course_name'] : '');
                $processor->setValue('INTAKE', !empty($validated['intake_name']) ? $validated['intake_name'] : '');

                $tempPath = tempnam(sys_get_temp_dir(), 'docx_') . '.docx';
                $processor->saveAs($tempPath);

                return response()->download($tempPath, "{$cleanTitle}.docx", [
                    'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                ])->deleteFileAfterSend(true);
            }

            $phpWord = new PhpWord();
            $phpWord->setDefaultFontName('Arial');
            $phpWord->setDefaultFontSize(10.5);

            $topMarginTwips = ($template->layout_type === 'stamp' && $template->header_space_mm > 0)
                ? Converter::pixelToTwip(intval($template->header_space_mm * 3.7795))
                : 1134;

            $section = $phpWord->addSection([
                'marginTop' => $topMarginTwips,
                'marginBottom' => 1134,
                'marginLeft' => 1134,
                'marginRight' => 1134,
                'paperSize' => 'A4',
                'orientation' => 'portrait',
            ]);

            if ($template->layout_type === 'pad') {
                $header = $section->addHeader();
                $header->addText(
                    'INSAF IMMIGRATION CONSULTANCY',
                    ['bold' => true, 'size' => 14, 'color' => '1E293B'],
                    ['alignment' => 'center']
                );
                $header->addText(
                    'Official Student Agreement & Clearance Service Hub | Dhaka, Bangladesh',
                    ['italic' => true, 'size' => 8.5, 'color' => '64748B'],
                    ['alignment' => 'center']
                );
                $header->addLine(['weight' => 1, 'width' => 450, 'height' => 0, 'color' => 'CBD5E1']);
            }

            $cleanHtml = $this->prepareHtmlForPhpWord($renderedContent);

            try {
                Html::addHtml($section, $cleanHtml, false, false);
            } catch (\Throwable $e) {
                $plainText = strip_tags(str_replace(['<br>', '</tr>', '</p>'], "\n", $renderedContent));
                $section->addText($plainText);
            }

            $tempPath = tempnam(sys_get_temp_dir(), 'docx_') . '.docx';
            $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
            $objWriter->save($tempPath);

            return response()->download($tempPath, "{$cleanTitle}.docx", [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            ])->deleteFileAfterSend(true);
        }

        return redirect()->back();
    }

    /**
     * Clean and normalize HTML tags for PhpWord converter.
     */
    private function prepareHtmlForPhpWord(string $html): string
    {
        // Convert unclosed void tags to self-closing XML: <br> -> <br/>, <hr> -> <hr/>
        $html = preg_replace('/<br(\s+[^>]*)?(?<!\/)>/i', '<br/>', $html);
        $html = preg_replace('/<hr(\s+[^>]*)?(?<!\/)>/i', '<hr/>', $html);
        $html = preg_replace('/<img(\s+[^>]*)?(?<!\/)>/i', '<img$1/>', $html);

        // Replace non-XML entities like &nbsp;
        $html = str_replace('&nbsp;', '&#160;', $html);

        // Replace unescaped & with &amp;
        $html = preg_replace('/&(?!(?:amp|lt|gt|quot|apos|#\d+|#x[a-f\d]+);)/i', '&amp;', $html);

        return $html;
    }
}
