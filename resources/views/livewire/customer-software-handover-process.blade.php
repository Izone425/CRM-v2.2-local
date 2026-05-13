<div class="cshp-container">
<style>
    .cshp-container {
        width: 100%;
    }

    /* --- Featured onboarding card --- */
    .cshp-featured {
        position: relative;
        background: #ffffff;
        border-radius: 16px;
        padding: 28px 32px 26px;
        border: 1px solid #e7eafc;
        box-shadow: 0 10px 30px -18px rgba(88, 95, 182, 0.35);
        overflow: hidden;
    }
    .cshp-featured::before {
        content: '';
        position: absolute;
        inset: 0;
        background:
            radial-gradient(70% 60% at 0% 0%, rgba(102,126,234,0.10) 0%, transparent 55%),
            radial-gradient(50% 45% at 100% 0%, rgba(233,56,134,0.07) 0%, transparent 60%);
        pointer-events: none;
    }
    .cshp-featured-head {
        position: relative;
        display: flex;
        gap: 18px;
        align-items: flex-start;
    }
    .cshp-badge {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        background: linear-gradient(135deg, #667eea 0%, #8f5df7 55%, #e93886 100%);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        flex-shrink: 0;
        box-shadow: 0 10px 22px -10px rgba(102,126,234,0.7);
    }
    .cshp-featured-titleblock {
        flex: 1;
        min-width: 0;
    }
    .cshp-title {
        font-size: 22px;
        font-weight: 700;
        color: #1e293b;
        line-height: 1.25;
        margin: 0;
    }
    .cshp-subtitle {
        font-size: 13px;
        color: #64748b;
        margin-top: 4px;
    }
    .cshp-subtitle strong {
        color: #334155;
        font-weight: 600;
    }

    /* --- Info chip strip --- */
    .cshp-chips {
        position: relative;
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 10px;
        margin-top: 20px;
    }
    .cshp-chip {
        background: #f8f9ff;
        border: 1px solid #e7eafc;
        border-radius: 10px;
        padding: 10px 14px;
        min-width: 0;
    }
    .cshp-chip-label {
        font-size: 10px;
        font-weight: 600;
        color: #94a3b8;
        margin-bottom: 4px;
    }
    .cshp-chip-value {
        font-size: 14px;
        font-weight: 700;
        color: #1e293b;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .cshp-chip-value.is-placeholder {
        color: #94a3b8;
        font-style: italic;
        font-weight: 500;
    }
    @media (max-width: 640px) {
        .cshp-chips { grid-template-columns: 1fr; }
    }

    /* --- Incomplete banner --- */
    .cshp-banner {
        position: relative;
        margin-top: 18px;
        padding: 12px 14px;
        background: #fff8e1;
        border: 1px solid #fde68a;
        border-radius: 10px;
        display: flex;
        gap: 10px;
        align-items: flex-start;
        font-size: 13px;
        color: #78350f;
    }
    .cshp-banner i { margin-top: 2px; color: #d97706; }

    .cshp-banner.is-error {
        background: #fef2f2;
        border-color: #fecaca;
        color: #991b1b;
    }
    .cshp-banner.is-error i { color: #dc2626; }

    /* --- PDF viewer --- */
    .cshp-viewer-wrap {
        position: relative;
        margin-top: 20px;
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        box-shadow: inset 0 0 0 1px rgba(255,255,255,0.6);
    }
    .cshp-viewer {
        display: block;
        width: 100%;
        min-height: 820px;
        border: 0;
        background: #f8fafc;
    }
    @media (max-width: 1024px) {
        .cshp-viewer { min-height: 680px; }
    }
    @media (max-width: 768px) {
        .cshp-viewer { min-height: 520px; }
    }
    .cshp-viewer-fallback {
        padding: 40px 24px;
        text-align: center;
        color: #64748b;
        font-size: 13px;
    }

    /* --- Actions --- */
    .cshp-actions {
        position: relative;
        display: flex;
        gap: 10px;
        margin-top: 18px;
        flex-wrap: wrap;
    }
    .cshp-btn-primary,
    .cshp-btn-ghost {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 18px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        transition: transform 0.15s ease, box-shadow 0.15s ease, background 0.15s ease, color 0.15s ease;
        cursor: pointer;
        border: none;
    }
    .cshp-btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #8f5df7 100%);
        color: #ffffff;
        box-shadow: 0 8px 18px -10px rgba(102,126,234,0.8);
    }
    .cshp-btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 10px 22px -10px rgba(102,126,234,0.95);
        color: #ffffff;
    }
    .cshp-btn-ghost {
        background: #f8f9ff;
        color: #667eea;
        border: 1px solid #e7eafc;
    }
    .cshp-btn-ghost:hover {
        background: #eef0ff;
        color: #4f62c7;
    }

</style>

    {{-- =========================================================
         BLOCK 1 — Featured personalized onboarding document
    ========================================================== --}}
    @if($context)
    <div class="cshp-featured">
        <div class="cshp-featured-head">
            <div class="cshp-badge">
                <i class="fas fa-file-contract"></i>
            </div>
            <div class="cshp-featured-titleblock">
                <h2 class="cshp-title">Software Handover Process</h2>
                <div class="cshp-subtitle">
                    Personalized for <strong>{{ $context['companyName'] }}</strong>
                </div>
            </div>
        </div>

        <div class="cshp-chips">
            <div class="cshp-chip">
                <div class="cshp-chip-label">Project Code</div>
                <div class="cshp-chip-value {{ $context['projectCode'] === '—' ? 'is-placeholder' : '' }}">
                    {{ $context['projectCode'] }}
                </div>
            </div>
            <div class="cshp-chip">
                <div class="cshp-chip-label">Implementer</div>
                <div class="cshp-chip-value {{ $context['implementer'] === 'To be assigned' ? 'is-placeholder' : '' }}">
                    {{ $context['implementer'] }}
                </div>
            </div>
            <div class="cshp-chip">
                <div class="cshp-chip-label">License Activation</div>
                <div class="cshp-chip-value {{ $context['licenseDate'] === 'To be confirmed' ? 'is-placeholder' : '' }}">
                    {{ $context['licenseDate'] }}
                </div>
            </div>
        </div>

        @if($templateMissing)
            <div class="cshp-banner is-error">
                <i class="fas fa-triangle-exclamation"></i>
                <div>
                    <strong>Onboarding template not yet available.</strong>
                    The master document has not been uploaded by our team. Please contact your implementer if this persists.
                </div>
            </div>
        @elseif(!$hasCompleteData)
            <div class="cshp-banner">
                <i class="fas fa-circle-info"></i>
                <div>
                    Some details will be filled in once your implementer completes setup — kick-off date and temporary credentials appear here as soon as they're confirmed.
                </div>
            </div>
        @endif

        <div class="cshp-viewer-wrap">
            @if($templateMissing)
                <div class="cshp-viewer-fallback">
                    <i class="fas fa-file-pdf" style="font-size:32px; color:#cbd5e1; display:block; margin-bottom:8px;"></i>
                    Preview will be available once the template is in place.
                </div>
            @else
                <iframe
                    src="{{ route('customer.onboarding-pdf.view') }}#view=FitH&toolbar=1"
                    class="cshp-viewer"
                    title="Software Onboarding Document"
                    loading="lazy">
                </iframe>
            @endif
        </div>

        <div class="cshp-actions">
            <a href="{{ route('customer.onboarding-pdf.download') }}" class="cshp-btn-primary"
               @if($templateMissing) aria-disabled="true" style="pointer-events:none; opacity:0.5;" @endif>
                <i class="fas fa-download"></i>
                Download PDF
            </a>
            <a href="{{ route('customer.onboarding-pdf.view') }}" target="_blank" rel="noopener" class="cshp-btn-ghost"
               @if($templateMissing) aria-disabled="true" style="pointer-events:none; opacity:0.5;" @endif>
                <i class="fas fa-up-right-from-square"></i>
                Open in new tab
            </a>
        </div>
    </div>
    @endif

</div>
