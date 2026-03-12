import { X, Upload, ChevronDown, Loader2, Bold, Italic, Link2, Paperclip, Search, CheckCircle, Send } from 'lucide-react';
import { useState, useEffect } from 'react';

interface CreateTicketModalProps {
  isOpen: boolean;
  onClose: () => void;
  onSubmit: (ticketData: any) => void;
}

// Predefined email templates
const emailTemplates = {
  'No Template': {
    subject: '',
    body: ''
  },
  'First Response': {
    subject: 'Re: Your Support Request - We\'re On It!',
    body: `Dear [Client Name],

Thank you for reaching out to our support team. We have received your request and our team is currently reviewing the details.

We aim to provide you with a comprehensive response within [X hours/days] as per our SLA agreement. If you have any additional information that might help us resolve this faster, please feel free to share it.

Best regards,
[Implementer Name]
Support Team`
  },
  'Require More Time': {
    subject: 'Update: Additional Time Required for Your Request',
    body: `Dear [Client Name],

We are writing to update you on the status of your support ticket [Ticket ID].

After thorough review, our team requires additional time to properly address your request. This will ensure we provide you with the best possible solution. We now estimate completion by [New Date/Time].

We appreciate your patience and understanding. Should you have any questions, please don't hesitate to reach out.

Best regards,
[Implementer Name]
Support Team`
  },
  'R&D Escalation': {
    subject: 'Escalation Notice: Your Ticket Requires R&D Investigation',
    body: `Dear [Client Name],

Thank you for your patience. Your support ticket [Ticket ID] has been escalated to our Research & Development team for further investigation.

This escalation allows our technical experts to conduct a deeper analysis and develop a comprehensive solution. We will keep you updated on the progress and provide an estimated resolution timeline shortly.

We appreciate your understanding as we work to resolve this matter.

Best regards,
[Implementer Name]
Support Team`
  }
};

