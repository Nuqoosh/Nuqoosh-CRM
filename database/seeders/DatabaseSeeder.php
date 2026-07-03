<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Company;
use App\Models\Client;
use App\Models\DocumentTemplate;
use App\Models\Document;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ═══════════════════════════════════════════════════════════════
        // 0. ROLES & PERMISSIONS (must run before assigning roles to users)
        // ═══════════════════════════════════════════════════════════════

        $this->call(RolesAndPermissionsSeeder::class);

        // ═══════════════════════════════════════════════════════════════
        // 1. CREATE COMPANIES
        // ═══════════════════════════════════════════════════════════════
        
        $nuqoosh = Company::create([
            'name' => 'Nuqoosh',
            'email' => 'info@nuqoosh.com',
            'phone' => '+971 4 123 4567',
            'address' => 'Dubai, United Arab Emirates',
            'country' => 'UAE',
        ]);
        
        $vmc = Company::create([
            'name' => 'VMC',
            'email' => 'info@vmc.com',
            'phone' => '+971 4 765 4321',
            'address' => 'Abu Dhabi, United Arab Emirates',
            'country' => 'UAE',
        ]);
        
        $hobs = Company::create([
            'name' => 'HOBS',
            'email' => 'info@hobs.com',
            'phone' => '+971 4 987 6543',
            'address' => 'Sharjah, United Arab Emirates',
            'country' => 'UAE',
        ]);
        
        // ═══════════════════════════════════════════════════════════════
        // 2. CREATE USERS
        // ═══════════════════════════════════════════════════════════════
        
        $user1 = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'active_company_id' => $nuqoosh->id,
        ]);
        
        $user2 = User::create([
            'name' => 'VMC Manager',
            'email' => 'vmc@example.com',
            'password' => Hash::make('password'),
            'active_company_id' => $vmc->id,
        ]);
        
        // Attach users to companies
        $user1->companies()->attach([$nuqoosh->id, $vmc->id, $hobs->id]);
        $user2->companies()->attach([$vmc->id]);

        // ─────────────────────────────────────────────────────────────
        // Assign roles (requires HasRoles trait on the User model)
        // Guard is passed explicitly ('web') — Spatie resolves guards from
        // config/auth.php guard keys ('web', 'api'), NOT from Sanctum's driver
        // name. 'web' is the standard Spatie+Sanctum pattern.
        // used throughout routes/api.php. Role names are lowercase
        // slugs to match the existing role:admin middleware and
        // User::isAdmin() which already checks hasRole('admin') /
        // hasRole('super-admin').
        // ─────────────────────────────────────────────────────────────
        $user1->assignRole(Role::findByName('super-admin', 'web'));
        $user2->assignRole(Role::findByName('admin', 'web')); // adjust to 'office-manager' if that fits the job better
        
        // ═══════════════════════════════════════════════════════════════
        // 3. CREATE CLIENTS (For Nuqoosh)
        // ═══════════════════════════════════════════════════════════════
        
        $clients = [
            ['name' => 'Ahmed Al Mansouri', 'email' => 'ahmed@almansouri.ae', 'phone' => '+971 50 111 2222', 'address' => 'Dubai Marina, Dubai'],
            ['name' => 'Fatima Al Zahra', 'email' => 'fatima@alzahra.com', 'phone' => '+971 50 333 4444', 'address' => 'Downtown Dubai'],
            ['name' => 'Omar Bin Khalid', 'email' => 'omar@binkhalid.ae', 'phone' => '+971 50 555 6666', 'address' => 'Abu Dhabi Corniche'],
            ['name' => 'Layla Al Qasimi', 'email' => 'layla@alqasimi.ae', 'phone' => '+971 50 777 8888', 'address' => 'Sharjah City Center'],
            ['name' => 'Zayed Al Nahyan', 'email' => 'zayed@alnahyan.ae', 'phone' => '+971 50 999 0000', 'address' => 'Al Reem Island, Abu Dhabi'],
        ];
        
        foreach ($clients as $client) {
            Client::create([
                'company_id' => $nuqoosh->id,
                'name' => $client['name'],
                'email' => $client['email'],
                'phone' => $client['phone'],
                'address' => $client['address'],
            ]);
        }
        
        // Clients for VMC
        $vmcClients = [
            ['name' => 'Mohammed Al Maktoum', 'email' => 'mohammed@almaktoum.ae', 'phone' => '+971 50 121 2121', 'address' => 'Jumeirah, Dubai'],
            ['name' => 'Noor Al Sayed', 'email' => 'noor@alsayed.ae', 'phone' => '+971 50 343 4343', 'address' => 'Yas Island, Abu Dhabi'],
        ];
        
        foreach ($vmcClients as $client) {
            Client::create([
                'company_id' => $vmc->id,
                'name' => $client['name'],
                'email' => $client['email'],
                'phone' => $client['phone'],
                'address' => $client['address'],
            ]);
        }
        
        // ═══════════════════════════════════════════════════════════════
        // 4. CREATE DOCUMENT TEMPLATES
        // ═══════════════════════════════════════════════════════════════
        
        // NUQOOSH TEMPLATES
        $nuqooshTemplates = [
            [
                'name' => 'NDA',
                'type' => 'contract',
                'category' => 'NDA',
                'sub_category' => null,
                'content' => $this->getNdaContent(),
            ],
            [
                'name' => 'Website Only',
                'type' => 'contract',
                'category' => 'Contract',
                'sub_category' => 'Website Only',
                'content' => $this->getWebsiteOnlyContent(),
            ],
            [
                'name' => 'Website + Branding',
                'type' => 'contract',
                'category' => 'Contract',
                'sub_category' => 'Website + Branding',
                'content' => $this->getWebsiteBrandingContent(),
            ],
            [
                'name' => 'Branding Only',
                'type' => 'contract',
                'category' => 'Contract',
                'sub_category' => 'Branding Only',
                'content' => $this->getBrandingOnlyContent(),
            ],
        ];
        
        foreach ($nuqooshTemplates as $template) {
            DocumentTemplate::create([
                'company_id' => $nuqoosh->id,
                'name' => $template['name'],
                'type' => $template['type'],
                'category' => $template['category'],
                'sub_category' => $template['sub_category'],
                'content' => $template['content'],
            ]);
        }
        
        // VMC TEMPLATES
        $vmcTemplates = [
            [
                'name' => 'NDA',
                'type' => 'contract',
                'category' => 'NDA',
                'sub_category' => null,
                'content' => $this->getVmcNdaContent(),
            ],
            [
                'name' => 'Contract',
                'type' => 'contract',
                'category' => 'Contract',
                'sub_category' => null,
                'content' => $this->getVmcContractContent(),
            ],
        ];
        
        foreach ($vmcTemplates as $template) {
            DocumentTemplate::create([
                'company_id' => $vmc->id,
                'name' => $template['name'],
                'type' => $template['type'],
                'category' => $template['category'],
                'sub_category' => $template['sub_category'],
                'content' => $template['content'],
            ]);
        }
        
        $this->command->info('✅ Database seeded successfully!');
        $this->command->info('   Companies: 3');
        $this->command->info('   Users: 2');
        $this->command->info('   Clients: 7');
        $this->command->info('   Templates: 6');
    }
    
    private function getNdaContent(): string
    {
        return '<div class="section-title">1. Introduction</div>
<p>This Non-Disclosure Agreement ("Agreement") is entered into as of <strong>{{contract_date}}</strong>, by and between <strong>{{company_name}}</strong> ("Disclosing Party"), and <strong>{{client_name}}</strong> ("Receiving Party").</p>

<div class="section-title">2. Confidential Information</div>
<p>"Confidential Information" means any data or information that is proprietary to the Disclosing Party including but not limited to: business plans, financial data, client lists, marketing strategies, trade secrets, and technical information.</p>

<div class="section-title">3. Obligations</div>
<p>The Receiving Party agrees to:</p>
<ul>
<li>Hold all Confidential Information in strict confidence</li>
<li>Not disclose to any third party without prior written consent</li>
<li>Use solely for the purpose of evaluating a potential business relationship</li>
</ul>

<div class="section-title">4. Term</div>
<p>This Agreement shall remain in effect for a period of <strong>two (2) years</strong> from the date of execution.</p>

<div class="section-title">5. Governing Law</div>
<p>This Agreement shall be governed by the laws of the United Arab Emirates.</p>';
    }
    
    private function getWebsiteOnlyContent(): string
    {
        return '<div class="section-title">1. Scope of Work</div>
<p>The Agency agrees to design and develop a standard website for the Client with the following specifications:</p>
<ul>
<li>Up to 5 static pages (Home, About, Services, Contact, Blog)</li>
<li>Responsive design compatible with all devices</li>
<li>Basic SEO optimization</li>
<li>Contact form integration</li>
<li>Social media links integration</li>
</ul>

<div class="section-title">2. Deliverables</div>
<ul>
<li>Fully functional website deployed on Client\'s hosting</li>
<li>Admin panel access credentials</li>
<li>Source code files</li>
<li>Basic user documentation</li>
</ul>

<div class="section-title">3. Timeline</div>
<p>The project shall be completed within <strong>15 business days</strong> from receipt of:</p>
<ul>
<li>Signed Agreement</li>
<li>Initial payment</li>
<li>All content from Client</li>
</ul>

<div class="section-title">4. Payment Terms</div>
<p>The total project cost is <strong>{{amount}} {{currency}}</strong>. Payment schedule:</p>
<ul>
<li>50% advance payment to commence work</li>
<li>50% upon completion and before final delivery</li>
</ul>

<div class="section-title">5. Warranty</div>
<p>The Agency warrants that the website will function substantially in accordance with the specifications for a period of 30 days from final delivery.</p>';
    }
    
    private function getWebsiteBrandingContent(): string
    {
        return '<div class="section-title">1. Branding Services</div>
<ul>
<li>Logo design — 3 initial concepts with revisions</li>
<li>Brand color palette and typography selection</li>
<li>Business card and letterhead design</li>
<li>Brand guidelines document (PDF)</li>
</ul>

<div class="section-title">2. Website Services</div>
<ul>
<li>Custom website design — up to 10 pages</li>
<li>Fully responsive and mobile-first design</li>
<li>Content management system (CMS) integration</li>
<li>Advanced SEO optimization</li>
<li>Speed optimization and security hardening</li>
</ul>

<div class="section-title">3. Timeline</div>
<p>Total project duration: <strong>30-45 business days</strong></p>
<ul>
<li>Branding Phase: 15-20 business days</li>
<li>Website Development: 15-25 business days</li>
</ul>

<div class="section-title">4. Payment Terms</div>
<p>Total Package Price: <strong>{{amount}} {{currency}}</strong></p>
<ul>
<li>30% advance to initiate branding phase</li>
<li>30% upon branding approval</li>
<li>40% upon final delivery</li>
</ul>

<div class="section-title">5. Ownership</div>
<p>Upon full payment, the Client shall own all intellectual property rights to the final branding assets and website code.</p>';
    }
    
    private function getBrandingOnlyContent(): string
    {
        return '<div class="section-title">1. Branding Deliverables</div>
<ul>
<li>Logo design — 3 unique concepts with revisions</li>
<li>Brand color palette (primary, secondary, accent)</li>
<li>Typography system (heading, subheading, body fonts)</li>
<li>Business card design (front and back)</li>
<li>Letterhead and envelope design</li>
<li>Email signature template</li>
<li>Complete brand style guide document</li>
</ul>

<div class="section-title">2. Process</div>
<ul>
<li>Discovery & Research: 5-7 days</li>
<li>Concept Development: 7-10 days</li>
<li>Refinement: 5-7 days</li>
<li>Finalization: 3-5 days</li>
</ul>

<div class="section-title">3. Payment Terms</div>
<p>Total project value: <strong>{{amount}} {{currency}}</strong></p>
<ul>
<li>50% advance payment</li>
<li>50% upon final delivery</li>
</ul>

<div class="section-title">4. Revisions</div>
<p>3 rounds of revisions on logo design. Additional revisions billed at hourly rate.</p>';
    }
    
    private function getVmcNdaContent(): string
    {
        return '<div class="section-title">1. Confidentiality Agreement</div>
<p>This Non-Disclosure Agreement is entered into between VMC ("Disclosing Party") and the Client ("Receiving Party").</p>

<div class="section-title">2. Confidential Information</div>
<p>Includes technical data, trade secrets, product plans, customer lists, markets, software, developments, processes, and financial information.</p>

<div class="section-title">3. Obligations</div>
<p>The Client agrees to hold Confidential Information in strict confidence and use it only for the purpose of evaluating a potential business relationship.</p>

<div class="section-title">4. Term</div>
<p>This Agreement shall continue for a period of 5 years from the date of signing.</p>';
    }
    
    private function getVmcContractContent(): string
    {
        return '<div class="section-title">1. Services</div>
<p>VMC agrees to provide marketing and consulting services as described in the attached Statement of Work.</p>

<div class="section-title">2. Payment</div>
<p>Total contract value: <strong>{{amount}} {{currency}}</strong>. Invoices are due net 15 days from date of invoice.</p>

<div class="section-title">3. Intellectual Property</div>
<p>All intellectual property created by VMC under this Agreement shall be owned by Client upon full payment.</p>

<div class="section-title">4. Termination</div>
<p>Either party may terminate this Agreement with 30 days written notice.</p>';
    }
}