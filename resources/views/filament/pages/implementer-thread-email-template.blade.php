<x-filament::page>
    <div x-data="{
        editorContent: @entangle('templateContent'),
        categoryOpen: false,
        categoryValue: @entangle('templateCategory'),
        categories: {{ Js::from(\App\Models\EmailTemplate::availableCategories()) }},
        get categoryLabel() {
            return this.categoryValue ? this.categories[this.categoryValue] || this.categoryValue : 'Select Category';
        },
        selectCategory(key) {
            this.categoryValue = key;
            this.categoryOpen = false;
        },
        clearCategory() {
            this.categoryValue = '';
            this.categoryOpen = false;
        },
        initEditor() {
            const editor = this.$refs.emtEditor;
            if (editor) {
                editor.innerHTML = this.editorContent || '';
            }
        },
        updateContent() {
            const editor = this.$refs.emtEditor;
            if (editor) {
                this.editorContent = editor.innerHTML;
            }
        },
        formatText(command, value = null) {
            document.execCommand(command, false, value);
            this.updateContent();
        },
        savedRange: null,
        saveSelection() {
            const sel = window.getSelection();
            if (sel.rangeCount > 0) {
                const range = sel.getRangeAt(0);
                const editor = this.$refs.emtEditor;
                if (editor && editor.contains(range.commonAncestorContainer)) {
                    this.savedRange = range.cloneRange();
                }
            }
        },
        restoreSelection() {
            if (this.savedRange) {
                const sel = window.getSelection();
                sel.removeAllRanges();
                sel.addRange(this.savedRange);
            }
        },
        insertPlaceholder(placeholder) {
            const editor = this.$refs.emtEditor;
            if (editor) {
                editor.focus();
                if (this.savedRange) {
                    this.restoreSelection();
                }
                document.execCommand('insertText', false, placeholder);
                this.saveSelection();
                this.updateContent();
            }
        }
    }"
    x-on:edit-template-loaded.window="$nextTick(() => initEditor())"
    >

    <style>
        .emt-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .emt-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .emt-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1f2937;
        }

        .emt-btn-create {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.625rem 1.25rem;
            background: linear-gradient(135deg, #7c3aed, #6d28d9);
            color: white;
            border: none;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 2px 4px rgba(124, 58, 237, 0.3);
        }

        .emt-btn-create:hover {
            background: linear-gradient(135deg, #6d28d9, #5b21b6);
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(124, 58, 237, 0.4);
        }

        /* Table */
        .emt-table-wrapper {
            background: white;
            border-radius: 0.75rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            border: 1px solid #e5e7eb;
        }

        .emt-table {
            width: 100%;
            border-collapse: collapse;
        }

        .emt-table thead {
            background: #f9fafb;
        }

        .emt-table th {
            padding: 0.75rem 1rem;
            text-align: left;
            font-size: 0.75rem;
            font-weight: 600;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 1px solid #e5e7eb;
        }

        .emt-table td {
            padding: 0.875rem 1rem;
            font-size: 0.875rem;
            color: #374151;
            border-bottom: 1px solid #f3f4f6;
        }

        .emt-table tr:last-child td {
            border-bottom: none;
        }

        .emt-table tr:hover {
            background: #f9fafb;
        }

        .emt-category-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.625rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .emt-category-first-response { background: #dbeafe; color: #1d4ed8; }
        .emt-category-follow-up { background: #fef3c7; color: #92400e; }
        .emt-category-escalation { background: #fee2e2; color: #dc2626; }
        .emt-category-general { background: #f3f4f6; color: #4b5563; }

        .emt-actions {
            display: flex;
            gap: 0.5rem;
        }

        .emt-btn-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 2rem;
            height: 2rem;
            border-radius: 0.375rem;
            border: 1px solid #e5e7eb;
            background: white;
            color: #6b7280;
            cursor: pointer;
            transition: all 0.15s;
        }

        .emt-btn-icon:hover {
            background: #f3f4f6;
            color: #374151;
        }

        .emt-btn-icon.emt-btn-delete:hover {
            background: #fef2f2;
            color: #dc2626;
            border-color: #fca5a5;
        }

        /* Empty state */
        .emt-empty {
            padding: 3rem;
            text-align: center;
            color: #9ca3af;
        }

        .emt-empty i {
            font-size: 3rem;
            margin-bottom: 1rem;
            display: block;
        }

        /* Modal Overlay */
        .emt-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            padding: 2rem;
        }

        .emt-modal {
            background: white;
            border-radius: 1rem;
            width: 100%;
            max-width: 800px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.25);
        }

        .emt-modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #e5e7eb;
        }

        .emt-modal-title {
            font-size: 1.125rem;
            font-weight: 700;
            color: #1f2937;
        }

        .emt-modal-close {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 2rem;
            height: 2rem;
            border-radius: 0.375rem;
            border: none;
            background: transparent;
            color: #6b7280;
            cursor: pointer;
            font-size: 1.25rem;
        }

        .emt-modal-close:hover {
            background: #f3f4f6;
            color: #374151;
        }

        .emt-modal-body {
            padding: 1.5rem;
        }

        .emt-form-group {
            margin-bottom: 1.25rem;
        }

        .emt-form-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.375rem;
        }

        .emt-form-input {
            width: 100%;
            padding: 0.625rem 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            color: #1f2937;
            transition: border-color 0.15s;
            outline: none;
            box-sizing: border-box;
        }

        .emt-form-input:focus {
            border-color: #7c3aed;
            box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1);
        }

        /* Custom Alpine.js Dropdown (replaces native select) */
        .emt-select {
            position: relative;
        }

        .emt-select-trigger {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            padding: 0.625rem 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            color: #1f2937;
            background: white;
            cursor: pointer;
            transition: border-color 0.15s;
            box-sizing: border-box;
        }

        .emt-select-trigger:hover {
            border-color: #9ca3af;
        }

        .emt-select-trigger.emt-select-open {
            border-color: #7c3aed;
            box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1);
        }

        .emt-select-label {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .emt-select-placeholder {
            color: #9ca3af;
        }

        .emt-select-chevron {
            width: 16px;
            height: 16px;
            color: #6b7280;
            transition: transform 0.2s;
            flex-shrink: 0;
        }

        .emt-chevron-open {
            transform: rotate(180deg);
        }

        .emt-select-clear {
            display: flex;
            align-items: center;
            justify-content: center;
            background: none;
            border: none;
            color: #9ca3af;
            cursor: pointer;
            padding: 2px;
            border-radius: 4px;
        }

        .emt-select-clear:hover {
            color: #dc2626;
            background: #fef2f2;
        }

        .emt-select-dropdown {
            position: absolute;
            top: calc(100% + 4px);
            left: 0;
            right: 0;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            z-index: 50;
            overflow: hidden;
        }

        .emt-select-option {
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
            color: #374151;
            cursor: pointer;
            transition: background 0.1s;
        }

        .emt-select-option:hover {
            background: #f3f4f6;
        }

        .emt-select-active {
            background: #f5f3ff;
            color: #6d28d9;
            font-weight: 500;
        }

        .emt-select-active:hover {
            background: #ede9fe;
        }

        /* Rich Text Editor */
        .emt-editor-toolbar {
            display: flex;
            gap: 0.25rem;
            padding: 0.5rem;
            background: #f9fafb;
            border: 1px solid #d1d5db;
            border-bottom: none;
            border-radius: 0.5rem 0.5rem 0 0;
            flex-wrap: wrap;
        }

        .emt-toolbar-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 2rem;
            height: 2rem;
            border: 1px solid transparent;
            border-radius: 0.25rem;
            background: transparent;
            color: #4b5563;
            cursor: pointer;
            font-size: 0.875rem;
            transition: all 0.15s;
        }

        .emt-toolbar-btn:hover {
            background: #e5e7eb;
            color: #1f2937;
        }

        .emt-toolbar-separator {
            width: 1px;
            height: 1.5rem;
            background: #d1d5db;
            margin: 0.25rem 0.25rem;
            align-self: center;
        }

        .emt-editor-body {
            min-height: 200px;
            max-height: 300px;
            overflow-y: auto;
            padding: 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 0 0 0.5rem 0.5rem;
            font-size: 0.875rem;
            color: #1f2937;
            line-height: 1.6;
            outline: none;
        }

        .emt-editor-body:focus {
            border-color: #7c3aed;
            box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1);
        }

        .emt-editor-body p {
            margin: 0 0 0.5rem 0;
        }

        /* Placeholder Guide */
        .emt-placeholder-guide {
            margin-top: 1rem;
            padding: 1rem;
            background: #f5f3ff;
            border: 1px solid #e9d5ff;
            border-radius: 0.5rem;
        }

        .emt-placeholder-title {
            font-size: 0.8rem;
            font-weight: 600;
            color: #6d28d9;
            margin-bottom: 0.625rem;
        }

        .emt-placeholder-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 0.375rem;
        }

        .emt-placeholder-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.375rem 0.5rem;
            background: white;
            border-radius: 0.375rem;
            cursor: pointer;
            transition: all 0.15s;
            border: 1px solid #e9d5ff;
        }

        .emt-placeholder-item:hover {
            background: #ede9fe;
            border-color: #c4b5fd;
        }

        .emt-placeholder-key {
            font-family: monospace;
            font-size: 0.75rem;
            font-weight: 600;
            color: #7c3aed;
        }

        .emt-placeholder-desc {
            font-size: 0.7rem;
            color: #6b7280;
        }

        /* Modal Footer */
        .emt-modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 0.75rem;
            padding: 1rem 1.5rem;
            border-top: 1px solid #e5e7eb;
        }

        .emt-btn-cancel {
            padding: 0.625rem 1.25rem;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            background: white;
            color: #374151;
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.15s;
        }

        .emt-btn-cancel:hover {
            background: #f3f4f6;
        }

        .emt-btn-save {
            padding: 0.625rem 1.5rem;
            border: none;
            border-radius: 0.5rem;
            background: linear-gradient(135deg, #7c3aed, #6d28d9);
            color: white;
            font-size: 0.875rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }

        .emt-btn-save:hover {
            background: linear-gradient(135deg, #6d28d9, #5b21b6);
        }

        /* Delete Confirmation Modal */
        .emt-confirm-modal {
            background: white;
            border-radius: 0.75rem;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.25);
            padding: 1.5rem;
            text-align: center;
        }

        .emt-confirm-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 3rem;
            height: 3rem;
            margin: 0 auto 1rem;
            background: #fef2f2;
            border-radius: 50%;
            color: #dc2626;
            font-size: 1.25rem;
        }

        .emt-confirm-title {
            font-size: 1.125rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 0.5rem;
        }

        .emt-confirm-text {
            font-size: 0.875rem;
            color: #6b7280;
            margin-bottom: 1.25rem;
        }

        .emt-confirm-actions {
            display: flex;
            gap: 0.75rem;
            justify-content: center;
        }

        .emt-btn-delete-confirm {
            padding: 0.625rem 1.25rem;
            border: none;
            border-radius: 0.5rem;
            background: #dc2626;
            color: white;
            font-size: 0.875rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s;
        }

        .emt-btn-delete-confirm:hover {
            background: #b91c1c;
        }

        .emt-creator-name {
            font-size: 0.8rem;
            color: #6b7280;
        }

        .emt-date {
            font-size: 0.8rem;
            color: #9ca3af;
        }
    </style>

    <div class="emt-container">
        <!-- Header -->
        <div class="emt-header">
            <h1 class="emt-title">Project Thread Email Templates</h1>
            <button class="emt-btn-create" wire:click="openCreateModal">
                <i class="bi bi-plus-lg"></i>
                Create Template
            </button>
        </div>

        <!-- Table -->
        <div class="emt-table-wrapper">
            @if(count($templates) > 0)
                <table class="emt-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Subject</th>
                            <th>Category</th>
                            <th>Created By</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($templates as $template)
                            <tr>
                                <td style="font-weight: 500;">{{ $template['name'] }}</td>
                                <td>{{ $template['subject'] }}</td>
                                <td>
                                    @if($template['category'])
                                        @php
                                            $catClass = match($template['category']) {
                                                'First Response' => 'emt-category-first-response',
                                                'Follow-up' => 'emt-category-follow-up',
                                                'Escalation' => 'emt-category-escalation',
                                                default => 'emt-category-general',
                                            };
                                        @endphp
                                        <span class="emt-category-badge {{ $catClass }}">{{ $template['category'] }}</span>
                                    @else
                                        <span class="emt-category-badge emt-category-general">General</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="emt-creator-name">{{ $template['creator']['name'] ?? 'System' }}</span>
                                </td>
                                <td>
                                    <span class="emt-date">{{ \Carbon\Carbon::parse($template['created_at'])->format('d M Y') }}</span>
                                </td>
                                <td>
                                    <div class="emt-actions">
                                        <button class="emt-btn-icon" wire:click="openEditModal({{ $template['id'] }})" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="emt-btn-icon" wire:click="duplicateTemplate({{ $template['id'] }})" title="Duplicate">
                                            <i class="bi bi-copy"></i>
                                        </button>
                                        <button class="emt-btn-icon emt-btn-delete" wire:click="confirmDelete({{ $template['id'] }})" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="emt-empty">
                    <i class="bi bi-envelope-paper"></i>
                    <p style="font-size: 1rem; font-weight: 500; color: #6b7280;">No templates yet</p>
                    <p style="font-size: 0.875rem;">Click "Create Template" to add your first email template.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Create/Edit Modal -->
    @if($showModal)
        <div class="emt-modal-overlay" wire:click.self="closeModal">
            <div class="emt-modal" wire:ignore.self>
                <div class="emt-modal-header">
                    <h2 class="emt-modal-title">
                        {{ $editingTemplateId ? 'Edit Template' : 'Create Template' }}
                    </h2>
                    <button class="emt-modal-close" wire:click="closeModal">&times;</button>
                </div>

                <div class="emt-modal-body">
                    <!-- Name -->
                    <div class="emt-form-group">
                        <label class="emt-form-label">Template Name <span style="color: #dc2626;">*</span></label>
                        <input type="text" class="emt-form-input" wire:model="templateName" placeholder="e.g. First Response Template">
                        @error('templateName') <span style="color: #dc2626; font-size: 0.75rem;">{{ $message }}</span> @enderror
                    </div>

                    <!-- Subject -->
                    <div class="emt-form-group">
                        <label class="emt-form-label">Email Subject <span style="color: #dc2626;">*</span></label>
                        <input type="text" class="emt-form-input" wire:model="templateSubject" placeholder="e.g. Re: Your Support Request">
                        @error('templateSubject') <span style="color: #dc2626; font-size: 0.75rem;">{{ $message }}</span> @enderror
                    </div>

                    <!-- Category -->
                    <div class="emt-form-group" wire:ignore>
                        <label class="emt-form-label">Category</label>
                        <div class="emt-select" @click.away="categoryOpen = false">
                            <div class="emt-select-trigger" :class="{ 'emt-select-open': categoryOpen }" @click="categoryOpen = !categoryOpen">
                                <span class="emt-select-label" :class="{ 'emt-select-placeholder': !categoryValue }" x-text="categoryLabel"></span>
                                <div style="display: flex; align-items: center; gap: 4px;">
                                    <template x-if="categoryValue">
                                        <button type="button" class="emt-select-clear" @click.stop="clearCategory()" title="Clear">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" style="width: 14px; height: 14px;">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </template>
                                    <svg class="emt-select-chevron" :class="{ 'emt-chevron-open': categoryOpen }" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                    </svg>
                                </div>
                            </div>
                            <div class="emt-select-dropdown" x-show="categoryOpen" x-cloak x-transition>
                                <template x-for="(label, key) in categories" :key="key">
                                    <div class="emt-select-option" :class="{ 'emt-select-active': categoryValue === key }" @click="selectCategory(key)" x-text="label"></div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Body Editor -->
                    <div class="emt-form-group" wire:ignore>
                        <label class="emt-form-label">Email Body <span style="color: #dc2626;">*</span></label>
                        <div class="emt-editor-toolbar">
                            <button type="button" class="emt-toolbar-btn" @click="formatText('bold')" title="Bold">
                                <i class="bi bi-type-bold"></i>
                            </button>
                            <button type="button" class="emt-toolbar-btn" @click="formatText('italic')" title="Italic">
                                <i class="bi bi-type-italic"></i>
                            </button>
                            <button type="button" class="emt-toolbar-btn" @click="formatText('underline')" title="Underline">
                                <i class="bi bi-type-underline"></i>
                            </button>
                            <div class="emt-toolbar-separator"></div>
                            <button type="button" class="emt-toolbar-btn" @click="formatText('insertUnorderedList')" title="Bullet List">
                                <i class="bi bi-list-ul"></i>
                            </button>
                            <button type="button" class="emt-toolbar-btn" @click="formatText('insertOrderedList')" title="Numbered List">
                                <i class="bi bi-list-ol"></i>
                            </button>
                            <div class="emt-toolbar-separator"></div>
                            <button type="button" class="emt-toolbar-btn" @click="formatText('justifyLeft')" title="Align Left">
                                <i class="bi bi-text-left"></i>
                            </button>
                            <button type="button" class="emt-toolbar-btn" @click="formatText('justifyCenter')" title="Align Center">
                                <i class="bi bi-text-center"></i>
                            </button>
                        </div>
                        <div class="emt-editor-body"
                             contenteditable="true"
                             x-ref="emtEditor"
                             x-init="initEditor()"
                             @input="updateContent()"
                             @blur="saveSelection(); updateContent()"
                             @mouseup="saveSelection()"
                             @keyup="saveSelection()"
                             data-placeholder="Type your email template body here...">
                        </div>
                    </div>
                    @error('templateContent') <span style="color: #dc2626; font-size: 0.75rem;">{{ $message }}</span> @enderror

                    <!-- Placeholder Guide -->
                    <div class="emt-placeholder-guide">
                        <div class="emt-placeholder-title">
                            <i class="bi bi-info-circle"></i> Available Placeholders (click to insert)
                        </div>
                        <div class="emt-placeholder-grid">
                            @foreach(\App\Models\EmailTemplate::availablePlaceholders() as $key => $desc)
                                <div class="emt-placeholder-item" @click="insertPlaceholder('{{ $key }}')">
                                    <span class="emt-placeholder-key">{{ $key }}</span>
                                    <span class="emt-placeholder-desc">{{ $desc }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="emt-modal-footer">
                    <button class="emt-btn-cancel" wire:click="closeModal">Cancel</button>
                    <button class="emt-btn-save" wire:click="saveTemplate">
                        {{ $editingTemplateId ? 'Update Template' : 'Create Template' }}
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Delete Confirmation Modal -->
    @if($showDeleteConfirm)
        <div class="emt-modal-overlay" wire:click.self="cancelDelete">
            <div class="emt-confirm-modal">
                <div class="emt-confirm-icon">
                    <i class="bi bi-exclamation-triangle"></i>
                </div>
                <h3 class="emt-confirm-title">Delete Template</h3>
                <p class="emt-confirm-text">Are you sure you want to delete this template? This action cannot be undone.</p>
                <div class="emt-confirm-actions">
                    <button class="emt-btn-cancel" wire:click="cancelDelete">Cancel</button>
                    <button class="emt-btn-delete-confirm" wire:click="deleteTemplate">Delete</button>
                </div>
            </div>
        </div>
    @endif

    </div>
</x-filament::page>
