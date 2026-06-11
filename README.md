# Nuqoosh CRM API

A production-ready **Multi-Company CRM Backend API** built with Laravel 12, designed for secure company management, client records, document templates, dynamic contract generation, and professional PDF exports.

---

# 🚀 Project Overview

Nuqoosh CRM API is a scalable SaaS-ready CRM backend that enables multiple companies to operate within a single platform while maintaining complete data isolation.

The system provides:

* Secure Authentication
* Multi-Company Management
* Role & Permission Control
* Client Management
* Dynamic Document Templates
* Contract Generation
* Professional PDF Export
* Auto Contract Numbering

Built for integration with:

* Next.js
* React.js
* Vue.js
* Mobile Applications
* Admin Dashboards
* SaaS CRM Platforms

---

# 🛠 Technology Stack

* Laravel 12
* PHP 8.2+
* MySQL
* Laravel Sanctum
* Spatie Laravel Permission
* DomPDF

---

# ✅ Core Features

## Authentication

* Secure Login API
* Sanctum Token Authentication
* Protected API Routes

---

## Roles & Permissions

Role Based Access Control using Spatie Permission.

### Default Role

* admin

### Default Permissions

* view
* create
* delete

---

## Multi-Company Architecture

Supports multiple companies within a single CRM installation.

### Default Companies

* VMC
* Nuqoosh
* Hobs Innovation

Features:

* Company Switching
* Active Company Selection
* Company Data Isolation

---

## Client Management

* Create Clients
* View Clients
* Delete Clients
* Company-Level Client Isolation

---

## Document Templates

Reusable document templates with dynamic placeholders.

### Supported Placeholders

```text
{{client_name}}
{{company_name}}
{{price}}

{{contract_number}}
{{client_address}}
{{contract_date}}
{{delivery_date}}
{{amount}}
```

---

## Categories & Sub Categories

### VMC

* NDA
* Contract

### Nuqoosh

* NDA

* Contract

  * Website Only
  * Website + Branding
  * Branding Only

---

## Dynamic Contract Generation

Generate professional business documents using templates and client information.

Supported Fields:

* Client Name
* Client Address
* Contract Date
* Delivery Date
* Amount
* Company Information

---

## Auto Contract Number Generation

Contract numbers are generated automatically.

Examples:

```text
VMC-NDA-2026-001
VMC-CON-2026-001

NQ-WE-2026-001
NQ-WB-2026-001
NQ-BR-2026-001


```

---

## Professional PDF Generation

Features:

* Company Logo
* Company Header
* Contract Information Section
* Dynamic Content Replacement
* Signature Section
* Professional Layout
* PDF Download

---

# 📡 API Base URL

```text
http://127.0.0.1:8000/api
```

---

# 🔗 Main API Endpoints

## Authentication

```http
POST /login
```

---

## Companies

```http
GET /companies
POST /companies
POST /companies/select
```

---

## Clients

```http
GET /clients
POST /clients
DELETE /clients/{id}
```

---

## Document Templates

```http
GET /document-templates
POST /document-templates
GET /document-template-categories
```

---

## Documents

```http
POST /documents/generate
```

---

# 🔒 Security Features

* Sanctum Authentication
* Permission Middleware
* Company Access Validation
* Protected Routes
* Role-Based Authorization

---

# 🌱 Seeder Support

Project includes automatic database seeding.

Default seeded data:

### User

```text
Email:
admin@gmail.com

Password:
123456
```

### Role

```text
admin
```

### Permissions

```text
view
create
delete
```

### Companies

```text
VMC
Nuqoosh
Hobs Innovation
```

Setup command:

```bash
php artisan migrate --seed
```

Laravel recommends using seeders for repeatable environment setup and initial application data.

---

# 🧪 Testing Status

Successfully Tested:

* Authentication
* Company Switching
* Roles & Permissions
* Client Management
* Template Management
* Category Filtering
* Contract Generation
* Dynamic Placeholders
* PDF Generation
* Contract Number Generation

---

# 📈 Future Enhancements

* PPT Export
* Dashboard Analytics
* Advanced Search
* Pagination
* Email PDF Delivery
* Team User Accounts
* Subscription Billing

---

# 👨‍💻 Developer

Adnan Yousaf

Email:
[adnan@nuqoosh.io](mailto:adnan@nuqoosh.io)

---

# 📌 Project Status

✅ Production Ready

Backend development completed and fully tested.

Current completion status:

* Backend: 100%
* Frontend Integration: In Progress

Ready for QA, UAT and Production Deployment.
