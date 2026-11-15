<?php

namespace Database\Seeders;

use App\Models\AgreementTemplate;
use Illuminate\Database\Seeder;

class OtherTemplatesSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Backfill docx_path on existing agreement templates
        $agreements = AgreementTemplate::agreements()->with('country')->get();
        foreach ($agreements as $t) {
            if ($t->docx_path) {
                continue;
            }
            $country = strtolower($t->country->name ?? '');
            $layout = $t->layout_type === 'stamp' ? 'stamp' : 'pad';
            if (str_contains($country, 'malta')) {
                $t->update(['docx_path' => "storage/app/templates/malta_agreement_{$layout}.docx"]);
            } elseif (str_contains($country, 'korea')) {
                $t->update(['docx_path' => "storage/app/templates/korea_agreement_{$layout}.docx"]);
            } else {
                $t->update(['docx_path' => "storage/app/templates/default_agreement_{$layout}.docx"]);
            }
        }

        // 2. Seed the 5 "Other" Declaration / Receipt / Acknowledgement Templates
        $templates = [
            [
                'type' => 'other',
                'layout_type' => 'pad',
                'country_id' => null,
                'header_space_mm' => 0,
                'sort_order' => 1,
                'docx_path' => 'storage/app/templates/other/document_declaration.docx',
                'title' => 'Document Declaration (Original Documents Return)',
                'content' => <<<'HTML'
<div class="agreement-document">
    <div style="text-align: center; margin-bottom: 18px;">
        <h2 style="font-size: 16pt; font-weight: bold; text-transform: uppercase; margin: 0; color: #1e293b;">
            DOCUMENT DECLARATION
        </h2>
    </div>
    <p style="font-size: 10.5pt; margin-bottom: 14px;"><strong>Date:</strong> [DATE]</p>
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 16px; font-size: 10.5pt;">
        <tr><td style="border: 1px solid #cbd5e1; padding: 6px 10px; width: 32%; font-weight: bold;">Applicant Name</td><td style="border: 1px solid #cbd5e1; padding: 6px 10px;">[APPLICANT_NAME]</td></tr>
        <tr><td style="border: 1px solid #cbd5e1; padding: 6px 10px; font-weight: bold;">Father's Name</td><td style="border: 1px solid #cbd5e1; padding: 6px 10px;">[FATHER_NAME]</td></tr>
        <tr><td style="border: 1px solid #cbd5e1; padding: 6px 10px; font-weight: bold;">Mother's Name</td><td style="border: 1px solid #cbd5e1; padding: 6px 10px;">[MOTHER_NAME]</td></tr>
        <tr><td style="border: 1px solid #cbd5e1; padding: 6px 10px; font-weight: bold;">Passport No</td><td style="border: 1px solid #cbd5e1; padding: 6px 10px;">[PASSPORT_NO]</td></tr>
        <tr><td style="border: 1px solid #cbd5e1; padding: 6px 10px; font-weight: bold;">Documents</td><td style="border: 1px solid #cbd5e1; padding: 6px 10px;">[DOCUMENTS_LIST]</td></tr>
    </table>
    <h4 style="font-size: 11.5pt; font-weight: bold; margin: 14px 0 8px;">Declaration</h4>
    <p style="font-size: 10.5pt; line-height: 1.6; text-align: justify; margin-bottom: 10px;">
        I hereby confirm that, upon the successful completion of my entire visa processing and after receiving my visa, I have received all of my original documents from Insaf Immigration in full and in satisfactory condition.
    </p>
    <p style="font-size: 10.5pt; line-height: 1.6; text-align: justify; margin-bottom: 24px;">
        I further confirm that I have no allegation, complaint, claim, or outstanding dispute against Insaf Immigration regarding the services provided to me or the return of my documents.
    </p>
    <table style="width: 100%; border-collapse: collapse; margin-top: 30px; font-size: 10.5pt;">
        <tr>
            <td style="width: 50%; vertical-align: top;">Applicant Signature:<br><br>____________________<br>Date: [DATE]</td>
            <td style="width: 50%; vertical-align: top;">Authorized Signature:<br><br>____________________<br>Date: [DATE]</td>
        </tr>
    </table>
</div>
HTML,
            ],
            [
                'type' => 'other',
                'layout_type' => 'pad',
                'country_id' => null,
                'header_space_mm' => 0,
                'sort_order' => 2,
                'docx_path' => 'storage/app/templates/other/document_received_acknowledgement.docx',
                'title' => 'Document Receipt Acknowledgement',
                'content' => <<<'HTML'
<div class="agreement-document">
    <div style="text-align: center; margin-bottom: 18px;">
        <h2 style="font-size: 16pt; font-weight: bold; margin: 0; color: #1e293b;">
            Document Receipt Acknowledgement
        </h2>
    </div>
    <p style="font-size: 10.5pt; line-height: 1.6; margin-bottom: 14px;">
        This is to certify that Insaf Immigration has received the following original documents from the student:
    </p>
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 16px; font-size: 10.5pt;">
        <tr><td style="border: 1px solid #cbd5e1; padding: 6px 10px; width: 32%; font-weight: bold;">Applicant Name</td><td style="border: 1px solid #cbd5e1; padding: 6px 10px;">[APPLICANT_NAME]</td></tr>
        <tr><td style="border: 1px solid #cbd5e1; padding: 6px 10px; font-weight: bold;">Passport No.</td><td style="border: 1px solid #cbd5e1; padding: 6px 10px;">[PASSPORT_NO]</td></tr>
        <tr><td style="border: 1px solid #cbd5e1; padding: 6px 10px; font-weight: bold;">Father's Name</td><td style="border: 1px solid #cbd5e1; padding: 6px 10px;">[FATHER_NAME]</td></tr>
        <tr><td style="border: 1px solid #cbd5e1; padding: 6px 10px; font-weight: bold;">Mother's Name</td><td style="border: 1px solid #cbd5e1; padding: 6px 10px;">[MOTHER_NAME]</td></tr>
    </table>
    <p style="font-size: 10.5pt; font-weight: bold; margin-bottom: 6px;">Received Documents:</p>
    <p style="font-size: 10.5pt; margin-bottom: 14px; padding-left: 16px;">[DOCUMENTS_LIST]</p>
    <p style="font-size: 10.5pt; line-height: 1.6; text-align: justify; margin-bottom: 18px;">
        The above-mentioned documents have been duly received and are currently in the custody of Insaf Immigration for the purpose of processing.
    </p>
    <p style="font-size: 10.5pt; margin-bottom: 26px;"><strong>Date:</strong> [DATE]</p>
    <table style="width: 100%; border-collapse: collapse; margin-top: 24px; font-size: 10.5pt;">
        <tr>
            <td style="width: 50%; vertical-align: top;">Authorized Signature: _____________<br>Date: [DATE]</td>
            <td style="width: 50%; vertical-align: top;">Student Signature: _____________<br>Date: [DATE]</td>
        </tr>
    </table>
</div>
HTML,
            ],
            [
                'type' => 'other',
                'layout_type' => 'pad',
                'country_id' => null,
                'header_space_mm' => 0,
                'sort_order' => 3,
                'docx_path' => 'storage/app/templates/other/file_receive_agreement.docx',
                'title' => 'File Receive Agreement (Original Documents Custody)',
                'content' => <<<'HTML'
<div class="agreement-document">
    <p style="font-size: 11pt; margin-bottom: 14px;"><strong>Date:</strong> [DATE]</p>
    <h3 style="font-size: 13pt; font-weight: bold; text-align: center; margin-bottom: 16px;">Covenant</h3>
    <p style="font-size: 10.5pt; line-height: 1.6; text-align: justify; margin-bottom: 12px;">
        This is to confirm that <strong>[APPLICANT_NAME]</strong>, Passport No. <strong>[PASSPORT_NO]</strong>, has temporarily submitted [DOCUMENTS_LIST] to our office for security and verification purposes related to [COUNTRY_NAME] student visa application.
    </p>
    <p style="font-size: 10.5pt; line-height: 1.6; text-align: justify; margin-bottom: 28px;">
        If the applicant decides to withdraw his/her application after signing this agreement and submitting the application, he/she will be required to pay <strong>[AMOUNT] ([AMOUNT_WORDS])</strong> to Insaf Immigration as compensation.
    </p>
    <table style="width: 100%; border-collapse: collapse; margin-top: 32px; font-size: 10.5pt;">
        <tr>
            <td style="width: 50%; vertical-align: top;">Signature of Applicant<br>Name: [APPLICANT_NAME]<br>Date: [DATE]</td>
            <td style="width: 50%; vertical-align: top;">Received By:<br>______________________<br>Insaf Immigration</td>
        </tr>
    </table>
</div>
HTML,
            ],
            [
                'type' => 'other',
                'layout_type' => 'pad',
                'country_id' => null,
                'header_space_mm' => 0,
                'sort_order' => 4,
                'docx_path' => 'storage/app/templates/other/refund_confirmation_declaration.docx',
                'title' => 'Refund Confirmation and No-Claim Declaration',
                'content' => <<<'HTML'
<div class="agreement-document">
    <div style="text-align: center; margin-bottom: 18px;">
        <h2 style="font-size: 15.5pt; font-weight: bold; text-transform: uppercase; margin: 0; color: #1e293b;">
            REFUND CONFIRMATION AND NO-CLAIM DECLARATION
        </h2>
    </div>
    <p style="font-size: 10.5pt; margin-bottom: 14px;"><strong>Date:</strong> [DATE]</p>
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 16px; font-size: 10.5pt;">
        <tr><td style="border: 1px solid #cbd5e1; padding: 6px 10px; width: 32%; font-weight: bold;">Applicant Name</td><td style="border: 1px solid #cbd5e1; padding: 6px 10px;">[APPLICANT_NAME]</td></tr>
        <tr><td style="border: 1px solid #cbd5e1; padding: 6px 10px; font-weight: bold;">Father's Name</td><td style="border: 1px solid #cbd5e1; padding: 6px 10px;">[FATHER_NAME]</td></tr>
        <tr><td style="border: 1px solid #cbd5e1; padding: 6px 10px; font-weight: bold;">Mother's Name</td><td style="border: 1px solid #cbd5e1; padding: 6px 10px;">[MOTHER_NAME]</td></tr>
        <tr><td style="border: 1px solid #cbd5e1; padding: 6px 10px; font-weight: bold;">Passport No</td><td style="border: 1px solid #cbd5e1; padding: 6px 10px;">[PASSPORT_NO]</td></tr>
        <tr><td style="border: 1px solid #cbd5e1; padding: 6px 10px; font-weight: bold;">Amount</td><td style="border: 1px solid #cbd5e1; padding: 6px 10px;">[AMOUNT] ([AMOUNT_WORDS])</td></tr>
    </table>
    <h4 style="font-size: 11.5pt; font-weight: bold; margin: 14px 0 8px;">Declaration</h4>
    <p style="font-size: 10.5pt; line-height: 1.6; text-align: justify; margin-bottom: 10px;">
        I hereby irrevocably acknowledge and confirm that I have received the entire agreed refund amount from Insaf Immigration in full, with no outstanding balance, dues, or further amount payable to me in connection with the refund. I confirm that the refund has been received and settled to my full satisfaction.
    </p>
    <p style="font-size: 10.5pt; line-height: 1.6; text-align: justify; margin-bottom: 10px;">
        I further confirm that I have no allegation, complaint, claim, or outstanding dispute against Insaf Immigration regarding the services provided to me or the return of my money. I acknowledge that, to the extent permitted by applicable law, no further refund, compensation, reimbursement, or monetary claim remains outstanding in relation to the matters covered by this declaration.
    </p>
    <p style="font-size: 10.5pt; line-height: 1.6; text-align: justify; margin-bottom: 10px;">
        I further undertake and agree that neither I nor any person acting on my behalf, with my knowledge, consent, or instruction, shall post, comment, share, publish, or make any statement, allegation, or accusation on social media or any other public platform about Insaf Immigration, or about any of its directors, employees, staff, representatives, or associates. I understand that if I, or anyone acting on my behalf, does so, Insaf Immigration shall have the full right and authority to take appropriate legal action against me and/or such person, in accordance with applicable law.
    </p>
    <p style="font-size: 10.5pt; line-height: 1.6; text-align: justify; margin-bottom: 22px;">
        I make this declaration voluntarily, with full understanding of its contents and implications, and confirm that the statements made herein are true and accurate to the best of my knowledge.
    </p>
    <table style="width: 100%; border-collapse: collapse; margin-top: 24px; font-size: 10.5pt;">
        <tr>
            <td style="width: 50%; vertical-align: top;">Applicant Signature:<br><br>____________________<br>Date: [DATE]</td>
            <td style="width: 50%; vertical-align: top;">Authorized Signature:<br><br>____________________<br>Date: [DATE]</td>
        </tr>
    </table>
</div>
HTML,
            ],
            [
                'type' => 'other',
                'layout_type' => 'pad',
                'country_id' => null,
                'header_space_mm' => 0,
                'sort_order' => 5,
                'docx_path' => 'storage/app/templates/other/security_cheque_receipt.docx',
                'title' => 'Security Cheque Receipt Declaration',
                'content' => <<<'HTML'
<div class="agreement-document">
    <div style="text-align: center; margin-bottom: 18px;">
        <h2 style="font-size: 15.5pt; font-weight: bold; text-transform: uppercase; margin: 0; color: #1e293b;">
            SECURITY CHEQUE RECEIPT DECLARATION
        </h2>
    </div>
    <p style="font-size: 10.5pt; margin-bottom: 12px;"><strong>Date:</strong> [DATE]</p>
    <p style="font-size: 10.5pt; line-height: 1.6; text-align: justify; margin-bottom: 10px;">
        This is to declare that <strong>[APPLICANT_NAME]</strong>, Passport/NID No. <strong>[PASSPORT_NO]</strong>, has submitted a security cheque to Insaf Immigration as security against the agreed terms and conditions of the service.
    </p>
    <p style="font-size: 10.5pt; line-height: 1.6; text-align: justify; margin-bottom: 14px;">
        The security cheque has been received and kept in the custody of the company for security purposes. The cheque will be handled in accordance with the mutually agreed terms and conditions between the student and the company.
    </p>
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 10.5pt;">
        <tr><td style="border: 1px solid #cbd5e1; padding: 6px 10px; width: 32%; font-weight: bold;">Applicant Name</td><td style="border: 1px solid #cbd5e1; padding: 6px 10px;">[APPLICANT_NAME]</td></tr>
        <tr><td style="border: 1px solid #cbd5e1; padding: 6px 10px; font-weight: bold;">Father's Name</td><td style="border: 1px solid #cbd5e1; padding: 6px 10px;">[FATHER_NAME]</td></tr>
        <tr><td style="border: 1px solid #cbd5e1; padding: 6px 10px; font-weight: bold;">Mother's Name</td><td style="border: 1px solid #cbd5e1; padding: 6px 10px;">[MOTHER_NAME]</td></tr>
        <tr><td style="border: 1px solid #cbd5e1; padding: 6px 10px; font-weight: bold;">Passport No</td><td style="border: 1px solid #cbd5e1; padding: 6px 10px;">[PASSPORT_NO]</td></tr>
        <tr><td style="border: 1px solid #cbd5e1; padding: 6px 10px; font-weight: bold;">Cheque Amount</td><td style="border: 1px solid #cbd5e1; padding: 6px 10px;">[AMOUNT] ([AMOUNT_WORDS])</td></tr>
        <tr><td style="border: 1px solid #cbd5e1; padding: 6px 10px; font-weight: bold;">Cheque No</td><td style="border: 1px solid #cbd5e1; padding: 6px 10px;">[CHEQUE_NO]</td></tr>
    </table>
    <table style="width: 100%; border-collapse: collapse; margin-top: 24px; font-size: 10.5pt;">
        <tr>
            <td style="width: 50%; vertical-align: top;">Applicant Signature:<br><br>____________________<br>Date: [DATE]</td>
            <td style="width: 50%; vertical-align: top;">Authorized Signature:<br><br>____________________<br>Date: [DATE]</td>
        </tr>
    </table>
</div>
HTML,
            ],
        ];

        foreach ($templates as $data) {
            AgreementTemplate::updateOrCreate(
                ['type' => $data['type'], 'title' => $data['title']],
                array_merge($data, ['is_active' => true])
            );
        }
    }
}

