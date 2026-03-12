<x-filament-panels::page>
    <style>
        .imp-client-wrapper {
            background: #F9FAFB;
            min-height: 100vh;
            padding: 0;
        }

        /* Back Link */
        .imp-client-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #4B5563;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 24px;
            transition: color 0.15s;
        }
        .imp-client-back:hover {
            color: #111827;
        }

        /* Client Details Card */
        .imp-client-card {
            background: white;
            border-radius: 12px;
            border: 1px solid #E5E7EB;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            padding: 24px;
            margin-bottom: 24px;
        }
        .imp-client-card-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 24px;
        }
        .imp-client-card-identity {
            display: flex;
            align-items: center;
            gap: 16px;
        }
        .imp-client-avatar {
            width: 64px;
            height: 64px;
            background: linear-gradient(135deg, #7C3AED, #2563EB);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .imp-client-avatar svg {
            width: 32px;
            height: 32px;
            color: white;
        }
        .imp-client-name {
            font-size: 24px;
            font-weight: 600;
            color: #111827;
            margin: 0;
        }
        .imp-client-subtitle {
            font-size: 14px;
            color: #6B7280;
            margin: 2px 0 0 0;
        }
        .imp-client-crm-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            background: #F5F3FF;
            color: #6D28D9;
            border: 1px solid #DDD6FE;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.15s;
            white-space: nowrap;
        }
        .imp-client-crm-btn:hover {
            background: #EDE9FE;
            border-color: #C4B5FD;
            color: #5B21B6;
        }

        /* Info Grid */
        .imp-client-info-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
        }
        .imp-client-info-item {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .imp-client-info-icon {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .imp-client-info-icon.blue { background: #EFF6FF; }
        .imp-client-info-icon.blue svg { color: #2563EB; }
        .imp-client-info-icon.green { background: #F0FDF4; }
        .imp-client-info-icon.green svg { color: #16A34A; }
        .imp-client-info-icon.orange { background: #FFF7ED; }
        .imp-client-info-icon.orange svg { color: #EA580C; }
        .imp-client-info-icon svg {
            width: 20px;
            height: 20px;
        }
        .imp-client-info-label {
            font-size: 12px;
            color: #6B7280;
            margin: 0;
        }
        .imp-client-info-value {
            font-size: 14px;
            font-weight: 500;
            color: #111827;
            margin: 2px 0 0 0;
        }

        /* Tickets Section */
        .imp-client-tickets-card {
            background: white;
            border-radius: 12px;
            border: 1px solid #E5E7EB;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }
        .imp-client-tickets-header {
            padding: 16px 24px;
            border-bottom: 1px solid #E5E7EB;
        }
        .imp-client-tickets-title {
            font-size: 18px;
            font-weight: 600;
            color: #111827;
            margin: 0;
        }
        .imp-client-tickets-count {
            font-size: 14px;
            color: #6B7280;
            font-weight: 400;
            margin-left: 8px;
        }

        /* Tickets Table */
        .imp-client-table {
            width: 100%;
            border-collapse: collapse;
        }
        .imp-client-table thead {
            background: #F9FAFB;
            border-bottom: 1px solid #E5E7EB;
        }
        .imp-client-table th {
            padding: 12px 24px;
            text-align: left;
            font-size: 11px;
            font-weight: 600;
            color: #6B7280;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .imp-client-table tbody tr {
            border-bottom: 1px solid #F3F4F6;
            transition: background 0.15s;
        }
        .imp-client-table tbody tr:hover {
            background: #F9FAFB;
        }
        .imp-client-table td {
            padding: 16px 24px;
            font-size: 14px;
            color: #374151;
        }
        .imp-client-ticket-id {
            font-weight: 600;
            color: #111827;
        }
        .imp-client-cat-primary {
            font-weight: 500;
            color: #111827;
        }
        .imp-client-cat-secondary {
            font-size: 13px;
            color: #6B7280;
        }

        /* Status Badge */
        .imp-client-status-badge {
            display: inline-flex;
            align-items: center;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            border: 1px solid;
        }
        .imp-client-status-badge.open { background: #DBEAFE; color: #1D4ED8; border-color: #BFDBFE; }
        .imp-client-status-badge.pending_support { background: #FEF3C7; color: #92400E; border-color: #FDE68A; }
        .imp-client-status-badge.pending_client { background: #EDE9FE; color: #6D28D9; border-color: #DDD6FE; }
        .imp-client-status-badge.pending_rnd { background: #FCE7F3; color: #BE185D; border-color: #FBCFE8; }
        .imp-client-status-badge.closed { background: #DCFCE7; color: #166534; border-color: #BBF7D0; }

        /* Priority */
        .imp-client-priority-high { color: #DC2626; font-weight: 500; }
        .imp-client-priority-medium { color: #D97706; font-weight: 500; }
        .imp-client-priority-low { color: #2563EB; font-weight: 500; }

        /* Action Button */
        .imp-client-view-btn {
            padding: 6px;
            color: #2563EB;
            background: none;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: background 0.15s;
        }
        .imp-client-view-btn:hover {
            background: #EFF6FF;
        }
        .imp-client-view-btn svg {
            width: 16px;
            height: 16px;
        }

        /* Empty State */
        .imp-client-empty {
            text-align: center;
            padding: 48px 24px;
            color: #6B7280;
            font-size: 14px;
        }

        /* === Ticket Detail Drawer (reused from dashboard) === */
        body.imp-drawer-open {
            overflow: hidden !important;
        }
        body.imp-drawer-open .fi-topbar {
            display: none !important;
        }
        .imp-detail-overlay {
            position: fixed;
            inset: 0;
            z-index: 200;
            display: flex;
            justify-content: flex-end;
            background: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(4px);
            overflow: hidden;
        }
        .imp-detail-drawer {
            width: 100%;
            max-width: 920px;
            height: 100vh;
            background: white;
            box-shadow: -4px 0 24px rgba(0, 0, 0, 0.15);
            display: flex;
            flex-direction: column;
            border-radius: 0;
            overflow: hidden;
        }
        .imp-detail-header {
            border-bottom: 1px solid #E5E7EB;
            padding: 16px 20px;
            flex-shrink: 0;
        }
        .imp-detail-header-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 8px;
        }
        .imp-detail-header-info {
            display: flex;
            align-items: center;
            gap: 10px;
            flex: 1;
            min-width: 0;
        }
        .imp-detail-title {
            font-size: 16px;
            font-weight: 600;
            color: #111827;
            margin: 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .imp-detail-ticket-id { font-size: 12px; color: #6B7280; flex-shrink: 0; }
        .imp-detail-close {
            background: none; border: none; padding: 6px; cursor: pointer;
            border-radius: 6px; color: #6B7280; transition: background 0.15s; flex-shrink: 0;
        }
        .imp-detail-close:hover { background: #F3F4F6; }
        .imp-detail-header-meta { display: flex; align-items: center; gap: 12px; margin-bottom: 12px; }
        .imp-detail-sla-badge {
            display: inline-flex; align-items: center; gap: 4px;
            padding: 3px 10px; border-radius: 12px; font-size: 12px; font-weight: 500;
        }
        .imp-detail-sla-badge.on_track { background: #DCFCE7; color: #166534; }
        .imp-detail-sla-badge.at_risk { background: #FEF3C7; color: #92400E; }
        .imp-detail-sla-badge.overdue { background: #FEE2E2; color: #991B1B; }
        .imp-detail-sla-badge.resolved { background: #F3F4F6; color: #374151; }
        .imp-detail-company-name { font-size: 13px; color: #6B7280; }
        .imp-detail-actions-bar { display: flex; gap: 8px; }
        .imp-detail-action-btn {
            display: flex; align-items: center; gap: 6px; padding: 6px 12px;
            border-radius: 6px; font-size: 12px; font-weight: 500; cursor: pointer;
            border: 1px solid; transition: all 0.15s;
        }
        .imp-detail-action-btn.internal { background: #FFFBEB; color: #92400E; border-color: #FDE68A; }
        .imp-detail-action-btn.internal:hover { background: #FEF3C7; }
        .imp-detail-content { flex: 1; display: flex; overflow: hidden; min-height: 0; }
        .imp-detail-sidebar {
            width: 280px; flex-shrink: 0; border-right: 1px solid #E5E7EB;
            background: #F9FAFB; padding: 16px; overflow-y: auto;
        }
        .imp-detail-sidebar-title { font-size: 14px; font-weight: 600; color: #111827; margin: 0 0 16px 0; }
        .imp-detail-section { margin-bottom: 20px; }
        .imp-detail-label { display: block; font-size: 10px; font-weight: 600; color: #6B7280; letter-spacing: 0.05em; margin-bottom: 6px; }
        .imp-detail-card { background: white; border: 1px solid #E5E7EB; border-radius: 8px; padding: 10px; }
        .imp-detail-card-row { display: flex; align-items: center; gap: 8px; }
        .imp-detail-avatar { width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .imp-detail-avatar.blue { background: #DBEAFE; color: #2563EB; }
        .imp-detail-avatar.purple { background: #EDE9FE; color: #7C3AED; }
        .imp-detail-avatar.yellow { background: #FEF3C7; color: #92400E; }
        .imp-detail-name { font-size: 13px; font-weight: 500; color: #111827; margin: 0; }
        .imp-detail-sublabel { font-size: 11px; color: #6B7280; margin: 0; }
        .imp-detail-email-row { display: flex; align-items: center; gap: 6px; margin-top: 8px; font-size: 11px; color: #6B7280; }
        .imp-detail-status-wrapper { position: relative; }
        .imp-detail-status-btn {
            width: 100%; background: white; border: 1px solid #E5E7EB; border-radius: 8px;
            padding: 8px 10px; display: flex; align-items: center; justify-content: space-between;
            cursor: pointer; transition: all 0.15s;
        }
        .imp-detail-status-btn:hover { background: #F9FAFB; }
        .imp-detail-status-dropdown {
            position: absolute; top: 100%; left: 0; right: 0; margin-top: 4px;
            background: white; border: 1px solid #E5E7EB; border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); z-index: 20; padding: 4px 0;
        }
        .imp-detail-status-option {
            display: block; width: 100%; text-align: left; padding: 8px 12px;
            font-size: 13px; background: none; border: none; cursor: pointer;
            color: #374151; transition: background 0.1s;
        }
        .imp-detail-status-option:hover { background: #F9FAFB; }
        .imp-detail-status-option.active { background: #F5F3FF; font-weight: 500; color: #7C3AED; }
        .imp-detail-dates-card { background: white; border: 1px solid #E5E7EB; border-radius: 8px; }
        .imp-detail-date-item { padding: 10px; }
        .imp-detail-date-item + .imp-detail-date-item { border-top: 1px solid #F3F4F6; }
        .imp-detail-date-label { display: flex; align-items: center; gap: 4px; font-size: 11px; color: #6B7280; margin-bottom: 2px; }
        .imp-detail-date-value { font-size: 13px; color: #111827; margin: 0; }
        .imp-detail-props-card { background: white; border: 1px solid #E5E7EB; border-radius: 8px; padding: 10px; }
        .imp-detail-prop-row { display: flex; justify-content: space-between; font-size: 13px; padding: 3px 0; }
        .imp-detail-prop-label { color: #6B7280; }
        .imp-detail-prop-value { color: #111827; }
        .imp-detail-prop-value.priority-high { color: #DC2626; font-weight: 500; }
        .imp-detail-prop-value.priority-medium { color: #D97706; font-weight: 500; }
        .imp-detail-prop-value.priority-low { color: #2563EB; font-weight: 500; }
        .imp-detail-thread-panel { flex: 1; display: flex; flex-direction: column; overflow: hidden; min-width: 0; }
        .imp-detail-thread { flex: 1; overflow-y: auto; padding: 16px; background: #F9FAFB; }
        .imp-detail-message {
            background: white; border: 1px solid #E5E7EB; border-radius: 8px;
            padding: 12px; margin-bottom: 12px; box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }
        .imp-detail-message.internal { background: #FFFBEB; border: 2px solid #FDE68A; }
        .imp-detail-msg-header { display: flex; align-items: flex-start; gap: 10px; margin-bottom: 8px; }
        .imp-detail-msg-avatar {
            width: 36px; height: 36px; border-radius: 50%; display: flex;
            align-items: center; justify-content: center; flex-shrink: 0;
        }
        .imp-detail-msg-avatar.blue { background: #DBEAFE; color: #2563EB; }
        .imp-detail-msg-avatar.purple { background: #EDE9FE; color: #7C3AED; }
        .imp-detail-msg-avatar.yellow { background: #FEF3C7; color: #92400E; }
        .imp-detail-msg-info { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
        .imp-detail-msg-name { font-size: 13px; font-weight: 600; color: #111827; }
        .imp-detail-msg-badge { font-size: 11px; padding: 1px 8px; border-radius: 4px; font-weight: 500; }
        .imp-detail-msg-badge.blue { background: #DBEAFE; color: #1D4ED8; }
        .imp-detail-msg-badge.purple { background: #EDE9FE; color: #6D28D9; }
        .imp-detail-msg-badge.yellow { background: #FEF3C7; color: #92400E; }
        .imp-detail-msg-time { font-size: 11px; color: #6B7280; }
        .imp-detail-msg-body { font-size: 13px; color: #374151; line-height: 1.6; padding-left: 46px; }
        .imp-note-edit-btn {
            margin-left: auto; padding: 4px; border: none; background: none;
            color: #9CA3AF; border-radius: 4px; cursor: pointer; transition: all 0.15s; flex-shrink: 0;
        }
        .imp-note-edit-btn:hover { background: #FEF3C7; color: #92400E; }
        .imp-note-edit-area { padding-left: 46px; }
        .imp-note-edit-textarea {
            width: 100%; padding: 8px 10px; border: 1px solid #FDE68A; border-radius: 6px;
            font-size: 13px; font-family: inherit; line-height: 1.6; resize: vertical;
            background: #FFFEF5; color: #374151;
        }
        .imp-note-edit-textarea:focus { outline: none; border-color: #D97706; box-shadow: 0 0 0 2px rgba(217, 119, 6, 0.1); }
        .imp-note-edit-actions { display: flex; justify-content: flex-end; gap: 8px; margin-top: 6px; }
        .imp-note-edit-cancel {
            padding: 4px 12px; border: 1px solid #D1D5DB; background: white;
            border-radius: 6px; font-size: 12px; cursor: pointer; color: #6B7280;
        }
        .imp-note-edit-cancel:hover { background: #F3F4F6; }
        .imp-note-edit-save {
            padding: 4px 12px; border: none; background: #D97706; color: white;
            border-radius: 6px; font-size: 12px; font-weight: 500; cursor: pointer;
        }
        .imp-note-edit-save:hover { background: #B45309; }
        .imp-note-edited-label { font-size: 11px; color: #92400E; font-style: italic; }
        .imp-note-action-btns { margin-left: auto; display: flex; align-items: center; gap: 2px; flex-shrink: 0; }
        .imp-note-delete-btn {
            padding: 4px; border: none; background: none; color: #9CA3AF;
            border-radius: 4px; cursor: pointer; transition: all 0.15s;
        }
        .imp-note-delete-btn:hover { background: #FEE2E2; color: #DC2626; }
        .imp-note-delete-confirm { display: flex; align-items: center; gap: 6px; font-size: 12px; color: #6B7280; }
        .imp-note-delete-yes {
            padding: 2px 10px; border: none; background: #DC2626; color: white;
            border-radius: 4px; font-size: 11px; font-weight: 500; cursor: pointer;
        }
        .imp-note-delete-yes:hover { background: #B91C1C; }
        .imp-note-delete-no {
            padding: 2px 10px; border: 1px solid #D1D5DB; background: white; color: #6B7280;
            border-radius: 4px; font-size: 11px; cursor: pointer;
        }
        .imp-note-delete-no:hover { background: #F3F4F6; }
        .imp-detail-msg-attachments { display: flex; flex-wrap: wrap; gap: 8px; padding-left: 46px; margin-top: 8px; }
        .imp-detail-attachment-link {
            display: inline-flex; align-items: center; gap: 4px; font-size: 12px;
            color: #2563EB; text-decoration: none; padding: 3px 8px; background: #EFF6FF; border-radius: 4px;
        }
        .imp-detail-attachment-link:hover { background: #DBEAFE; }
        .imp-detail-no-messages { text-align: center; padding: 40px 20px; color: #6B7280; font-size: 13px; }
        .imp-detail-reply-box { border-top: 1px solid #E5E7EB; padding: 12px 16px; background: white; flex-shrink: 0; }
        .imp-reply-chevron { width: 16px; height: 16px; color: #9CA3AF; transition: transform 0.2s; }
        .imp-reply-chevron.expanded { transform: rotate(180deg); }
        .imp-detail-reply-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px; }
        .imp-detail-reply-label { font-size: 13px; font-weight: 500; color: #374151; }
        .imp-detail-internal-toggle { display: flex; align-items: center; gap: 6px; font-size: 12px; color: #6B7280; cursor: pointer; }
        .imp-detail-internal-toggle input { accent-color: #D97706; }
        .imp-detail-reply-textarea {
            width: 100%; padding: 10px; border: 1px solid #D1D5DB; border-radius: 8px;
            font-size: 13px; resize: none; font-family: inherit; transition: border-color 0.15s;
        }
        .imp-detail-reply-textarea:focus { outline: none; border-color: #7C3AED; box-shadow: 0 0 0 2px rgba(124, 58, 237, 0.1); }
        .imp-detail-reply-textarea.internal { border-color: #FDE68A; background: #FFFBEB; }
        .imp-detail-reply-textarea.internal:focus { border-color: #D97706; box-shadow: 0 0 0 2px rgba(217, 119, 6, 0.1); }
        .imp-detail-reply-footer { display: flex; align-items: center; justify-content: flex-end; margin-top: 8px; }
        .imp-detail-send-btn {
            display: flex; align-items: center; gap: 6px; padding: 8px 16px;
            background: linear-gradient(135deg, #7C3AED, #2563EB); color: white;
            border: none; border-radius: 8px; font-size: 13px; font-weight: 500;
            cursor: pointer; transition: all 0.15s;
        }
        .imp-detail-send-btn:hover { opacity: 0.9; transform: translateY(-1px); }
        .imp-detail-send-btn:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }
        .imp-detail-send-btn.internal { background: linear-gradient(135deg, #D97706, #B45309); }

        /* Reply email fields */
        .imp-detail-reply-email-fields { padding: 0 0 8px 0; display: flex; flex-direction: column; gap: 6px; }
        .imp-detail-reply-field-row { display: flex; align-items: center; gap: 8px; }
        .imp-detail-reply-field-row label { font-size: 12px; font-weight: 500; color: #6B7280; width: 90px; flex-shrink: 0; }
        .imp-detail-reply-field-row input {
            flex: 1; padding: 6px 10px; border: 1px solid #E5E7EB; border-radius: 6px;
            font-size: 13px; background: #F9FAFB;
        }
        .imp-detail-reply-field-row input:focus { outline: none; border-color: #7C3AED; box-shadow: 0 0 0 2px rgba(124, 58, 237, 0.1); }
        .imp-detail-reply-field-row input[readonly] { color: #6B7280; cursor: default; }
        .imp-ccbcc-toggle {
            background: none; border: none; color: #7C3AED; font-size: 12px;
            font-weight: 500; cursor: pointer; padding: 2px 6px; border-radius: 4px;
            white-space: nowrap; flex-shrink: 0;
        }
        .imp-ccbcc-toggle:hover { background: #F3F0FF; }
        .imp-detail-reply-field-row select {
            flex: 1; padding: 6px 10px; border: 1px solid #E5E7EB; border-radius: 6px;
            font-size: 13px; background: #F9FAFB; cursor: pointer;
        }
        .imp-detail-reply-field-row select:focus { outline: none; border-color: #7C3AED; box-shadow: 0 0 0 2px rgba(124, 58, 237, 0.1); }

        /* Reply rich text editor */
        .imp-detail-reply-toolbar {
            display: flex; align-items: center; gap: 4px; padding: 6px 10px;
            background: #F9FAFB; border: 1px solid #D1D5DB; border-radius: 8px 8px 0 0;
        }
        .imp-detail-reply-toolbar button { padding: 5px; border: none; background: none; border-radius: 5px; cursor: pointer; color: #6B7280; transition: all 0.15s; }
        .imp-detail-reply-toolbar button:hover { background: #E5E7EB; }
        .imp-detail-reply-toolbar button.imp-toolbar-active { background: #E5E7EB; color: #111827; }
        .imp-detail-reply-toolbar .imp-toolbar-divider { width: 1px; height: 18px; background: #D1D5DB; margin: 0 3px; }
        .imp-detail-reply-editor[contenteditable] {
            width: 100%; padding: 10px; border: 1px solid #D1D5DB; border-top: none;
            border-radius: 0 0 8px 8px; font-size: 13px; font-family: inherit;
            min-height: 120px; max-height: 250px; overflow-y: auto; line-height: 1.6; cursor: text; outline: none;
        }
        .imp-detail-reply-editor[contenteditable]:focus { border-color: #7C3AED; box-shadow: 0 0 0 2px rgba(124, 58, 237, 0.1); }
        .imp-detail-reply-editor[contenteditable]:empty:before { content: attr(data-placeholder); color: #9CA3AF; pointer-events: none; }
        .imp-detail-reply-editor[contenteditable] p { margin: 0 0 8px 0; }
        .imp-detail-reply-editor[contenteditable] a { color: #7C3AED; text-decoration: underline; }
        .imp-detail-reply-editor.internal { border-color: #FDE68A; background: #FFFBEB; }
        .imp-detail-reply-editor.internal:focus { border-color: #D97706; box-shadow: 0 0 0 2px rgba(217, 119, 6, 0.1); }

        /* Status badge for drawer sidebar */
        .imp-status-badge {
            display: inline-block; padding: 3px 10px; border-radius: 12px;
            font-size: 12px; font-weight: 500;
        }
        .imp-status-badge.open { background: #DBEAFE; color: #1D4ED8; }
        .imp-status-badge.pending_support { background: #FEF3C7; color: #92400E; }
        .imp-status-badge.pending_client { background: #EDE9FE; color: #6D28D9; }
        .imp-status-badge.pending_rnd { background: #FCE7F3; color: #BE185D; }
        .imp-status-badge.closed { background: #DCFCE7; color: #166534; }
    </style>

    <div class="imp-client-wrapper">
        <!-- Back Link -->
        <a href="{{ route('filament.admin.pages.implementer-ticketing-dashboard') }}" class="imp-client-back">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 16px; height: 16px;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
            Back to Ticketing Dashboard
        </a>

        <!-- Client Details Card -->
        @if($customer)
            <div class="imp-client-card">
                <div class="imp-client-card-header">
                    <div class="imp-client-card-identity">
                        <div class="imp-client-avatar">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                            </svg>
                        </div>
                        <div>
                            <h1 class="imp-client-name">{{ $customer->name }}</h1>
                            <p class="imp-client-subtitle">{{ $customer->company_name ?? 'No Company' }}</p>
                        </div>
                    </div>
                    @if($customer->lead_id)
                        <a href="{{ route('filament.admin.resources.leads.view', $customer->lead_id) }}" class="imp-client-crm-btn" target="_blank">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 16px; height: 16px;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                            </svg>
                            View Company CRM
                        </a>
                    @endif
                </div>

                <div class="imp-client-info-grid">
                    <div class="imp-client-info-item">
                        <div class="imp-client-info-icon blue">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                            </svg>
                        </div>
                        <div>
                            <p class="imp-client-info-label">Company</p>
                            <p class="imp-client-info-value">{{ $customer->company_name ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="imp-client-info-item">
                        <div class="imp-client-info-icon green">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                            </svg>
                        </div>
                        <div>
                            <p class="imp-client-info-label">Email</p>
                            <p class="imp-client-info-value">{{ $customer->email ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="imp-client-info-item">
                        <div class="imp-client-info-icon orange">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
                            </svg>
                        </div>
                        <div>
                            <p class="imp-client-info-label">Phone</p>
                            <p class="imp-client-info-value">{{ $customer->phone ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Tickets Raised by This User -->
        <div class="imp-client-tickets-card">
            <div class="imp-client-tickets-header">
                <h2 class="imp-client-tickets-title">
                    Tickets Raised by This User
                    <span class="imp-client-tickets-count">({{ $ticketCount }} total)</span>
                </h2>
            </div>

            @if($ticketCount > 0)
                <table class="imp-client-table">
                    <thead>
                        <tr>
                            <th>TICKET ID</th>
                            <th>CATEGORY & MODULE</th>
                            <th>STATUS</th>
                            <th>PRIORITY</th>
                            <th>CREATED</th>
                            <th>ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tickets as $ticket)
                            <tr>
                                <td>
                                    <span class="imp-client-ticket-id">{{ $ticket->formatted_ticket_number }}</span>
                                </td>
                                <td>
                                    <div>
                                        <div class="imp-client-cat-primary">{{ $ticket->category ?? '-' }}</div>
                                        <div class="imp-client-cat-secondary">{{ $ticket->module ?? '' }}</div>
                                    </div>
                                </td>
                                <td>
                                    <span class="imp-client-status-badge {{ $ticket->status->value }}">
                                        {{ $ticket->status->label() }}
                                    </span>
                                </td>
                                <td>
                                    <span class="imp-client-priority-{{ strtolower($ticket->priority ?? 'medium') }}">
                                        {{ ucfirst($ticket->priority ?? 'Medium') }}
                                    </span>
                                </td>
                                <td style="color: #6B7280;">
                                    {{ $ticket->created_at->format('n/j/Y') }}
                                </td>
                                <td>
                                    <button wire:click="openTicketDetail({{ $ticket->id }})"
                                            class="imp-client-view-btn"
                                            title="View">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="imp-client-empty">
                    No tickets found for this user
                </div>
            @endif
        </div>
    </div>

    <!-- Ticket Detail Drawer -->
    @if($showTicketDetail && $selectedTicket)
        <div class="imp-detail-overlay"
             x-data="{ open: true }"
             x-init="document.body.classList.add('imp-drawer-open')"
             x-show="open"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click.self="document.body.classList.remove('imp-drawer-open'); $wire.closeTicketDetail()"
             @keydown.window.escape="document.body.classList.remove('imp-drawer-open'); $wire.closeTicketDetail()"
             wire:ignore.self>

            <div class="imp-detail-drawer"
                 x-show="open"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="translate-x-full"
                 x-transition:enter-end="translate-x-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="translate-x-0"
                 x-transition:leave-end="translate-x-full">

                <!-- Header -->
                <div class="imp-detail-header">
                    <div class="imp-detail-header-top">
                        <div class="imp-detail-header-info">
                            <h2 class="imp-detail-title">
                                {{ $selectedTicket->subject ?? 'No Subject' }}
                            </h2>
                            <span class="imp-detail-ticket-id">{{ $selectedTicket->formatted_ticket_number }} - {{ $selectedTicket->category ?? '' }}</span>
                        </div>
                        <button wire:click="closeTicketDetail" @click="document.body.classList.remove('imp-drawer-open')" class="imp-detail-close">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 20px; height: 20px;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <div class="imp-detail-header-meta">
                        @php $detailSlaStatus = $selectedTicket->getSlaStatus(); @endphp
                        <span class="imp-detail-sla-badge {{ $detailSlaStatus }}">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 14px; height: 14px;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ $selectedTicket->getTimeRemaining() }}
                        </span>
                        <span class="imp-detail-company-name">{{ $selectedTicket->customer?->company_name ?? '-' }}</span>
                    </div>
                    <div class="imp-detail-actions-bar"></div>
                </div>

                <!-- Main Content - Split View -->
                <div class="imp-detail-content">
                    <!-- Left Sidebar -->
                    <div class="imp-detail-sidebar">
                        <h3 class="imp-detail-sidebar-title">Ticket Properties</h3>

                        <!-- Client Contact -->
                        <div class="imp-detail-section">
                            <label class="imp-detail-label">CLIENT CONTACT</label>
                            <div class="imp-detail-card">
                                <div class="imp-detail-card-row">
                                    <div class="imp-detail-avatar blue">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 16px; height: 16px;">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="imp-detail-name">{{ $selectedTicket->customer?->name ?? 'Unknown' }}</p>
                                        <p class="imp-detail-sublabel">Primary Contact</p>
                                    </div>
                                </div>
                                @if($selectedTicket->customer?->email)
                                    <div class="imp-detail-email-row">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 12px; height: 12px;">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                                        </svg>
                                        <span>{{ $selectedTicket->customer->email }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Ticket Owner -->
                        <div class="imp-detail-section">
                            <label class="imp-detail-label">TICKET OWNER</label>
                            <div class="imp-detail-card">
                                <div class="imp-detail-card-row">
                                    <div class="imp-detail-avatar purple">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 16px; height: 16px;">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="imp-detail-name">{{ $selectedTicket->implementerUser?->name ?? $selectedTicket->implementer_name ?? 'Unassigned' }}</p>
                                        <p class="imp-detail-sublabel">Implementer</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="imp-detail-section" x-data="{ showStatusDrop: false }">
                            <label class="imp-detail-label">STATUS</label>
                            <div class="imp-detail-status-wrapper">
                                <button @click="showStatusDrop = !showStatusDrop" class="imp-detail-status-btn">
                                    <span class="imp-status-badge {{ $selectedTicket->status->value }}">
                                        {{ $selectedTicket->status->label() }}
                                    </span>
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 16px; height: 16px; color: #6B7280;">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                    </svg>
                                </button>
                                <div x-show="showStatusDrop" @click.away="showStatusDrop = false" class="imp-detail-status-dropdown" x-cloak>
                                    @foreach(['open', 'pending_support', 'pending_client', 'pending_rnd', 'closed'] as $statusVal)
                                        <button wire:click="changeTicketStatus('{{ $statusVal }}')"
                                                @click="showStatusDrop = false"
                                                class="imp-detail-status-option {{ $selectedTicket->status->value === $statusVal ? 'active' : '' }}">
                                            {{ \App\Enums\ImplementerTicketStatus::from($statusVal)->label() }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Key Dates -->
                        <div class="imp-detail-section">
                            <label class="imp-detail-label">KEY DATES</label>
                            <div class="imp-detail-dates-card">
                                <div class="imp-detail-date-item">
                                    <div class="imp-detail-date-label">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 12px; height: 12px;">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                                        </svg>
                                        Created
                                    </div>
                                    <p class="imp-detail-date-value">{{ $selectedTicket->created_at->format('M d, Y') }}</p>
                                </div>
                                <div class="imp-detail-date-item">
                                    <div class="imp-detail-date-label">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 12px; height: 12px;">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        SLA Deadline
                                    </div>
                                    <p class="imp-detail-date-value">{{ $selectedTicket->getSlaDeadline()->format('M d, Y') }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Details -->
                        <div class="imp-detail-section">
                            <label class="imp-detail-label">DETAILS</label>
                            <div class="imp-detail-props-card">
                                <div class="imp-detail-prop-row">
                                    <span class="imp-detail-prop-label">Priority:</span>
                                    <span class="imp-detail-prop-value priority-{{ strtolower($selectedTicket->priority ?? 'medium') }}">
                                        {{ ucfirst($selectedTicket->priority ?? 'Medium') }}
                                    </span>
                                </div>
                                <div class="imp-detail-prop-row">
                                    <span class="imp-detail-prop-label">Category:</span>
                                    <span class="imp-detail-prop-value">{{ $selectedTicket->category ?? '-' }}</span>
                                </div>
                                <div class="imp-detail-prop-row">
                                    <span class="imp-detail-prop-label">Module:</span>
                                    <span class="imp-detail-prop-value">{{ $selectedTicket->module ?? '-' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Panel - Thread & Reply -->
                    <div class="imp-detail-thread-panel">
                        <!-- Conversation Thread -->
                        <div class="imp-detail-thread">
                            @if($selectedTicket->replies->count() > 0)
                                @foreach($selectedTicket->replies as $reply)
                                    @if($reply->is_internal_note)
                                        <div class="imp-detail-message internal"
                                             @if($reply->sender_type === 'App\\Models\\User' && $reply->sender_id === auth()->id())
                                             x-data="{ editing: false, editText: @js($reply->message), confirmDelete: false }"
                                             @endif
                                        >
                                            <div class="imp-detail-msg-header">
                                                <div class="imp-detail-msg-avatar yellow">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 16px; height: 16px;">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                                    </svg>
                                                </div>
                                                <div class="imp-detail-msg-info">
                                                    <span class="imp-detail-msg-name">{{ $reply->sender_name }}</span>
                                                    <span class="imp-detail-msg-badge yellow">Internal Only</span>
                                                    @if($reply->updated_at->gt($reply->created_at->addSecond()))
                                                        <span class="imp-detail-msg-time">Edited - {{ $reply->updated_at->format('M d, g:i A') }}</span>
                                                    @else
                                                        <span class="imp-detail-msg-time">{{ $reply->created_at->format('M d, g:i A') }}</span>
                                                    @endif
                                                </div>
                                                @if($reply->sender_type === 'App\\Models\\User' && $reply->sender_id === auth()->id())
                                                    <div class="imp-note-action-btns">
                                                        <button class="imp-note-edit-btn" @click="editing = !editing" title="Edit note">
                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 14px; height: 14px;">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                                            </svg>
                                                        </button>
                                                        <button class="imp-note-delete-btn" @click="confirmDelete = true" x-show="!confirmDelete" title="Delete note">
                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 14px; height: 14px;">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                                            </svg>
                                                        </button>
                                                        <div class="imp-note-delete-confirm" x-show="confirmDelete" x-cloak>
                                                            <span>Delete?</span>
                                                            <button class="imp-note-delete-yes" @click="$wire.deleteInternalNote({{ $reply->id }})">Yes</button>
                                                            <button class="imp-note-delete-no" @click="confirmDelete = false">No</button>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                            @if($reply->sender_type === 'App\\Models\\User' && $reply->sender_id === auth()->id())
                                                <div x-show="!editing" class="imp-detail-msg-body">{!! strip_tags($reply->message, '<p><br><strong><b><em><i><a><ul><ol><li>') !!}</div>
                                                <div x-show="editing" x-cloak class="imp-note-edit-area">
                                                    <textarea x-model="editText" class="imp-note-edit-textarea" rows="3"></textarea>
                                                    <div class="imp-note-edit-actions">
                                                        <button class="imp-note-edit-cancel" @click="editing = false; editText = @js($reply->message)">Cancel</button>
                                                        <button class="imp-note-edit-save" @click="$wire.updateInternalNote({{ $reply->id }}, editText); editing = false;">Save</button>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="imp-detail-msg-body">{!! strip_tags($reply->message, '<p><br><strong><b><em><i><a><ul><ol><li>') !!}</div>
                                            @endif
                                        </div>
                                    @else
                                        @php $isClient = str_contains($reply->sender_type, 'Customer'); @endphp
                                        <div class="imp-detail-message {{ $isClient ? 'client' : 'staff' }}">
                                            <div class="imp-detail-msg-header">
                                                <div class="imp-detail-msg-avatar {{ $isClient ? 'blue' : 'purple' }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 16px; height: 16px;">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                                    </svg>
                                                </div>
                                                <div class="imp-detail-msg-info">
                                                    <span class="imp-detail-msg-name">{{ $reply->sender_name }}</span>
                                                    <span class="imp-detail-msg-badge {{ $isClient ? 'blue' : 'purple' }}">
                                                        {{ $isClient ? 'Client' : 'HR Support' }}
                                                    </span>
                                                    <span class="imp-detail-msg-time">{{ $reply->created_at->format('M d, g:i A') }}</span>
                                                </div>
                                            </div>
                                            <div class="imp-detail-msg-body">{!! strip_tags($reply->message, '<p><br><strong><b><em><i><a><ul><ol><li>') !!}</div>
                                            @if($reply->attachments)
                                                <div class="imp-detail-msg-attachments">
                                                    @foreach($reply->attachments as $attachment)
                                                        <a href="{{ Storage::url($attachment) }}" target="_blank" class="imp-detail-attachment-link">
                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 14px; height: 14px;">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 01-6.364-6.364l10.94-10.94A3 3 0 1119.5 7.372L8.552 18.32m.009-.01l-.01.01m5.699-9.941l-7.81 7.81a1.5 1.5 0 002.112 2.13" />
                                                            </svg>
                                                            {{ basename($attachment) }}
                                                        </a>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                @endforeach
                            @else
                                <div class="imp-detail-no-messages">
                                    <p>No messages yet. Start the conversation by sending a reply.</p>
                                </div>
                            @endif
                        </div>

                        <!-- Reply Box -->
                        <div class="imp-detail-reply-box"
                             x-data="{
                                exec(command, value = null) {
                                    document.execCommand(command, false, value);
                                    this.$refs.replyEditor.focus();
                                },
                                isActive(command) {
                                    return document.queryCommandState(command);
                                },
                                insertLink() {
                                    const url = prompt('Enter URL:');
                                    if (url) this.exec('createLink', url);
                                },
                                handlePaste(e) {
                                    e.preventDefault();
                                    const html = e.clipboardData.getData('text/html');
                                    const text = e.clipboardData.getData('text/plain');
                                    document.execCommand('insertHTML', false, html || text);
                                },
                                replyOpen: false,
                                showCcBcc: false,
                                syncAndSubmit() {
                                    $wire.set('replyMessage', this.$refs.replyEditor.innerHTML);
                                    $wire.submitReply();
                                }
                             }"
                             x-init="
                                $wire.on('replyTemplateApplied', () => {
                                    replyOpen = true;
                                    $nextTick(() => { $refs.replyEditor.innerHTML = $wire.replyMessage || ''; });
                                });
                                $wire.on('replyEditorReset', () => {
                                    $refs.replyEditor.innerHTML = '';
                                    showCcBcc = false;
                                    replyOpen = false;
                                });
                             "
                        >
                            <div class="imp-detail-reply-header" @click="replyOpen = !replyOpen" style="cursor: pointer;">
                                <label class="imp-detail-reply-label" style="cursor: pointer;">
                                    {{ $isInternalNote ? 'Internal Note (Private)' : 'Reply to Client' }}
                                </label>
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <label class="imp-detail-internal-toggle" @click.stop>
                                        <input type="checkbox" wire:model.live="isInternalNote">
                                        <span>Internal Note</span>
                                    </label>
                                    <svg class="imp-reply-chevron" :class="{ 'expanded': replyOpen }" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5" />
                                    </svg>
                                </div>
                            </div>

                            <div x-show="replyOpen" x-collapse x-cloak>

                            <!-- TO / CC / BCC fields (hidden when Internal Note) -->
                            <div class="imp-detail-reply-email-fields" x-show="!$wire.isInternalNote" x-cloak>
                                <div class="imp-detail-reply-field-row">
                                    <label>To</label>
                                    <input type="email" wire:model="replyTo" readonly>
                                    <button type="button" class="imp-ccbcc-toggle" @click="showCcBcc = !showCcBcc" x-text="showCcBcc ? 'Hide CC/BCC' : 'CC/BCC'"></button>
                                </div>
                                <div x-show="showCcBcc" x-collapse>
                                    <div class="imp-detail-reply-field-row" style="margin-top: 6px;">
                                        <label>CC</label>
                                        <input type="text" wire:model="replyCc" placeholder="cc@example.com">
                                    </div>
                                    <div class="imp-detail-reply-field-row" style="margin-top: 6px;">
                                        <label>BCC</label>
                                        <input type="text" wire:model="replyBcc" placeholder="bcc@example.com">
                                    </div>
                                </div>
                            </div>

                            <!-- Email Template selector (hidden when Internal Note) -->
                            <div class="imp-detail-reply-field-row" x-show="!$wire.isInternalNote" x-cloak style="padding-bottom: 8px;">
                                <label>Email Template</label>
                                <select wire:model="replyEmailTemplate" wire:change="applyReplyTemplate($event.target.value)">
                                    <option value="">No Template</option>
                                    <option value="First Response">First Response</option>
                                    <option value="Require More Time">Require More Time</option>
                                    <option value="R&D Escalation">R&D Escalation</option>
                                </select>
                            </div>

                            <!-- Rich text toolbar -->
                            <div class="imp-detail-reply-toolbar">
                                <button type="button" title="Bold" @mousedown.prevent="exec('bold')" :class="{ 'imp-toolbar-active': isActive('bold') }">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 14px; height: 14px;"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3.744h-.753v8.25h7.125a4.125 4.125 0 000-8.25H6.75zm0 0v8.25m0 0h7.875a4.875 4.875 0 010 9.75H6.75v-9.75z" /></svg>
                                </button>
                                <button type="button" title="Italic" @mousedown.prevent="exec('italic')" :class="{ 'imp-toolbar-active': isActive('italic') }">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 14px; height: 14px;"><path stroke-linecap="round" stroke-linejoin="round" d="M5.248 20.246H9.05m0 0h3.696m-3.696 0l5.893-16.502m0 0H11.25m3.696 0h3.803" /></svg>
                                </button>
                                <button type="button" title="Link" @mousedown.prevent="insertLink()">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 14px; height: 14px;"><path stroke-linecap="round" stroke-linejoin="round" d="M10 13a5 5 0 007.54.54l3-3a5 5 0 00-7.07-7.07l-1.72 1.71" /><path stroke-linecap="round" stroke-linejoin="round" d="M14 11a5 5 0 00-7.54-.54l-3 3a5 5 0 007.07 7.07l1.71-1.71" /></svg>
                                </button>
                                <div class="imp-toolbar-divider"></div>
                                <button type="button" title="Attach" @mousedown.prevent="document.getElementById('imp-reply-file-upload-cp').click()">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 14px; height: 14px;"><path stroke-linecap="round" stroke-linejoin="round" d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 01-6.364-6.364l10.94-10.94A3 3 0 1119.5 7.372L8.552 18.32m.009-.01l-.01.01m5.699-9.941l-7.81 7.81a1.5 1.5 0 002.112 2.13" /></svg>
                                </button>
                                <input type="file" id="imp-reply-file-upload-cp" wire:model="replyAttachments" multiple accept=".pdf,.png,.jpg,.jpeg,.xlsx" style="display: none;">
                            </div>

                            <!-- Rich text editor -->
                            <div wire:ignore>
                                <div class="imp-detail-reply-editor {{ $isInternalNote ? 'internal' : '' }}"
                                     contenteditable="true"
                                     x-ref="replyEditor"
                                     @paste="handlePaste($event)"
                                     data-placeholder="{{ $isInternalNote ? 'Add internal notes, troubleshooting steps, or team coordination notes...' : 'Type your response to the client here...' }}"></div>
                            </div>

                            @if(!empty($replyAttachments))
                                <div class="imp-drawer-file-list" style="margin-top: 8px;">
                                    @foreach($replyAttachments as $index => $file)
                                        <div class="imp-drawer-file-item">
                                            <div class="imp-drawer-file-info">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 16px; height: 16px; color: #9CA3AF;">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 01-6.364-6.364l10.94-10.94A3 3 0 1119.5 7.372L8.552 18.32m.009-.01l-.01.01m5.699-9.941l-7.81 7.81a1.5 1.5 0 002.112 2.13" />
                                                </svg>
                                                <span>{{ $file->getClientOriginalName() }}</span>
                                                <span class="imp-drawer-file-size">({{ number_format($file->getSize() / 1024, 1) }} KB)</span>
                                            </div>
                                            <button type="button" class="imp-drawer-file-remove" wire:click="removeReplyAttachment({{ $index }})">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 16px; height: 16px;">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <div class="imp-detail-reply-footer">
                                <div class="imp-detail-reply-left"></div>
                                <button @click="syncAndSubmit()"
                                        wire:loading.attr="disabled"
                                        wire:target="submitReply"
                                        class="imp-detail-send-btn {{ $isInternalNote ? 'internal' : '' }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 16px; height: 16px;">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                                    </svg>
                                    <span wire:loading.remove wire:target="submitReply">
                                        {{ $isInternalNote ? 'Save Internal Note' : 'Send & Update Status' }}
                                    </span>
                                    <span wire:loading wire:target="submitReply">Sending...</span>
                                </button>
                            </div>

                            </div><!-- end x-collapse wrapper -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</x-filament-panels::page>
