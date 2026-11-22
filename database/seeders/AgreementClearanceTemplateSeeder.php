<?php

namespace Database\Seeders;

use App\Models\AgreementTemplate;
use App\Models\Country;
use Illuminate\Database\Seeder;

class AgreementClearanceTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $malaysia = Country::where('name', 'Malaysia')->first();
        $russia = Country::where('name', 'Russia')->first();
        $malta = Country::where('name', 'Malta')->first();
        $korea = Country::where('name', 'like', '%Korea%')->first();

        // 1. Malaysia - Official Pad Agreement
        AgreementTemplate::updateOrCreate(
            ['title' => 'Malaysia Student Agreement (Agency Pad Format)'],
            [
                'type' => 'agreement',
                'country_id' => $malaysia?->id,
                'layout_type' => 'pad',
                'header_space_mm' => 0,
                'sort_order' => 10,
                'is_active' => true,
                'docx_path' => 'storage/app/templates/default_agreement_pad.docx',
                'content' => $this->getAgreementContent('Malaysia', 'Agency Official Pad'),
            ]
        );

        // 2. Malaysia - Stamp Paper Agreement
        AgreementTemplate::updateOrCreate(
            ['title' => 'Malaysia Student Agreement (Stamp Paper Format)'],
            [
                'type' => 'agreement',
                'country_id' => $malaysia?->id,
                'layout_type' => 'stamp',
                'header_space_mm' => 90,
                'sort_order' => 20,
                'is_active' => true,
                'docx_path' => 'storage/app/templates/default_agreement_stamp.docx',
                'content' => $this->getAgreementContent('Malaysia', 'Non-Judicial Stamp Paper'),
            ]
        );

        // 3. Russia - Official Pad Agreement
        AgreementTemplate::updateOrCreate(
            ['title' => 'Russia Student Agreement (Agency Pad Format)'],
            [
                'type' => 'agreement',
                'country_id' => $russia?->id,
                'layout_type' => 'pad',
                'header_space_mm' => 0,
                'sort_order' => 30,
                'is_active' => true,
                'docx_path' => 'storage/app/templates/default_agreement_pad.docx',
                'content' => $this->getAgreementContent('Russia', 'Agency Official Pad'),
            ]
        );

        // 4. Russia - Stamp Paper Agreement
        AgreementTemplate::updateOrCreate(
            ['title' => 'Russia Student Agreement (Stamp Paper Format)'],
            [
                'type' => 'agreement',
                'country_id' => $russia?->id,
                'layout_type' => 'stamp',
                'header_space_mm' => 90,
                'sort_order' => 40,
                'is_active' => true,
                'docx_path' => 'storage/app/templates/default_agreement_stamp.docx',
                'content' => $this->getAgreementContent('Russia', 'Non-Judicial Stamp Paper'),
            ]
        );

        // 5. Malta - Official Pad Agreement (also updates ID 1 if named Malta Agreement)
        $existingMalta = AgreementTemplate::where('id', 1)->orWhere('title', 'Malta Agreement')->first();
        if ($existingMalta) {
            $existingMalta->update([
                'title' => 'Malta Student Agreement (Agency Pad Format)',
                'type' => 'agreement',
                'country_id' => $malta?->id,
                'layout_type' => 'pad',
                'header_space_mm' => 0,
                'sort_order' => 50,
                'is_active' => true,
                'docx_path' => 'storage/app/templates/malta_agreement_pad.docx',
                'content' => $this->getAgreementContent('Malta', 'Agency Official Pad'),
            ]);
        } else {
            AgreementTemplate::updateOrCreate(
                ['title' => 'Malta Student Agreement (Agency Pad Format)'],
                [
                    'type' => 'agreement',
                    'country_id' => $malta?->id,
                    'layout_type' => 'pad',
                    'header_space_mm' => 0,
                    'sort_order' => 50,
                    'is_active' => true,
                    'docx_path' => 'storage/app/templates/malta_agreement_pad.docx',
                    'content' => $this->getAgreementContent('Malta', 'Agency Official Pad'),
                ]
            );
        }

        // 6. Malta - Stamp Paper Agreement
        AgreementTemplate::updateOrCreate(
            ['title' => 'Malta Student Agreement (Stamp Paper Format)'],
            [
                'type' => 'agreement',
                'country_id' => $malta?->id,
                'layout_type' => 'stamp',
                'header_space_mm' => 90,
                'sort_order' => 55,
                'is_active' => true,
                'docx_path' => 'storage/app/templates/malta_agreement_stamp.docx',
                'content' => $this->getAgreementContent('Malta', 'Non-Judicial Stamp Paper'),
            ]
        );

        // 7. South Korea - Official Pad Agreement
        AgreementTemplate::updateOrCreate(
            ['title' => 'South Korea Student Agreement (Agency Pad Format)'],
            [
                'type' => 'agreement',
                'country_id' => $korea?->id,
                'layout_type' => 'pad',
                'header_space_mm' => 0,
                'sort_order' => 56,
                'is_active' => true,
                'docx_path' => 'storage/app/templates/korea_agreement_pad.docx',
                'content' => $this->getAgreementContent('South Korea', 'Agency Official Pad'),
            ]
        );

        // 8. South Korea - Stamp Paper Agreement
        AgreementTemplate::updateOrCreate(
            ['title' => 'South Korea Student Agreement (Stamp Paper Format)'],
            [
                'type' => 'agreement',
                'country_id' => $korea?->id,
                'layout_type' => 'stamp',
                'header_space_mm' => 90,
                'sort_order' => 58,
                'is_active' => true,
                'docx_path' => 'storage/app/templates/korea_agreement_stamp.docx',
                'content' => $this->getAgreementContent('South Korea', 'Non-Judicial Stamp Paper'),
            ]
        );

        // 9. General / Other Countries - Official Pad Agreement
        AgreementTemplate::updateOrCreate(
            ['title' => 'General Student Agreement (Agency Pad Format)'],
            [
                'type' => 'agreement',
                'country_id' => null,
                'layout_type' => 'pad',
                'header_space_mm' => 0,
                'sort_order' => 60,
                'is_active' => true,
                'docx_path' => 'storage/app/templates/default_agreement_pad.docx',
                'content' => $this->getAgreementContent('International Higher Education', 'Agency Official Pad'),
            ]
        );

        // 10. General / Other Countries - Stamp Paper Agreement
        AgreementTemplate::updateOrCreate(
            ['title' => 'General Student Agreement (Stamp Paper Format)'],
            [
                'type' => 'agreement',
                'country_id' => null,
                'layout_type' => 'stamp',
                'header_space_mm' => 90,
                'sort_order' => 65,
                'is_active' => true,
                'docx_path' => 'storage/app/templates/default_agreement_stamp.docx',
                'content' => $this->getAgreementContent('International Higher Education', 'Non-Judicial Stamp Paper'),
            ]
        );

        // 11. Withdrawal File Clearance
        AgreementTemplate::updateOrCreate(
            ['title' => 'Student File Withdrawal & Document Release Clearance'],
            [
                'type' => 'clearance_withdrawal',
                'country_id' => null,
                'layout_type' => 'pad',
                'header_space_mm' => 0,
                'sort_order' => 70,
                'is_active' => true,
                'docx_path' => 'storage/app/templates/other/refund_confirmation_declaration.docx',
                'content' => $this->getWithdrawalClearanceContent(),
            ]
        );

        // 12. Full Clearance (All Done)
        AgreementTemplate::updateOrCreate(
            ['title' => 'Final Process Completion & Full Clearance Certificate'],
            [
                'type' => 'clearance_full',
                'country_id' => null,
                'layout_type' => 'pad',
                'header_space_mm' => 0,
                'sort_order' => 80,
                'is_active' => true,
                'docx_path' => 'storage/app/templates/other/document_declaration.docx',
                'content' => $this->getFullClearanceContent(),
            ]
        );
    }

    private function getAgreementContent(string $country, string $formatNote): string
    {
        return <<<HTML
<div class="agreement-document">
    <div style="text-align: center; margin-bottom: 25px;">
        <h2 style="font-size: 18pt; font-weight: bold; text-transform: uppercase; margin: 0 0 6px 0; color: #1e293b;">STUDENT CONSULTANCY AGREEMENT</h2>
        <p style="font-size: 11pt; color: #64748b; margin: 0;">Destination: <strong>[COUNTRY_NAME]</strong> | Layout: <strong>{$formatNote}</strong></p>
    </div>

    <p style="font-size: 11pt; line-height: 1.6; text-align: justify; margin-bottom: 18px;">
        This Consultancy Service Agreement (hereinafter referred to as the <strong>"Agreement"</strong>) is entered into on this day of <strong>[DATE]</strong>, by and between:
    </p>

    <div style="border-left: 4px solid #2563eb; padding-left: 14px; margin-bottom: 18px;">
        <p style="font-size: 11pt; line-height: 1.6; margin: 0 0 6px 0;">
            <strong>FIRST PARTY:</strong> <strong>INSAF IMMIGRATION CONSULTANCY</strong>, having its principal registered office at Dhaka, Bangladesh, represented by its authorized executive (hereinafter referred to as the <em>"Consultancy"</em>).
        </p>
        <p style="font-size: 11pt; line-height: 1.6; margin: 0;">
            <strong>AND</strong>
        </p>
        <p style="font-size: 11pt; line-height: 1.6; margin: 6px 0 0 0;">
            <strong>SECOND PARTY:</strong> The Student / Legal Guardian as described in the particulars below (hereinafter referred to as the <em>"Student"</em>).
        </p>
    </div>

    <h3 style="font-size: 12pt; font-weight: bold; margin: 18px 0 8px 0; border-bottom: 1px solid #cbd5e1; padding-bottom: 4px; color: #0f172a;">1. STUDENT PARTICULARS</h3>
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 18px; font-size: 10.5pt;">
        <tr>
            <td style="padding: 6px 10px; border: 1px solid #cbd5e1; width: 25%; font-weight: bold; background: #f8fafc;">Student Full Name:</td>
            <td style="padding: 6px 10px; border: 1px solid #cbd5e1; width: 40%;"><strong>[APPLICANT_NAME]</strong></td>
            <td style="padding: 6px 10px; border: 1px solid #cbd5e1; width: 15%; font-weight: bold; background: #f8fafc;">Passport No:</td>
            <td style="padding: 6px 10px; border: 1px solid #cbd5e1; width: 20%;">[PASSPORT_NO]</td>
        </tr>
        <tr>
            <td style="padding: 6px 10px; border: 1px solid #cbd5e1; font-weight: bold; background: #f8fafc;">Father's Name:</td>
            <td style="padding: 6px 10px; border: 1px solid #cbd5e1;">[FATHER_NAME]</td>
            <td style="padding: 6px 10px; border: 1px solid #cbd5e1; font-weight: bold; background: #f8fafc;">Mother's Name:</td>
            <td style="padding: 6px 10px; border: 1px solid #cbd5e1;">[MOTHER_NAME]</td>
        </tr>
        <tr>
            <td style="padding: 6px 10px; border: 1px solid #cbd5e1; font-weight: bold; background: #f8fafc;">Phone:</td>
            <td style="padding: 6px 10px; border: 1px solid #cbd5e1;">[PHONE]</td>
            <td style="padding: 6px 10px; border: 1px solid #cbd5e1; font-weight: bold; background: #f8fafc;">Address:</td>
            <td style="padding: 6px 10px; border: 1px solid #cbd5e1;">[ADDRESS]</td>
        </tr>
        <tr>
            <td style="padding: 6px 10px; border: 1px solid #cbd5e1; font-weight: bold; background: #f8fafc;">Destination Country:</td>
            <td style="padding: 6px 10px; border: 1px solid #cbd5e1;"><strong>[COUNTRY_NAME]</strong></td>
            <td style="padding: 6px 10px; border: 1px solid #cbd5e1; font-weight: bold; background: #f8fafc;">Intake:</td>
            <td style="padding: 6px 10px; border: 1px solid #cbd5e1;">[INTAKE_NAME]</td>
        </tr>
        <tr>
            <td style="padding: 6px 10px; border: 1px solid #cbd5e1; font-weight: bold; background: #f8fafc;">Target University / Course:</td>
            <td style="padding: 6px 10px; border: 1px solid #cbd5e1;" colspan="3">[UNIVERSITY_NAME] - [COURSE_NAME]</td>
        </tr>
        <tr>
            <td style="padding: 6px 10px; border: 1px solid #cbd5e1; font-weight: bold; background: #f8fafc;">Agreed Package (BDT):</td>
            <td style="padding: 6px 10px; border: 1px solid #cbd5e1;" colspan="3"><strong>[SERVICE_CHARGE] (In Words: [SERVICE_CHARGE_WORDS])</strong></td>
        </tr>
    </table>

    <h3 style="font-size: 12pt; font-weight: bold; margin: 18px 0 8px 0; border-bottom: 1px solid #cbd5e1; padding-bottom: 4px; color: #0f172a;">2. TERMS & CONDITIONS</h3>
    <ol style="font-size: 10.5pt; line-height: 1.7; margin: 0 0 18px 20px; padding: 0;">
        <li><strong>Scope of Service:</strong> The Consultancy shall provide professional assistance including admission processing, offer letter issuance, document apostille/attestation guidance, visa application preparation, and interview grooming.</li>
        <li><strong>Document Authenticity:</strong> The Student certifies that all academic certificates, marks sheets, passport copies, and financial records submitted are genuine and authentic. Any forgery or misrepresentation shall cause immediate termination without refund.</li>
        <li><strong>Installment Schedule:</strong> Payments shall strictly follow agreed project milestones (Application/Offer Letter, Visa Approval / EMGS Approval, Final Clearance).</li>
        <li><strong>Embassy Discretion:</strong> The Student acknowledges that issuance of entry permits and visas remains the sole sovereign discretion of the respective embassy/government authorities.</li>
        <li><strong>Refund & Cancellation:</strong> In the event of standard visa refusal under genuine conditions with official refusal letter, refunds shall be processed after deducting authorized institutional/application expenditures.</li>
    </ol>

    <h3 style="font-size: 12pt; font-weight: bold; margin: 24px 0 12px 0; color: #0f172a;">3. EXECUTION & ACKNOWLEDGMENT</h3>
    <p style="font-size: 10.5pt; line-height: 1.5; margin-bottom: 30px;">
        Both parties have read, understood, and voluntarily signed this agreement with sound mind and without any undue coercion.
    </p>

    <table style="width: 100%; border: none; margin-top: 40px; font-size: 10.5pt;">
        <tr>
            <td style="width: 45%; vertical-align: top; text-align: center;">
                <div style="border-top: 1px solid #475569; width: 80%; margin: 0 auto 6px auto;"></div>
                <strong>Student / Guardian Signature</strong><br>
                <span>Name: [APPLICANT_NAME]</span><br>
                <span>Date: [DATE]</span>
            </td>
            <td style="width: 10%;"></td>
            <td style="width: 45%; vertical-align: top; text-align: center;">
                <div style="border-top: 1px solid #475569; width: 80%; margin: 0 auto 6px auto;"></div>
                <strong>For Insaf Immigration Consultancy</strong><br>
                <span>Authorized Signatory & Seal</span><br>
                <span>Date: [DATE]</span>
            </td>
        </tr>
    </table>
</div>
HTML;
    }

    private function getWithdrawalClearanceContent(): string
    {
        return <<<HTML
<div class="clearance-document">
    <div style="text-align: center; margin-bottom: 25px;">
        <h2 style="font-size: 17pt; font-weight: bold; text-transform: uppercase; margin: 0 0 6px 0; color: #991b1b;">FILE WITHDRAWAL & SETTLEMENT CLEARANCE</h2>
        <p style="font-size: 11pt; color: #64748b; margin: 0;">Official Release Certificate & Mutual Discharge</p>
    </div>

    <p style="font-size: 11pt; line-height: 1.6; text-align: justify; margin-bottom: 18px;">
        This Certificate of File Withdrawal and Settlement is executed on this day of <strong>[DATE]</strong>, certifying that the student specified below has officially requested cancellation and withdrawal of their student visa processing file with <strong>Insaf Immigration Consultancy</strong>.
    </p>

    <table style="width: 100%; border-collapse: collapse; margin-bottom: 18px; font-size: 10.5pt;">
        <tr>
            <td style="padding: 6px 10px; border: 1px solid #cbd5e1; width: 25%; font-weight: bold; background: #f8fafc;">Student Name:</td>
            <td style="padding: 6px 10px; border: 1px solid #cbd5e1; width: 40%;"><strong>[APPLICANT_NAME]</strong></td>
            <td style="padding: 6px 10px; border: 1px solid #cbd5e1; width: 15%; font-weight: bold; background: #f8fafc;">Passport No:</td>
            <td style="padding: 6px 10px; border: 1px solid #cbd5e1; width: 20%;">[PASSPORT_NO]</td>
        </tr>
        <tr>
            <td style="padding: 6px 10px; border: 1px solid #cbd5e1; font-weight: bold; background: #f8fafc;">Applied Country:</td>
            <td style="padding: 6px 10px; border: 1px solid #cbd5e1;">[COUNTRY_NAME]</td>
            <td style="padding: 6px 10px; border: 1px solid #cbd5e1; font-weight: bold; background: #f8fafc;">Contact No:</td>
            <td style="padding: 6px 10px; border: 1px solid #cbd5e1;">[PHONE]</td>
        </tr>
        <tr>
            <td style="padding: 6px 10px; border: 1px solid #cbd5e1; font-weight: bold; background: #f8fafc;">Reason for Withdrawal:</td>
            <td style="padding: 6px 10px; border: 1px solid #cbd5e1;" colspan="3">Personal Circumstances / Mutual File Settlement</td>
        </tr>
    </table>

    <h3 style="font-size: 12pt; font-weight: bold; margin: 18px 0 8px 0; border-bottom: 1px solid #cbd5e1; padding-bottom: 4px; color: #0f172a;">1. ORIGINAL DOCUMENTS RETURN CHECKLIST</h3>
    <p style="font-size: 10.5pt; margin: 0 0 10px 0;">The Student hereby acknowledges safe return and receipt of all physical original documents held by the consultancy:</p>
    <ul style="font-size: 10.5pt; line-height: 1.7; margin: 0 0 18px 20px;">
        <li>[ &check; ] Original Passport</li>
        <li>[ &check; ] Academic Certificates & Transcripts (SSC / HSC / Bachelor's)</li>
        <li>[ &check; ] Police Clearance Certificate / Medical Reports</li>
        <li>[ &check; ] Bank Statements / Solvency Documents / Affidavits</li>
        <li>[ &check; ] [DOCUMENTS_LIST]</li>
    </ul>

    <h3 style="font-size: 12pt; font-weight: bold; margin: 18px 0 8px 0; border-bottom: 1px solid #cbd5e1; padding-bottom: 4px; color: #0f172a;">2. FINANCIAL SETTLEMENT SUMMARY</h3>
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 18px; font-size: 10.5pt;">
        <tr>
            <td style="padding: 6px 10px; border: 1px solid #cbd5e1; width: 50%; background: #f8fafc;">Total Amount Paid to Date:</td>
            <td style="padding: 6px 10px; border: 1px solid #cbd5e1; width: 50%;">[AMOUNT]</td>
        </tr>
        <tr>
            <td style="padding: 6px 10px; border: 1px solid #cbd5e1; background: #f8fafc;">Final Refund Paid to Student:</td>
            <td style="padding: 6px 10px; border: 1px solid #cbd5e1;">[AMOUNT] ([AMOUNT_WORDS])</td>
        </tr>
        <tr>
            <td style="padding: 6px 10px; border: 1px solid #cbd5e1; font-weight: bold; background: #f1f5f9;">Outstanding Balance Remaining:</td>
            <td style="padding: 6px 10px; border: 1px solid #cbd5e1; font-weight: bold; color: #16a34a;">BDT 0.00 (NIL - FULLY SETTLED)</td>
        </tr>
    </table>

    <h3 style="font-size: 12pt; font-weight: bold; margin: 18px 0 8px 0; color: #0f172a;">3. MUTUAL DISCHARGE & DECLARATION</h3>
    <p style="font-size: 10.5pt; line-height: 1.6; text-align: justify; margin-bottom: 30px;">
        Both the Student and Insaf Immigration Consultancy hereby mutually release and forever discharge each other from any and all claims, financial dues, or legal liabilities arising from the aforementioned file.
    </p>

    <table style="width: 100%; border: none; margin-top: 40px; font-size: 10.5pt;">
        <tr>
            <td style="width: 45%; vertical-align: top; text-align: center;">
                <div style="border-top: 1px solid #475569; width: 80%; margin: 0 auto 6px auto;"></div>
                <strong>Student Signature (Received All Originals)</strong><br>
                <span>Name: [APPLICANT_NAME]</span><br>
                <span>Date: [DATE]</span>
            </td>
            <td style="width: 10%;"></td>
            <td style="width: 45%; vertical-align: top; text-align: center;">
                <div style="border-top: 1px solid #475569; width: 80%; margin: 0 auto 6px auto;"></div>
                <strong>Authorized Accounts & Admin Officer</strong><br>
                <span>Insaf Immigration Consultancy</span><br>
                <span>Date: [DATE]</span>
            </td>
        </tr>
    </table>
</div>
HTML;
    }

    private function getFullClearanceContent(): string
    {
        return <<<HTML
<div class="clearance-document">
    <div style="text-align: center; margin-bottom: 25px;">
        <h2 style="font-size: 17pt; font-weight: bold; text-transform: uppercase; margin: 0 0 6px 0; color: #15803d;">FINAL PROCESS COMPLETION & FULL CLEARANCE</h2>
        <p style="font-size: 11pt; color: #64748b; margin: 0;">Successful Immigration & Travel Completion Certificate</p>
    </div>

    <p style="font-size: 11pt; line-height: 1.6; text-align: justify; margin-bottom: 18px;">
        This is to certify that the student named below has successfully completed all stages of their student visa application, documentation, visa grant, and departure preparation with <strong>Insaf Immigration Consultancy</strong>.
    </p>

    <table style="width: 100%; border-collapse: collapse; margin-bottom: 18px; font-size: 10.5pt;">
        <tr>
            <td style="padding: 6px 10px; border: 1px solid #cbd5e1; width: 25%; font-weight: bold; background: #f8fafc;">Student Name:</td>
            <td style="padding: 6px 10px; border: 1px solid #cbd5e1; width: 40%;"><strong>[APPLICANT_NAME]</strong></td>
            <td style="padding: 6px 10px; border: 1px solid #cbd5e1; width: 15%; font-weight: bold; background: #f8fafc;">Passport No:</td>
            <td style="padding: 6px 10px; border: 1px solid #cbd5e1; width: 20%;">[PASSPORT_NO]</td>
        </tr>
        <tr>
            <td style="padding: 6px 10px; border: 1px solid #cbd5e1; font-weight: bold; background: #f8fafc;">Destination Country:</td>
            <td style="padding: 6px 10px; border: 1px solid #cbd5e1;"><strong>[COUNTRY_NAME]</strong></td>
            <td style="padding: 6px 10px; border: 1px solid #cbd5e1; font-weight: bold; background: #f8fafc;">Visa Status:</td>
            <td style="padding: 6px 10px; border: 1px solid #cbd5e1; font-weight: bold; color: #15803d;">APPROVED / ISSUED</td>
        </tr>
        <tr>
            <td style="padding: 6px 10px; border: 1px solid #cbd5e1; font-weight: bold; background: #f8fafc;">University / Institution:</td>
            <td style="padding: 6px 10px; border: 1px solid #cbd5e1;" colspan="3">[UNIVERSITY_NAME]</td>
        </tr>
        <tr>
            <td style="padding: 6px 10px; border: 1px solid #cbd5e1; font-weight: bold; background: #f8fafc;">Delivery Checklist:</td>
            <td style="padding: 6px 10px; border: 1px solid #cbd5e1;" colspan="3">Original Passport with Approved Visa, University Acceptance, Travel Itinerary</td>
        </tr>
    </table>

    <h3 style="font-size: 12pt; font-weight: bold; margin: 18px 0 8px 0; border-bottom: 1px solid #cbd5e1; padding-bottom: 4px; color: #0f172a;">1. DELIVERABLES HANDOVER CHECKLIST</h3>
    <ul style="font-size: 10.5pt; line-height: 1.7; margin: 0 0 18px 20px;">
        <li>[ &check; ] Original Passport with Approved Visa Sticker / eVisa Document</li>
        <li>[ &check; ] Official University Acceptance / Offer Letter & EMGS / Embassy Approval Documents</li>
        <li>[ &check; ] Confirmed Air Ticket & Travel Itinerary</li>
        <li>[ &check; ] Pre-Departure Briefing & Airport Arrival Guidelines Completed</li>
        <li>[ &check; ] Return of all auxiliary original documents</li>
    </ul>

    <h3 style="font-size: 12pt; font-weight: bold; margin: 18px 0 8px 0; border-bottom: 1px solid #cbd5e1; padding-bottom: 4px; color: #0f172a;">2. FINAL ACCOUNT STATUS</h3>
    <p style="font-size: 10.5pt; line-height: 1.6; margin-bottom: 15px;">
        The Accounts Department confirms that the total consultancy package and all associated service charges have been <strong>PAID IN FULL</strong>. There is <strong>ZERO (0.00)</strong> outstanding balance due.
    </p>

    <h3 style="font-size: 12pt; font-weight: bold; margin: 24px 0 12px 0; color: #0f172a;">3. SATISFACTION & CLEARANCE SIGN-OFF</h3>
    <p style="font-size: 10.5pt; line-height: 1.5; margin-bottom: 30px;">
        I, the student, confirm that I have received all my original documents, visa approvals, and flight guidance in full satisfaction.
    </p>

    <table style="width: 100%; border: none; margin-top: 40px; font-size: 10.5pt;">
        <tr>
            <td style="width: 45%; vertical-align: top; text-align: center;">
                <div style="border-top: 1px solid #475569; width: 80%; margin: 0 auto 6px auto;"></div>
                <strong>Student Signature</strong><br>
                <span>Name: [APPLICANT_NAME]</span><br>
                <span>Date: [DATE]</span>
            </td>
            <td style="width: 10%;"></td>
            <td style="width: 45%; vertical-align: top; text-align: center;">
                <div style="border-top: 1px solid #475569; width: 80%; margin: 0 auto 6px auto;"></div>
                <strong>Managing Director / Authorized Officer</strong><br>
                <span>Insaf Immigration Consultancy</span><br>
                <span>Date: [DATE]</span>
            </td>
        </tr>
    </table>
</div>
HTML;
    }
}
