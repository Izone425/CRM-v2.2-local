<div class="cshp-container">
<style>
    .cshp-container {
        width: 100%;
        height: calc(100vh - 112px);
        min-height: 520px;
        display: flex;
        flex-direction: column;
        overflow-y: auto;
        overflow-x: hidden;
    }

    /* --- Featured onboarding card --- */
    .cshp-featured {
        position: relative;
        background: #ffffff;
        border-radius: 16px;
        padding: 18px 28px 18px;
        border: 1px solid #e7eafc;
        box-shadow: 0 10px 30px -18px rgba(88, 95, 182, 0.35);
        overflow: hidden;
        display: flex;
        flex-direction: column;
        flex: 1;
        min-height: 0;
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
        gap: 14px;
        align-items: center;
        flex-shrink: 0;
    }
    .cshp-badge {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: rgba(6, 182, 212, 0.10);
        color: #06b6d4;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        flex-shrink: 0;
    }
    .cshp-featured-titleblock {
        flex: 1;
        min-width: 0;
    }
    .cshp-title {
        font-size: 18px;
        font-weight: 700;
        color: #1e293b;
        line-height: 1.25;
        margin: 0;
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
        flex-shrink: 0;
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
        flex: 1;
        min-height: 0;
        display: flex;
    }
    .cshp-viewer {
        display: block;
        width: 100%;
        flex: 1;
        height: 100%;
        border: 0;
        background: #f8fafc;
    }
    .cshp-viewer:fullscreen { background: #000; }
    .cshp-viewer-fallback {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 40px 24px;
        text-align: center;
        color: #64748b;
        font-size: 13px;
    }

    /* --- Header icon actions --- */
    .cshp-head-actions {
        position: relative;
        display: flex;
        gap: 10px;
        margin-left: auto;
        align-self: flex-start;
        flex-shrink: 0;
    }
    .cshp-icon-btn {
        position: relative;
        z-index: 5;
        width: 40px;
        height: 40px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        text-decoration: none;
        cursor: pointer;
        border: none;
        transition: transform 0.15s ease, box-shadow 0.15s ease, background 0.15s ease, color 0.15s ease;
    }
    .cshp-icon-btn--primary {
        background: linear-gradient(135deg, #667eea 0%, #8f5df7 100%);
        color: #ffffff;
        box-shadow: 0 8px 18px -10px rgba(102,126,234,0.8);
    }
    .cshp-icon-btn--primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 10px 22px -10px rgba(102,126,234,0.95);
        color: #ffffff;
    }
    .cshp-icon-btn--ghost {
        background: #f8f9ff;
        color: #667eea;
        border: 1px solid #e7eafc;
    }
    .cshp-icon-btn--ghost:hover {
        background: #eef0ff;
        color: #4f62c7;
    }

    /* Tooltip — right-anchored so it never extends past the card's right padding
       (the card uses overflow:hidden for the gradient ::before overlay). */
    .cshp-icon-btn::after {
        content: attr(data-tooltip);
        position: absolute;
        top: calc(100% + 10px);
        right: 0;
        background: #1e293b;
        color: #f1f5f9;
        font-size: 11.5px;
        font-weight: 600;
        letter-spacing: 0.01em;
        padding: 6px 10px;
        border-radius: 6px;
        white-space: nowrap;
        opacity: 0;
        pointer-events: none;
        transform: translateY(-4px);
        transition: opacity 0.18s ease, transform 0.18s ease;
        box-shadow: 0 6px 18px -8px rgba(15, 23, 42, 0.5);
        z-index: 10;
    }
    .cshp-icon-btn::before {
        content: '';
        position: absolute;
        top: calc(100% + 4px);
        left: 50%;
        width: 0;
        height: 0;
        border-left: 5px solid transparent;
        border-right: 5px solid transparent;
        border-bottom: 6px solid #1e293b;
        opacity: 0;
        pointer-events: none;
        transform: translateX(-50%) translateY(-4px);
        transition: opacity 0.18s ease, transform 0.18s ease;
        z-index: 10;
    }
    .cshp-icon-btn:hover::after,
    .cshp-icon-btn:focus-visible::after {
        opacity: 1;
        transform: translateY(0);
    }
    .cshp-icon-btn:hover::before,
    .cshp-icon-btn:focus-visible::before {
        opacity: 1;
        transform: translateX(-50%) translateY(0);
    }
    .cshp-icon-btn[aria-disabled="true"]::after,
    .cshp-icon-btn[aria-disabled="true"]::before {
        display: none;
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
                <h2 class="cshp-title">Software Onboarding Process</h2>
            </div>
            <div class="cshp-head-actions">
                <a href="{{ route('customer.onboarding-pdf.download') }}"
                   class="cshp-icon-btn cshp-icon-btn--primary"
                   data-tooltip="Download PDF"
                   aria-label="Download PDF"
                   @if($templateMissing) aria-disabled="true" tabindex="-1" style="pointer-events:none; opacity:0.5;" @endif>
                    <i class="fas fa-download"></i>
                </a>
                <button type="button"
                        class="cshp-icon-btn cshp-icon-btn--ghost"
                        data-tooltip="Full screen"
                        aria-label="View in full screen"
                        onclick="this.closest('.cshp-featured').querySelector('.cshp-viewer').requestFullscreen()"
                        @if($templateMissing) aria-disabled="true" disabled style="pointer-events:none; opacity:0.5;" @endif>
                    <i class="fas fa-expand"></i>
                </button>
                <a href="{{ route('customer.onboarding-pdf.view') }}"
                   target="_blank" rel="noopener"
                   class="cshp-icon-btn cshp-icon-btn--ghost"
                   data-tooltip="Open in new tab"
                   aria-label="Open in new tab"
                   @if($templateMissing) aria-disabled="true" tabindex="-1" style="pointer-events:none; opacity:0.5;" @endif>
                    <i class="fas fa-up-right-from-square"></i>
                </a>
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
                    src="{{ route('customer.onboarding-pdf.view') }}#toolbar=0&navpanes=0&view=FitH"
                    class="cshp-viewer"
                    title="Software Onboarding Document"
                    loading="lazy">
                </iframe>
            @endif
        </div>
    </div>
    @endif

</div>
