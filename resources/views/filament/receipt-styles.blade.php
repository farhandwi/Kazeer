<style>
    @media print {
        .fi-topbar, .fi-header, .fi-breadcrumbs, .fi-header-heading, 
        .fi-action-group, .fi-header-subheading, body > div > header,
        .fi-simple-footer {
            display: none !important;
        }
        
        .fi-infolist-section-content.print-section {
            page-break-inside: avoid;
        }
        
        @page {
            margin: 0.5cm;
        }
    }
    
    .receipt-container {
        font-family: 'Inter', sans-serif;
        max-width: 800px;
        margin: 0 auto;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        background-color: #fff;
    }
    
    .receipt-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
        padding-bottom: 20px;
        border-bottom: 1px solid #eaeaea;
    }
    
    .receipt-logo img {
        max-width: 160px;
        height: auto;
    }
    
    .receipt-title {
        font-size: 24px;
        font-weight: 700;
        color: #1a1a1a;
        margin-bottom: 5px;
    }
    
    .receipt-meta {
        display: flex;
        flex-direction: column;
        text-align: right;
    }
    
    .receipt-meta .date {
        font-size: 14px;
        color: #6b7280;
    }
    
    .receipt-meta .invoice {
        font-size: 16px;
        font-weight: 600;
        color: #1a1a1a;
    }
    
    .receipt-section {
        margin-bottom: 25px;
    }
    
    .receipt-section-title {
        font-size: 16px;
        font-weight: 600;
        color: #4b5563;
        margin-bottom: 12px;
        padding-bottom: 8px;
        border-bottom: 1px solid #f3f4f6;
    }
    
    .receipt-customer-details {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }
    
    .receipt-detail-item {
        margin-bottom: 10px;
    }
    
    .receipt-detail-label {
        font-size: 14px;
        color: #6b7280;
        margin-bottom: 4px;
    }
    
    .receipt-detail-value {
        font-size: 15px;
        color: #1a1a1a;
        font-weight: 500;
    }
    
    .receipt-items {
        border-collapse: collapse;
        width: 100%;
        margin-bottom: 25px;
    }
    
    .receipt-items th {
        background-color: #f9fafb;
        border-bottom: 1px solid #e5e7eb;
        padding: 12px;
        text-align: left;
        font-weight: 600;
        font-size: 14px;
        color: #374151;
    }
    
    .receipt-items td {
        padding: 12px;
        border-bottom: 1px solid #f3f4f6;
        color: #1a1a1a;
        font-size: 14px;
    }
    
    .receipt-items tr:last-child td {
        border-bottom: none;
    }
    
    .receipt-totals {
        width: 100%;
        display: flex;
        flex-direction: column;
        align-items: flex-end;
    }
    
    .receipt-total-row {
        display: flex;
        justify-content: space-between;
        width: 250px;
        margin-bottom: 8px;
    }
    
    .receipt-total-label {
        font-size: 14px;
        color: #6b7280;
    }
    
    .receipt-total-value {
        font-size: 15px;
        color: #1a1a1a;
        font-weight: 500;
    }
    
    .receipt-grand-total {
        margin-top: 10px;
        padding-top: 10px;
        border-top: 2px solid #e5e7eb;
        width: 250px;
    }
    
    .receipt-grand-total .receipt-total-label {
        font-weight: 600;
        color: #1a1a1a;
        font-size: 16px;
    }
    
    .receipt-grand-total .receipt-total-value {
        font-weight: 700;
        font-size: 18px;
        color: #0f766e;
    }
    
    .receipt-footer {
        margin-top: 30px;
        text-align: center;
        font-size: 14px;
        color: #6b7280;
        border-top: 1px solid #eaeaea;
        padding-top: 20px;
    }
    
    .receipt-barcode {
        display: flex;
        justify-content: center;
        margin-top: 20px;
    }
    
    .receipt-barcode img {
        max-width: 200px;
    }
    
    .receipt-thank-you {
        font-weight: 600;
        font-size: 16px;
        color: #1a1a1a;
        margin-bottom: 5px;
    }
</style>