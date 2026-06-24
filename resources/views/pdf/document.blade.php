<!DOCTYPE html>
<html dir="{{ $language === 'ar' ? 'rtl' : 'ltr' }}" lang="{{ $language }}">
<head>
<meta charset="utf-8">
<style>

/* ── Arabic font (used when $language === 'ar') ───────────────────────── */
@font-face {
    font-family: 'Cairo';
    src: url('{{ public_path("fonts/Cairo-Regular.ttf") }}');
    font-weight: normal;
}

@page {
    size: A4;
    margin: 155px 50px 105px 50px;
}

* {
    box-sizing: border-box;
}

body {
    font-family: {{ $language === 'ar' ? "'Cairo'" : 'DejaVu Sans' }}, sans-serif;
    font-size: 12px;
    line-height: 1.8;
    color: #2d2d2d;
    direction: {{ $language === 'ar' ? 'rtl' : 'ltr' }};
    text-align: {{ $language === 'ar' ? 'right' : 'left' }};
    margin: 0;
    padding: 0;
}

/* ── HEADER (fixed, repeats on every page) ─────────────────────────────── */
.header {
    position: fixed;
    top: -137px;
    left: 0;
    right: 0;
    height: 110px;
    padding-bottom: 12px;
    border-bottom: 3px solid #0b1f3a;
}

.header-inner {
    display: table;
    width: 100%;
    table-layout: fixed;
}

.header-col {
    display: table-cell;
    vertical-align: middle;
}

.header-col-logo {
    width: 45%;
}

.header-col-company {
    width: 55%;
    text-align: {{ $language === 'ar' ? 'left' : 'right' }};
}

.logo {
    height: 55px;
    max-width: 180px;
    object-fit: contain;
}

/* Shown when no logo file is found for the company. */
.logo-placeholder {
    width: 55px;
    height: 55px;
    background: #0b1f3a;
    color: white;
    border-radius: 8px;
    text-align: center;
    line-height: 55px;
    font-weight: bold;
    font-size: 20px;
}

.company-name {
    font-size: 17px;
    font-weight: bold;
    color: #0b1f3a;
    letter-spacing: 1px;
}

.company-info {
    font-size: 10px;
    color: #666;
    margin-top: 3px;
    line-height: 1.6;
}

/* ── FOOTER (fixed, repeats on every page) ─────────────────────────────── */
.footer {
    position: fixed;
    bottom: -85px;
    left: 0;
    right: 0;
    height: 70px;
    border-top: 1px solid #d1d5db;
    padding-top: 8px;
    font-size: 10px;
    color: #999;
}

.footer-inner {
    display: table;
    width: 100%;
}

.footer-col {
    display: table-cell;
    vertical-align: top;
    width: 50%;
}

.footer-left {
    font-style: italic;
}

.footer-right {
    text-align: {{ $language === 'ar' ? 'left' : 'right' }};
}

/* ── DOCUMENT TITLE ─────────────────────────────────────────────────────── */
.document-title {
    text-align: center;
    margin: 0 0 28px 0;
    padding-bottom: 14px;
    border-bottom: 1px solid #e5e7eb;
}

.document-title h1 {
    margin: 0 0 5px 0;
    color: #0b1f3a;
    font-size: 22px;
    letter-spacing: 0.5px;
    text-transform: uppercase;
}

.document-title .doc-subtitle {
    font-size: 11px;
    color: #888;
}

/* ── CONTRACT INFO TABLE ────────────────────────────────────────────────── */
.contract-box {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 28px;
    border: 1px solid #d1d5db;
    border-radius: 4px;
}

.contract-box td {
    padding: 10px 14px;
    border-bottom: 1px solid #e5e7eb;
    vertical-align: top;
}

.contract-box tr:last-child td {
    border-bottom: none;
}

.contract-box .label {
    width: 38%;
    font-weight: bold;
    color: #0b1f3a;
    background: #f1f5f9;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.contract-box .value {
    color: #333;
}

