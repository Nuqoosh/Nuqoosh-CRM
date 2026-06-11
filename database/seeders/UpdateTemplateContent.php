<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateTemplateContent extends Seeder
{
    public function run(): void
    {
        // ═══════════════════════════════════════════════════════════════
        // ID 7 — VMC NDA
        // ═══════════════════════════════════════════════════════════════
        DB::table('document_templates')->where('id', 7)->update(['content' => '
<div class="section-title">1. Introduction</div>
<p>
    This Non-Disclosure Agreement ("Agreement") is entered into as of <strong>{{contract_date}}</strong>,
    by and between <strong>{{company_name}}</strong> ("Disclosing Party"), and
    <strong>{{client_name}}</strong> ("Receiving Party"), collectively referred to as the "Parties."
</p>

<div class="section-title">2. Definition of Confidential Information</div>
<p>
    "Confidential Information" means any data or information that is proprietary to the Disclosing Party
    and not generally known to the public, whether in tangible or intangible form, including but not limited to:
    business plans, financial data, client lists, marketing strategies, trade secrets, technical information,
    and any other information designated as confidential.
</p>

<div class="section-title">3. Obligations of Receiving Party</div>
<p>The Receiving Party agrees to:</p>
<ul>
    <li>Hold all Confidential Information in strict confidence.</li>
    <li>Not disclose Confidential Information to any third party without prior written consent.</li>
    <li>Use the Confidential Information solely for the purpose of evaluating a potential business relationship.</li>
    <li>Protect the Confidential Information with at least the same degree of care used for its own confidential information.</li>
</ul>

<div class="section-title">4. Exclusions</div>
<p>This Agreement does not apply to information that:</p>
<ul>
    <li>Is or becomes publicly known through no breach of this Agreement.</li>
    <li>Was rightfully known to the Receiving Party prior to disclosure.</li>
    <li>Is required to be disclosed by applicable law or court order.</li>
</ul>

<div class="section-title">5. Term</div>
<p>
    This Agreement shall remain in effect for a period of <strong>two (2) years</strong> from the date
    of execution, unless terminated earlier by mutual written consent of both Parties.
</p>

<div class="section-title">6. Return of Information</div>
<p>
    Upon request by the Disclosing Party, the Receiving Party shall promptly return or destroy
    all materials containing Confidential Information.
</p>

<div class="section-title">7. Governing Law</div>
<p>
    This Agreement shall be governed by and construed in accordance with the laws of the
    United Arab Emirates, without regard to conflict of law principles.
</p>

<div class="section-title">8. Entire Agreement</div>
<p>
    This Agreement constitutes the entire agreement between the Parties with respect to the
    subject matter herein and supersedes all prior discussions and agreements.
</p>
        ']);

        // ═══════════════════════════════════════════════════════════════
        // ID 8 — VMC Contract
        // ═══════════════════════════════════════════════════════════════
        DB::table('document_templates')->where('id', 8)->update(['content' => '
<div class="section-title">1. Parties</div>
<p>
    This Service Agreement ("Agreement") is entered into as of <strong>{{contract_date}}</strong>,
    between <strong>{{company_name}}</strong> ("Service Provider") and
    <strong>{{client_name}}</strong> ("Client").
</p>

<div class="section-title">2. Scope of Services</div>
<p>The Service Provider agrees to deliver the following services to the Client:</p>
<ul>
    <li>Strategic marketing planning and campaign management.</li>
    <li>Brand identity development and visual communication.</li>
    <li>Digital marketing including social media management and content creation.</li>
    <li>Performance reporting and analytics on a monthly basis.</li>
</ul>

<div class="section-title">3. Project Timeline</div>
<p>
    The project shall commence on <strong>{{contract_date}}</strong> and is expected to be
    completed by <strong>{{delivery_date}}</strong>. Any changes to the timeline must be
    agreed upon in writing by both Parties.
</p>

<div class="section-title">4. Payment Terms</div>
<p>
    The total project value is <strong>{{amount}}</strong>. Payment shall be made as follows:
</p>
<ul>
    <li><strong>50%</strong> advance payment upon signing this Agreement.</li>
    <li><strong>50%</strong> upon project completion and final delivery.</li>
</ul>
<p>
    All payments are non-refundable once the respective phase of work has commenced.
</p>

<div class="section-title">5. Intellectual Property</div>
<p>
    All deliverables produced under this Agreement shall become the property of the Client
    upon receipt of full payment. The Service Provider retains the right to use completed
    work in its portfolio unless otherwise agreed in writing.
</p>

<div class="section-title">6. Revisions</div>
<p>
    The Client is entitled to <strong>two (2) rounds of revisions</strong> per deliverable.
    Additional revisions will be billed at an agreed hourly rate.
</p>

<div class="section-title">7. Confidentiality</div>
<p>
    Both Parties agree to keep all business information, strategies, and data exchanged
    under this Agreement strictly confidential.
</p>

<div class="section-title">8. Termination</div>
<p>
    Either Party may terminate this Agreement with <strong>14 days written notice</strong>.
    Work completed up to the termination date shall be billed and payable by the Client.
</p>

<div class="section-title">9. Governing Law</div>
<p>
    This Agreement is governed by the laws of the United Arab Emirates.
</p>
        ']);

        // ═══════════════════════════════════════════════════════════════
        // ID 9 — Nuqoosh NDA
        // ═══════════════════════════════════════════════════════════════
        DB::table('document_templates')->where('id', 9)->update(['content' => '
<div class="section-title">1. Introduction</div>
<p>
    This Non-Disclosure Agreement ("Agreement") is entered into as of <strong>{{contract_date}}</strong>,
    between <strong>{{company_name}}</strong> ("Disclosing Party") and
    <strong>{{client_name}}</strong> of <strong>{{client_address}}</strong> ("Receiving Party").
</p>

<div class="section-title">2. Purpose</div>
<p>
    The Parties wish to explore a potential business relationship in the fields of marketing,
    branding, and public relations. In connection with this, the Disclosing Party may share
    certain proprietary and confidential information with the Receiving Party.
</p>

<div class="section-title">3. Confidential Information</div>
<p>Confidential Information includes, but is not limited to:</p>
<ul>
    <li>Marketing strategies, campaign concepts, and creative briefs.</li>
    <li>Client lists, pricing structures, and business plans.</li>
    <li>Design assets, brand guidelines, and unpublished content.</li>
    <li>Any information marked as "Confidential" or disclosed in a confidential context.</li>
</ul>

<div class="section-title">4. Obligations</div>
<p>The Receiving Party shall:</p>
<ul>
    <li>Keep all Confidential Information strictly confidential.</li>
    <li>Not reproduce, distribute, or disclose any Confidential Information to third parties.</li>
    <li>Use Confidential Information solely for the stated business purpose.</li>
    <li>Notify the Disclosing Party immediately upon any unauthorized disclosure.</li>
</ul>

<div class="section-title">5. Duration</div>
<p>
    This Agreement shall be effective for <strong>two (2) years</strong> from the date of signing.
    Obligations regarding Confidential Information shall survive termination of this Agreement.
</p>

<div class="section-title">6. Remedies</div>
<p>
    The Receiving Party acknowledges that any breach of this Agreement may cause irreparable harm
    to the Disclosing Party, and that monetary damages may be insufficient. The Disclosing Party
    shall be entitled to seek equitable relief in addition to other remedies.
</p>

<div class="section-title">7. Governing Law</div>
<p>
    This Agreement shall be governed by the laws applicable in the jurisdiction where
    <strong>{{company_name}}</strong> is registered and operates.
</p>
        ']);

        // ═══════════════════════════════════════════════════════════════
        // ID 10 — Website Only
        // ═══════════════════════════════════════════════════════════════
        DB::table('document_templates')->where('id', 10)->update(['content' => '
<div class="section-title">1. Project Overview</div>
<p>
    This Agreement is entered into on <strong>{{contract_date}}</strong> between
    <strong>{{company_name}}</strong> ("Agency") and <strong>{{client_name}}</strong> ("Client").
    The Agency agrees to design and develop a professional website for the Client as detailed herein.
</p>

<div class="section-title">2. Scope of Work</div>
<p>The following services are included in this contract:</p>
<ul>
    <li>Custom website design — up to <strong>5 pages</strong> (Home, About, Services, Portfolio, Contact).</li>
    <li>Responsive design compatible with desktop, tablet, and mobile devices.</li>
    <li>SEO-friendly structure and meta tag optimization.</li>
    <li>Integration of contact forms and social media links.</li>
    <li>Basic on-page content upload (content to be provided by Client).</li>
    <li>Browser compatibility testing across major browsers.</li>
</ul>

<div class="section-title">3. Client Responsibilities</div>
<p>The Client agrees to provide the following in a timely manner:</p>
<ul>
    <li>All text content, images, logos, and brand assets.</li>
    <li>Domain name and hosting credentials (if applicable).</li>
    <li>Timely feedback and approvals at each project milestone.</li>
</ul>

<div class="section-title">4. Timeline</div>
<p>
    The project will commence upon receipt of the advance payment and required assets.
    Estimated delivery: <strong>{{delivery_date}}</strong>.
    Delays caused by late Client feedback may extend the delivery timeline accordingly.
</p>

<div class="section-title">5. Payment</div>
<p>Total project cost: <strong>{{amount}}</strong></p>
<ul>
    <li><strong>50%</strong> — Due upon signing this Agreement.</li>
    <li><strong>50%</strong> — Due upon website handover.</li>
</ul>

<div class="section-title">6. Revisions</div>
<p>
    This contract includes <strong>three (3) rounds of design revisions</strong>.
    Additional revisions beyond this will be charged separately at an agreed rate.
</p>

<div class="section-title">7. Ownership</div>
<p>
    Full ownership of the website and all its assets transfers to the Client upon
    receipt of final payment in full.
</p>

<div class="section-title">8. Post-Launch Support</div>
<p>
    The Agency will provide <strong>30 days of complimentary support</strong> after launch
    to address any bugs or technical issues. This does not include new features or content changes.
</p>

<div class="section-title">9. Limitation of Liability</div>
<p>
    The Agency shall not be liable for any indirect, incidental, or consequential damages
    arising from the use of the delivered website.
</p>
        ']);

        // ═══════════════════════════════════════════════════════════════
        // ID 11 — Website + Branding
        // ═══════════════════════════════════════════════════════════════
        DB::table('document_templates')->where('id', 11)->update(['content' => '
<div class="section-title">1. Project Overview</div>
<p>
    This Agreement, dated <strong>{{contract_date}}</strong>, is between
    <strong>{{company_name}}</strong> ("Agency") and <strong>{{client_name}}</strong> ("Client").
    The Agency shall provide a complete Website Design and Brand Identity package as outlined below.
</p>

<div class="section-title">2. Scope of Work</div>

<h3>A. Branding Package</h3>
<ul>
    <li>Logo design — up to 3 initial concepts with refinements.</li>
    <li>Brand color palette and typography selection.</li>
    <li>Business card design.</li>
    <li>Letterhead and email signature design.</li>
    <li>Brand guidelines document (PDF).</li>
</ul>

<h3>B. Website Package</h3>
<ul>
    <li>Custom website design — up to <strong>7 pages</strong>.</li>
    <li>Fully responsive layout (mobile, tablet, desktop).</li>
    <li>Brand-consistent visual design using approved brand assets.</li>
    <li>SEO structure and meta optimization.</li>
    <li>Contact forms, social media integration.</li>
    <li>Browser compatibility testing.</li>
</ul>

<div class="section-title">3. Client Responsibilities</div>
<ul>
    <li>Provide all required content (text, images, existing brand assets if any).</li>
    <li>Provide timely approvals at each milestone to avoid delays.</li>
    <li>Provide domain and hosting access when required.</li>
</ul>

<div class="section-title">4. Timeline</div>
<p>
    Project commencement: Upon advance payment and asset receipt.<br>
    Estimated completion: <strong>{{delivery_date}}</strong>.
</p>

<div class="section-title">5. Payment</div>
<p>Total project value: <strong>{{amount}}</strong></p>
<ul>
    <li><strong>50%</strong> — Advance, due upon signing.</li>
    <li><strong>25%</strong> — Upon brand approval.</li>
    <li><strong>25%</strong> — Upon website launch.</li>
</ul>

<div class="section-title">6. Revisions</div>
<ul>
    <li>Logo: Up to <strong>3 rounds</strong> of revisions.</li>
    <li>Website: Up to <strong>3 rounds</strong> of revisions.</li>
    <li>Additional revisions billed separately.</li>
</ul>

<div class="section-title">7. Ownership & Rights</div>
<p>
    All brand assets and website files transfer to the Client upon full payment.
    The Agency retains the right to showcase completed work in its portfolio.
</p>

<div class="section-title">8. Post-Launch Support</div>
<p>
    <strong>30 days</strong> complimentary support post-launch for bug fixes only.
    Feature additions or content changes are not included.
</p>

<div class="section-title">9. Governing Law</div>
<p>This Agreement is governed by applicable UAE laws.</p>
        ']);

        // ═══════════════════════════════════════════════════════════════
        // ID 12 — Branding Only
        // ═══════════════════════════════════════════════════════════════
        DB::table('document_templates')->where('id', 12)->update(['content' => '
<div class="section-title">1. Project Overview</div>
<p>
    This Branding Services Agreement is made on <strong>{{contract_date}}</strong> between
    <strong>{{company_name}}</strong> ("Agency") and <strong>{{client_name}}</strong> ("Client").
    The Agency shall provide professional brand identity services as described below.
</p>

<div class="section-title">2. Scope of Work</div>
<p>The following branding deliverables are included:</p>
<ul>
    <li><strong>Logo Design</strong> — Up to 3 unique initial concepts; final logo in all formats (PNG, SVG, PDF).</li>
    <li><strong>Color Palette</strong> — Primary and secondary brand colors with HEX, RGB, and CMYK codes.</li>
    <li><strong>Typography</strong> — Selected brand fonts with usage guidelines.</li>
    <li><strong>Business Card</strong> — Front and back design (print-ready files included).</li>
    <li><strong>Letterhead</strong> — Official letterhead design in A4 format.</li>
    <li><strong>Email Signature</strong> — HTML-ready professional email signature.</li>
    <li><strong>Brand Guidelines Document</strong> — Complete PDF guide for consistent brand usage.</li>
</ul>

<div class="section-title">3. Client Responsibilities</div>
<ul>
    <li>Provide a clear brief including business nature, target audience, and style preferences.</li>
    <li>Supply any existing assets (if applicable) — old logos, references, etc.</li>
    <li>Provide timely feedback within <strong>3 business days</strong> per revision round.</li>
</ul>

<div class="section-title">4. Timeline</div>
<p>
    Initial concepts will be presented within <strong>7 business days</strong> of project commencement.
    Full project delivery by: <strong>{{delivery_date}}</strong>.
</p>

<div class="section-title">5. Payment</div>
<p>Total project value: <strong>{{amount}}</strong></p>
<ul>
    <li><strong>50%</strong> — Advance payment upon contract signing.</li>
    <li><strong>50%</strong> — Upon final delivery of all brand files.</li>
</ul>

<div class="section-title">6. Revisions</div>
<p>
    Each deliverable includes <strong>two (2) revision rounds</strong>.
    Additional revisions will be quoted and billed separately.
</p>

<div class="section-title">7. Ownership</div>
<p>
    All final brand files and intellectual property rights transfer to the Client
    upon receipt of full and final payment. The Agency retains rights to display
    the work in its portfolio and marketing materials.
</p>

<div class="section-title">8. Cancellation</div>
<p>
    If the Client cancels the project after work has commenced, the advance payment
    is non-refundable. Any work completed beyond the advance-covered scope will be
    invoiced separately.
</p>

<div class="section-title">9. Governing Law</div>
<p>
    This Agreement shall be governed by the laws of the United Arab Emirates.
</p>
        ']);

        $this->command->info('✅ All 6 templates updated successfully!');
        $this->command->info('   ID 7  — VMC NDA');
        $this->command->info('   ID 8  — VMC Contract');
        $this->command->info('   ID 9  — Nuqoosh NDA');
        $this->command->info('   ID 10 — Website Only');
        $this->command->info('   ID 11 — Website + Branding');
        $this->command->info('   ID 12 — Branding Only');
    }
}