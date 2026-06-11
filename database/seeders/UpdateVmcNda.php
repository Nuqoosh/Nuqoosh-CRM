<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateVmcNda extends Seeder
{
    public function run(): void
    {
        DB::table('document_templates')->where('id', 5)->update(['content' => '
<p style="text-align:center;font-weight:bold;font-size:14px;">NON DISCLOSURE AGREEMENT (NDA)</p>
<p>This Non-Disclosure Agreement (the "Agreement") is entered into on <strong>{{contract_date}}</strong> by and between:</p>

<div class="section-title">Parties</div>
<p><strong>First Party (Disclosing Authority):</strong> Vault Management Consultants (VMC), a Sole Establishment duly licensed under the Dubai Department of Economy &amp; Tourism (License No. 733853), having its principal office at Premises No. 218, Ali Rashed Lootah Buildings, Al Rigga, Deira, Dubai, UAE, represented by its Owner, Sultan Ali Rashed Lootah ("First Party").</p>
<p><strong>Second Party (Receiving Party):</strong> <strong>{{client_name}}</strong>, of <strong>{{client_address}}</strong> ("Second Party"). Each a "Party" and together the "Parties."</p>

<div class="section-title">Background</div>
<p>The First Party possesses confidential, proprietary, strategic, technical, financial, and operational information. In consideration for receiving such information for the evaluation, negotiation, and potential consummation of a business relationship or transaction (the "Specified Purpose"), the Second Party agrees to hold such information in strict confidence and abide by the terms herein.</p>

<div class="section-title">1. Definitions</div>
<p><strong>1.1</strong> "Confidential Information" means all information disclosed by the First Party to the Second Party (whether written, oral, digital, visual, tangible, or otherwise), including without limitation: business plans, financial statements, projections, feasibility studies, trade secrets, operational processes, source code, algorithms, product road-maps, IP filings, reports, data, technical know-how, drawings, contracts, customer lists, supplier information, pricing, proposals, and all materials derived therefrom ("Derived Materials").</p>
<p><strong>1.2</strong> "Representatives" means a Party\'s directors, officers, partners, employees, temporary staff, advisers, auditors, accountants, attorneys, consultants, contractors, and subcontractors who need to know the Confidential Information for the Specified Purpose and who are bound by confidentiality obligations no less protective than this Agreement.</p>
<p><strong>1.3</strong> "Trade Secrets" means information that derives independent economic value, actual or potential, from not being generally known to or readily ascertainable by others and which is the subject of efforts that are reasonable under the circumstances to maintain its secrecy.</p>
<p><strong>1.4</strong> "Circulars" means any written instructions, guidelines, notices, memorandum, compliance directives, operational protocols, frameworks, security standards, or policies issued by the First Party from time to time in connection with this Agreement.</p>

<div class="section-title">2. Use of Confidential Information</div>
<p><strong>2.1</strong> The Second Party and its Representatives shall not use Confidential Information for any purpose other than the Specified Purpose.</p>
<p><strong>2.2 No Reverse Engineering.</strong> The Second Party shall not analyze, decompile, disassemble, or reverse engineer any samples, software, devices, or other tangible objects that embody the First Party\'s Confidential Information.</p>
<p><strong>2.3 Clean Room.</strong> If evaluation or development is required, the Second Party shall utilize a clean-room methodology segregating any personnel exposed to the First Party\'s Confidential Information from those developing competing or related products, for a period of 24 months.</p>

<div class="section-title">3. Confidentiality Obligations</div>
<p><strong>3.1 Standard of Care.</strong> The Second Party shall safeguard the Confidential Information with the same degree of care it uses to protect its own most sensitive information, but in no event less than a reasonable standard of care.</p>
<p><strong>3.2 Controls.</strong> The Second Party shall implement and maintain appropriate technical and organizational measures, including access controls, multi-factor authentication, encryption in transit and at rest, secure key management, logging and monitoring, secure disposal, and background checks for personnel with access.</p>
<p><strong>3.3 Copy Controls &amp; Register.</strong> The Second Party shall keep Confidential Information segregated, clearly labeled, and maintain a register of all copies and reproductions (including Derived Materials), recording date, custodian, and location. The register shall be made available for inspection by the First Party upon forty-eight (48) hours\' written notice.</p>
<p><strong>3.4 Minimum Necessary.</strong> Disclosure within the Second Party shall be limited strictly to those Representatives who have a demonstrable need to know and are bound in writing by obligations at least as protective as those in this Agreement.</p>
<p><strong>3.5 Notification.</strong> The Second Party shall notify the First Party immediately upon becoming aware of any potential, suspected, or actual unauthorized access, use, or disclosure of Confidential Information, and shall cooperate fully in investigation, containment, remediation, and notification activities.</p>
<p><strong>3.6 Audit Rights.</strong> The First Party may audit the Second Party\'s compliance with this Agreement and any applicable Circulars, including on-site reviews, document inspections, and system walkthroughs, during normal business hours on two (2) business days\' notice. The Second Party shall promptly remedy any deficiencies identified.</p>

<div class="section-title">4. Disclosure to Third Parties</div>
<p><strong>4.1</strong> The Second Party shall not disclose Confidential Information to any third party without the First Party\'s prior written consent, except to its Representatives strictly in accordance with Clause 3.4.</p>
<p><strong>4.2 Sub-processors &amp; Subcontractors.</strong> No subcontracting or use of sub-processors for any processing, storage, or transmission of Confidential Information is permitted without the First Party\'s prior written approval and subject to written agreements imposing obligations no less protective than this Agreement.</p>

<div class="section-title">5. Compelled Disclosure</div>
<p><strong>5.1</strong> If the Second Party is required by law, regulation, or court/authority order to disclose any Confidential Information, it shall, to the extent legally permissible, provide the First Party with at least seven (7) business days\' prior written notice to allow the First Party to seek protective orders or other remedies. The Second Party shall disclose only that portion of the Confidential Information that it is legally required to disclose.</p>

<div class="section-title">6. Exclusions</div>
<p><strong>6.1</strong> The obligations do not apply to information that the Second Party can demonstrate with contemporaneous written records: (a) is or becomes public through no breach; (b) was lawfully in the Second Party\'s possession prior to disclosure; or (c) is independently developed without reference to the Confidential Information.</p>

<div class="section-title">7. Circular Clause — Binding Authority of the First Party</div>
<p><strong>7.1 Automatic Integration.</strong> All Circulars issued by the First Party in relation to this Agreement shall automatically and immediately form part of this Agreement without further action.</p>
<p><strong>7.2 Absolute Supremacy.</strong> In the event of any inconsistency or conflict between this Agreement and any Circular, the Circular shall prevail and supersede the conflicting provisions.</p>
<p><strong>7.3 Irrevocable Compliance.</strong> The Second Party shall fully, promptly, and unconditionally comply with all Circulars.</p>
<p><strong>7.4 Enforcement.</strong> Failure to comply with a Circular constitutes a fundamental and material breach entitling the First Party to immediate termination, injunctive relief, specific performance, damages, suspension of dealings, and indemnification.</p>
<p><strong>7.5 Waiver of Objection.</strong> The Second Party irrevocably waives any right to object to, contest, or delay compliance with any Circular. The First Party shall be the sole and final authority on the scope, interpretation, and enforcement of Circulars.</p>

<div class="section-title">8. Return and Destruction</div>
<p><strong>8.1</strong> Upon the earlier of the First Party\'s written request or completion of the Specified Purpose, the Second Party shall, within three (3) business days, return or irreversibly destroy all Confidential Information and Derived Materials, including copies and backups, and certify such destruction in writing by an officer of the Second Party. For avoidance of doubt, routine IT backups may be overwritten in accordance with secure retention schedules but shall remain subject to this Agreement until deletion.</p>

<div class="section-title">9. Ownership; No License</div>
<p><strong>9.1</strong> All Confidential Information and intellectual property rights therein remain the exclusive property of the First Party. No license or other rights are granted by implication, estoppel, or otherwise, except the limited right to use the Confidential Information for the Specified Purpose.</p>

<div class="section-title">10. Non-Solicitation; Non-Circumvention</div>
<p><strong>10.1</strong> For a period of 24 months from the Effective Date, the Second Party shall not, directly or indirectly, solicit for employment or engagement any employee or key consultant of the First Party with whom the Second Party had contact in connection with the Specified Purpose, except with the First Party\'s prior written consent.</p>
<p><strong>10.2</strong> The Second Party shall not bypass, circumvent, or interfere with the First Party\'s business relationships, sources, counterparties, or opportunities disclosed under this Agreement, including attempts to transact directly without the First Party\'s written consent.</p>

<div class="section-title">11. Data Protection; Export Controls; Sanctions</div>
<p><strong>11.1</strong> If the Confidential Information includes personal data, the Second Party shall process such data in accordance with applicable data protection laws (including UAE Federal Decree-Law No. 45 of 2021 (PDPL) and any other applicable regulations).</p>
<p><strong>11.2</strong> The Second Party shall comply with applicable export control, re-export, and economic sanctions laws and shall not use, transfer, or access the Confidential Information in or from any jurisdiction or to any person prohibited by such laws.</p>

<div class="section-title">12. Disclaimer</div>
<p><strong>12.1</strong> The First Party makes no representations or warranties, express or implied, as to the accuracy or completeness of the Confidential Information, and shall have no liability for the Second Party\'s reliance thereon.</p>

<div class="section-title">13. Remedies; Liquidated Damages; Equitable Relief</div>
<p><strong>13.1 Irreparable Harm.</strong> The Second Party acknowledges that breaches may cause irreparable harm for which monetary damages are inadequate. The First Party is entitled to immediate injunctive relief, specific performance, and other equitable remedies without posting bond or proving actual damages.</p>
<p><strong>13.2 Liquidated Damages.</strong> Without prejudice to other remedies, the Parties agree that for each breach of this Agreement (including breach of any Circular), the Second Party shall pay to the First Party liquidated damages of AED 500,000 per breach (or such other amount specified by Circular), representing a genuine pre-estimate of loss and not a penalty. Liquidated damages shall be cumulative with, and not in limitation of, other relief.</p>
<p><strong>13.3 Indemnity.</strong> The Second Party shall indemnify, defend, and hold harmless the First Party, its affiliates, and their officers, employees, and agents from and against all losses, costs, liabilities, damages, claims, fines, penalties, and expenses (including legal fees) arising out of or in connection with any breach by the Second Party or its Representatives.</p>

<div class="section-title">14. Limitation of Liability</div>
<p><strong>14.1</strong> Nothing in this Agreement limits or excludes the First Party\'s right to recover for breaches by the Second Party, nor limits the Second Party\'s liability for breach of confidentiality, infringement of intellectual property, violation of Circulars, willful misconduct, gross negligence, or indemnity obligations.</p>

<div class="section-title">15. Term; Survival</div>
<p><strong>15.1 Term.</strong> This Agreement commences on <strong>{{contract_date}}</strong> and remains in force for five (5) years, unless terminated earlier by the First Party by written notice.</p>
<p><strong>15.2 Survival.</strong> Obligations relating to Confidential Information shall survive for five (5) years after termination or expiry; obligations relating to Trade Secrets, non-circumvention, and the Circular Clause shall survive indefinitely or for the maximum period permitted by law.</p>

<div class="section-title">16. Notices</div>
<p><strong>16.1</strong> Notices shall be in writing and deemed given when delivered by hand (with signature), by reputable courier (with tracking), or by email with confirmation of receipt to the addresses below (or as updated by Circular).</p>
<p><strong>First Party:</strong> Vault Management Consultants (VMC), Premises No. 218, Ali Rashed Lootah Buildings, Al Rigga, Deira, Dubai, UAE.<br>
<strong>Second Party:</strong> {{client_address}}</p>

<div class="section-title">17. Governing Law; Jurisdiction; Interim Relief</div>
<p><strong>17.1</strong> This Agreement is governed by the laws of the Emirate of Dubai and the applicable federal laws of the United Arab Emirates.</p>
<p><strong>17.2</strong> The courts of Dubai shall have exclusive jurisdiction. Notwithstanding the foregoing, the First Party may seek injunctive or interim relief in any competent jurisdiction to protect its interests.</p>

<div class="section-title">18. Miscellaneous</div>
<p><strong>18.1 Entire Agreement.</strong> This Agreement, together with all Circulars, constitutes the entire agreement with respect to its subject matter and supersedes all prior discussions and understandings.</p>
<p><strong>18.2 Amendments.</strong> Amendments may be effected unilaterally by Circulars issued by the First Party; otherwise, any amendment must be in writing signed by the First Party.</p>
<p><strong>18.3 Assignment.</strong> The Second Party may not assign, delegate, or transfer this Agreement without the First Party\'s prior written consent. The First Party may assign freely.</p>
<p><strong>18.4 Severability.</strong> If any provision is held invalid or unenforceable, it shall be modified to the minimum extent necessary to be valid and enforceable, and the remainder shall continue in full force.</p>
<p><strong>18.5 No Waiver.</strong> No failure or delay by the First Party in exercising any right shall operate as a waiver of that right; a single or partial exercise shall not preclude any other or further exercise.</p>
<p><strong>18.6 Counterparts; E-Signatures.</strong> This Agreement may be executed in counterparts and by electronic signatures, each of which shall be deemed an original.</p>
<p><strong>18.7 Publicity.</strong> The Second Party shall not make any public announcement or disclosure relating to this Agreement or the Parties\' relationship without the First Party\'s prior written consent.</p>
<p><strong>18.8 Force Majeure (Limited).</strong> The First Party shall not be liable for delays or failures due to events beyond its reasonable control; such events do not excuse the Second Party\'s confidentiality and security obligations.</p>
<p><strong>18.9 Third-Party Rights.</strong> No third party has any rights to enforce any term of this Agreement.</p>
'
        ]);

        $this->command->info('✅ VMC NDA (ID 5) updated with full legal content!');
    }
}