@php
    use App\Models\SoftwareHandoverProcessFile;

    $lead = $this->record;
    $user = auth()->user();
    $canUpload = $user && in_array($user->role_id, [1, 3, 4, 5]);

    $files = SoftwareHandoverProcessFile::where('lead_id', $lead->id)
        ->with('uploader')
        ->orderBy('version', 'desc')
        ->get();

    $filesJson = [];
    foreach ($files as $file) {
        $filesJson[] = [
            'id' => $file->id,
            'version' => $file->version,
            'file_name' => $file->file_name,
            'uploader_name' => $file->uploader?->name ?? 'Unknown',
            'uploader_email' => $file->uploader?->email ?? '',
            'remark' => $file->remark,
            'created_at' => $file->created_at->format('M d, Y'),
            'created_time' => $file->created_at->format('h:i A'),
            'download_url' => route('admin.software-handover-process-file.download', $file->id),
        ];
    }
@endphp

<style>
    .shp-container { max-width: 900px; }
    .shp-upload-card {
        background: #fff; border: 1px solid #e2e8f0; border-radius: 10px;
        padding: 20px; margin-bottom: 20px;
    }
    .shp-upload-title {
        font-size: 15px; font-weight: 700; color: #1e293b;
        margin-bottom: 16px; display: flex; align-items: center; gap: 8px;
    }
    .shp-upload-title i { color: #667eea; }
    .shp-form-group { margin-bottom: 14px; }
    .shp-label {
        display: block; font-size: 12px; font-weight: 600; color: #64748b;
        margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.3px;
    }
    .shp-file-input {
        width: 100%; padding: 10px 12px; border: 2px dashed #e2e8f0;
        border-radius: 8px; font-size: 13px; color: #475569;
        background: #f8fafc; cursor: pointer; transition: border-color 0.2s;
    }
    .shp-file-input:hover { border-color: #667eea; }
    .shp-textarea {
        width: 100%; min-height: 70px; padding: 10px 12px;
        border: 1px solid #e2e8f0; border-radius: 8px; font-size: 13px;
        color: #334155; resize: vertical; font-family: inherit;
    }
    .shp-textarea:focus {
        outline: none; border-color: #667eea;
        box-shadow: 0 0 0 2px rgba(102,126,234,0.15);
    }
    .shp-btn-upload {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 9px 20px; background: #667eea; color: #fff; border: none;
        border-radius: 8px; font-size: 13px; font-weight: 600;
        cursor: pointer; transition: background 0.2s;
    }
    .shp-btn-upload:hover { background: #5a6fd6; }
    .shp-btn-upload:disabled { opacity: 0.5; cursor: not-allowed; }
    .shp-hint { font-size: 11px; color: #94a3b8; margin-top: 4px; }
    .shp-alert {
        padding: 10px 14px; border-radius: 8px; font-size: 13px;
        font-weight: 500; margin-bottom: 16px; display: flex;
        align-items: center; gap: 8px;
    }
    .shp-alert-success { background: #dcfce7; color: #16a34a; }
    .shp-alert-error { background: #fef2f2; color: #dc2626; }
    .shp-history-card {
        background: #fff; border: 1px solid #e2e8f0;
        border-radius: 10px; overflow: hidden;
    }
    .shp-history-header {
        padding: 16px 20px; border-bottom: 1px solid #f1f5f9;
        display: flex; align-items: center; justify-content: space-between;
    }
    .shp-history-title {
        font-size: 15px; font-weight: 700; color: #1e293b;
        display: flex; align-items: center; gap: 8px;
    }
    .shp-history-title i { color: #667eea; }
    .shp-history-count {
        background: #eef2ff; color: #667eea; padding: 2px 10px;
        border-radius: 10px; font-size: 12px; font-weight: 600;
    }
    .shp-table { width: 100%; border-collapse: collapse; }
    .shp-table th {
        padding: 10px 16px; font-size: 11px; font-weight: 700; color: #94a3b8;
        text-transform: uppercase; letter-spacing: 0.5px; text-align: left;
        background: #f8fafc; border-bottom: 1px solid #e2e8f0;
    }
    .shp-table td {
        padding: 12px 16px; font-size: 13px; color: #334155;
        border-bottom: 1px solid #f1f5f9; vertical-align: middle;
    }
    .shp-table tr:last-child td { border-bottom: none; }
    .shp-table tr:hover td { background: #fafbff; }
    .shp-version-badge {
        display: inline-flex; align-items: center; justify-content: center;
        width: 28px; height: 28px; background: #eef2ff; color: #667eea;
        border-radius: 6px; font-size: 12px; font-weight: 700;
    }
    .shp-file-name { font-weight: 500; color: #1e293b; }
    .shp-meta { font-size: 11px; color: #94a3b8; margin-top: 2px; }
    .shp-btn-download {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 6px 12px; background: #eff6ff; color: #2563eb; border: none;
        border-radius: 6px; font-size: 12px; font-weight: 600;
        cursor: pointer; transition: background 0.2s; text-decoration: none;
    }
    .shp-btn-download:hover { background: #dbeafe; }
    .shp-empty {
        text-align: center; padding: 40px 20px; color: #94a3b8;
    }
    .shp-empty i {
        font-size: 40px; margin-bottom: 12px; display: block; color: #cbd5e1;
    }
    .shp-empty-text { font-size: 14px; font-weight: 500; color: #64748b; }
    .shp-empty-sub { font-size: 12px; margin-top: 4px; }
    .shp-remark-text {
        font-size: 12px; color: #64748b; font-style: italic;
        max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
    }
    .shp-error-text { color: #dc2626; font-size: 12px; margin-top: 4px; }
</style>

<div class="shp-container" x-data="shpTab()" x-cloak>
    {{-- Flash messages --}}
    <template x-if="successMsg">
        <div class="shp-alert shp-alert-success">
            <i class="fas fa-check-circle"></i>
            <span x-text="successMsg"></span>
        </div>
    </template>
    <template x-if="errorMsg">
        <div class="shp-alert shp-alert-error">
            <i class="fas fa-exclamation-circle"></i>
            <span x-text="errorMsg"></span>
        </div>
    </template>

    {{-- Upload Form (role-gated) --}}
    @if($canUpload)
        <div class="shp-upload-card">
            <div class="shp-upload-title">
                <i class="fas fa-cloud-upload-alt"></i>
                Upload Software Handover File
            </div>
            <div>
                <div class="shp-form-group">
                    <label class="shp-label">File</label>
                    <input type="file" x-ref="fileInput" @change="selectedFile = $event.target.files[0]"
                           class="shp-file-input" accept=".pdf,.doc,.docx,.xls,.xlsx">
                    <div class="shp-hint">Accepted: PDF, DOC, DOCX, XLS, XLSX (max 10MB)</div>
                    <template x-if="fileError"><div class="shp-error-text" x-text="fileError"></div></template>
                </div>
                <div class="shp-form-group">
                    <label class="shp-label">Remark (optional)</label>
                    <textarea x-model="remark" class="shp-textarea" placeholder="Add a note about this file..."></textarea>
                </div>
                <button type="button" class="shp-btn-upload" @click="uploadFile()" :disabled="uploading">
                    <template x-if="!uploading">
                        <i class="fas fa-upload"></i>
                    </template>
                    <template x-if="uploading">
                        <i class="fas fa-spinner fa-spin"></i>
                    </template>
                    <span x-text="uploading ? 'Uploading...' : 'Upload'"></span>
                </button>
            </div>
        </div>
    @endif

    {{-- Version History --}}
    <div class="shp-history-card">
        <div class="shp-history-header">
            <div class="shp-history-title">
                <i class="fas fa-history"></i>
                Version History
            </div>
            <span class="shp-history-count" x-text="files.length + ' file(s)'"></span>
        </div>

        <template x-if="files.length === 0">
            <div class="shp-empty">
                <i class="fas fa-folder-open"></i>
                <div class="shp-empty-text">No files uploaded yet</div>
                <div class="shp-empty-sub">Upload a software handover file to get started</div>
            </div>
        </template>

        <template x-if="files.length > 0">
            <table class="shp-table">
                <thead>
                    <tr>
                        <th>Ver</th>
                        <th>File Name</th>
                        <th>Uploaded By</th>
                        <th>Remark</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="file in files" :key="file.id">
                        <tr>
                            <td><span class="shp-version-badge" x-text="file.version"></span></td>
                            <td><div class="shp-file-name" x-text="file.file_name"></div></td>
                            <td>
                                <div x-text="file.uploader_name"></div>
                                <div class="shp-meta" x-text="file.uploader_email"></div>
                            </td>
                            <td>
                                <template x-if="file.remark">
                                    <div class="shp-remark-text" x-text="file.remark" :title="file.remark"></div>
                                </template>
                                <template x-if="!file.remark">
                                    <span style="color: #cbd5e1;">&mdash;</span>
                                </template>
                            </td>
                            <td>
                                <div x-text="file.created_at"></div>
                                <div class="shp-meta" x-text="file.created_time"></div>
                            </td>
                            <td>
                                <a :href="file.download_url" class="shp-btn-download">
                                    <i class="fas fa-download"></i> Download
                                </a>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </template>
    </div>
</div>

<script>
    function shpTab() {
        return {
            files: @json($filesJson),
            selectedFile: null,
            remark: '',
            uploading: false,
            successMsg: '',
            errorMsg: '',
            fileError: '',
            leadId: {{ $lead->id }},

            async uploadFile() {
                this.successMsg = '';
                this.errorMsg = '';
                this.fileError = '';

                if (!this.selectedFile) {
                    this.fileError = 'Please select a file.';
                    return;
                }

                const allowed = ['pdf', 'doc', 'docx', 'xls', 'xlsx'];
                const ext = this.selectedFile.name.split('.').pop().toLowerCase();
                if (!allowed.includes(ext)) {
                    this.fileError = 'File type not allowed. Accepted: PDF, DOC, DOCX, XLS, XLSX';
                    return;
                }

                if (this.selectedFile.size > 10 * 1024 * 1024) {
                    this.fileError = 'File size exceeds 10MB limit.';
                    return;
                }

                this.uploading = true;

                const formData = new FormData();
                formData.append('lead_id', this.leadId);
                formData.append('file', this.selectedFile);
                if (this.remark) formData.append('remark', this.remark);

                try {
                    const res = await fetch('{{ route("admin.software-handover-process-file.upload") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: formData,
                    });

                    const data = await res.json();

                    if (data.success) {
                        this.files.unshift(data.file);
                        this.successMsg = 'File uploaded successfully as version ' + data.file.version + '.';
                        this.selectedFile = null;
                        this.remark = '';
                        this.$refs.fileInput.value = '';
                    } else {
                        this.errorMsg = data.message || 'Upload failed.';
                    }
                } catch (e) {
                    console.error(e);
                    this.errorMsg = 'Upload failed. Please try again.';
                } finally {
                    this.uploading = false;
                }
            }
        };
    }
</script>