export function CreateTicketModal({ isOpen, onClose, onSubmit }: CreateTicketModalProps) {
  const [formData, setFormData] = useState({
    company: '',
    category: '',
    module: '',
    status: 'Open',
    emailTemplate: 'No Template',
    emailSubject: '',
    emailBody: ''
  });

  const [attachments, setAttachments] = useState<File[]>([]);
  const [showCompanyDropdown, setShowCompanyDropdown] = useState(false);
  const [showCategoryDropdown, setShowCategoryDropdown] = useState(false);
  const [showModuleDropdown, setShowModuleDropdown] = useState(false);
  const [showStatusDropdown, setShowStatusDropdown] = useState(false);
  const [showTemplateDropdown, setShowTemplateDropdown] = useState(false);
  const [companySearch, setCompanySearch] = useState('');
  const [autoSaveStatus, setAutoSaveStatus] = useState<'saved' | 'saving' | 'idle'>('idle');
  const [isLoadingTemplate, setIsLoadingTemplate] = useState(false);

  const companies = [
    'Maperow Sdn Bhd',
    'Acme Corporation',
    'TechStart Inc',
    'GlobalTech Solutions',
    'Innovate Labs',
    'FutureCorp',
    'Digital Dynamics',
    'NextGen Industries'
  ];

  const categories = [
    'License Activation',
    'Data Migration',
    'Software Enquiries',
    'Session Enquiries',
    'Training Enquiries',
    'Enhancement/CR',
    'Add-on License',
    'Others'
  ];

  const modules = [
    'Profile',
    'Attendance',
    'Leave',
    'Claim',
    'Payroll',
    'Appraisal',
    'Hire'
  ];

  const statuses = [
    'Open',
    'Pending Support',
    'Pending Client',
    'Pending R&D',
    'Closed'
  ];

  const filteredCompanies = companies.filter(company =>
    company.toLowerCase().includes(companySearch.toLowerCase())
  );

  // Auto-close logic for specific categories
  useEffect(() => {
    if (formData.category === 'License Activation') {
      setFormData(prev => ({ ...prev, status: 'Closed' }));
    } else if (formData.status === 'Closed' && formData.category !== 'License Activation') {
      setFormData(prev => ({ ...prev, status: 'Open' }));
    }
  }, [formData.category]);

  // Email template loading
  const handleTemplateChange = (template: string) => {
    setFormData({ ...formData, emailTemplate: template });
    
    if (template !== 'No Template') {
      setIsLoadingTemplate(true);
      // Simulate loading animation
      setTimeout(() => {
        const selectedTemplate = emailTemplates[template as keyof typeof emailTemplates];
        setFormData(prev => ({
          ...prev,
          emailSubject: selectedTemplate.subject,
          emailBody: selectedTemplate.body
        }));
        setIsLoadingTemplate(false);
      }, 800);
    }
  };

  // Auto-save simulation
  useEffect(() => {
    if (formData.emailBody.trim() === '' && formData.emailSubject.trim() === '') {
      setAutoSaveStatus('idle');
      return;
    }

    setAutoSaveStatus('saving');
    const timer = setTimeout(() => {
      setAutoSaveStatus('saved');
    }, 1500);

    return () => clearTimeout(timer);
  }, [formData.emailSubject, formData.emailBody]);

  const handleFileChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    if (e.target.files) {
      setAttachments([...attachments, ...Array.from(e.target.files)]);
    }
  };

  const removeAttachment = (index: number) => {
    setAttachments(attachments.filter((_, i) => i !== index));
  };

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    onSubmit({ ...formData, attachments });
    onClose();
    // Reset form
    setFormData({
      company: '',
      category: '',
      module: '',
      status: 'Open',
      emailTemplate: 'No Template',
      emailSubject: '',
      emailBody: ''
    });
    setAttachments([]);
    setAutoSaveStatus('idle');
  };

  if (!isOpen) return null;

  return (
    <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div className="bg-white rounded-xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-hidden flex flex-col">
        {/* Header */}
        <div className="border-b border-gray-200 px-6 py-4 flex items-center justify-between bg-gradient-to-r from-purple-600 to-blue-600">
          <div>
            <h2 className="text-xl font-semibold text-white">Create New Ticket</h2>
            <p className="text-purple-100 text-sm mt-0.5">Create and send a support ticket to the client</p>
          </div>
          <button
            onClick={onClose}
            className="p-2 hover:bg-white/20 rounded-lg transition-colors"
          >
            <X className="w-5 h-5 text-white" />
          </button>
        </div>

        {/* Form Content */}
        <form onSubmit={handleSubmit} className="flex-1 overflow-y-auto p-6">
          <div className="space-y-6">
            {/* Company & Category Row */}
            <div className="grid grid-cols-2 gap-4">
              {/* Company Selection - Searchable */}
              <div className="relative">
                <label className="block text-sm font-medium text-gray-700 mb-2">
                  Company <span className="text-red-500">*</span>
                </label>
                <button
                  type="button"
                  onClick={() => setShowCompanyDropdown(!showCompanyDropdown)}
                  className="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-left flex items-center justify-between hover:bg-gray-50 transition-colors"
                >
                  <span className={formData.company ? 'text-gray-900' : 'text-gray-400'}>
                    {formData.company || 'Select company'}
                  </span>
                  <ChevronDown className="w-4 h-4 text-gray-400" />
                </button>
                
                {showCompanyDropdown && (
                  <>
                    <div 
                      className="fixed inset-0 z-10" 
                      onClick={() => setShowCompanyDropdown(false)}
                    />
                    <div className="absolute top-full left-0 right-0 mt-2 bg-white border border-gray-200 rounded-xl shadow-lg z-20 overflow-hidden">
                      {/* Search Input */}
                      <div className="p-3 border-b border-gray-200 bg-gray-50">
                        <div className="relative">
                          <Search className="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" />
                          <input
                            type="text"
                            value={companySearch}
                            onChange={(e) => setCompanySearch(e.target.value)}
                            placeholder="Search companies..."
                            className="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500"
                            onClick={(e) => e.stopPropagation()}
                          />
                        </div>
                      </div>
                      {/* Company List */}
                      <div className="max-h-60 overflow-y-auto py-2">
                        {filteredCompanies.length > 0 ? (
                          filteredCompanies.map((company) => (
                            <button
                              key={company}
                              type="button"
                              onClick={() => {
                                setFormData({ ...formData, company });
                                setShowCompanyDropdown(false);
                                setCompanySearch('');
                              }}
                              className="w-full text-left px-4 py-2.5 text-sm hover:bg-purple-50 transition-colors"
                            >
                              {company}
                            </button>
                          ))
                        ) : (
                          <div className="px-4 py-3 text-sm text-gray-500 text-center">
                            No companies found
                          </div>
                        )}
                      </div>
                    </div>
                  </>
                )}
              </div>

              {/* Category */}
              <div className="relative">
                <label className="block text-sm font-medium text-gray-700 mb-2">
                  Category <span className="text-red-500">*</span>
                </label>
                <button
                  type="button"
                  onClick={() => setShowCategoryDropdown(!showCategoryDropdown)}
                  className="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-left flex items-center justify-between hover:bg-gray-50 transition-colors"
                >
                  <span className={formData.category ? 'text-gray-900' : 'text-gray-400'}>
                    {formData.category || 'Select category'}
                  </span>
                  <ChevronDown className="w-4 h-4 text-gray-400" />
                </button>
                
                {showCategoryDropdown && (
                  <>
                    <div 
                      className="fixed inset-0 z-10" 
                      onClick={() => setShowCategoryDropdown(false)}
                    />
                    <div className="absolute top-full left-0 right-0 mt-2 bg-white border border-gray-200 rounded-xl shadow-lg z-20 py-2 max-h-60 overflow-y-auto">
                      {categories.map((cat) => (
                        <button
                          key={cat}
                          type="button"
                          onClick={() => {
                            setFormData({ ...formData, category: cat });
                            setShowCategoryDropdown(false);
                          }}
                          className="w-full text-left px-4 py-2.5 text-sm hover:bg-purple-50 transition-colors"
                        >
                          {cat}
                        </button>
                      ))}
                    </div>
                  </>
                )}
              </div>
            </div>

            {/* Module & Status Row */}
            <div className="grid grid-cols-2 gap-4">
              {/* Module */}
              <div className="relative">
                <label className="block text-sm font-medium text-gray-700 mb-2">
                  Module <span className="text-red-500">*</span>
                </label>
                <button
                  type="button"
                  onClick={() => setShowModuleDropdown(!showModuleDropdown)}
                  className="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-left flex items-center justify-between hover:bg-gray-50 transition-colors"
                >
                  <span className={formData.module ? 'text-gray-900' : 'text-gray-400'}>
                    {formData.module || 'Select module'}
                  </span>
                  <ChevronDown className="w-4 h-4 text-gray-400" />
                </button>
                
                {showModuleDropdown && (
                  <>
                    <div 
                      className="fixed inset-0 z-10" 
                      onClick={() => setShowModuleDropdown(false)}
                    />
                    <div className="absolute top-full left-0 right-0 mt-2 bg-white border border-gray-200 rounded-xl shadow-lg z-20 py-2">
                      {modules.map((module) => (
                        <button
                          key={module}
                          type="button"
                          onClick={() => {
                            setFormData({ ...formData, module });
                            setShowModuleDropdown(false);
                          }}
                          className="w-full text-left px-4 py-2.5 text-sm hover:bg-purple-50 transition-colors"
                        >
                          {module}
                        </button>
                      ))}
                    </div>
                  </>
                )}
              </div>

              {/* Status */}
              <div className="relative">
                <label className="block text-sm font-medium text-gray-700 mb-2">
                  Status <span className="text-red-500">*</span>
                </label>
                <button
                  type="button"
                  onClick={() => setShowStatusDropdown(!showStatusDropdown)}
                  className="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-left flex items-center justify-between hover:bg-gray-50 transition-colors"
                  disabled={formData.category === 'License Activation'}
                >
                  <span className="text-gray-900">{formData.status}</span>
                  <ChevronDown className="w-4 h-4 text-gray-400" />
                </button>
                
                {showStatusDropdown && (
                  <>
                    <div 
                      className="fixed inset-0 z-10" 
                      onClick={() => setShowStatusDropdown(false)}
                    />
                    <div className="absolute top-full left-0 right-0 mt-2 bg-white border border-gray-200 rounded-xl shadow-lg z-20 py-2">
                      {statuses.map((status) => (
                        <button
                          key={status}
                          type="button"
                          onClick={() => {
                            setFormData({ ...formData, status });
                            setShowStatusDropdown(false);
                          }}
                          className="w-full text-left px-4 py-2.5 text-sm hover:bg-purple-50 transition-colors"
                        >
                          {status}
                        </button>
                      ))}
                    </div>
                  </>
                )}
                
                {formData.category === 'License Activation' && (
                  <p className="text-xs text-green-600 mt-1.5 flex items-center gap-1">
                    <CheckCircle className="w-3 h-3" />
                    Auto-set to "Closed" for License Activation
                  </p>
                )}
              </div>
            </div>

            {/* Email Template Selection */}
            <div className="relative">
              <label className="block text-sm font-medium text-gray-700 mb-2">
                Email Template
                <span className="ml-2 text-xs text-purple-600 bg-purple-50 px-2 py-0.5 rounded-md">
                  Pre-fill subject & body
                </span>
              </label>
              <button
                type="button"
                onClick={() => setShowTemplateDropdown(!showTemplateDropdown)}
                className="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-left flex items-center justify-between hover:bg-gray-50 transition-colors"
              >
                <span className="text-gray-900">{formData.emailTemplate}</span>
                <ChevronDown className="w-4 h-4 text-gray-400" />
              </button>
              
              {showTemplateDropdown && (
                <>
                  <div 
                    className="fixed inset-0 z-10" 
                    onClick={() => setShowTemplateDropdown(false)}
                  />
                  <div className="absolute top-full left-0 right-0 mt-2 bg-white border border-gray-200 rounded-xl shadow-lg z-20 py-2">
                    {Object.keys(emailTemplates).map((template) => (
                      <button
                        key={template}
                        type="button"
                        onClick={() => {
                          handleTemplateChange(template);
                          setShowTemplateDropdown(false);
                        }}
                        className="w-full text-left px-4 py-2.5 text-sm hover:bg-purple-50 transition-colors"
                      >
                        {template}
                      </button>
                    ))}
                  </div>
                </>
              )}
            </div>

            {/* Loading Template Animation */}
            {isLoadingTemplate && (
              <div className="flex items-center gap-3 p-4 bg-blue-50 rounded-xl border border-blue-200">
                <Loader2 className="w-5 h-5 text-blue-600 animate-spin" />
                <span className="text-sm text-blue-700 font-medium">Loading email template...</span>
              </div>
            )}

            {/* Email Subject */}
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                Email Subject <span className="text-red-500">*</span>
              </label>
              <input
                type="text"
                required
                value={formData.emailSubject}
                onChange={(e) => setFormData({ ...formData, emailSubject: e.target.value })}
                className="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                placeholder="Enter email subject line"
              />
            </div>

            {/* Email Body - Rich Text Editor */}
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                Email Detail / Body <span className="text-red-500">*</span>
              </label>
              
              {/* Toolbar */}
              <div className="border border-gray-300 rounded-t-xl bg-gray-50 px-3 py-2 flex items-center gap-2">
                <button
                  type="button"
                  className="p-2 hover:bg-gray-200 rounded-lg transition-colors"
                  title="Bold"
                >
                  <Bold className="w-4 h-4 text-gray-600" />
                </button>
                <button
                  type="button"
                  className="p-2 hover:bg-gray-200 rounded-lg transition-colors"
                  title="Italic"
                >
                  <Italic className="w-4 h-4 text-gray-600" />
                </button>
                <button
                  type="button"
                  className="p-2 hover:bg-gray-200 rounded-lg transition-colors"
                  title="Link"
                >
                  <Link2 className="w-4 h-4 text-gray-600" />
                </button>
                <div className="w-px h-6 bg-gray-300 mx-1"></div>
                <button
                  type="button"
                  className="p-2 hover:bg-gray-200 rounded-lg transition-colors"
                  title="Attach"
                >
                  <Paperclip className="w-4 h-4 text-gray-600" />
                </button>
              </div>
              
              {/* Text Area */}
              <textarea
                required
                value={formData.emailBody}
                onChange={(e) => setFormData({ ...formData, emailBody: e.target.value })}
                className="w-full px-4 py-3 border border-t-0 border-gray-300 rounded-b-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent resize-none"
                rows={10}
                placeholder="Type your email message here..."
              />
            </div>

            {/* Attachments - Drag & Drop */}
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                Attachments
              </label>
              <div className="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-purple-400 transition-colors bg-gray-50">
                <input
                  type="file"
                  multiple
                  onChange={handleFileChange}
                  className="hidden"
                  id="file-upload"
                />
                <label
                  htmlFor="file-upload"
                  className="cursor-pointer flex flex-col items-center"
                >
                  <div className="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mb-3">
                    <Upload className="w-6 h-6 text-purple-600" />
                  </div>
                  <span className="text-sm font-medium text-gray-700">Click to upload or drag and drop</span>
                  <span className="text-xs text-gray-500 mt-1">PDF, PNG, JPG, XLSX up to 10MB each</span>
                </label>
              </div>
              
              {attachments.length > 0 && (
                <div className="mt-3 space-y-2">
                  {attachments.map((file, index) => (
                    <div key={index} className="flex items-center justify-between bg-white border border-gray-200 px-4 py-2.5 rounded-xl">
                      <div className="flex items-center gap-3">
                        <Paperclip className="w-4 h-4 text-gray-400" />
                        <span className="text-sm text-gray-700">{file.name}</span>
                        <span className="text-xs text-gray-400">
                          ({(file.size / 1024).toFixed(1)} KB)
                        </span>
                      </div>
                      <button
                        type="button"
                        onClick={() => removeAttachment(index)}
                        className="p-1 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                      >
                        <X className="w-4 h-4" />
                      </button>
                    </div>
                  ))}
                </div>
              )}
            </div>
          </div>
        </form>

        {/* Footer */}
        <div className="border-t border-gray-200 px-6 py-4 bg-gray-50 flex justify-between items-center">
          <div className="flex items-center gap-3">
            {/* Auto-save indicator */}
            {autoSaveStatus !== 'idle' && (
              <div className="flex items-center gap-2 text-xs">
                {autoSaveStatus === 'saving' && (
                  <>
                    <Loader2 className="w-3.5 h-3.5 text-gray-500 animate-spin" />
                    <span className="text-gray-600">Auto-saving...</span>
                  </>
                )}
                {autoSaveStatus === 'saved' && (
                  <>
                    <CheckCircle className="w-3.5 h-3.5 text-green-600" />
                    <span className="text-green-600 font-medium">Auto-save enabled</span>
                  </>
                )}
              </div>
            )}
          </div>
          
          <div className="flex gap-3">
            <button
              type="button"
              onClick={onClose}
              className="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-100 transition-colors font-medium"
            >
              Cancel
            </button>
            <button
              onClick={handleSubmit}
              className="px-6 py-2.5 bg-gradient-to-r from-purple-600 to-blue-600 text-white rounded-xl hover:from-purple-700 hover:to-blue-700 transition-all shadow-md hover:shadow-lg font-medium flex items-center gap-2"
            >
              <Send className="w-4 h-4" />
              Submit & Send Email
            </button>
          </div>
        </div>
      </div>
    </div>
  );
}