/* ── SECTION HEADING (used inside the dynamic template content) ─────────── */
.section-title {
    background: #0b1f3a;
    color: #fff;
    padding: 9px 15px;
    font-size: 12px;
    font-weight: bold;
    margin-top: 22px;
    margin-bottom: 10px;
    letter-spacing: 0.5px;
    text-transform: uppercase;
}

/* ── DYNAMIC CONTENT (the template's HTML body, placeholders replaced) ──── */
.content-area {
    margin-top: 10px;
    line-height: 1.9;
}

.content-area h2 {
    color: #0b1f3a;
    font-size: 14px;
    margin-top: 20px;
    margin-bottom: 6px;
    border-bottom: 1px solid #e5e7eb;
    padding-bottom: 4px;
}

.content-area h3 {
    color: #0b1f3a;
    font-size: 13px;
    margin-top: 16px;
    margin-bottom: 4px;
}

.content-area p {
    margin: 0 0 10px 0;
    text-align: justify;
}

.content-area ul, .content-area ol {
    margin: 6px 0 10px 20px;
    padding: 0;
}

.content-area li {
    margin-bottom: 4px;
}

/* ── SIGNATURE BLOCK ────────────────────────────────────────────────────── */
.signature-section {
    margin-top: 60px;
    page-break-inside: avoid;
}

.signature-section .sig-title {
    font-size: 11px;
    font-weight: bold;
    color: #0b1f3a;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    margin-bottom: 8px;
    border-bottom: 1px solid #e5e7eb;
    padding-bottom: 5px;
}

.signature-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
}

.signature-table td {
    width: 50%;
    vertical-align: bottom;
    padding: 0 20px;
    text-align: center;
}

.signature-table td:first-child {
    padding-left: 0;
    border-right: 1px solid #e5e7eb;
}

.signature-table td:last-child {
    padding-right: 0;
}

.sig-name {
    font-weight: bold;
    color: #0b1f3a;
    font-size: 12px;
    margin-bottom: 4px;
}

.sig-role {
    font-size: 10px;
    color: #666;
    margin-bottom: 8px;
}

.sig-line {
    border-top: 1.5px solid #0b1f3a;
    margin: 50px 20px 8px 20px;
}

.sig-date {
    font-size: 10px;
    color: #888;
    margin-top: 6px;
}

/* ── PAGE NUMBER (in footer) ────────────────────────────────────────────── */
.page-number:before {
    content: "Page " counter(page) " of " counter(pages);
}

</style>
</head>

<body>

{{-- FIXED HEADER: logo + company name/info. Side order flips for Arabic. --}}
<div class="header">
    <div class="header-inner">

        @if($language === 'ar')
            <div class="header-col header-col-company">
                <div class="company-name">{{ strtoupper($company->name) }}</div>
                <div class="company-info">
                    @if(isset($company->address) && $company->address)
                        {{ $company->address }}<br>
                    @endif
                    @if(isset($company->phone) && $company->phone)
                        {{ $company->phone }} :هاتف
                    @endif
                    @if(isset($company->email) && $company->email)
                        &nbsp;|&nbsp; {{ $company->email }}
                    @endif
                </div>
            </div>

            <div class="header-col header-col-logo">
                @if(isset($logo) && $logo && file_exists($logo))
                    <img src="{{ $logo }}" class="logo" alt="{{ $company->name }}">
                @else
                    <div class="logo-placeholder">{{ substr($company->name, 0, 2) }}</div>
                @endif
            </div>
        @else
            <div class="header-col header-col-logo">
                @if(isset($logo) && $logo && file_exists($logo))
                    <img src="{{ $logo }}" class="logo" alt="{{ $company->name }}">
                @else
                    <div class="logo-placeholder">{{ substr($company->name, 0, 2) }}</div>
                @endif
            </div>

            <div class="header-col header-col-company">
                <div class="company-name">{{ strtoupper($company->name) }}</div>
                <div class="company-info">
                    @if(isset($company->address) && $company->address)
                        {{ $company->address }}<br>
                    @endif
                    @if(isset($company->phone) && $company->phone)
                        Tel: {{ $company->phone }}
                    @endif
                    @if(isset($company->email) && $company->email)
                        &nbsp;|&nbsp; {{ $company->email }}
                    @endif
                </div>
            </div>
        @endif

    </div>
