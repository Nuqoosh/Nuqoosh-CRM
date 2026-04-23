# Nuqoosh CRM API

A production-ready **Multi-Company CRM Backend API** built with Laravel, designed for secure business management, client handling, document templates, and dynamic PDF generation.

---

## 🚀 Project Overview

Nuqoosh CRM API is a scalable backend solution that allows businesses to manage multiple companies within a single system. It provides secure REST APIs for authentication, company switching, client records, reusable templates, and downloadable PDF documents.

This backend is ready for integration with:

* React.js Frontend
* Vue.js Frontend
* Mobile Applications
* Admin Dashboards
* SaaS CRM Platforms

---

## ✅ Core Features

### 🔐 Authentication

* Secure User Login
* Laravel Sanctum Token Authentication
* Protected API Routes

### 👤 Roles & Permissions

* Admin Role Management
* Permission-Based Access Control
* Secure Middleware Protection

### 🏢 Multi-Company Management

* Create Multiple Companies
* Switch Active Company
* Company-Level Data Isolation

### 👥 Client Management

* Create Clients
* View Company Clients
* Delete Clients

### 📄 Document Templates

* Create Reusable Templates
* Dynamic Placeholder Support

Supported placeholders:

* `{{client_name}}`
* `{{company_name}}`
* `{{price}}`

### 📑 Dynamic PDF Generation

* Generate Quotations
* Generate Invoices
* Generate Custom Business Documents
* Instant PDF Download

---

## 🛠 Technology Stack

* Laravel
* PHP
* MySQL
* Laravel Sanctum
* Spatie Roles & Permissions
* DomPDF

---

## 📡 API Base URL

```text
http://127.0.0.1:8000/api
```

---

## 🔗 Main API Endpoints

### 🔐 Authentication

* `POST /login`

### 🏢 Companies

* `POST /companies`
* `POST /companies/select`
* `GET /companies`

### 👥 Clients

* `POST /clients`
* `GET /clients`
* `DELETE /clients/{id}`

### 📄 Templates

* `POST /document-templates`
* `GET /document-templates`

### 📑 Documents

* `POST /document-templates/{id}/generate`

---

## 🔒 Security Features

* Token Authentication
* Role-Based Authorization
* Permission Middleware
* Company Access Middleware
* Protected API Routes

---

## 🧪 Testing Status

All APIs have been tested successfully using **Apidog**.

Tested Modules:

* Login Authentication
* Company Creation
* Company Switching
* Client Management
* Template Management
* Dynamic Preview Generation
* PDF Download Generation

---

## 📈 Future Enhancements

* Dashboard Analytics
* Search & Filters
* Pagination
* Team User Accounts
* Subscription Billing
* Email PDF Delivery

---

## 👨‍💻 Author

**Hafiz Adnan Yousaf**

---

## 📌 Project Status

CRM Backend API is completed, tested, and ready for production-level frontend integration.
