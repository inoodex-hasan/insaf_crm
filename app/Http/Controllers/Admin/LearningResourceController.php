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
use PhpOffice\PhpWord\Settings;
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

        // 3. Other Templates (Declarations, Receipts, Acknowledgements)
        $otherTemplates = AgreementTemplate::others()
            ->active()
            ->ordered()
            ->get();

        // 4. Country Based Documents
        $docQuery = LearningResource::category('country_document')
            ->with(['country', 'creator'])
            ->ordered();

        if ($countryFilter) {
            $docQuery->where('country_id', $countryFilter);
        }
        $countryDocuments = $docQuery->get();

        // 5. Interview Preparation Resources
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
            'otherTemplates',
            'countryDocuments',
            'interviewPreps'
        ));
    }

    /**
     * Resolve redirect tab name for an AgreementTemplate.
     */
    protected function resolveTemplateTab(AgreementTemplate $template): string
    {
        if (in_array($template->type, ['clearance_withdrawal', 'clearance_full'])) {
            return 'clearances';
        }
        if ($template->type === 'other') {
            return 'other';
        }
        return 'agreements';
    }

    /**
     * Store a newly created template (used for Agreements, Clearances, and Other templates).
     */
    public function storeTemplate(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'nullable|string|in:other,agreement,clearance_withdrawal,clearance_full',
            'country_id' => 'nullable|exists:countries,id',
            'layout_type' => 'nullable|string|in:pad,stamp',
            'header_space_mm' => 'nullable|integer|min:0|max:200',
            'content' => 'nullable|string',
            'docx_template' => 'nullable|file|mimes:docx|max:20480',
        ]);

        $type = $validated['type'] ?? 'other';
        $countryId = $type === 'agreement' ? ($validated['country_id'] ?? null) : null;

        $fallbackContent = '<div style="padding: 30px; text-align: center; color: #64748b; border: 1px dashed #cbd5e1; border-radius: 8px;">
            <h3 style="font-size: 16px; font-weight: bold; margin-bottom: 8px; color: #1e293b;">Official Template Managed via Word Document</h3>
            <p style="font-size: 13px; margin: 0;">This format is provided as an official Microsoft Word (.docx) file. Please click <strong>Download DOCX</strong> above to view, fill, and print.</p>
        </div>';

        $template = AgreementTemplate::create([
            'type' => $type,
            'country_id' => $countryId,
            'layout_type' => $validated['layout_type'] ?? 'pad',
            'header_space_mm' => $validated['header_space_mm'] ?? 0,
            'title' => $validated['title'],
            'content' => !empty($validated['content']) ? $validated['content'] : $fallbackContent,
            'is_active' => true,
            'sort_order' => (AgreementTemplate::where('type', $type)->max('sort_order') ?? 0) + 1,
        ]);

        if ($request->hasFile('docx_template')) {
            $subDir = match($type) {
                'agreement' => 'agreements',
                'clearance_withdrawal', 'clearance_full' => 'clearances',
                default => 'other',
            };
            $filename = Str::slug($template->title) . "_{$template->id}.docx";
            $relPath = "storage/app/templates/{$subDir}/{$filename}";
            $absDir = base_path("storage/app/templates/{$subDir}");
            if (!is_dir($absDir)) {
                mkdir($absDir, 0755, true);
            }
            $request->file('docx_template')->move($absDir, $filename);
            $template->update(['docx_path' => $relPath]);
        }

        return redirect()->route('admin.learning-resources.index', ['tab' => $this->resolveTemplateTab($template)])
            ->with('success', "Template '{$template->title}' created successfully.");
    }

    /**
     * Update an agreement, clearance, or other master template.
     */
    public function updateTemplate(Request $request, AgreementTemplate $template): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'header_space_mm' => 'nullable|integer|min:0|max:200',
            'content' => 'nullable|string',
            'is_active' => 'nullable|boolean',
            'docx_template' => 'nullable|file|mimes:docx|max:20480',
        ]);

        $updateData = [
            'title' => $validated['title'],
            'header_space_mm' => $validated['header_space_mm'] ?? 0,
            'is_active' => $request->has('is_active') ? true : false,
        ];

        if (!empty($validated['content'])) {
            $updateData['content'] = $validated['content'];
        }

        $template->update($updateData);

        // If user uploaded a customized master DOCX file directly in the CRM
        if ($request->hasFile('docx_template')) {
            $masterPath = $this->getMasterDocxTemplatePath($template);
            if ($masterPath) {
                $dir = dirname($masterPath);
                if (!is_dir($dir)) {
                    mkdir($dir, 0755, true);
                }
                $request->file('docx_template')->move($dir, basename($masterPath));
            } else {
                $subDir = $template->type === 'other' ? 'other' : 'custom';
                $filename = Str::slug($template->title) . "_{$template->id}.docx";
                $relPath = "storage/app/templates/{$subDir}/{$filename}";
                $absDir = base_path("storage/app/templates/{$subDir}");
                if (!is_dir($absDir)) {
                    mkdir($absDir, 0755, true);
                }
                $request->file('docx_template')->move($absDir, $filename);
                $template->update(['docx_path' => $relPath]);
            }
        }

        return redirect()->route('admin.learning-resources.index', ['tab' => $this->resolveTemplateTab($template)])
            ->with('success', "Template '{$template->title}' updated successfully.");
    }

    /**
     * Delete an agreement, clearance, or other template.
     */
    public function destroyTemplate(AgreementTemplate $template): RedirectResponse
    {
        $tab = $this->resolveTemplateTab($template);
        $title = $template->title;

        // Clean up custom uploaded docx file if exists
        if (!empty($template->docx_path)) {
            $absPath = base_path($template->docx_path);
            if (file_exists($absPath) && (str_contains($absPath, 'templates/agreements/') || str_contains($absPath, 'templates/custom/') || str_contains($absPath, 'templates/clearances/'))) {
                @unlink($absPath);
            }
        }

        $template->delete();

        return redirect()->route('admin.learning-resources.index', ['tab' => $tab])
            ->with('success', "Template '{$title}' removed successfully.");
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
        if (!empty($template->docx_path)) {
            $explicitPath = base_path($template->docx_path);
            if (file_exists($explicitPath)) {
                return $explicitPath;
            }
        }

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

        if ($template->type === 'agreement') {
            $defaultPath = storage_path("app/templates/default_agreement_{$layout}.docx");
            if (file_exists($defaultPath)) {
                return $defaultPath;
            }
        }

        return null;
    }

    /**
     * Build shared token maps for both HTML (Preview/PDF) and TemplateProcessor (DOCX).
     */
    protected function buildTemplateTokenMap(AgreementTemplate $template, array $data = []): array
    {
        $countryName = !empty($data['country_name'])
            ? $data['country_name']
            : ($template->country->name ?? 'Destination Country');
        $isKorea = str_contains(strtolower($countryName), 'korea');

        $defaultFee = $isKorea ? 'BDT 120,000' : 'BDT 50,000';
        $defaultFeeWords = $isKorea ? 'One Lac Twenty Thousand Taka only' : 'Fifty Thousand Taka only';

        $serviceCharge = !empty($data['service_charge']) ? $data['service_charge'] : $defaultFee;
        $serviceChargeWords = !empty($data['service_charge_words']) ? $data['service_charge_words'] : $defaultFeeWords;

        $plain = [
            'DATE' => !empty($data['date']) ? $data['date'] : now()->format('d F Y'),
            'APPLICANT_NAME' => !empty($data['applicant_name']) ? $data['applicant_name'] : '________________________',
            'FATHER_NAME' => !empty($data['father_name']) ? $data['father_name'] : '________________________',
            'MOTHER_NAME' => !empty($data['mother_name']) ? $data['mother_name'] : '________________________',
            'PASSPORT_NO' => !empty($data['passport_no']) ? $data['passport_no'] : '________________________',
            'ADDRESS' => !empty($data['address']) ? $data['address'] : '________________________',
            'PHONE' => !empty($data['phone']) ? $data['phone'] : '________________________',
            'SERVICE_CHARGE' => $serviceCharge,
            'SERVICE_CHARGE_WORDS' => $serviceChargeWords,
            'AMOUNT' => !empty($data['amount']) ? $data['amount'] : $serviceCharge,
            'AMOUNT_WORDS' => !empty($data['amount_words']) ? $data['amount_words'] : $serviceChargeWords,
            'CHEQUE_NO' => !empty($data['cheque_no']) ? $data['cheque_no'] : '________________________',
            'DOCUMENTS_LIST' => !empty($data['documents_list']) ? $data['documents_list'] : 'Passport, SSC (Certificate & Transcript), HSC (Certificate & Transcript)',
            'COUNTRY_NAME' => $countryName,
            'COUNTRY_UPPER' => strtoupper($countryName),
            'UNIVERSITY_NAME' => !empty($data['university_name']) ? $data['university_name'] : '________________________',
            'COURSE_NAME' => !empty($data['course_name']) ? $data['course_name'] : '________________________',
            'INTAKE_NAME' => !empty($data['intake_name']) ? $data['intake_name'] : '________________________',
            'INTAKE' => !empty($data['intake_name']) ? $data['intake_name'] : '',
        ];

        $bracket = [];
        foreach ($plain as $k => $v) {
            $bracket["[{$k}]"] = $v;
        }

        return ['plain' => $plain, 'bracket' => $bracket];
    }

    /**
     * Populate a master DOCX template safely with XML escaping and return download response.
     */
    protected function renderMasterDocxResponse(string $masterPath, string $cleanTitle, array $plainTokens): BinaryFileResponse
    {
        Settings::setOutputEscapingEnabled(true);

        // Normalize template XML so that both [VARIABLE] and ${VARIABLE} are supported
        $tempMaster = tempnam(sys_get_temp_dir(), 'tmpl_norm_') . '.docx';
        copy($masterPath, $tempMaster);

        $zip = new \ZipArchive();
        if ($zip->open($tempMaster) === true) {
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $entryName = $zip->getNameIndex($i);
                if (str_ends_with($entryName, '.xml')) {
                    $xml = $zip->getFromIndex($i);
                    // Convert [TOKEN] to ${TOKEN} for standard TemplateProcessor
                    $newXml = preg_replace('/\[([A-Z0-9_]+)\]/', '${$1}', $xml);
                    if ($newXml !== $xml) {
                        $zip->addFromString($entryName, $newXml);
                    }
                }
            }
            $zip->close();
        }

        $processor = new TemplateProcessor($tempMaster);
        $processor->setValues($plainTokens);

        $tempPath = tempnam(sys_get_temp_dir(), 'docx_') . '.docx';
        $processor->saveAs($tempPath);

        @unlink($tempMaster);

        return response()->download($tempPath, "{$cleanTitle}.docx", [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Download template as editable Microsoft Word DOCX.
     */
    public function downloadDocx(AgreementTemplate $template): BinaryFileResponse
    {
        $cleanTitle = Str::slug($template->title);
        $masterPath = $this->getMasterDocxTemplatePath($template);

        if ($masterPath && file_exists($masterPath)) {
            $tokens = $this->buildTemplateTokenMap($template);
            return $this->renderMasterDocxResponse($masterPath, $cleanTitle, $tokens['plain']);
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
            'amount' => 'nullable|string|max:100',
            'amount_words' => 'nullable|string|max:255',
            'cheque_no' => 'nullable|string|max:100',
            'documents_list' => 'nullable|string|max:1000',
            'country_name' => 'nullable|string|max:150',
            'university_name' => 'nullable|string|max:255',
            'course_name' => 'nullable|string|max:255',
            'intake_name' => 'nullable|string|max:255',
        ]);

        $template = AgreementTemplate::with('country')->findOrFail($validated['template_id']);
        $tokens = $this->buildTemplateTokenMap($template, $validated);

        $renderedContent = str_replace(array_keys($tokens['bracket']), array_values($tokens['bracket']), $template->content);
        $dollarTokens = [];
        foreach ($tokens['plain'] as $k => $v) {
            $dollarTokens['${' . $k . '}'] = $v;
        }
        $renderedContent = str_replace(array_keys($dollarTokens), array_values($dollarTokens), $renderedContent);

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
                return $this->renderMasterDocxResponse($masterPath, $cleanTitle, $tokens['plain']);
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
     * Display the dedicated full-page document editor.
     */
    public function editor(Request $request)
    {
        $templateId = $request->get('template_id');
        $studentId = $request->get('student_id');

        $templates = AgreementTemplate::where('is_active', true)->ordered()->with('country')->get();
        $template = $templateId ? AgreementTemplate::with('country')->find($templateId) : $templates->first();
        if (!$template && $templates->isNotEmpty()) {
            $template = $templates->first();
        }

        $student = null;
        if ($studentId) {
            $student = \App\Models\Student::with(['country', 'university', 'course', 'intake'])->find($studentId);
        }

        $initialValues = [];
        if ($template) {
            $data = [];
            if ($student) {
                $data = [
                    'applicant_name' => $student->full_name,
                    'father_name' => $student->father_name ?? '',
                    'mother_name' => $student->mother_name ?? '',
                    'passport_no' => $student->passport_number ?? '',
                    'phone' => $student->phone ?? '',
                    'address' => $student->address ?? '',
                    'country_name' => $student->country->name ?? '',
                    'university_name' => $student->university->name ?? '',
                    'course_name' => $student->course->name ?? '',
                    'intake_name' => $student->intake->intake_name ?? '',
                ];
            }
            $tokenMap = $this->buildTemplateTokenMap($template, $data);
            $initialValues = $tokenMap['plain'];
        }

        return view('admin.learning-resources.editor', compact('templates', 'template', 'student', 'initialValues'));
    }

    /**
     * Search students for auto-fill in the editor.
     */
    public function searchStudents(Request $request)
    {
        $q = trim($request->get('q', ''));
        $query = \App\Models\Student::query();

        if (!empty($q)) {
            $query->where(function($sq) use ($q) {
                $sq->where('first_name', 'like', "%{$q}%")
                   ->orWhere('last_name', 'like', "%{$q}%")
                   ->orWhere('passport_number', 'like', "%{$q}%")
                   ->orWhere('phone', 'like', "%{$q}%")
                   ->orWhere('email', 'like', "%{$q}%");
            });
        }

        $students = $query->with(['country', 'university', 'course', 'intake'])
            ->orderBy('id', 'desc')
            ->limit(20)
            ->get()
            ->map(function($st) {
                return [
                    'id' => $st->id,
                    'name' => $st->full_name,
                    'passport' => $st->passport_number,
                    'phone' => $st->phone,
                    'country' => $st->country->name ?? '',
                    'university' => $st->university->name ?? '',
                    'course' => $st->course->name ?? '',
                    'intake' => $st->intake->intake_name ?? '',
                    'father_name' => $st->father_name ?? '',
                    'mother_name' => $st->mother_name ?? '',
                    'address' => $st->address ?? '',
                ];
            });

        return response()->json($students);
    }

    /**
     * Get single student detailed data for autofill.
     */
    public function getStudentData(\App\Models\Student $student)
    {
        $student->load(['country', 'university', 'course', 'intake']);
        return response()->json([
            'id' => $student->id,
            'name' => $student->full_name,
            'applicant_name' => $student->full_name,
            'father_name' => $student->father_name ?? '',
            'mother_name' => $student->mother_name ?? '',
            'passport_no' => $student->passport_number ?? '',
            'phone' => $student->phone ?? '',
            'address' => $student->address ?? '',
            'country_name' => $student->country->name ?? '',
            'university_name' => $student->university->name ?? '',
            'course_name' => $student->course->name ?? '',
            'intake_name' => $student->intake->intake_name ?? '',
        ]);
    }

    /**
     * Return populated raw DOCX binary for client-side 1:1 DOCX renderers.
     */
    public function getRawDocxTemplate(Request $request, AgreementTemplate $template)
    {
        $masterPath = $this->getMasterDocxTemplatePath($template);
        if (!$masterPath || !file_exists($masterPath)) {
            return response()->json(['error' => 'Master DOCX file not found'], 404);
        }

        $studentId = $request->get('student_id');
        $student = $studentId ? \App\Models\Student::with(['country', 'university', 'course', 'intake'])->find($studentId) : null;

        $data = [];
        if ($student) {
            $data = [
                'applicant_name' => $student->full_name,
                'father_name' => $student->father_name ?? '',
                'mother_name' => $student->mother_name ?? '',
                'passport_no' => $student->passport_number ?? '',
                'phone' => $student->phone ?? '',
                'address' => $student->address ?? '',
                'country_name' => $student->country->name ?? '',
                'university_name' => $student->university->name ?? '',
                'course_name' => $student->course->name ?? '',
                'intake_name' => $student->intake->intake_name ?? '',
            ];
        }

        $tokens = $this->buildTemplateTokenMap($template, $data);

        // Normalize template XML so that both [VARIABLE] and ${VARIABLE} are supported
        $tempMaster = tempnam(sys_get_temp_dir(), 'tmpl_raw_') . '.docx';
        copy($masterPath, $tempMaster);

        $zip = new \ZipArchive();
        if ($zip->open($tempMaster) === true) {
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $entryName = $zip->getNameIndex($i);
                if (str_ends_with($entryName, '.xml')) {
                    $xml = $zip->getFromIndex($i);
                    $newXml = preg_replace('/\[([A-Z0-9_]+)\]/', '${$1}', $xml);
                    if ($newXml !== $xml) {
                        $zip->addFromString($entryName, $newXml);
                    }
                }
            }
            $zip->close();
        }

        Settings::setOutputEscapingEnabled(true);
        $processor = new TemplateProcessor($tempMaster);
        $processor->setValues($tokens['plain']);

        $tempPath = tempnam(sys_get_temp_dir(), 'docx_stream_') . '.docx';
        $processor->saveAs($tempPath);
        @unlink($tempMaster);

        $cleanTitle = Str::slug($template->title) . '.docx';

        return response()->download($tempPath, $cleanTitle, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'Access-Control-Allow-Origin' => '*',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Download fully edited document as DOCX.
     */
    public function downloadEditorDocx(Request $request)
    {
        $validated = $request->validate([
            'template_id' => 'required|exists:agreement_clearance_templates,id',
            'html_content' => 'required|string',
            'document_title' => 'nullable|string|max:255',
        ]);

        $template = AgreementTemplate::with('country')->findOrFail($validated['template_id']);
        $cleanTitle = Str::slug($validated['document_title'] ?: $template->title);

        $html = $validated['html_content'];
        $cleanHtml = $this->prepareHtmlForPhpWord($html);

        $phpWord = new PhpWord();
        $phpWord->setDefaultFontName('Calibri');
        $phpWord->setDefaultFontSize(11);

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
                ['bold' => true, 'size' => 13, 'color' => '1E293B', 'name' => 'Calibri'],
                ['alignment' => 'center']
            );
            $header->addText(
                'Official Student Agreement & Clearance Service Hub | Dhaka, Bangladesh',
                ['italic' => true, 'size' => 8.5, 'color' => '64748B', 'name' => 'Calibri'],
                ['alignment' => 'center']
            );
            $header->addLine(['weight' => 1, 'width' => 450, 'height' => 0, 'color' => 'CBD5E1']);
        }

        try {
            Html::addHtml($section, $cleanHtml, false, false);
        } catch (\Throwable $e) {
            // Fallback plain paragraph if complex inline HTML styles conflict
            $plainText = strip_tags(str_replace(['<br>', '</tr>', '</p>'], "\n", $html));
            $section->addText($plainText);
        }

        $tempPath = tempnam(sys_get_temp_dir(), 'docx_edit_') . '.docx';
        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save($tempPath);

        return response()->download($tempPath, "{$cleanTitle}.docx", [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ])->deleteFileAfterSend(true);
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
