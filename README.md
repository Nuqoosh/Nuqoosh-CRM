# Nuqoosh CRM API

A production-ready multi-company CRM backend API developed in Laravel.

## 🚀 Project Overview

Nuqoosh CRM API is designed to manage multiple companies, clients, templates, and document generation through secure REST APIs.

This backend system is ready for frontend integration (React, Vue, Mobile App).

---

## ✅ Core Features

### 🔐 Authentication
- Secure Login API
- Laravel Sanctum Token Authentication

### 👤 Roles & Permissions
- Admin Role
- Permission Based Route Protection

### 🏢 Multi Company System
- Create Multiple Companies
- Switch Active Company
- Company-wise Data Isolation

### 👥 Client Management
- Create Clients
- View Company Clients
- Delete Clients

### 📄 Document Templates
- Create Templates
- Dynamic Placeholder Support

### 📑 PDF Document Generation
- Generate Quotation / Invoice / Custom Docs
- PDF Download

---

## 🛠 Tech Stack

- Laravel
- PHP
- MySQL
- Laravel Sanctum
- Spatie Permission
- DomPDF

---

## 📡 API Base URL

```text
http://127.0.0.1:8000/api


🔗 Main Endpoints
Auth
POST /login
Companies
POST /companies
POST /companies/select
GET /companies
Clients
POST /clients
GET /clients
DELETE /clients/{id}
Templates
POST /document-templates
GET /document-templates
Documents
POST /document-templates/{id}/generate
🔒 Security Features
Token Authentication
Role Based Access
Permission Middleware
Company Access Middleware
🧪 Testing

All APIs tested successfully using Apidog.

📈 Ready For
React Frontend
Vue Frontend
Mobile Application
SaaS CRM Expansion
👨‍💻 Author

Hafiz Adnan Yousaf

📌 Status

Completed and production-ready backend API.