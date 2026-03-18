<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .email-header {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            color: #ffffff;
            padding: 24px 30px;
        }
        .email-header h1 {
            margin: 0 0 4px 0;
            font-size: 18px;
            font-weight: 600;
        }
        .email-header p {
            margin: 0;
            font-size: 13px;
            opacity: 0.85;
        }
        .email-body {
            padding: 30px;
        }
        .action-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 16px;
        }
        .action-created { background-color: #dbeafe; color: #1d4ed8; }
        .action-replied_by_implementer { background-color: #d1fae5; color: #065f46; }
        .action-status_changed { background-color: #fef3c7; color: #92400e; }
        .action-closed { background-color: #f3f4f6; color: #374151; }
        .action-merged { background-color: #ede9fe; color: #5b21b6; }
        .ticket-details {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            overflow: hidden;
            margin: 16px 0;
        }
        .ticket-details table {
            width: 100%;
            border-collapse: collapse;
        }
        .ticket-details th,
        .ticket-details td {
            padding: 10px 14px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
            font-size: 14px;
        }
        .ticket-details tr:last-child th,
        .ticket-details tr:last-child td {
            border-bottom: none;
        }
        .ticket-details th {
            background-color: #f9fafb;
            font-weight: 600;
            width: 35%;
            color: #374151;
        }
        .description-preview {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 14px;
            margin: 16px 0;
            font-size: 14px;
            color: #4b5563;
        }
        .description-preview p {
            margin: 0;
        }
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            color: #ffffff !important;
            text-decoration: none;
            padding: 12px 28px;
            border-radius: 6px;
            font-size: 15px;
            font-weight: 600;
            margin: 20px 0;
        }
        .cta-section {
            text-align: center;
            padding: 10px 0;
        }
        .email-footer {
            background-color: #f9fafb;
            padding: 16px 30px;
            border-top: 1px solid #e5e7eb;
            font-size: 12px;
            color: #9ca3af;
            text-align: center;
        }
        .by-line {
            font-size: 13px;
            color: #6b7280;
            margin-bottom: 16px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1>TimeTec CRM</h1>
            <p>Implementer Support Notification</p>
        </div>

        <div class="email-body">
            <span class="action-badge action-{{ $action }}">{{ $actionLabel }}</span>

            <p class="by-line">
                @if($action === 'created')
                    A new support ticket has been created by <strong>{{ e($actionByName) }}</strong>.
                @elseif($action === 'replied_by_implementer')
                    <strong>{{ e($actionByName) }}</strong> has replied to a ticket.
                @elseif($action === 'status_changed')
                    Ticket status has been updated by <strong>{{ e($actionByName) }}</strong>.
                @elseif($action === 'closed')
                    Ticket has been closed by <strong>{{ e($actionByName) }}</strong>.
                @elseif($action === 'merged')
                    Ticket has been merged by <strong>{{ e($actionByName) }}</strong>.
                @else
                    Ticket updated by <strong>{{ e($actionByName) }}</strong>.
                @endif
            </p>

            <div class="ticket-details">
                <table>
                    <tr>
                        <th>Ticket No.</th>
                        <td>{{ $ticket->ticket_number ?? $ticket->formatted_ticket_number ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Subject</th>
                        <td>{{ e($ticket->subject ?? '-') }}</td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>{{ $ticket->status?->label() ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Category</th>
                        <td>{{ e($ticket->category ?? '-') }}</td>
                    </tr>
                    @if($ticket->module)
                    <tr>
                        <th>Module</th>
                        <td>{{ e($ticket->module) }}</td>
                    </tr>
                    @endif
                    <tr>
                        <th>Priority</th>
                        <td>{{ ucfirst($ticket->priority ?? '-') }}</td>
                    </tr>
                </table>
            </div>

            @if($descriptionPreview)
            <div class="description-preview">
                <p>{{ $descriptionPreview }}</p>
            </div>
            @endif

            <div class="cta-section">
                <a href="{{ $portalUrl }}" class="cta-button">View in Customer Portal</a>
            </div>

            <p style="font-size: 13px; color: #6b7280; text-align: center;">
                Please log in to the Customer Portal to view the full details and respond.
            </p>
        </div>

        <div class="email-footer">
            This is an automated notification from TimeTec CRM. Please do not reply to this email.
        </div>
    </div>
</body>
</html>