</div>

{{-- FIXED FOOTER: confidentiality note, contract number, page number --}}
<div class="footer">
    <div class="footer-inner">
        <div class="footer-col footer-left">
            {{ $language === 'ar' ? 'سري — ' : 'Confidential — ' }}{{ $company->name }}
        </div>
        <div class="footer-col footer-right">
            {{ $language === 'ar' ? 'رقم العقد' : 'Contract No' }}: {{ $contractNumber }}<br>
            <span class="page-number"></span>
        </div>
    </div>
</div>

{{-- DOCUMENT TITLE --}}
<div class="document-title">
    <h1>{{ $documentTitle }}</h1>
    <div class="doc-subtitle">
        {{ $language === 'ar' ? 'تاريخ العقد' : 'Contract Date' }}: {{ $contractDate }}
    </div>
</div>

{{-- CONTRACT INFO BOX: contract number, client, dates, amount --}}
<table class="contract-box">
    <tr>
        <td class="label">{{ $language === 'ar' ? 'رقم العقد' : 'Contract Number' }}</td>
        <td class="value">{{ $contractNumber }}</td>
    </tr>
    <tr>
        <td class="label">{{ $language === 'ar' ? 'اسم العميل' : 'Client Name' }}</td>
        <td class="value">{{ $client->name }}</td>
    </tr>
    <tr>
        <td class="label">{{ $language === 'ar' ? 'عنوان العميل' : 'Client Address' }}</td>
        <td class="value">{{ $clientAddress }}</td>
    </tr>
    <tr>
        <td class="label">{{ $language === 'ar' ? 'تاريخ العقد' : 'Contract Date' }}</td>
        <td class="value">{{ $contractDate }}</td>
    </tr>
    <tr>
        <td class="label">{{ $language === 'ar' ? 'تاريخ التسليم' : 'Delivery Date' }}</td>
        <td class="value">{{ $deliveryDate }}</td>
    </tr>
    <tr>
        <td class="label">{{ $language === 'ar' ? 'مبلغ المشروع' : 'Project Amount' }}</td>
        <td class="value"><strong>{{ $amount }} {{ $currency ?? 'AED' }}</strong></td>
    </tr>
</table>

{{-- DYNAMIC CONTENT: the template's HTML body (placeholders already replaced
     by the controller before this view is rendered). --}}
<div class="content-area">
    {!! $content !!}
</div>

{{-- SIGNATURE BLOCK: Service Provider (company) + Client signature lines --}}
<div class="signature-section">
    <div class="sig-title">
        {{ $language === 'ar' ? 'التوقيعات المعتمدة' : 'Authorized Signatures' }}
    </div>
    <table class="signature-table">
        <tr>
            <td>
                <div class="sig-name">{{ $company->name }}</div>
                <div class="sig-role">{{ $language === 'ar' ? 'مزود الخدمة' : 'Service Provider' }}</div>
                <div class="sig-line"></div>
                <div>{{ $language === 'ar' ? 'التوقيع المعتمد' : 'Authorized Signature' }}</div>
                <div class="sig-date">{{ $language === 'ar' ? 'التاريخ' : 'Date' }}: _______________</div>
            </td>
            <td>
                <div class="sig-name">{{ $client->name }}</div>
                <div class="sig-role">{{ $language === 'ar' ? 'العميل' : 'Client' }}</div>
                <div class="sig-line"></div>
                <div>{{ $language === 'ar' ? 'توقيع العميل' : 'Client Signature' }}</div>
                <div class="sig-date">{{ $language === 'ar' ? 'التاريخ' : 'Date' }}: _______________</div>
            </td>
        </tr>
    </table>
</div>

</body>
</html>