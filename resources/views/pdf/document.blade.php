<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>

@page {
    size: A4;
    margin: 140px 50px 100px 50px;
}

* {
    box-sizing: border-box;
}

body {
    font-family: DejaVu Sans, sans-serif;
    font-size: 12px;
    line-height: 1.8;
    color: #2d2d2d;
}

/* ─── HEADER (fixed, repeats on every page) ─── */
.header {
    position: fixed;
    top: -125px;
    left: 0;
    right: 0;
    padding-bottom: 12px;
    border-bottom: 3px solid #0b1f3a;
}

.header-inner {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.logo {
    height: 55px;
    max-width: 180px;
    object-fit: contain;
}

.logo-placeholder {
    width: 55px;
    height: 55px;
    background: #0b1f3a;
    color: white;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 20px;
}

.company-block {
    text-align: right;
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

/* ─── FOOTER (fixed, repeats on every page) ─── */
.footer {
    position: fixed;
    bottom: -80px;
    left: 0;
    right: 0;
    border-top: 1px solid #d1d5db;
    padding-top: 8px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 10px;
    color: #999;
}

.footer-left {
    font-style: italic;
}

.footer-right {
    text-align: right;
}

/* ─── DOCUMENT TITLE ─── */
.document-title {
    text-align: center;
    margin-bottom: 28px;
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

/* ─── CONTRACT INFO TABLE ─── */
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

/* ─── SECTION HEADING ─── */
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

/* ─── DYNAMIC CONTENT AREA ─── */
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

/* ─── SIGNATURE BLOCK ─── */
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

/* ─── PAGE NUMBER ─── */
.page-number:before {
    content: "Page " counter(page) " of " counter(pages);
}

</style>
</head>

<body>

{{-- ═══ FIXED HEADER ═══ --}}
<div class="header">
    <div class="header-inner">

        {{-- Logo with fallback --}}
        @if(isset($logo) && $logo && file_exists($logo))
            <img src="{{ $logo }}" class="logo" alt="{{ $company->name }}">
        @else
            <div class="logo-placeholder">{{ substr($company->name, 0, 2) }}</div>
        @endif

        <div class="company-block">
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

    </div>
</div>

{{-- ═══ FIXED FOOTER ═══ --}}
<div class="footer">
    <div class="footer-left">Confidential — {{ $company->name }}</div>
    <div class="footer-right">
        Contract No: {{ $contractNumber }}<br>
        <span class="page-number"></span>
    </div>
</div>

{{-- ═══ DOCUMENT TITLE ═══ --}}
<div class="document-title">
    <h1>{{ $documentTitle }}</h1>
    <div class="doc-subtitle">Contract Date: {{ $contractDate }}</div>
</div>

{{-- ═══ CONTRACT INFO BOX ═══ --}}
<table class="contract-box">
    <tr>
        <td class="label">Contract Number</td>
        <td class="value">{{ $contractNumber }}</td>
    </tr>
    <tr>
        <td class="label">Client Name</td>
        <td class="value">{{ $client->name }}</td>
    </tr>
    <tr>
        <td class="label">Client Address</td>
        <td class="value">{{ $clientAddress }}</td>
    </tr>
    <tr>
        <td class="label">Contract Date</td>
        <td class="value">{{ $contractDate }}</td>
    </tr>
    <tr>
        <td class="label">Delivery Date</td>
        <td class="value">{{ $deliveryDate }}</td>
    </tr>
    <tr>
        <td class="label">Project Amount</td>
        <td class="value"><strong>{{ $amount }} {{ $currency ?? 'AED' }}</strong></td>
    </tr>
</table>

{{-- ═══ DYNAMIC CONTENT ═══ --}}
<div class="content-area">
    {!! $content !!}
</div>

{{-- ═══ SIGNATURE BLOCK ═══ --}}
<div class="signature-section">
    <div class="sig-title">Authorized Signatures</div>
    <table class="signature-table">
        <tr>
            <td>
                <div class="sig-name">{{ $company->name }}</div>
                <div class="sig-role">Service Provider</div>
                <div class="sig-line"></div>
                <div>Authorized Signature</div>
                <div class="sig-date">Date: _______________</div>
            </td>
            <td>
                <div class="sig-name">{{ $client->name }}</div>
                <div class="sig-role">Client</div>
                <div class="sig-line"></div>
                <div>Client Signature</div>
                <div class="sig-date">Date: _______________</div>
            </td>
        </tr>
    </table>
</div>

</body>
</html>