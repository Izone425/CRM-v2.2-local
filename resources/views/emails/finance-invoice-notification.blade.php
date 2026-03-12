<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Self Billed Invoice - TimeTec HR</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <p>Hi Finance,</p>

        <div style="margin: 20px 0; padding: 15px; background-color: #f5f5f5; border-radius: 5px;">
            <p style="margin: 5px 0;"><strong>ID:</strong> {{ $invoice->formatted_id }}</p>
            <p style="margin: 5px 0;"><strong>TT Invoice:</strong> {{ $invoice->timetec_invoice_number ?? 'N/A' }}</p>
            <p style="margin: 5px 0;"><strong>AutoCount Invoice:</strong> <span style="color: #dc2626; font-weight: bold;">{{ $invoice->autocount_invoice_number }}</span></p>
            <p style="margin: 5px 0;"><strong>Reseller Name:</strong> {{ $invoice->reseller_name }}</p>
            <p style="margin: 5px 0;"><strong>Subscriber Name:</strong> {{ $invoice->subscriber_name }}</p>
            <p style="margin: 5px 0;"><strong>Source:</strong>
                @php
                    $sourceLabel = match($invoice->portal_type) {
                        'reseller' => 'Reseller Portal',
                        'admin' => 'Admin Portal',
                        'software' => 'Software Handover',
                        'hardware' => 'Hardware Handover',
                        'reseller_handover' => 'Reseller Handover',
                        default => ucwords(str_replace('_', ' ', $invoice->portal_type ?? 'N/A')),
                    };
                @endphp
                <span style="color: #dc2626; font-weight: bold;">{{ $sourceLabel }}</span>
            </p>
        </div>

        <p>Best regards,<br>TimeTec Cloud System</p>
    </div>
</body>
</html>
