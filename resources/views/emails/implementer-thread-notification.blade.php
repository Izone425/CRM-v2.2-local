<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $emailSubject }}</title>
</head>
<body style="margin:0;padding:0;font-family:Arial,Helvetica,sans-serif;background:#F8FAFC;color:#1E293B;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#F8FAFC;padding:32px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="background:#FFFFFF;border-radius:8px;border:1px solid #E2E8F0;">
                    <tr>
                        <td style="padding:32px;">
                            <p style="margin:0 0 16px 0;font-size:16px;color:#1E293B;">Dear Customer,</p>
                            <p style="margin:0 0 24px 0;font-size:14px;line-height:1.6;color:#334155;">
                                You have a new update in your customer portal. Please log in to view the full message and any attached files.
                            </p>
                            <p style="margin:0 0 32px 0;text-align:center;">
                                <a href="{!! $portalUrl !!}"
                                   style="display:inline-block;padding:12px 28px;background:#3B82F6;color:#FFFFFF;text-decoration:none;border-radius:6px;font-size:14px;font-weight:600;">
                                    View in Customer Portal
                                </a>
                            </p>
                            <hr style="margin:24px 0;border:0;border-top:1px solid #E2E8F0;" />
                            <p style="margin:0;font-size:13px;color:#64748B;line-height:1.6;">
                                Regards,<br>
                                {{ $implementerName }}<br>
                                {{ $implementerDesignation }}<br>
                                {{ $implementerCompany }}<br>
                                @if(!empty($implementerPhone))Phone: {{ $implementerPhone }}<br>@endif
                                @if(!empty($implementerEmail))Email: {{ $implementerEmail }}@endif
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
