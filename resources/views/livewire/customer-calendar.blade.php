@php
use Carbon\Carbon;
@endphp

<div>
    <style>
        /* ──────────────────────────────────────────────────────────────
           TimeTec Split Canvas — calendar layout tokens + palette
        ────────────────────────────────────────────────────────────── */
        .calendar-container {
            --cal-primary: #00a4e0;
            --cal-accent-dark: #003c75;
            --cal-accent-mid: #1a6dd4;
            --cal-hover-bg: #f0f7ff;
            --cal-border: #e5e7eb;
            --cal-border-strong: #d1d5db;
            --cal-text: #111827;
            --cal-text-muted: #6b7280;
            --cal-text-quiet: #9ca3af;
            --cal-surface: #ffffff;
            --cal-surface-soft: #f9fafb;
            --cal-available-bg: #ecfdf5;
            --cal-available-border: #10b981;
            --cal-available-text: #065f46;
            --cal-weekend-bg: #fffbeb;
            --cal-weekend-text: #b45309;
            --cal-holiday-bg: #fef2f2;
            --cal-holiday-text: #b91c1c;
            --cal-full-bg: #fef2f2;
            --cal-full-text: #dc2626;
            --cal-meeting-bg: #eff6ff;
            --cal-meeting-border: #003c75;
            --cal-meeting-text: #003c75;

            background: var(--cal-surface);
            border-radius: 18px;
            padding: 1.75rem;
            border: 1px solid var(--cal-border);
            box-shadow: 0 1px 3px rgba(16, 24, 40, 0.04), 0 8px 24px -12px rgba(16, 24, 40, 0.08);
        }

        .cal-split {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 360px;
            gap: 1.75rem;
            align-items: start;
        }

        @media (max-width: 1200px) {
            .cal-split {
                grid-template-columns: minmax(0, 1fr) 320px;
                gap: 1.25rem;
            }
        }

        @media (max-width: 960px) {
            .cal-split {
                grid-template-columns: 1fr;
            }
        }

        .cal-left {
            min-width: 0;
        }

        .calendar-header-section {
            background: transparent;
            backdrop-filter: none;
            border-radius: 0;
            padding: 0 0 1.25rem 0;
            margin-bottom: 1.25rem;
            border: none;
            border-bottom: 1px solid var(--cal-border);
        }

        .cal-title-wrap {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .cal-title-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: var(--cal-hover-bg);
            color: var(--cal-accent-dark);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }

        .cal-title-text h2 {
            font-family: 'Poppins', sans-serif;
            font-size: 1.375rem;
            font-weight: 600;
            color: var(--cal-text);
            letter-spacing: -0.01em;
            margin: 0;
            line-height: 1.2;
        }

        .cal-title-text p {
            font-size: 0.8125rem;
            color: var(--cal-text-muted);
            margin: 2px 0 0 0;
        }

        .cal-month-nav {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .cal-month-nav .nav-button {
            width: 36px;
            height: 36px;
            padding: 0;
            border-radius: 10px;
            background: var(--cal-surface);
            border: 1px solid var(--cal-border);
            color: var(--cal-accent-dark);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.15s;
            box-shadow: none;
        }

        .cal-month-nav .nav-button:hover {
            background: var(--cal-hover-bg);
            border-color: transparent;
            color: var(--cal-accent-mid);
            transform: none;
        }

        .cal-month-label {
            font-family: 'Poppins', sans-serif;
            font-size: 0.9375rem;
            font-weight: 600;
            color: var(--cal-accent-dark);
            min-width: 120px;
            text-align: center;
            letter-spacing: -0.005em;
        }

        /* Compact chip-row legend */
        .cal-legend-row {
            display: flex;
            flex-wrap: wrap;
            gap: 8px 14px;
            margin-bottom: 0.75rem;
            font-size: 12px;
            color: var(--cal-text-muted);
        }

        .cal-legend-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 3px 10px 3px 8px;
            background: var(--cal-surface);
            border: 1px solid var(--cal-border);
            border-radius: 999px;
            font-weight: 500;
        }

        .cal-legend-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
        }

        .cal-legend-dot.avail { background: var(--cal-available-border); }
        .cal-legend-dot.meet  { background: var(--cal-meeting-border); }
        .cal-legend-dot.wknd  { background: #f59e0b; }
        .cal-legend-dot.hol   { background: #ef4444; }
        .cal-legend-dot.full  { background: var(--cal-text-quiet); }

        /* Grid + day cells */
        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 6px;
            background: transparent;
            border-radius: 0;
            overflow: visible;
            box-shadow: none;
        }

        .calendar-day {
            background: var(--cal-surface);
            backdrop-filter: none;
            padding: 10px 10px 8px;
            min-height: 82px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            cursor: default;
            transition: all 0.15s ease;
            position: relative;
            border: 1px solid var(--cal-border);
            border-radius: 10px;
        }

        .calendar-day:hover {
            transform: none;
            box-shadow: none;
            background: var(--cal-surface);
        }

        .calendar-day.other-month {
            background: transparent;
            color: var(--cal-text-quiet);
            border-color: transparent;
        }

        .calendar-day.today {
            background: var(--cal-surface);
            border: 2px solid var(--cal-accent-dark);
            box-shadow: 0 0 0 3px rgba(0, 60, 117, 0.08);
        }

        .calendar-day.past {
            background: var(--cal-surface-soft);
            color: var(--cal-text-quiet);
            cursor: not-allowed;
            border-color: var(--cal-border);
        }

        .calendar-day.past .day-number {
            color: var(--cal-text-quiet);
            font-weight: 500;
        }

        .calendar-day.weekend {
            background: var(--cal-weekend-bg);
            border-color: #fde68a;
        }

        .calendar-day.weekend .day-number {
            color: var(--cal-weekend-text);
        }

        .calendar-day.holiday {
            background: var(--cal-holiday-bg);
            border-color: #fecaca;
        }

        .calendar-day.holiday .day-number {
            color: var(--cal-holiday-text);
        }

        .calendar-day.bookable {
            background: var(--cal-available-bg);
            border: 1.5px solid var(--cal-available-border);
            cursor: pointer;
        }

        .calendar-day.bookable:hover {
            background: #d1fae5;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px -4px rgba(16, 185, 129, 0.25);
        }

        .calendar-day.bookable .day-number {
            color: var(--cal-available-text);
        }

        .calendar-day.selected {
            background: var(--cal-accent-dark);
            border-color: var(--cal-accent-dark);
            box-shadow: 0 6px 20px -6px rgba(0, 60, 117, 0.55);
            transform: translateY(-2px);
        }

        .calendar-day.selected .day-number,
        .calendar-day.selected .available-count {
            color: #ffffff;
        }

        .calendar-day.selected .available-count {
            background: rgba(255, 255, 255, 0.18);
        }

        .calendar-day.has-meeting {
            background: var(--cal-meeting-bg);
            border: 1.5px solid var(--cal-meeting-border);
            cursor: pointer;
        }

        .calendar-day.has-meeting .day-number {
            color: var(--cal-meeting-text);
        }

        .calendar-day.has-meeting:hover {
            background: #dbeafe;
            transform: translateY(-2px);
        }

        .calendar-day.disabled {
            background: var(--cal-surface-soft);
            color: var(--cal-text-quiet);
            cursor: not-allowed;
            border-color: var(--cal-border);
        }

        .calendar-day.disabled .day-number {
            color: var(--cal-text-quiet);
            font-weight: 500;
        }

        .day-number {
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            font-size: 15px;
            color: var(--cal-text);
            line-height: 1;
        }

        /* Corner state pictogram */
        .cal-day-icon {
            position: absolute;
            top: 8px;
            right: 9px;
            font-size: 11px;
            line-height: 1;
            opacity: 0.55;
            transition: transform 0.2s ease, opacity 0.2s ease;
            pointer-events: none;
        }

        .calendar-day.bookable .cal-day-icon  { color: var(--cal-available-border); opacity: 0.7; }
        .calendar-day.weekend .cal-day-icon   { color: #d97706; opacity: 0.55; }
        .calendar-day.holiday .cal-day-icon   { color: #dc2626; opacity: 0.6; }
        .calendar-day.has-meeting .cal-day-icon { color: var(--cal-meeting-text); opacity: 0.75; }

        .calendar-day.bookable:hover .cal-day-icon {
            transform: rotate(14deg) scale(1.18);
            opacity: 1;
        }
        .calendar-day.has-meeting:hover .cal-day-icon {
            transform: scale(1.12);
            opacity: 1;
        }

        .calendar-day.selected .cal-day-icon {
            color: #ffffff;
            opacity: 0.8;
        }

        /* Today: elevate specificity so the blue ring wins over .bookable */
        .calendar-day.today,
        .calendar-day.today.bookable,
        .calendar-day.today.has-meeting {
            border: 2px solid var(--cal-accent-dark);
            box-shadow: 0 0 0 3px rgba(0, 60, 117, 0.08);
        }

        .calendar-day.today::after {
            content: "Today";
            position: absolute;
            top: -9px;
            left: 10px;
            padding: 1px 7px;
            background: var(--cal-accent-dark);
            color: #ffffff;
            font-family: 'Poppins', sans-serif;
            font-size: 9px;
            font-weight: 600;
            border-radius: 999px;
            line-height: 1.4;
            pointer-events: none;
        }

        .available-count {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 11px;
            color: var(--cal-available-text);
            font-weight: 600;
            background: rgba(16, 185, 129, 0.14);
            padding: 2px 8px;
            border-radius: 999px;
            text-align: center;
            align-self: flex-start;
            letter-spacing: 0.01em;
        }

        .available-count i {
            font-size: 9px;
            color: var(--cal-available-border);
        }

        .meeting-indicator {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 11px;
            color: var(--cal-meeting-text);
            font-weight: 600;
            background: rgba(0, 60, 117, 0.08);
            padding: 2px 8px;
            border-radius: 999px;
            align-self: flex-start;
        }

        .meeting-indicator i {
            font-size: 9px;
        }

        .meeting-indicator.completed {
            color: #047857;
            background: rgba(16, 185, 129, 0.14);
        }

        .cal-full-marker {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 11px;
            color: var(--cal-full-text);
            font-weight: 600;
            align-self: flex-start;
        }

        .cal-full-marker i {
            font-size: 9px;
        }

        /* ──────────────────────────────────────────────────────────────
           Right booking panel
        ────────────────────────────────────────────────────────────── */
        .cal-book-panel {
            background: var(--cal-surface);
            border: 1px solid var(--cal-border);
            border-radius: 14px;
            padding: 1.25rem;
            position: sticky;
            top: 96px;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .cal-panel-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 10px;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--cal-border);
        }

        .cal-panel-eyebrow {
            font-size: 11px;
            font-weight: 600;
            color: var(--cal-primary);
            margin: 0 0 4px 0;
        }

        .cal-panel-date {
            font-family: 'Poppins', sans-serif;
            font-size: 1.125rem;
            font-weight: 700;
            color: var(--cal-accent-dark);
            margin: 0;
            line-height: 1.2;
        }

        .cal-panel-sub {
            font-size: 12px;
            color: var(--cal-text-muted);
            margin: 4px 0 0 0;
        }

        .cal-panel-clear {
            background: none;
            border: none;
            color: var(--cal-text-quiet);
            font-size: 12px;
            cursor: pointer;
            padding: 4px 6px;
            border-radius: 6px;
            transition: all 0.15s;
        }

        .cal-panel-clear:hover {
            color: var(--cal-accent-dark);
            background: var(--cal-hover-bg);
        }

        .cal-panel-empty {
            text-align: center;
            padding: 2rem 0.5rem;
            color: var(--cal-text-muted);
        }

        .cal-panel-empty-icon {
            width: 52px;
            height: 52px;
            margin: 0 auto 0.75rem;
            border-radius: 50%;
            background: var(--cal-hover-bg);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--cal-accent-dark);
            font-size: 22px;
        }

        .cal-panel-empty-title {
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
            font-weight: 600;
            color: var(--cal-text);
            margin: 0 0 4px 0;
        }

        .cal-panel-empty-body {
            font-size: 12px;
            color: var(--cal-text-muted);
            margin: 0;
            line-height: 1.5;
        }

        .cal-tutorial-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-top: 14px;
            padding: 9px 18px;
            background: transparent;
            color: var(--cal-accent-dark);
            border: 1.25px solid var(--cal-accent-dark);
            border-radius: 999px;
            font-family: 'Poppins', sans-serif;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: 0.01em;
            cursor: pointer;
            transition: background 0.18s ease,
                        color 0.18s ease,
                        transform 0.18s ease,
                        box-shadow 0.18s ease;
        }

        .cal-tutorial-link:hover {
            background: var(--cal-accent-dark);
            color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 6px 16px -8px rgba(0, 60, 117, 0.45);
        }

        .cal-tutorial-link:hover .cal-tutorial-link-icon {
            transform: rotate(8deg);
        }

        .cal-tutorial-link:focus-visible {
            outline: 2px solid var(--cal-primary);
            outline-offset: 3px;
        }

        .cal-tutorial-link-icon {
            font-size: 14px;
            transition: transform 0.18s ease;
        }

        /* One slot per row — horizontal layout */
        .cal-slot-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 6px;
        }

        .cal-slot {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 14px;
            border: 1.5px solid var(--cal-border);
            border-radius: 10px;
            background: var(--cal-surface);
            font-family: 'Poppins', sans-serif;
            cursor: pointer;
            transition: transform 0.15s ease, border-color 0.15s ease, background 0.15s ease, box-shadow 0.18s ease;
            text-align: left;
            min-height: 52px;
            position: relative;
            overflow: hidden;
        }

        .cal-slot::before {
            content: '';
            position: absolute;
            left: 0; top: 0; bottom: 0;
            width: 3px;
            background: var(--cal-accent-dark);
            transform: scaleY(0);
            transform-origin: center;
            transition: transform 0.2s ease;
        }

        .cal-slot-icon {
            width: 34px;
            height: 34px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 9px;
            background: var(--cal-hover-bg);
            color: var(--cal-accent-dark);
            flex-shrink: 0;
            font-size: 13px;
            transition: background 0.15s ease, color 0.15s ease, transform 0.18s ease;
        }

        .cal-slot-info {
            display: flex;
            flex-direction: column;
            gap: 4px;
            min-width: 0;
            flex: 1;
        }

        .cal-slot-time-range {
            font-size: 14px;
            font-weight: 600;
            color: var(--cal-accent-dark);
            line-height: 1;
            font-variant-numeric: tabular-nums;
            letter-spacing: -0.01em;
            white-space: nowrap;
        }

        .cal-slot-session-label {
            font-size: 11px;
            font-weight: 600;
            color: var(--cal-text-muted);
            line-height: 1;
        }

        .cal-slot-arrow {
            color: var(--cal-text-quiet);
            font-size: 12px;
            transition: transform 0.18s ease, color 0.15s ease;
            flex-shrink: 0;
            margin-left: auto;
        }

        .cal-slot:hover {
            border-color: var(--cal-accent-mid);
            background: var(--cal-hover-bg);
        }
        .cal-slot:hover::before { transform: scaleY(0.7); }
        .cal-slot:hover .cal-slot-icon {
            background: var(--cal-accent-mid);
            color: #ffffff;
            transform: rotate(-6deg) scale(1.04);
        }
        .cal-slot:hover .cal-slot-arrow {
            transform: translateX(4px);
            color: var(--cal-accent-mid);
        }

        .cal-slot.is-selected {
            background: var(--cal-accent-dark);
            border-color: var(--cal-accent-dark);
            box-shadow: 0 6px 16px -6px rgba(0, 60, 117, 0.45);
        }
        .cal-slot.is-selected::before { transform: scaleY(1); background: #ffffff; }
        .cal-slot.is-selected .cal-slot-icon {
            background: rgba(255, 255, 255, 0.18);
            color: #ffffff;
            transform: none;
        }
        .cal-slot.is-selected .cal-slot-time-range { color: #ffffff; }
        .cal-slot.is-selected .cal-slot-session-label { color: rgba(255, 255, 255, 0.78); }
        .cal-slot.is-selected .cal-slot-arrow {
            color: #ffffff;
            transform: translateX(0);
        }

        .cal-continue-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            padding: 12px 16px;
            background: var(--cal-accent-dark);
            color: #ffffff;
            border: none;
            border-radius: 10px;
            font-family: 'Poppins', sans-serif;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s;
            letter-spacing: 0.01em;
        }

        .cal-continue-btn:hover:not(:disabled) {
            background: var(--cal-accent-mid);
            transform: translateY(-1px);
            box-shadow: 0 8px 20px -6px rgba(0, 60, 117, 0.35);
        }

        .cal-continue-btn:disabled {
            background: var(--cal-border-strong);
            cursor: not-allowed;
            opacity: 0.8;
        }

        .cal-panel-footnote {
            font-size: 11px;
            color: var(--cal-text-quiet);
            text-align: center;
            margin: 0;
            line-height: 1.5;
        }

        .cal-panel-footnote i {
            color: var(--cal-primary);
            margin-right: 4px;
        }

        .cal-panel-error {
            padding: 10px 12px;
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 8px;
            color: #b91c1c;
            font-size: 12px;
            line-height: 1.45;
        }

        /* ──────────────────────────────────────────────────────────────
           Inline booking form (meeting type + required attendees)
        ────────────────────────────────────────────────────────────── */
        .cal-field-block {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .cal-field-label {
            font-family: 'Poppins', sans-serif;
            font-size: 10px;
            font-weight: 600;
            color: var(--cal-text-muted);
            margin: 0;
        }

        .cal-field-required {
            color: var(--cal-holiday-text);
            margin-left: 2px;
            font-weight: 700;
        }

        .cal-field-static {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            background: var(--cal-hover-bg);
            border: 1px solid var(--cal-border);
            border-radius: 10px;
            font-family: 'Poppins', sans-serif;
            font-size: 13px;
            font-weight: 500;
            color: var(--cal-accent-dark);
            line-height: 1.3;
        }

        .cal-field-static i {
            color: var(--cal-primary);
            font-size: 13px;
            flex-shrink: 0;
        }

        .cal-field-input {
            width: 100%;
            padding: 10px 12px;
            background: var(--cal-surface);
            border: 1.5px solid var(--cal-border);
            border-radius: 10px;
            font-family: 'Poppins', sans-serif;
            font-size: 13px;
            color: var(--cal-text);
            transition: border-color 0.15s, box-shadow 0.15s;
            outline: none;
        }

        .cal-field-input::placeholder {
            color: var(--cal-text-quiet);
            font-weight: 400;
        }

        .cal-field-input:hover:not(:focus) {
            border-color: var(--cal-border-strong);
        }

        .cal-field-input:focus {
            border-color: var(--cal-accent-dark);
            box-shadow: 0 0 0 3px rgba(0, 60, 117, 0.12);
        }

        /* Locked variant — read-only display, opens Bulk Edit drawer on click */
        .cal-field-input--locked {
            background-color: #f8fafc;
            color: #334155;
            cursor: pointer;
            border-style: dashed;
        }
        .cal-field-input--locked::placeholder {
            color: #94a3b8;
            font-style: italic;
        }
        .cal-field-input--locked:hover {
            border-color: var(--cal-accent-dark);
            background-color: #f1f5f9;
        }
        .cal-field-input--locked:focus {
            outline: none;
            border-color: var(--cal-accent-dark);
            box-shadow: 0 0 0 3px rgba(0, 60, 117, 0.12);
        }

        .cal-field-hint {
            display: flex;
            align-items: flex-start;
            gap: 5px;
            font-size: 11px;
            color: var(--cal-text-quiet);
            margin: 0;
            line-height: 1.5;
        }

        .cal-field-hint i {
            color: var(--cal-primary);
            font-size: 10px;
            margin-top: 2px;
            flex-shrink: 0;
        }

        .cal-submit-loading {
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .cal-spinner {
            width: 14px;
            height: 14px;
            animation: cal-spin 0.8s linear infinite;
        }

        .cal-spinner circle {
            opacity: 0.85;
        }

        @keyframes cal-spin {
            to { transform: rotate(360deg); }
        }

        /* Allow the sticky panel to scroll internally on short viewports */
        .cal-book-panel {
            max-height: calc(100vh - 112px);
            overflow-y: auto;
        }
        .cal-book-panel::-webkit-scrollbar { width: 6px; }
        .cal-book-panel::-webkit-scrollbar-track { background: transparent; }
        .cal-book-panel::-webkit-scrollbar-thumb { background: var(--cal-border); border-radius: 3px; }

        /* ──────────────────────────────────────────────────────────────
           Bulk attendees drawer
        ────────────────────────────────────────────────────────────── */

        /* Variables scoped to the teleported drawer root so the
           drawer keeps the TimeTec palette after Alpine moves it
           out of .calendar-container (which owns the vars otherwise). */
        .cal-att-root {
            --cal-primary: #00a4e0;
            --cal-accent-dark: #003c75;
            --cal-accent-mid: #1a6dd4;
            --cal-hover-bg: #f0f7ff;
            --cal-border: #e5e7eb;
            --cal-border-strong: #d1d5db;
            --cal-text: #111827;
            --cal-text-muted: #6b7280;
            --cal-text-quiet: #9ca3af;
            --cal-surface: #ffffff;
            --cal-holiday-text: #b91c1c;
        }

        /* Inline "Bulk edit" trigger that sits next to the field label */
        .cal-field-label-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            min-height: 20px;
        }

        .cal-att-btn-inline {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 3px 9px 3px 8px;
            background: transparent;
            border: 1px solid var(--cal-border);
            border-radius: 999px;
            color: var(--cal-accent-dark);
            font-family: 'Poppins', sans-serif;
            font-size: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s ease;
            line-height: 1.4;
            white-space: nowrap;
        }

        .cal-att-btn-inline:hover {
            background: var(--cal-hover-bg);
            border-color: var(--cal-accent-dark);
            color: var(--cal-accent-mid);
        }

        .cal-att-btn-inline i {
            font-size: 9px;
            color: var(--cal-primary);
        }

        .cal-att-btn-inline:hover i {
            color: var(--cal-accent-mid);
        }

        .cal-att-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.48);
            backdrop-filter: blur(3px);
            z-index: 200;
        }

        .cal-att-drawer {
            position: fixed;
            top: 0;
            right: 0;
            bottom: 0;
            width: 100%;
            max-width: 420px;
            background: var(--cal-surface);
            box-shadow: -24px 0 60px -16px rgba(15, 23, 42, 0.22);
            z-index: 201;
            display: flex;
            flex-direction: column;
            font-family: 'Poppins', sans-serif;
        }

        .cal-att-head {
            padding: 22px 24px 18px;
            border-bottom: 1px solid var(--cal-border);
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
            flex-shrink: 0;
        }

        .cal-att-head-text {
            min-width: 0;
        }

        .cal-att-head-eyebrow {
            font-size: 11px;
            font-weight: 600;
            color: var(--cal-primary);
            margin: 0 0 4px 0;
        }

        .cal-att-head-title {
            font-size: 18px;
            font-weight: 700;
            color: var(--cal-accent-dark);
            margin: 0;
            line-height: 1.25;
            letter-spacing: -0.01em;
        }

        .cal-att-head-sub {
            font-size: 12px;
            color: var(--cal-text-muted);
            margin: 6px 0 0 0;
            line-height: 1.45;
        }

        .cal-att-close {
            width: 34px;
            height: 34px;
            border: 1px solid var(--cal-border);
            border-radius: 10px;
            background: var(--cal-surface);
            color: var(--cal-text-muted);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.15s;
            flex-shrink: 0;
            font-size: 13px;
        }

        .cal-att-close:hover {
            background: var(--cal-hover-bg);
            color: var(--cal-accent-dark);
            border-color: var(--cal-accent-dark);
        }

        .cal-att-body {
            flex: 1;
            overflow-y: auto;
            padding: 18px 24px 8px;
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .cal-att-body::-webkit-scrollbar { width: 6px; }
        .cal-att-body::-webkit-scrollbar-track { background: transparent; }
        .cal-att-body::-webkit-scrollbar-thumb { background: var(--cal-border); border-radius: 3px; }

        .cal-att-row {
            display: flex;
            flex-direction: column;
            gap: 6px;
            animation: cal-att-rowIn 0.18s ease-out;
        }

        @keyframes cal-att-rowIn {
            from { opacity: 0; transform: translateY(-4px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .cal-att-row-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .cal-att-row-num {
            font-size: 10px;
            font-weight: 600;
            color: var(--cal-text-muted);
        }

        .cal-att-row-remove {
            width: 26px;
            height: 26px;
            border: none;
            background: transparent;
            color: var(--cal-text-quiet);
            cursor: pointer;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.15s;
            font-size: 11px;
        }

        .cal-att-row-remove:hover {
            background: #fef2f2;
            color: var(--cal-holiday-text);
        }

        .cal-att-input {
            width: 100%;
            padding: 10px 12px;
            background: var(--cal-surface);
            border: 1.5px solid var(--cal-border);
            border-radius: 10px;
            font-family: 'Poppins', sans-serif;
            font-size: 13px;
            color: var(--cal-text);
            transition: border-color 0.15s, box-shadow 0.15s;
            outline: none;
        }

        .cal-att-input::placeholder {
            color: var(--cal-text-quiet);
            font-weight: 400;
        }

        .cal-att-input:hover:not(:focus) {
            border-color: var(--cal-border-strong);
        }

        .cal-att-input:focus {
            border-color: var(--cal-accent-dark);
            box-shadow: 0 0 0 3px rgba(0, 60, 117, 0.12);
        }

        .cal-att-input.is-invalid {
            border-color: var(--cal-holiday-text);
            background: #fef2f2;
        }

        .cal-att-input.is-invalid:focus {
            box-shadow: 0 0 0 3px rgba(185, 28, 28, 0.14);
        }

        .cal-att-row-error {
            margin: 6px 2px 0;
            font-size: 12px;
            font-weight: 500;
            color: var(--cal-holiday-text);
            font-family: 'Poppins', sans-serif;
            line-height: 1.4;
        }

        .cal-att-add {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 11px 12px;
            background: transparent;
            border: 1.5px dashed var(--cal-border-strong);
            border-radius: 10px;
            color: var(--cal-accent-dark);
            font-family: 'Poppins', sans-serif;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s;
            letter-spacing: 0.01em;
            margin-top: 2px;
        }

        .cal-att-add:hover:not(:disabled) {
            background: var(--cal-hover-bg);
            border-color: var(--cal-accent-dark);
            border-style: solid;
            color: var(--cal-accent-mid);
        }

        .cal-att-add:disabled {
            color: var(--cal-text-quiet);
            cursor: not-allowed;
            opacity: 0.65;
        }

        .cal-att-counter {
            font-size: 11px;
            color: var(--cal-text-muted);
            text-align: center;
            font-weight: 500;
            margin: 4px 0 8px 0;
            letter-spacing: 0.02em;
        }

        .cal-att-counter.is-over {
            color: var(--cal-holiday-text);
            font-weight: 600;
        }

        .cal-att-foot {
            padding: 14px 24px 18px;
            border-top: 1px solid var(--cal-border);
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            background: var(--cal-surface);
            flex-shrink: 0;
        }

        .cal-att-btn-cancel {
            padding: 10px 18px;
            background: var(--cal-surface);
            border: 1.5px solid var(--cal-border);
            border-radius: 10px;
            color: var(--cal-text-muted);
            font-family: 'Poppins', sans-serif;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s;
        }

        .cal-att-btn-cancel:hover {
            background: var(--cal-hover-bg);
            border-color: var(--cal-accent-dark);
            color: var(--cal-accent-dark);
        }

        .cal-att-btn-save {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            background: var(--cal-accent-dark);
            color: #ffffff;
            border: none;
            border-radius: 10px;
            font-family: 'Poppins', sans-serif;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s;
        }

        .cal-att-btn-save:hover {
            background: var(--cal-accent-mid);
            box-shadow: 0 6px 16px -4px rgba(0, 60, 117, 0.35);
            transform: translateY(-1px);
        }

        .cal-att-btn-save:disabled,
        .cal-att-btn-save:disabled:hover {
            background: var(--cal-accent-dark);
            opacity: 0.45;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        body.cal-att-drawer-open {
            overflow: hidden;
        }

        @media (max-width: 480px) {
            .cal-att-drawer {
                max-width: 100%;
            }
        }

        .existing-bookings {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border: 2px solid #0ea5e9;
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .booking-card {
            background: white;
            border-radius: 12px;
            padding: 0.5rem;
            margin-bottom: 1rem;
            border-left: 4px solid #6366f1;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .booking-card:last-child {
            margin-bottom: 0;
        }

        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .status-confirmed {
            background: #d1fae5;
            color: #065f46;
        }

        .status-new {
            background: #dbeafe;
            color: #1e40af;
        }

        /* Rest of existing styles remain the same */
        .calendar-days-header {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 2px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 16px 16px 0 0;
            overflow: hidden;
            margin-bottom: 2px;
        }

        .header-day {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            padding: 1rem 0.75rem;
            text-align: center;
            font-weight: 700;
            color: #374151;
            font-size: 0.875rem;
        }

        .modal-overlay {
            position: fixed;
            inset: 0;
            z-index: 50;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(4px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            animation: fadeIn 0.3s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .modal-container {
            background: white;
            border-radius: 20px;
            width: 100%;
            max-width: 48rem;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            animation: slideUp 0.3s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 2rem;
            color: white;
            border-radius: 20px 20px 0 0;
        }

        .modal-body {
            padding: 2rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.5rem;
        }

        .form-input,
        .form-select,
        .form-textarea {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 0.875rem;
            transition: all 0.2s;
            background: #fafafa;
        }

        .form-input:focus,
        .form-select:focus,
        .form-textarea:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            outline: none;
            background: white;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            transition: all 0.2s;
            font-size: 0.875rem;
            min-width: 200px; /* Add minimum width */
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
            min-height: 50px; /* Add minimum height for two lines */
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }

        .btn-secondary {
            background: #6b7280;
            color: white;
            min-height: 50px; /* Match height */
        }

        .btn-secondary:hover {
            background: #4b5563;
            transform: translateY(-1px);
        }

        /* Update modal footer to give more space */
        .modal-footer {
            padding: 1rem 2rem;
            border-top: 1px solid #e5e7eb;
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
            border-radius: 0 0 20px 20px;
            background: #f8fafc;
            flex-wrap: wrap; /* Allow wrapping on smaller screens */
        }

        .session-option {
            padding: 1.25rem;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s;
            margin-bottom: 0.75rem;
            background: #fafafa;
        }

        .session-option:hover {
            border-color: #667eea;
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            transform: translateY(-1px);
        }

        .session-option.selected {
            border-color: #667eea;
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.2);
        }

        .implementer-info {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border: 2px solid #3b82f6;
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .legend-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            padding: 1.5rem;
            margin-top: 1.5rem;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .nav-button {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            padding: 0.75rem;
            color: #374151;
            font-weight: 600;
            transition: all 0.2s;
        }

        .nav-button:hover {
            background: white;
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .month-title {
            color: white;
            font-weight: 700;
            font-size: 1.5rem;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .modal-container.max-w-2xl {
            max-width: 42rem;
        }

        .progress-step {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
        }

        .progress-step.active {
            background: #3b82f6;
            font-weight: 600;
        }

        .resource-download {
            transition: all 0.2s ease;
        }

        .resource-download:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .modal-container {
            background: white;
            border-radius: 20px;
            width: 100%;
            max-width: 48rem;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            animation: slideUp 0.3s ease-out;

            /* Hide scrollbar for webkit browsers (Chrome, Safari, Edge) */
            scrollbar-width: none; /* Firefox */
            -ms-overflow-style: none; /* Internet Explorer 10+ */
        }

        .modal-container::-webkit-scrollbar {
            display: none; /* Safari and Chrome */
        }

        /* Also hide scrollbar for modal body if needed */
        .modal-body {
            padding: 1.5rem 2rem 1rem 2rem;
            scrollbar-width: none; /* Firefox */
            -ms-overflow-style: none; /* Internet Explorer 10+ */
        }

        .modal-body::-webkit-scrollbar {
            display: none; /* Safari and Chrome */
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none !important;
        }

        .btn:disabled:hover {
            transform: none !important;
            box-shadow: none !important;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .animate-spin {
            animation: spin 1s linear infinite;
        }

        .cancel-button {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-size: 0.75rem;
            font-weight: 600;
            cursor: pointer;
            border: none;
            transition: all 0.2s;
            text-align: center;
            min-width: 80px;
        }

        .cancel-button:hover {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3);
        }

        .cancel-button:disabled {
            background: #9ca3af;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .cancel-reason {
            font-size: 0.65rem;
            color: #6b7280;
            font-style: italic;
            margin-top: 0.25rem;
        }

        .modal-header {
            position: relative; /* Add this to enable absolute positioning for close button */
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 2rem;
            color: white;
            border-radius: 20px 20px 0 0;
        }

        /* Style for the close button */
        .modal-close-btn {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: rgba(255, 255, 255, 0.2);
            border: none;
            border-radius: 50%;
            width: 2.5rem;
            height: 2.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
            backdrop-filter: blur(10px);
        }

        .modal-close-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: scale(1.1);
        }

        .session-option {
            padding: 1rem 0.75rem; /* Reduce horizontal padding for narrower boxes */
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s;
            margin-bottom: 0; /* Remove bottom margin since we're using grid gap */
            background: #fafafa;
            min-height: 80px; /* Ensure consistent height */
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .session-option:hover {
            border-color: #667eea;
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.2);
        }

        .session-option.selected {
            border-color: #667eea;
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
            transform: translateY(-2px);
        }

        /* Responsive adjustments for smaller screens */
        @media (max-width: 768px) {
            .session-grid {
                grid-template-columns: 1fr; /* Stack vertically on mobile */
                gap: 1rem;
            }

            .session-option {
                padding: 1.25rem; /* Restore padding on mobile */
            }
        }

        @media (min-width: 769px) and (max-width: 1024px) {
            .session-grid {
                grid-template-columns: repeat(2, 1fr); /* 2 columns on tablets */
            }
        }

        .existing-bookings {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border: 2px solid #0ea5e9;
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
        }

        .bookings-header {
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: between;
            margin-bottom: 0.5rem;
            padding: 0.5rem 0;
            border-radius: 8px;
            transition: all 0.2s ease;
        }

        .bookings-header:hover {
            background: rgba(59, 130, 246, 0.05);
            padding: 0.5rem;
        }

        .collapse-icon {
            transition: transform 0.1s ease;
            width: 1.5rem;
            height: 1.5rem;
            color: #3b82f6;
        }

        .collapse-icon.rotated {
            transform: rotate(180deg);
        }

        .bookings-list {
            transition: all 0.2s ease-in-out;
            overflow: hidden;
        }

        .bookings-list.collapsed {
            max-height: 0;
            opacity: 0;
            margin: 0;
        }

        .bookings-list.expanded {
            max-height: 1000px;
            opacity: 1;
        }

        .meeting-indicator.completed {
            background: rgba(34, 197, 94, 0.1);
            color: #22c55e;
            border: 1px solid #22c55e;
        }

        .modal-close-btn {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: rgba(255, 255, 255, 0.2);
            border: none;
            border-radius: 50%;
            width: 2.5rem;
            height: 2.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
            backdrop-filter: blur(10px);
            color: white;
        }

        .modal-close-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: scale(1.1);
        }

        .help-button {
            animation: helpButtonPulse 3s ease-in-out infinite;
        }

        @keyframes helpButtonPulse {
            0%, 100% {
                box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.4);
            }
            50% {
                box-shadow: 0 0 0 10px rgba(59, 130, 246, 0);
            }
        }

        .tutorial-step-indicator {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .tutorial-step-indicator.active {
            transform: scale(1.2);
        }

        .tutorial-modal {
            position: absolute;
            inset: 0;
            z-index: 5;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(0, 60, 117, 0.55);
            backdrop-filter: blur(6px);
            border-radius: 16px;
            padding: 1rem;
            animation: tutorialFade 200ms ease-out;
        }

        @keyframes tutorialFade {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .tutorial-container {
            background: white;
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            width: 100%;
            max-width: 700px;
            max-height: 100%;
            margin: 0;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            transform: scale(1);
            transition: all 0.3s ease;
        }

        .tutorial-header {
            position: relative;
            padding: 2rem 2rem 1.5rem 2rem; /* Reduced padding */
            color: white;
            background: linear-gradient(135deg, #003c75 0%, #1a6dd4 55%, #00a4e0 100%);
        }

        .tutorial-header-pattern {
            position: absolute;
            inset: 0;
            opacity: 0.1;
        }

        .tutorial-header-pattern::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 160px;
            height: 160px;
            border-radius: 50%;
            background: linear-gradient(to right, white, transparent);
            transform: translate(-80px, -80px);
        }

        .tutorial-header-pattern::after {
            content: '';
            position: absolute;
            bottom: 0;
            right: 0;
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(to right, white, transparent);
            transform: translate(60px, 60px);
        }

        .tutorial-header-content {
            position: relative;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
        }

        .tutorial-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 60px;
            height: 60px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 16px;
            backdrop-filter: blur(10px);
            margin-right: 1rem;
        }

        .tutorial-title {
            font-family: 'Poppins', sans-serif;
            font-size: 1.5rem; /* Reduced from 3xl */
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 0.5rem;
            letter-spacing: -0.01em;
        }

        .tutorial-step-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-top: 0.5rem;
        }

        .tutorial-step-badge {
            font-family: 'Poppins', sans-serif;
            padding: 0.25rem 0.75rem;
            font-size: 0.75rem;
            font-weight: 500;
            background: rgba(255, 255, 255, 0.18);
            border: 1px solid rgba(255, 255, 255, 0.28);
            border-radius: 20px;
            backdrop-filter: blur(10px);
        }

        .tutorial-progress-dots {
            display: flex;
            gap: 0.25rem;
        }

        .tutorial-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.35);
        }

        .tutorial-dot.active {
            background: white;
        }

        .tutorial-close-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            color: white;
            background: rgba(255, 255, 255, 0.2);
            border: none;
            border-radius: 12px;
            backdrop-filter: blur(10px);
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .tutorial-close-btn:hover {
            background: rgba(255, 255, 255, 0.28);
            transform: scale(1.06);
        }

        .tutorial-progress-bar {
            position: relative;
            margin-top: 1rem;
        }

        .tutorial-progress-bg {
            width: 100%;
            height: 5px;
            background: rgba(255, 255, 255, 0.22);
            border-radius: 20px;
            backdrop-filter: blur(10px);
        }

        .tutorial-progress-fill {
            height: 5px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 0 12px rgba(255, 255, 255, 0.4);
            transition: all 0.7s ease-out;
        }

        .tutorial-body {
            padding: 1.5rem 2rem;
            background: #ffffff;
            text-align: center;
            overflow-y: auto;
            flex: 1;
            min-height: 0;
        }

        .tutorial-emoji {
            font-size: 2rem; /* Reduced from 8xl */
        }

        .tutorial-emoji.bounce {
            animation: bounce 2s infinite;
        }

        .tutorial-step-title {
            font-family: 'Poppins', sans-serif;
            font-size: 1.25rem; /* Reduced from 3xl */
            font-weight: 700;
            margin-bottom: 1rem;
            color: #003c75;
            letter-spacing: -0.01em;
        }

        .tutorial-step-description {
            font-family: 'Poppins', sans-serif;
            font-weight: 400;
            font-size: 1rem; /* Reduced from xl */
            color: #4b5563;
            line-height: 1.6;
            max-width: 500px;
            margin-left: auto;
            margin-right: auto;
            margin-bottom: 1rem;
        }

        .tutorial-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 0.5rem;
        }

        .tutorial-card {
            padding: 0.5rem 1rem 0.5rem 1rem;;
            border: 2px solid;
            border-radius: 16px;
            transition: all 0.3s ease;
        }

        .tutorial-card:hover {
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .tutorial-card.blue {
            background: #eff6ff;
            border-color: #003c75;
        }

        .tutorial-card.purple {
            background: #ecfeff;
            border-color: #00a4e0;
        }

        .tutorial-card.green {
            background: #ecfdf5;
            border-color: #10b981;
        }

        .tutorial-tip {
            padding: 1rem;
            margin-top: 1rem;
            border-left: 4px solid #10b981;
            border-radius: 8px;
            background: #ecfdf5;
        }

        .tutorial-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem 2rem; /* Reduced padding */
            border-top: 1px solid #e5e7eb;
            background: #ffffff;
        }

        .tutorial-btn {
            display: flex;
            align-items: center;
            font-family: 'Poppins', sans-serif;
            padding: 0.75rem 1.5rem;
            border: 2px solid;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            font-size: 0.875rem;
        }

        .tutorial-btn.secondary {
            color: #6b7280;
            background: white;
            border-color: #d1d5db;
        }

        .tutorial-btn.secondary:hover {
            background: #f0f7ff;
            color: #003c75;
            border-color: #003c75;
            box-shadow: 0 4px 6px -1px rgba(0, 60, 117, 0.1);
        }

        .tutorial-btn.primary {
            color: white;
            background: #003c75;
            border-color: transparent;
            box-shadow: 0 4px 15px rgba(0, 60, 117, 0.25);
        }

        .tutorial-btn.primary:hover {
            background: #1a6dd4;
            box-shadow: 0 8px 20px -6px rgba(0, 60, 117, 0.35);
            transform: translateY(-1px);
        }

        .tutorial-btn.success {
            color: white;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border-color: transparent;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
        }

        .tutorial-btn.success:hover {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4);
            transform: translateY(-1px);
        }

        .tutorial-btn svg {
            width: 1rem;
            height: 1rem;
            transition: transform 0.2s ease;
        }

        .tutorial-btn:hover svg.arrow-right {
            transform: translateX(2px);
        }

        .tutorial-btn:hover svg.arrow-left {
            transform: translateX(-2px);
        }

        /* Responsive adjustments */
        @media (max-width: 640px) {
            .tutorial-container {
                max-width: 95%;
                margin: 0.5rem;
            }

            .tutorial-header {
                padding: 1.5rem 1rem 1rem 1rem;
            }

            .tutorial-body {
                padding: 1rem;
            }

            .tutorial-footer {
                padding: 1rem;
                flex-direction: column;
                gap: 0.5rem;
            }

            .tutorial-emoji {
                font-size: 3rem;
            }

            .tutorial-step-title {
                font-size: 1.25rem;
            }

            .tutorial-step-description {
                font-size: 0.875rem;
            }
        }

        @keyframes bounce {
            0%, 20%, 53%, 80%, 100% {
                transform: translate3d(0,0,0);
            }
            40%, 43% {
                transform: translate3d(0,-10px,0);
            }
            70% {
                transform: translate3d(0,-5px,0);
            }
            90% {
                transform: translate3d(0,-2px,0);
            }
        }

        .help-button-wrapper {
            position: relative;
            display: inline-block;
        }

        .help-tooltip {
            position: absolute;
            bottom: 120%;
            right: 0;
            background: rgba(0, 0, 0, 0.9);
            color: white;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 500;
            white-space: nowrap;
            opacity: 0;
            visibility: hidden;
            transform: translateY(10px);
            transition: all 0.3s ease;
            z-index: 60;
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);

            /* Auto-show animation every 10 seconds */
            animation: tooltipAutoShow 5s infinite;
        }

        .help-tooltip::after {
            content: '';
            position: absolute;
            top: 100%;
            right: 1rem;
            border: 6px solid transparent;
            border-top-color: rgba(0, 0, 0, 0.9);
        }

        /* Manual hover still works */
        .help-button-wrapper:hover .help-tooltip {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
            animation: none; /* Pause auto-animation on hover */
        }

        /* Auto-show animation keyframes */
        @keyframes tooltipAutoShow {
            0%, 85% {
                opacity: 0;
                visibility: hidden;
                transform: translateY(10px);
            }
            90%, 95% {
                opacity: 1;
                visibility: visible;
                transform: translateY(0);
            }
            100% {
                opacity: 0;
                visibility: hidden;
                transform: translateY(10px);
            }
        }

        /* Enhanced help button pulse when tooltip shows */
        .help-button {
            animation: helpButtonPulse 10s infinite;
        }

        @keyframes helpButtonPulse {
            0%, 85% {
                box-shadow: 0 0 0 0 rgba(0, 60, 117, 0.4);
            }
            90%, 95% {
                box-shadow: 0 0 0 10px rgba(0, 60, 117, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(0, 60, 117, 0.4);
            }
        }

        /* Pause animations when tutorial modal is open */
        .tutorial-modal ~ .help-button-wrapper .help-tooltip,
        .tutorial-modal ~ .help-button-wrapper .help-button {
            animation-play-state: paused;
        }

        @media (max-width: 640px) {
            .help-tooltip {
                bottom: 110%;
                right: -50px;
                left: -50px;
                text-align: center;
                white-space: normal;
            }

            .help-tooltip::after {
                right: 50%;
                transform: translateX(50%);
            }
        }

        /* ──────────────────────────────────────────────────────────────
           One-page layout: pin the page to the viewport so it never
           scrolls at the page level on ≥900px laptops.
        ────────────────────────────────────────────────────────────── */
        .calendar-container.cal-page-shell {
            height: calc(100vh - 112px);
            min-height: 560px;
            display: flex;
            flex-direction: column;
            padding: 1rem 1.25rem 1.1rem;
            border-radius: 16px;
            position: relative;
            overflow: hidden;
        }

        .cal-header {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 12px 18px;
            padding-bottom: 10px;
            margin-bottom: 12px;
            border-bottom: 1px solid var(--cal-border);
        }
        .cal-header .cal-title-icon { width: 36px; height: 36px; border-radius: 10px; font-size: 16px; }
        .cal-header .cal-title-text h2 { font-size: 1.0625rem; line-height: 1.2; }
        .cal-header .cal-title-text p  { font-size: 0.78rem; line-height: 1.25; margin-top: 2px; }

        .cal-header-right {
            display: flex;
            align-items: center;
            gap: 14px;
            flex-wrap: wrap;
        }

        .cal-bookings-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 12px;
            border-radius: 999px;
            background: var(--cal-meeting-bg);
            color: var(--cal-meeting-text);
            border: 1px solid #cfdcef;
            font-family: 'Poppins', sans-serif;
            font-size: 12.5px;
            font-weight: 600;
            cursor: pointer;
            transition: background .15s, border-color .15s;
        }
        .cal-bookings-pill:hover { background: #e0ecfb; border-color: #b6c8e1; }
        .cal-bookings-pill .cnt { font-weight: 700; }
        .cal-bookings-pill .chev { transition: transform .2s; font-size: 10px; }
        .cal-bookings-pill[aria-expanded="true"] .chev { transform: rotate(180deg); }

        .cal-legend-inline {
            display: inline-flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 4px 10px;
            font-size: 11.5px;
            color: var(--cal-text-muted);
        }
        .cal-legend-inline .cal-legend-chip {
            padding: 2px 8px 2px 6px;
            font-size: 11.5px;
            gap: 5px;
            background: var(--cal-surface);
            font-weight: 500;
        }

        /* Bottom-anchored legend strip under the calendar grid */
        .cal-page-shell .cal-legend-foot {
            flex-shrink: 0;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: center;
            gap: 6px 12px;
            margin-top: 8px;
            padding-top: 10px;
            border-top: 1px dashed var(--cal-border);
            font-size: 11.5px;
            color: var(--cal-text-muted);
        }
        .cal-page-shell .cal-legend-foot .cal-legend-chip {
            padding: 2px 8px 2px 6px;
            font-size: 11.5px;
            gap: 5px;
            background: var(--cal-surface);
            font-weight: 500;
        }

        /* Make the split fill the remaining shell height and stretch both columns */
        .cal-page-shell .cal-split {
            flex: 1;
            min-height: 0;
            grid-template-columns: minmax(0, 1fr) 340px;
            gap: 1.25rem;
            align-items: stretch;
        }
        @media (max-width: 1200px) {
            .cal-page-shell .cal-split {
                grid-template-columns: minmax(0, 1fr) 304px;
                gap: 1rem;
            }
        }
        @media (max-width: 960px) {
            .calendar-container.cal-page-shell { height: auto; min-height: 0; }
            .cal-page-shell .cal-split { grid-template-columns: 1fr; }
        }

        .cal-page-shell .cal-left {
            display: flex;
            flex-direction: column;
            min-height: 0;
        }

        /* Tighter days header + flex-filling grid */
        .cal-page-shell .calendar-days-header {
            padding: 6px 0 8px;
            grid-template-columns: repeat(7, 1fr);
            display: grid;
            gap: 6px;
        }
        .cal-page-shell .calendar-days-header .header-day {
            font-size: 11px;
            color: var(--cal-text-quiet);
            font-weight: 700;
            text-align: left;
            padding: 0 4px;
        }

        .cal-page-shell .calendar-grid {
            flex: 1;
            min-height: 0;
            grid-auto-rows: 1fr;
            gap: 5px;
        }

        .cal-page-shell .calendar-day {
            min-height: 0;
            padding: 6px 8px 6px;
            border-radius: 9px;
        }
        .cal-page-shell .calendar-day .day-number { font-size: 13px; }
        .cal-page-shell .calendar-day .cal-day-icon { font-size: 11px; top: 6px; right: 7px; }
        .cal-page-shell .calendar-day .available-count,
        .cal-page-shell .calendar-day .meeting-indicator,
        .cal-page-shell .calendar-day .cal-full-marker {
            font-size: 10.5px;
            padding: 1px 6px;
        }

        /* Right rail: stretch to full split height, scroll internally if overflowed */
        .cal-page-shell .cal-book-panel {
            position: relative;
            top: 0;
            height: 100%;
            max-height: 100%;
            overflow-y: auto;
            padding: 1rem 1.1rem;
            gap: 0.85rem;
            justify-content: flex-start;
        }

        .cal-page-shell .cal-book-panel > .cal-panel-empty {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
            gap: 0.5rem;
        }

        .cal-page-shell .cal-book-panel > .cal-panel-empty .cal-panel-empty-icon {
            width: 64px;
            height: 64px;
            font-size: 26px;
            margin-bottom: 0.5rem;
        }

        .cal-page-shell .cal-book-panel > .cal-panel-footnote {
            margin-top: auto;
        }
        .cal-page-shell .cal-confirm-row {
            margin-top: auto;
            padding-top: 12px;
            display: flex;
            flex-direction: column;
            gap: 0.65rem;
        }
        .cal-page-shell .cal-confirm-row .cal-panel-footnote { margin-top: 0; }

        /* Slide-over drawer for the "N scheduled" pill */
        .cal-bookings-drawer-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, .35);
            z-index: 80;
            opacity: 0;
            pointer-events: none;
            transition: opacity .2s ease;
        }
        .cal-bookings-drawer-backdrop.open { opacity: 1; pointer-events: auto; }
        .cal-bookings-drawer {
            position: fixed;
            top: 0; right: 0; bottom: 0;
            width: min(440px, 92vw);
            background: var(--cal-surface, #ffffff);
            z-index: 90;
            transform: translateX(100%);
            transition: transform .25s cubic-bezier(.4,0,.2,1);
            display: flex;
            flex-direction: column;
            box-shadow: -16px 0 32px -16px rgba(16,24,40,.18);
        }
        .cal-bookings-drawer.open { transform: translateX(0); }
        .cal-bookings-drawer-head {
            padding: 18px 20px;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-shrink: 0;
        }
        .cal-bookings-drawer-head h4 {
            font-family: 'Poppins', sans-serif;
            font-size: 1rem;
            font-weight: 600;
            color: #111827;
            margin: 0;
        }
        .cal-bookings-drawer-head h4 .count {
            font-size: 0.85rem;
            font-weight: 400;
            color: #6b7280;
            margin-left: 4px;
        }
        .cal-bookings-drawer-close {
            background: transparent;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6b7280;
            cursor: pointer;
            transition: all .15s;
        }
        .cal-bookings-drawer-close:hover { color: #111827; border-color: #d1d5db; }
        .cal-bookings-drawer-body {
            padding: 16px 20px 20px;
            overflow-y: auto;
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .cal-bookings-drawer-body .booking-card { margin: 0; }

        /* Collapse the in-page existing-bookings markup if it ever renders inside the shell */
        .cal-page-shell .existing-bookings { display: none; }

        /* ──────────────────────────────────────────────────────────────
           Refined booking-card for slide-over drawer (~360 px interior)
           Scoped so the legacy .booking-card rule (line 1188) is untouched.
        ────────────────────────────────────────────────────────────── */
        .cal-bookings-drawer-body .booking-card {
            margin: 0;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-left: 3px solid #cbd5e1;
            border-radius: 12px;
            padding: 13px 14px 12px;
            box-shadow: 0 1px 2px rgba(15, 23, 42, .035);
            transition: box-shadow .18s ease, transform .18s ease, border-color .18s;
            display: flex;
            flex-direction: column;
            gap: 6px;
            font-family: 'Poppins', sans-serif;
        }
        .cal-bookings-drawer-body .booking-card:hover {
            box-shadow: 0 8px 20px -10px rgba(15, 23, 42, .14);
            transform: translateY(-1px);
        }
        .cal-bookings-drawer-body .booking-card--done       { border-left-color: #10b981; }
        .cal-bookings-drawer-body .booking-card--upcoming   { border-left-color: #003c75; }
        .cal-bookings-drawer-body .booking-card--locked     { border-left-color: #94a3b8; }
        .cal-bookings-drawer-body .booking-card--cancelled  { border-left-color: #dc2626; opacity: .85; }

        .cal-bookings-drawer-body .bk-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 10px;
        }
        .cal-bookings-drawer-body .bk-date {
            margin: 0;
            font-size: 13.5px;
            font-weight: 600;
            color: #0f172a;
            line-height: 1.3;
            letter-spacing: -0.005em;
        }
        .cal-bookings-drawer-body .bk-pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 3px 9px;
            border-radius: 999px;
            font-size: 10.5px;
            font-weight: 600;
            line-height: 1;
            white-space: nowrap;
            flex-shrink: 0;
            letter-spacing: 0.01em;
        }
        .cal-bookings-drawer-body .bk-pill i { font-size: 9px; }
        .cal-bookings-drawer-body .bk-pill--done      { background: #ecfdf5; color: #047857; }
        .cal-bookings-drawer-body .bk-pill--upcoming  { background: #eff6ff; color: #003c75; }
        .cal-bookings-drawer-body .bk-pill--locked    { background: #f1f5f9; color: #475569; }
        .cal-bookings-drawer-body .bk-pill--cancelled { background: #fef2f2; color: #b91c1c; }

        .cal-bookings-drawer-body .bk-time {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 6px;
            margin: 0;
            font-size: 13.5px;
            font-weight: 600;
            color: #111827;
            line-height: 1.35;
        }
        .cal-bookings-drawer-body .bk-time .bk-clock { color: #94a3b8; font-size: 11.5px; }
        .cal-bookings-drawer-body .bk-time .bk-sep   { color: #cbd5e1; font-weight: 400; }
        .cal-bookings-drawer-body .bk-time .bk-session {
            color: #475569;
            font-weight: 500;
            font-size: 12.5px;
        }

        .cal-bookings-drawer-body .bk-type {
            margin: 0;
            font-size: 10.5px;
            font-weight: 600;
            color: #94a3b8;
        }

        .cal-bookings-drawer-body .bk-actions {
            margin-top: 4px;
            padding-top: 9px;
            border-top: 1px dashed #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: flex-end;
        }
        .cal-bookings-drawer-body .bk-cancel-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 10px;
            border: 1px solid transparent;
            border-radius: 7px;
            background: transparent;
            color: #dc2626;
            font-family: 'Poppins', sans-serif;
            font-size: 11.5px;
            font-weight: 600;
            cursor: pointer;
            transition: background .15s, border-color .15s, color .15s;
        }
        .cal-bookings-drawer-body .bk-cancel-btn:hover {
            background: #fef2f2;
            border-color: #fecaca;
            color: #b91c1c;
        }
        .cal-bookings-drawer-body .bk-cancel-btn i { font-size: 11px; }
        .cal-bookings-drawer-body .bk-note {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 11px;
            font-style: italic;
            color: #94a3b8;
        }
        .cal-bookings-drawer-body .bk-note i { font-size: 9px; color: #cbd5e1; }

        /* Staggered entrance when the drawer opens */
        @keyframes bk-card-in {
            from { opacity: 0; transform: translateX(10px); }
            to   { opacity: 1; transform: translateX(0); }
        }
        .cal-bookings-drawer.open .booking-card {
            animation: bk-card-in .32s ease both;
        }
        .cal-bookings-drawer.open .booking-card:nth-child(2) { animation-delay: 60ms; }
        .cal-bookings-drawer.open .booking-card:nth-child(3) { animation-delay: 120ms; }
        .cal-bookings-drawer.open .booking-card:nth-child(4) { animation-delay: 180ms; }
        .cal-bookings-drawer.open .booking-card:nth-child(n+5) { animation-delay: 220ms; }
    </style>

    @php
        $customer = auth()->guard('customer')->user();
        $hasNewAppointment = \App\Models\ImplementerAppointment::where('lead_id', $customer->lead_id)
            ->where('status', 'New')
            ->whereIn('type', ['KICK OFF MEETING SESSION', 'REVIEW SESSION'])
            ->exists();
    @endphp

    {{-- @if(!$canScheduleMeeting)
        <div class="p-4 mb-4 border rounded-lg border-amber-200 bg-amber-50">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-3 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
                <div>
                    @if($hasNewKickOffMeeting)
                        <h4 class="font-semibold text-amber-800">Pending Appointment</h4>
                        <p class="text-sm text-amber-700">You have a pending kick-off meeting. Please wait for it to be completed before scheduling another one.</p>
                    @else
                        <h4 class="font-semibold text-amber-800">Meeting Scheduling Disabled</h4>
                        <p class="text-sm text-amber-700">Please contact your sales representative or support team to enable meeting scheduling for your account.</p>
                    @endif
                </div>
            </div>
        </div>
    @endif --}}

    <div class="calendar-container cal-page-shell">
        <!-- Compact header: title + scheduled pill + month nav + inline legend -->
        <div class="cal-header">
            <div class="cal-title-wrap">
                <div class="cal-title-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="cal-title-text">
                    <h2>{{ $this->getSessionTitle() }}</h2>
                    <p>Pick a date — available time slots appear on the right.</p>
                </div>
            </div>

            <div class="cal-header-right">
                @if($hasExistingBooking)
                    <button type="button"
                            class="cal-bookings-pill"
                            wire:click="toggleExistingBookings"
                            aria-expanded="{{ $showExistingBookings ? 'true' : 'false' }}"
                            aria-controls="cal-bookings-drawer"
                            title="View your scheduled meetings">
                        <i class="fas fa-calendar-check"></i>
                        <span><span class="cnt">{{ count($existingBookings) }}</span>
                            scheduled</span>
                        <i class="fas fa-chevron-down chev"></i>
                    </button>
                @endif

                <div class="cal-month-nav">
                    <button wire:click="previousMonth" class="nav-button" aria-label="Previous month">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </button>
                    <span class="cal-month-label">{{ $currentDate->format('F Y') }}</span>
                    <button wire:click="nextMonth" class="nav-button" aria-label="Next month">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                </div>

            </div>
        </div>

        <!-- ─── Split Canvas ─────────────────────────────────────────── -->
        <div class="cal-split">

            <!-- LEFT: Calendar grid (legend moved into header) -->
            <div class="cal-left">

                <!-- Calendar Days Header -->
                <div class="calendar-days-header">
                    <div class="header-day">Monday</div>
                    <div class="header-day">Tuesday</div>
                    <div class="header-day">Wednesday</div>
                    <div class="header-day">Thursday</div>
                    <div class="header-day">Friday</div>
                    <div class="header-day">Saturday</div>
                    <div class="header-day">Sunday</div>
                </div>

                <!-- Calendar Grid -->
                <div class="calendar-grid">
                    @foreach($monthlyData as $dayData)
                        @php
                            $isSelected = $selectedDate === $dayData['dateString'];
                            $todaysMeeting = null;
                            if ($dayData['hasCustomerMeeting']) {
                                $todaysMeeting = collect($existingBookings)->first(function ($booking) use ($dayData) {
                                    return Carbon::parse($booking['date'])->format('Y-m-d') === $dayData['dateString'];
                                });
                            }
                        @endphp
                        <div class="calendar-day
                            {{ !$dayData['isCurrentMonth'] ? 'other-month' : '' }}
                            {{ $dayData['isToday'] ? 'today' : '' }}
                            {{ $dayData['isPast'] ? 'past' : '' }}
                            {{ $dayData['isWeekend'] ? 'weekend' : '' }}
                            {{ $dayData['isPublicHoliday'] ? 'holiday' : '' }}
                            {{ $dayData['hasCustomerMeeting'] ? 'has-meeting' : '' }}
                            {{ $dayData['canBook'] ? 'bookable' : '' }}
                            {{ $isSelected ? 'selected' : '' }}
                            {{ !$canScheduleMeeting && !$dayData['hasCustomerMeeting'] ? 'disabled' : '' }}
                            {{ $dayData['isBeyondBookingWindow'] ? 'disabled' : '' }}"
                            @if($dayData['canBook'])
                                wire:click="selectDateInline('{{ $dayData['dateString'] }}')"
                            @elseif($dayData['hasCustomerMeeting'] && $todaysMeeting)
                                wire:click="openMeetingDetailsModal({{ $todaysMeeting['id'] }})"
                            @endif>

                            <div class="day-number">{{ $dayData['day'] }}</div>

                            {{-- Corner state pictogram --}}
                            @if($dayData['hasCustomerMeeting'] && $todaysMeeting)
                                @if($todaysMeeting['status'] === 'Done')
                                    <span class="cal-day-icon"><i class="fas fa-circle-check"></i></span>
                                @else
                                    <span class="cal-day-icon"><i class="fas fa-video"></i></span>
                                @endif
                            @elseif($dayData['isPublicHoliday'] && $dayData['isCurrentMonth'])
                                <span class="cal-day-icon"><i class="fas fa-flag"></i></span>
                            @elseif($dayData['isWeekend'] && $dayData['isCurrentMonth'])
                                <span class="cal-day-icon"><i class="fas fa-umbrella-beach"></i></span>
                            @elseif($dayData['canBook'])
                                <span class="cal-day-icon"><i class="fas fa-bolt"></i></span>
                            @endif

                            {{-- Bottom status badge --}}
                            @if($dayData['hasCustomerMeeting'] && $todaysMeeting)
                                @if($todaysMeeting['status'] === 'Done')
                                    <div class="meeting-indicator completed"><i class="fas fa-check"></i>Done</div>
                                @else
                                    <div class="meeting-indicator"><i class="fas fa-calendar-check"></i>Your slot</div>
                                @endif
                            @elseif($dayData['canBook'])
                                <div class="available-count"><i class="fas fa-star"></i>{{ $dayData['availableCount'] }} Open</div>
                            @elseif($dayData['isCurrentMonth'] && !$dayData['isPast'] && !$dayData['isWeekend'] && !$dayData['isPublicHoliday'] && !$dayData['isBeyondBookingWindow'] && $canScheduleMeeting)
                                <div class="cal-full-marker"><i class="fas fa-lock"></i>Not Available</div>
                            @endif
                        </div>
                    @endforeach
                </div>

                {{-- Bottom-anchored legend; flex-shrink:0 keeps the grid sized via flex:1 above --}}
                <div class="cal-legend-foot" aria-label="Calendar key">
                    <span class="cal-legend-chip"><span class="cal-legend-dot avail"></span>Available</span>
                    <span class="cal-legend-chip"><span class="cal-legend-dot meet"></span>Your meeting</span>
                    <span class="cal-legend-chip"><span class="cal-legend-dot wknd"></span>Weekend</span>
                    <span class="cal-legend-chip"><span class="cal-legend-dot hol"></span>Holiday</span>
                    <span class="cal-legend-chip"><span class="cal-legend-dot full"></span>Not Available</span>
                </div>
            </div>

            <!-- RIGHT: Booking panel -->
            <aside class="cal-book-panel">

                @if(!$canScheduleMeeting)
                    {{-- Scheduling-disabled state --}}
                    <div class="cal-panel-empty">
                        <div class="cal-panel-empty-icon" style="background:#fef3c7;color:#b45309;">
                            <i class="fas fa-lock"></i>
                        </div>
                        <p class="cal-panel-empty-title">Booking unavailable</p>
                        <p class="cal-panel-empty-body">
                            You have a pending or completed meeting request. Please wait for it to be resolved before scheduling another one.
                        </p>
                    </div>

                @elseif(!$selectedDate)
                    {{-- Idle state --}}
                    <div class="cal-panel-empty">
                        <div class="cal-panel-empty-icon">
                            <i class="fas fa-hand-pointer"></i>
                        </div>
                        <p class="cal-panel-empty-title">Pick an available date</p>
                        <button type="button"
                                wire:click="showTutorialModal"
                                class="cal-tutorial-link"
                                aria-label="View calendar tutorial">
                            <i class="fas fa-circle-question cal-tutorial-link-icon"></i>
                            <span>View Tutorial</span>
                        </button>
                    </div>

                @else
                    {{-- Date-selected state --}}
                    <div class="cal-panel-head">
                        <div>
                            <p class="cal-panel-eyebrow">Selected Date</p>
                            <p class="cal-panel-date">{{ Carbon::parse($selectedDate)->format('l') }}</p>
                            <p class="cal-panel-sub">{{ Carbon::parse($selectedDate)->format('F j, Y') }} · {{ count($availableSessions) }} {{ count($availableSessions) === 1 ? 'slot' : 'slots' }}</p>
                        </div>
                        <button type="button" class="cal-panel-clear" wire:click="clearSelectedDate" title="Clear selection">
                            <i class="fas fa-xmark"></i>
                        </button>
                    </div>

                    @if(count($availableSessions) > 0)
                        {{-- Time slot selection --}}
                        <div class="cal-field-block">
                            <p class="cal-field-label">Time slot <span class="cal-field-required">*</span></p>
                            <div class="cal-slot-grid">
                                @foreach($availableSessions as $index => $session)
                                    @php $isSlotSelected = $selectedSession && $selectedSession['start_time'] === $session['start_time']; @endphp
                                    <button type="button"
                                            class="cal-slot {{ $isSlotSelected ? 'is-selected' : '' }}"
                                            wire:click="selectSession({{ $index }})">
                                        <span class="cal-slot-icon">
                                            <i class="far fa-clock"></i>
                                        </span>
                                        <span class="cal-slot-info">
                                            <span class="cal-slot-time-range">{{ $session['formatted_start'] }} – {{ $session['formatted_end'] }}</span>
                                            <span class="cal-slot-session-label">{{ $session['session_name'] }}</span>
                                        </span>
                                        <i class="cal-slot-arrow fas fa-arrow-right"></i>
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        {{-- Meeting type (read-only) --}}
                        <div class="cal-field-block">
                            <p class="cal-field-label">Meeting type</p>
                            <div class="cal-field-static">
                                <i class="fas fa-video"></i>
                                <span>Online Meeting via Microsoft Teams</span>
                            </div>
                        </div>

                        {{-- Required attendees --}}
                        <div class="cal-field-block">
                            <div class="cal-field-label-row">
                                <label for="cal-attendees-input" class="cal-field-label">
                                    Required Attendees <span class="cal-field-required">*</span>
                                </label>
                                <button type="button"
                                        class="cal-att-btn-inline"
                                        onclick="window.dispatchEvent(new CustomEvent('cal-open-attendees'))"
                                        title="Open bulk editor">
                                    <i class="fas fa-users"></i>
                                    <span>Bulk Edit</span>
                                </button>
                            </div>
                            <input type="text"
                                   id="cal-attendees-input"
                                   class="cal-field-input cal-field-input--locked"
                                   wire:model="requiredAttendees"
                                   readonly
                                   title="Use Bulk Edit to change attendees"
                                   onclick="window.dispatchEvent(new CustomEvent('cal-open-attendees'))"
                                   placeholder="Click Bulk Edit to add attendees">
                        </div>

                        <div class="cal-confirm-row">
                            @if($sessionValidationError)
                                <div class="cal-panel-error">{{ $sessionValidationError }}</div>
                            @endif

                            <button type="button"
                                    class="cal-continue-btn"
                                    wire:click="submitBooking"
                                    wire:loading.attr="disabled"
                                    wire:target="submitBooking"
                                    @if(!$selectedSession || empty(trim($requiredAttendees))) disabled @endif>
                                <span wire:loading.remove wire:target="submitBooking" class="cal-submit-loading">
                                    @if(!$selectedSession)
                                        Select a time slot
                                    @elseif(empty(trim($requiredAttendees)))
                                        Add attendees first
                                    @else
                                        <i class="fas fa-paper-plane"></i>
                                        Confirm booking
                                    @endif
                                </span>
                                <span wire:loading wire:target="submitBooking" class="cal-submit-loading">
                                    <svg class="cal-spinner" viewBox="0 0 24 24" fill="none">
                                        <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-dasharray="42 28"/>
                                    </svg>
                                    Submitting…
                                </span>
                            </button>

                            <p class="cal-panel-footnote">
                                <i class="fas fa-envelope"></i>
                                A Microsoft Teams invitation will be emailed to all attendees after booking.
                            </p>
                        </div>
                    @else
                        <div class="cal-panel-empty" style="padding:1rem 0.5rem;">
                            <div class="cal-panel-empty-icon" style="background:#fef2f2;color:#b91c1c;">
                                <i class="fas fa-calendar-xmark"></i>
                            </div>
                            <p class="cal-panel-empty-title">No slots on this day</p>
                            <p class="cal-panel-empty-body">All sessions are booked or outside the allowed window. Try a different day.</p>
                        </div>
                    @endif
                @endif
            </aside>

        </div>

        @if($showTutorial)
            <div class="tutorial-modal">
                <div class="tutorial-container">
                    <!-- Tutorial Header -->
                    <div class="tutorial-header">
                        <div class="tutorial-header-pattern"></div>

                        <div class="tutorial-header-content">
                            <div style="display: flex; align-items: center;">
                                <div>
                                    <h3 class="tutorial-title">How to Schedule Your Sessions</h3>
                                    <div class="tutorial-step-info">
                                        <span class="tutorial-step-badge">
                                            Step {{ $currentTutorialStep }} of {{ $totalTutorialSteps }}
                                        </span>
                                        <div class="tutorial-progress-dots">
                                            @for($i = 1; $i <= $totalTutorialSteps; $i++)
                                                <div class="tutorial-dot {{ $i <= $currentTutorialStep ? 'active' : '' }}"></div>
                                            @endfor
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <button wire:click="closeTutorial" class="tutorial-close-btn">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 1.25rem; height: 1.25rem;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>

                        <!-- Progress Bar -->
                        <div class="tutorial-progress-bar">
                            <div class="tutorial-progress-bg">
                                <div class="tutorial-progress-fill" style="width: {{ ($currentTutorialStep / $totalTutorialSteps) * 100 }}%"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Tutorial Content -->
                    <div class="tutorial-body">
                        @if($currentTutorialStep == 1)
                            <p class="tutorial-step-description">
                                This is your personal calendar where you can schedule <strong>Kick-Off Meetings</strong> and <strong>Review Sessions</strong> with your implementer.
                            </p>

                            <div class="tutorial-grid">
                                <div class="tutorial-card blue">
                                    <h5 style="font-weight: 700; color: #003c75; margin-bottom: 0.5rem;">Kick-Off <br>Meetings</h5>
                                </div>
                                <div class="tutorial-card purple">
                                    <h5 style="font-weight: 700; color: #005170; margin-bottom: 0.5rem;">Review <br>Sessions</h5>
                                </div>
                            </div>

                            <div class="tutorial-tip">
                                <p style="color: #065f46; font-size: 0.875rem;">
                                    <strong style="color: #10b981;">Green dates</strong> are available for booking, and your existing meetings appear in <strong style="color: #003c75;">blue</strong>.
                                </p>
                            </div>

                        @elseif($currentTutorialStep == 2)
                            <h4 class="tutorial-step-title">Finding Available Dates</h4>
                            <p class="tutorial-step-description">
                                Look for dates highlighted in <span style="padding: 0.25rem 0.75rem; background: #ecfdf5; color: #065f46; border-radius: 20px; font-weight: 600;">green</span>
                            </p>

                            <!-- Mini Calendar Preview -->
                            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin: 1.5rem auto; max-width: 600px;">
                                <div class="tutorial-card green" style="text-align: center; padding: 0.75rem;">
                                    <div style="font-weight: 700; color: #065f46;">15</div>
                                    <div style="font-size: 0.75rem; color: #10b981; margin-top: 0.25rem;">✨ 3 available</div>
                                </div>
                                <div style="padding: 0.75rem; background: #fffbeb; border: 2px solid #fde68a; border-radius: 12px; text-align: center;">
                                    <div style="font-weight: 700; color: #b45309;">16</div>
                                    <div style="font-size: 0.75rem; color: #b45309; margin-top: 0.25rem;">Weekend</div>
                                </div>
                                <div class="tutorial-card blue" style="text-align: center; padding: 0.75rem;">
                                    <div style="font-weight: 700; color: #003c75;">17</div>
                                    <div style="font-size: 0.75rem; color: #003c75; margin-top: 0.25rem;">📅 Your Meeting</div>
                                </div>
                            </div>

                        @elseif($currentTutorialStep == 3)
                            <h4 class="tutorial-step-title">Select Your Time Slot</h4>

                            <!-- Session Selector Preview -->
                            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.75rem; margin: 1.5rem 0;">
                                <div style="padding: 1rem; border: 1.5px solid #e5e7eb; border-radius: 12px; text-align: center; background: white; font-family: 'Poppins', sans-serif;">
                                    <div style="font-weight: 600; color: #003c75; font-size: 0.875rem;">Session 1</div>
                                    <div style="color: #6b7280; font-size: 0.75rem; margin-top: 0.25rem;">9:30 AM</div>
                                </div>
                                <div style="padding: 1rem; border: 1.5px solid #003c75; border-radius: 12px; text-align: center; background: #003c75; transform: scale(1.05); box-shadow: 0 6px 16px -4px rgba(0, 60, 117, 0.35); font-family: 'Poppins', sans-serif;">
                                    <div style="font-weight: 600; color: #ffffff; font-size: 0.875rem;">Session 2</div>
                                    <div style="color: rgba(255, 255, 255, 0.75); font-size: 0.75rem; margin-top: 0.25rem;">11:00 AM</div>
                                    <div style="margin-top: 0.5rem; padding: 0.25rem 0.5rem; background: rgba(255, 255, 255, 0.18); color: white; border-radius: 20px; font-size: 0.625rem; letter-spacing: 0.04em;">Selected</div>
                                </div>
                                <div style="padding: 1rem; border: 1.5px solid #e5e7eb; border-radius: 12px; text-align: center; background: white; font-family: 'Poppins', sans-serif;">
                                    <div style="font-weight: 600; color: #003c75; font-size: 0.875rem;">Session 3</div>
                                    <div style="color: #6b7280; font-size: 0.75rem; margin-top: 0.25rem;">2:00 PM</div>
                                </div>
                            </div>

                        @elseif($currentTutorialStep == 4)
                            <h4 class="tutorial-step-title">Add Attendees & Submit</h4>

                            <!-- Form Preview -->
                            <div style="padding: 1rem; border: 2px solid #10b981; border-radius: 12px; background: #ecfdf5; margin: 1.5rem 0; text-align: left; font-family: 'Poppins', sans-serif;">
                                <label style="display: block; font-weight: 600; color: #065f46; margin-bottom: 0.5rem; font-size: 0.875rem;">
                                    Required Attendees <span style="color: #ef4444;">*</span>
                                </label>
                                <input type="text"
                                    style="width: 100%; padding: 0.75rem; border: 2px solid #10b981; border-radius: 8px; background: #f0fdf4; font-size: 0.875rem; font-family: 'Poppins', sans-serif;"
                                    placeholder="john@example.com;jane@example.com"
                                    disabled>
                                <p style="margin-top: 0.5rem; font-size: 0.75rem; color: #065f46;">💡 Separate multiple emails with semicolons (;)</p>
                            </div>
                        @endif
                    </div>

                    <!-- Tutorial Footer -->
                    <div class="tutorial-footer">
                        <div style="display: flex; gap: 0.75rem;">
                            @if($currentTutorialStep > 1)
                                <button wire:click="previousTutorialStep" class="tutorial-btn secondary">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="arrow-left" style="margin-right: 0.5rem;">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                    </svg>
                                    Previous
                                </button>
                            @endif
                        </div>

                        <div style="display: flex; gap: 0.75rem;">
                            <button wire:click="skipTutorial" class="tutorial-btn secondary">
                                Skip Tutorial
                            </button>

                            @if($currentTutorialStep < $totalTutorialSteps)
                                <button wire:click="nextTutorialStep" class="tutorial-btn primary">
                                    Next Step
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="arrow-right" style="margin-left: 0.5rem;">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </button>
                            @else
                                <button wire:click="completeTutorial" class="tutorial-btn success">
                                    <span style="margin-right: 0.5rem;">🎉</span>
                                    Get Started!
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Slide-over drawer for "N scheduled" pill (reuses booking-card markup verbatim) -->
    @if($hasExistingBooking)
        <div class="cal-bookings-drawer-backdrop {{ $showExistingBookings ? 'open' : '' }}"
             wire:click="toggleExistingBookings"
             aria-hidden="true"></div>
        <aside id="cal-bookings-drawer"
               class="cal-bookings-drawer {{ $showExistingBookings ? 'open' : '' }}"
               role="dialog"
               aria-label="Your scheduled meetings"
               aria-hidden="{{ $showExistingBookings ? 'false' : 'true' }}">
            <div class="cal-bookings-drawer-head">
                <h4>
                    Your Scheduled Meetings
                    <span class="count">({{ count($existingBookings) }})</span>
                </h4>
                <button type="button"
                        class="cal-bookings-drawer-close"
                        wire:click="toggleExistingBookings"
                        aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="cal-bookings-drawer-body">
                @foreach($existingBookings as $booking)
                    @php
                        $appointmentDate = Carbon::parse($booking['raw_date'])->format('Y-m-d');
                        $appointmentDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $appointmentDate . ' ' . $booking['start_time']);
                        $now = Carbon::now();
                        $canCancel = ($booking['status'] === 'New') &&
                                     ($appointmentDateTime->isFuture() || ($appointmentDateTime->isToday() && $appointmentDateTime->gt($now)));

                        if ($booking['status'] === 'Done') {
                            $statusKey = 'done'; $statusLabel = 'Completed'; $statusIcon = 'fa-circle-check';
                        } elseif ($booking['status'] === 'Cancelled') {
                            $statusKey = 'cancelled'; $statusLabel = 'Cancelled'; $statusIcon = 'fa-circle-xmark';
                        } elseif ($canCancel) {
                            $statusKey = 'upcoming'; $statusLabel = 'Scheduled'; $statusIcon = 'fa-calendar-check';
                        } else {
                            $statusKey = 'locked'; $statusLabel = 'Locked'; $statusIcon = 'fa-lock';
                        }

                        $lockedReason = $appointmentDateTime->isPast() ? 'Session already passed' : 'Session has started';
                    @endphp
                    <div class="booking-card booking-card--{{ $statusKey }}">
                        <div class="bk-head">
                            <p class="bk-date">{{ $booking['date'] }}</p>
                            <span class="bk-pill bk-pill--{{ $statusKey }}">
                                <i class="fas {{ $statusIcon }}"></i>{{ $statusLabel }}
                            </span>
                        </div>

                        <p class="bk-time">
                            <i class="far fa-clock bk-clock"></i>
                            <span>{{ $booking['time'] }}</span>
                            <span class="bk-sep">·</span>
                            <span class="bk-session">{{ $booking['session'] }}</span>
                        </p>

                        <p class="bk-type">{{ $booking['type'] }}</p>

                        @if($canCancel)
                            <div class="bk-actions">
                                <button type="button"
                                        class="bk-cancel-btn"
                                        wire:click="openCancelModal({{ $booking['id'] }})">
                                    <i class="fas fa-times-circle"></i> Cancel meeting
                                </button>
                            </div>
                        @elseif($statusKey === 'locked')
                            <div class="bk-actions">
                                <span class="bk-note">
                                    <i class="fas fa-circle-info"></i>
                                    {{ $lockedReason }}
                                </span>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </aside>
    @endif

    <!-- Cancel Modal -->
    @if($showCancelModal && $appointmentToCancel)
        <div class="modal-overlay" wire:click="closeCancelModal">
            <div class="max-w-md modal-container" wire:click.stop>
                <div class="text-center modal-header bg-gradient-to-r from-red-500 to-red-600">
                    <h4 class="mb-2 text-lg font-semibold">Are you sure you want to cancel?</h4>
                    <p class="text-sm">This action cannot be undone. You'll need to schedule a new appointment after cancelling.</p>
                </div>

                <div class="modal-body">
                    <!-- Appointment Details -->
                    <div class="p-4 mb-6 rounded-lg bg-gray-50">
                        <h5 class="mb-2 font-medium text-gray-700">Appointment Details:</h5>
                        <div class="space-y-1 text-sm text-gray-600">
                            <div><strong>Date:</strong> {{ $appointmentToCancel['date'] }}</div>
                            <div><strong>Time:</strong> {{ $appointmentToCancel['time'] }}</div>
                            <div><strong>Session:</strong> {{ $appointmentToCancel['session'] }}</div>
                            <div><strong>Implementer:</strong> {{ $appointmentToCancel['implementer'] }}</div>
                        </div>
                    </div>

                    <!-- Warning Message -->
                    <div class="p-3 mb-6 border rounded-lg border-amber-200 bg-amber-50">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                            <p class="text-sm text-amber-700">
                                After cancellation, you can immediately schedule a new appointment.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button wire:click="confirmCancelAppointment"
                            class="btn btn-primary bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700"
                            wire:loading.attr="disabled" wire:target="confirmCancelAppointment">
                        <span wire:loading.remove wire:target="confirmCancelAppointment">
                            ❌ Yes, Cancel Appointment
                        </span>
                        <span wire:loading wire:target="confirmCancelAppointment" class="flex items-center">
                            <svg class="w-5 h-5 mr-2 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Cancelling...
                        </span>
                    </button>
                    <button wire:click="closeCancelModal"
                            class="btn btn-secondary"
                            wire:loading.attr="disabled" wire:target="confirmCancelAppointment">
                        🔙 Keep Appointment
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Booking Modal -->
    @if($showBookingModal)
        <div class="modal-overlay" wire:click="closeBookingModal">
            <div class="modal-container" wire:click.stop>
                <div class="modal-header">
                    <h3 class="text-2xl font-bold">{{ Carbon::parse($selectedDate)->format('l, j F Y') }}</h3>
                </div>

                <div class="modal-body">
                    <!-- Available Sessions -->
                    <div class="form-group">
                        <label class="form-label">Available Sessions</label>
                        <div class="grid grid-cols-3 gap-4">
                            @foreach($availableSessions as $index => $session)
                                <div class="session-option {{ $selectedSession && $selectedSession['session_name'] === $session['session_name'] ? 'selected' : '' }}"
                                    wire:click="selectSession({{ $index }})">
                                    <div class="text-center">
                                        <div class="mb-1 text-sm font-semibold text-gray-800">{{ $session['session_name'] }}</div>
                                        <div class="text-xs text-gray-600">{{ $session['formatted_time'] }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Appointment Type -->
                    <div class="form-group">
                        <label for="appointmentType" class="form-label">Meeting Type</label>
                        <div class="text-gray-700 bg-gray-100 cursor-not-allowed form-input">
                            Online Meeting via Microsoft Teams
                        </div>
                    </div>

                    <!-- Required Attendees -->
                    <div class="form-group">
                        <label for="requiredAttendees" class="form-label">Required Attendees <span class="text-red-600">*</span></label>
                        <input type="text" wire:model="requiredAttendees" id="requiredAttendees" class="form-input"
                            placeholder="john@example.com;jane@example.com">
                        <p class="mt-2 text-xs text-gray-500">
                            Separate multiple emails with semicolons (;)
                        </p>
                    </div>
                </div>

                <div class="modal-footer">
                    @if($sessionValidationError)
                        <div class="w-full p-3 mb-3 text-sm text-red-800 bg-red-100 border border-red-300 rounded-lg">
                            <div class="flex items-start">
                                <svg class="flex-shrink-0 w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                                <div class="flex-1">
                                    <strong class="font-semibold">Appointment session has been booked</strong>
                                    <p class="mt-1">{{ $sessionValidationError }}</p>
                                </div>
                            </div>
                        </div>
                    @endif
                    <button wire:click="submitBooking" class="btn btn-primary"
                        {{ !$selectedSession || empty(trim($requiredAttendees)) ? 'disabled' : '' }}
                        wire:loading.attr="disabled" wire:target="submitBooking">
                        <span wire:loading.remove wire:target="submitBooking">
                            @if(!$selectedSession)
                                🚫 Select Session First
                            @elseif(empty(trim($requiredAttendees)))
                                🚫 Add Attendees First
                            @else
                                📨 Submit Booking
                            @endif
                        </span>
                        <span wire:loading wire:target="submitBooking" class="flex items-center">
                            <svg class="w-5 h-5 mr-2 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Processing...
                        </span>
                    </button>
                    <button wire:click="closeBookingModal" class="btn btn-secondary"
                        wire:loading.attr="disabled" wire:target="submitBooking">
                        ❌ Cancel
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Update the Success Modal section -->
    @if($showSuccessModal && $submittedBooking)
        <div class="modal-overlay" wire:click="closeSuccessModal">
            <div class="max-w-2xl modal-container" wire:click.stop>
                <!-- Header with TimeTec branding -->
                <div class="text-center modal-header">
                    <!-- Close Button -->
                    <button wire:click="closeSuccessModal" class="absolute text-white transition-colors top-4 right-4 hover:text-gray-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                    <div class="text-4xl font-bold text-white">
                        TimeTec HRMS
                    </div>
                </div>

                <div class="text-center modal-body">
                    <!-- Success Content -->
                    <div class="mb-8">
                        <h2 class="mb-4 text-3xl font-bold text-green-600">Booking Submitted!</h2>
                        <p class="mb-6 text-lg text-gray-600" style="text-align: left;">
                            Your {{ strtolower($submittedBooking['session_type'] ?? 'meeting') }} request has been submitted successfully. <br>You'll receive an email for appointment details soon.
                        </p>
                    </div>

                    <!-- Booking Details Card -->
                    <div class="p-6 mb-6 border-2 border-blue-200 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl">
                        <div class="grid grid-cols-1 gap-4 text-sm md:grid-cols-2">
                            <div class="text-left">
                                <div class="font-medium text-gray-600">Date & Time</div>
                                <div class="font-bold text-gray-800">{{ $submittedBooking['date'] }}</div>
                                <div class="font-bold text-indigo-600">{{ $submittedBooking['time'] }}</div>
                            </div>
                            <div class="text-left">
                                <div class="font-medium text-gray-600">Session & Implementer</div>
                                <div class="font-bold text-gray-800">{{ $submittedBooking['session'] }}</div>
                                <div class="font-bold text-indigo-600">{{ $submittedBooking['implementer'] }}</div>
                            </div>
                        </div>

                        @if($submittedBooking['has_teams'])
                        <div class="p-3 mt-4 border border-green-200 rounded-lg bg-green-50">
                            <p class="mt-1 text-sm text-green-600">Meeting link will be included in your email.</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if($showMeetingDetailsModal && $selectedMeetingDetails)
        <div class="modal-overlay" wire:click="closeMeetingDetailsModal">
            <div class="modal-container" wire:click.stop>
                <div class="modal-header">
                    <!-- Close Button -->
                    <button wire:click="closeMeetingDetailsModal" class="absolute text-white transition-colors top-4 right-4 hover:text-gray-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>

                    <h3 class="text-2xl font-bold">
                        {{ $selectedMeetingDetails['type'] === 'KICK OFF MEETING SESSION' ? 'Kick-Off Meeting' : 'Review Session' }} Details
                    </h3>
                </div>

                <div class="modal-body">
                    <!-- Meeting Basic Info -->
                    <div class="p-4 mb-6 border-2 border-blue-200 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl">
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <div class="mb-2 text-sm font-medium text-gray-600">Date</div>
                                <div class="font-semibold text-gray-800 text-md">{{ $selectedMeetingDetails['date'] }}</div>
                            </div>
                            <div>
                                <div class="mb-2 text-sm font-medium text-gray-600">Time</div>
                                <div class="font-semibold text-indigo-600 text-md">{{ $selectedMeetingDetails['time'] }}</div>
                            </div>
                            <div>
                                <div class="mb-2 text-sm font-medium text-gray-600">Type</div>
                                <div class="font-semibold text-gray-800 text-md">
                                    {{ $selectedMeetingDetails['type'] === 'KICK OFF MEETING SESSION' ? 'Kick-Off Meeting' : 'Review Session' }}
                                </div>
                            </div>
                            <div>
                                <div class="mb-2 text-sm font-medium text-gray-600">Status</div>
                                <div class="inline-flex px-3 py-1 text-sm font-semibold rounded-full
                                    @if($selectedMeetingDetails['status'] === 'Done')
                                        bg-green-100 text-green-800
                                    @elseif($selectedMeetingDetails['status'] === 'New')
                                        bg-blue-100 text-blue-800
                                    @else
                                        bg-gray-100 text-gray-800
                                    @endif">
                                    @if($selectedMeetingDetails['status'] === 'Done')
                                        ✅ Completed
                                    @elseif($selectedMeetingDetails['status'] === 'New')
                                        🕒 Scheduled
                                    @else
                                        {{ $selectedMeetingDetails['status'] }}
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Implementer Details -->
                    <div class="form-group">
                        <label class="form-label">Implementer Details</label>
                        <div class="p-4 border border-indigo-200 rounded-lg bg-indigo-50">
                            <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                                <div>
                                    <div class="mb-1 text-sm font-medium text-indigo-700">Name:</div>
                                    <div class="font-semibold text-gray-800 text-md">{{ $selectedMeetingDetails['implementer_name'] }}</div>
                                </div>
                                <div>
                                    <div class="mb-1 text-sm font-medium text-indigo-700">Email:</div>
                                    <div class="text-sm text-gray-700">
                                        <a href="mailto:{{ $selectedMeetingDetails['implementer_email'] }}"
                                        class="text-blue-600 hover:text-blue-800 hover:underline">
                                            {{ $selectedMeetingDetails['implementer_email'] }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Required Attendees -->
                    @if($selectedMeetingDetails['required_attendees'])
                        <div class="form-group">
                            <label class="form-label">Required Attendees</label>
                            <div class="p-4 border border-gray-200 rounded-lg bg-gray-50">
                                <div class="grid grid-cols-1 gap-2 md:grid-cols-2">
                                    @foreach(explode(';', $selectedMeetingDetails['required_attendees']) as $email)
                                        @if(trim($email))
                                            <div class="flex items-center p-2 bg-white border border-gray-200 rounded">
                                                <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                                                </svg>
                                                <a href="mailto:{{ trim($email) }}"
                                                class="text-sm text-blue-600 hover:text-blue-800 hover:underline">
                                                    {{ trim($email) }}
                                                </a>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="modal-footer">
                    @if($selectedMeetingDetails['meeting_link'])
                        @php
                            // Check if meeting is past or completed
                            $meetingDate = Carbon::parse($selectedMeetingDetails['date']);
                            $isPastMeeting = $meetingDate->isPast();
                            $isCompleted = $selectedMeetingDetails['status'] === 'Done';
                            $shouldDisableJoin = $isPastMeeting || $isCompleted;
                        @endphp

                        @if($shouldDisableJoin)
                            <button class="btn btn-primary" disabled style="opacity: 0.5; cursor: not-allowed;">
                                @if($isCompleted)
                                    ✅ Meeting Completed
                                @else
                                    🕐 Meeting Expired
                                @endif
                            </button>
                        @else
                            <a href="{{ $selectedMeetingDetails['meeting_link'] }}"
                            target="_blank"
                            class="btn btn-primary">
                                🚀 Join Teams Meeting
                            </a>
                        @endif
                    @endif

                    <button wire:click="closeMeetingDetailsModal" class="btn btn-secondary">
                        ❌ Close
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ──────────────────────────────────────────────────────────────
         Bulk Attendees Drawer (Alpine-only, teleported to <body>)
         Triggered by CustomEvent 'cal-open-attendees' from window.
    ────────────────────────────────────────────────────────────── --}}
    <div x-data="{
            show: false,
            emails: [''],
            maxEmails: 10,
            emailRe: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
            open() {
                const current = this.$wire.get('requiredAttendees') || '';
                const parsed = current.split(';').map(e => e.trim()).filter(e => e.length > 0);
                this.emails = parsed.length > 0 ? parsed : [''];
                this.show = true;
                this.$nextTick(() => {
                    const first = document.querySelector('.cal-att-body input.cal-att-input');
                    if (first) first.focus();
                });
            },
            close() { this.show = false; },
            addRow() {
                if (this.emails.length < this.maxEmails) {
                    this.emails.push('');
                    this.$nextTick(() => {
                        const inputs = document.querySelectorAll('.cal-att-body input.cal-att-input');
                        inputs[inputs.length - 1]?.focus();
                    });
                }
            },
            removeRow(i) {
                this.emails.splice(i, 1);
                if (this.emails.length === 0) this.emails = [''];
            },
            handlePaste(ev, idx) {
                const text = (ev.clipboardData || window.clipboardData).getData('text') || '';
                if (!/[;,\n\t]/.test(text)) return;
                ev.preventDefault();
                const parts = text.split(/[;,\n\t]+/).map(e => e.trim()).filter(e => e.length > 0);
                if (parts.length === 0) return;
                this.emails[idx] = parts[0];
                for (let i = 1; i < parts.length && this.emails.length < this.maxEmails; i++) {
                    this.emails.push(parts[i]);
                }
            },
            isForbiddenDomain(email) {
                const t = (email || '').trim().toLowerCase();
                return /@(?:[^@\s]+\.)?timeteccloud\.com$/.test(t);
            },
            isInvalid(email) {
                const t = (email || '').trim();
                if (t.length === 0) return false;
                if (!this.emailRe.test(t)) return true;
                if (this.isForbiddenDomain(t)) return true;
                return false;
            },
            errorTextFor(email) {
                const t = (email || '').trim();
                if (t.length === 0) return '';
                if (!this.emailRe.test(t)) return 'Invalid email format.';
                if (this.isForbiddenDomain(t)) return 'Internal @timeteccloud.com addresses are not allowed.';
                return '';
            },
            hasInvalidRows() {
                return this.emails.some(e => this.isInvalid(e));
            },
            uniqueCount() {
                const seen = new Set();
                let n = 0;
                for (const e of this.emails) {
                    const k = (e || '').trim().toLowerCase();
                    if (k && !seen.has(k)) { seen.add(k); n++; }
                }
                return n;
            },
            save() {
                const seen = new Set();
                const cleaned = [];
                for (const e of this.emails) {
                    const trimmed = (e || '').trim();
                    if (!trimmed) continue;
                    if (!this.emailRe.test(trimmed)) continue;
                    if (this.isForbiddenDomain(trimmed)) continue;
                    const key = trimmed.toLowerCase();
                    if (seen.has(key)) continue;
                    seen.add(key);
                    cleaned.push(trimmed);
                }
                // Single server roundtrip: update the Livewire property,
                // persist to customers.saved_attendees for reuse on future
                // bookings, and push a success notification — all in the
                // saveAttendeeList() method on the component.
                this.$wire.call('saveAttendeeList', cleaned.join(';'));
                this.close();
            }
        }"
        x-effect="document.body.classList.toggle('cal-att-drawer-open', show)"
        @cal-open-attendees.window="open()"
        @keydown.escape.window="show && close()">

        <template x-teleport="body">
            <div x-show="show" x-cloak class="cal-att-root">
                {{-- Backdrop --}}
                <div class="cal-att-backdrop"
                     x-show="show"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     @click="close()"
                     style="opacity: 1;"></div>

                {{-- Drawer --}}
                <aside class="cal-att-drawer"
                       role="dialog"
                       aria-modal="true"
                       aria-labelledby="cal-att-title"
                       x-show="show"
                       x-transition:enter="transition ease-out duration-300"
                       x-transition:enter-start="transform translate-x-full"
                       x-transition:enter-end="transform translate-x-0"
                       x-transition:leave="transition ease-in duration-200"
                       x-transition:leave-start="transform translate-x-0"
                       x-transition:leave-end="transform translate-x-full"
                       style="transform: translateX(0);">

                    <header class="cal-att-head">
                        <div class="cal-att-head-text">
                            <h3 id="cal-att-title" class="cal-att-head-title">Manage Attendee List</h3>
                        </div>
                        <button type="button" class="cal-att-close" @click="close()" aria-label="Close drawer">
                            <i class="fas fa-xmark"></i>
                        </button>
                    </header>

                    <div class="cal-att-body">
                        <template x-for="(email, idx) in emails" :key="idx">
                            <div class="cal-att-row">
                                <div class="cal-att-row-head">
                                    <span class="cal-att-row-num" x-text="'Attendee ' + (idx + 1)"></span>
                                    <button type="button"
                                            class="cal-att-row-remove"
                                            @click="removeRow(idx)"
                                            aria-label="Remove attendee">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                <input type="email"
                                       class="cal-att-input"
                                       :class="{ 'is-invalid': isInvalid(email) }"
                                       x-model="emails[idx]"
                                       @paste="handlePaste($event, idx)"
                                       @keydown.enter.prevent="idx === emails.length - 1 ? addRow() : null"
                                       placeholder="name@example.com"
                                       autocomplete="off"
                                       spellcheck="false">
                                <p class="cal-att-row-error" x-show="isInvalid(email)" x-text="errorTextFor(email)"></p>
                            </div>
                        </template>

                        <button type="button"
                                class="cal-att-add"
                                @click="addRow()"
                                :disabled="emails.length >= maxEmails">
                            <i class="fas fa-plus"></i>
                            <span x-text="emails.length >= maxEmails ? 'Attendee limit reached' : 'Add another attendee'"></span>
                        </button>

                        <p class="cal-att-counter" :class="{ 'is-over': emails.length > maxEmails }">
                            <span x-text="emails.length"></span> of <span x-text="maxEmails"></span>
                            <template x-if="uniqueCount() !== emails.filter(e => (e || '').trim().length > 0).length">
                                <span> · <span x-text="uniqueCount()"></span> unique after dedupe</span>
                            </template>
                        </p>
                    </div>

                    <footer class="cal-att-foot">
                        <button type="button" class="cal-att-btn-cancel" @click="close()">Cancel</button>
                        <button type="button"
                                class="cal-att-btn-save"
                                @click="save()"
                                :disabled="hasInvalidRows()">
                            <i class="fas fa-check"></i>
                            Save attendees
                        </button>
                    </footer>

                </aside>
            </div>
        </template>
    </div>

</div>
