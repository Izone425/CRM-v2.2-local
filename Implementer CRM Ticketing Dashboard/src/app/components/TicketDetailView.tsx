import { X, Clock, User, Calendar, Mail, Plus, Send, ChevronDown, Paperclip, Bold, Italic, Link2 } from 'lucide-react';
import { Ticket } from '../data/mockTickets';
import { useState, useEffect } from 'react';

interface TicketDetailViewProps {
  ticket: Ticket;
  onClose: () => void;
}

export function TicketDetailView({ ticket, onClose }: TicketDetailViewProps) {
  const [replyText, setReplyText] = useState('');
  const [internalComment, setInternalComment] = useState('');
  const [showInternalComment, setShowInternalComment] = useState(false);
  const [ticketStatus, setTicketStatus] = useState(ticket.status);
  const [showStatusDropdown, setShowStatusDropdown] = useState(false);
  const [toEmail, setToEmail] = useState(ticket.messages[0]?.senderName || '');
  const [ccEmail, setCcEmail] = useState('');
  const [bccEmail, setBccEmail] = useState('');
  const [autoSaveStatus, setAutoSaveStatus] = useState<'saved' | 'saving' | 'idle'>('idle');
  const [lastSaved, setLastSaved] = useState<Date | null>(null);

  // Auto-save draft logic
  useEffect(() => {
    if (replyText.trim() === '') {
      setAutoSaveStatus('idle');
      return;
    }

    setAutoSaveStatus('saving');
    const timer = setTimeout(() => {
      // Simulate auto-save
      setAutoSaveStatus('saved');
      setLastSaved(new Date());
    }, 1500);

    return () => clearTimeout(timer);
  }, [replyText, toEmail, ccEmail, bccEmail]);

  const calculateTimeRemaining = (deadline: string) => {
    const now = new Date();
    const deadlineDate = new Date(deadline);
    const diff = deadlineDate.getTime() - now.getTime();
    const hours = Math.floor(diff / (1000 * 60 * 60));
    const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
    
    if (diff < 0) {
      return { text: 'Overdue', color: 'text-red-600 bg-red-50' };
    } else if (hours < 2) {
      return { text: `${hours}h ${minutes}m remaining`, color: 'text-orange-600 bg-orange-50' };
    } else {
      return { text: `${hours}h ${minutes}m remaining`, color: 'text-green-600 bg-green-50' };
    }
  };

  const formatTimestamp = (timestamp: string) => {
    const date = new Date(timestamp);
    return date.toLocaleString('en-US', {
      month: 'short',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    });
  };

  const formatDate = (timestamp: string) => {
    const date = new Date(timestamp);
    return date.toLocaleString('en-US', {
      month: 'short',
      day: 'numeric',
      year: 'numeric'
    });
  };

  const timeRemaining = calculateTimeRemaining(ticket.slaDeadline);

  const statusOptions = [
    { value: 'Open', color: 'text-blue-600' },
    { value: 'Awaiting Reply', color: 'text-yellow-600' },
    { value: 'Pending Implementer', color: 'text-purple-600' },
    { value: 'Pending R&D', color: 'text-pink-600' },
    { value: 'Resolved', color: 'text-green-600' },
  ];

  return (
    <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div className="bg-white rounded-xl shadow-2xl w-full max-w-7xl h-[90vh] overflow-hidden flex flex-col">
        {/* Top Header */}
        <div className="border-b border-gray-200 px-6 py-4">
          <div className="flex items-start justify-between mb-3">
            <div className="flex-1">
              <div className="flex items-center gap-3 mb-2">
                <h2 className="text-xl font-semibold text-gray-900">
                  Subject: {ticket.category} - {ticket.module}
                </h2>
                <span className="text-sm text-gray-500">{ticket.ticketId}</span>
              </div>
              <div className="flex items-center gap-4">
                <span className={`px-3 py-1 rounded-full text-sm font-medium ${timeRemaining.color}`}>
                  <Clock className="w-3 h-3 inline mr-1" />
                  {timeRemaining.text}
                </span>
                <span className="text-sm text-gray-600">{ticket.companyName}</span>
              </div>
            </div>
            <button
              onClick={onClose}
              className="p-2 hover:bg-gray-100 rounded-lg transition-colors"
            >
              <X className="w-5 h-5 text-gray-500" />
            </button>
          </div>

          {/* Action Bar */}
          <div className="flex items-center gap-3 mt-3">
            <button
              onClick={() => setShowInternalComment(!showInternalComment)}
              className="flex items-center gap-2 px-4 py-2 bg-yellow-50 text-yellow-700 border border-yellow-300 rounded-lg hover:bg-yellow-100 transition-colors"
            >
              <Plus className="w-4 h-4" />
              Add Internal Comment
            </button>
            <button className="flex items-center gap-2 px-4 py-2 bg-gray-50 text-gray-700 border border-gray-200 rounded-lg hover:bg-gray-100 transition-colors">
              <Paperclip className="w-4 h-4" />
              Attach File
            </button>
          </div>
        </div>

        {/* Main Content - Split View */}
        <div className="flex-1 flex overflow-hidden">
          {/* Left Sidebar - Ticket Properties */}
          <div className="w-80 border-r border-gray-200 bg-gray-50 p-6 overflow-y-auto">
            <h3 className="font-semibold text-gray-900 mb-4">Ticket Properties</h3>
            
            {/* PIC Contact Info */}
            <div className="mb-6">
              <label className="text-xs text-gray-500 uppercase mb-2 block">Client Contact</label>
              <div className="bg-white rounded-lg p-3 border border-gray-200">
                <div className="flex items-center gap-2 mb-2">
                  <div className="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                    <User className="w-4 h-4 text-blue-600" />
                  </div>
                  <div>
                    <p className="text-sm font-medium text-gray-900">
                      {ticket.messages[0]?.senderName || 'Unknown'}
                    </p>
                    <p className="text-xs text-gray-500">Primary Contact</p>
                  </div>
                </div>
                <div className="flex items-center gap-2 text-xs text-gray-600 mt-2">
                  <Mail className="w-3 h-3" />
                  <span>{ticket.messages[0]?.senderName.toLowerCase().replace(' ', '.')}@{ticket.companyName.toLowerCase().replace(/\s+/g, '')}.com</span>
                </div>
              </div>
            </div>

            {/* Ticket Owner */}
            <div className="mb-6">
              <label className="text-xs text-gray-500 uppercase mb-2 block">Ticket Owner</label>
              <div className="bg-white rounded-lg p-3 border border-gray-200">
                <div className="flex items-center gap-2">
                  <div className="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                    <User className="w-4 h-4 text-purple-600" />
                  </div>
                  <div>
                    <p className="text-sm font-medium text-gray-900">{ticket.assignee}</p>
                    <p className="text-xs text-gray-500">Implementer</p>
                  </div>
                </div>
              </div>
            </div>

            {/* Status Dropdown */}
            <div className="mb-6">
              <label className="text-xs text-gray-500 uppercase mb-2 block">Status</label>
              <div className="relative">
                <button
                  onClick={() => setShowStatusDropdown(!showStatusDropdown)}
                  className="w-full bg-white rounded-lg p-3 border border-gray-200 flex items-center justify-between hover:bg-gray-50 transition-colors"
                >
                  <span className="text-sm font-medium text-gray-900">{ticketStatus}</span>
                  <ChevronDown className="w-4 h-4 text-gray-500" />
                </button>
                
                {showStatusDropdown && (
                  <>
                    <div 
                      className="fixed inset-0 z-10" 
                      onClick={() => setShowStatusDropdown(false)}
                    />
                    <div className="absolute top-full left-0 right-0 mt-2 bg-white border border-gray-200 rounded-lg shadow-lg z-20 py-1">
                      {statusOptions.map((status) => (
                        <button
                          key={status.value}
                          onClick={() => {
                            setTicketStatus(status.value);
                            setShowStatusDropdown(false);
                          }}
                          className={`w-full text-left px-3 py-2 text-sm hover:bg-gray-50 transition-colors ${
                            ticketStatus === status.value ? 'bg-purple-50 font-medium' : ''
                          }`}
                        >
                          <span className={status.color}>{status.value}</span>
                        </button>
                      ))}
                    </div>
                  </>
                )}
              </div>
            </div>

            {/* Key Dates */}
            <div className="mb-6">
              <label className="text-xs text-gray-500 uppercase mb-2 block">Key Dates</label>
              <div className="bg-white rounded-lg border border-gray-200 divide-y divide-gray-100">
                <div className="p-3">
                  <div className="flex items-center gap-2 text-xs text-gray-500 mb-1">
                    <Calendar className="w-3 h-3" />
                    <span>Created</span>
                  </div>
                  <p className="text-sm text-gray-900">{formatDate(ticket.createdAt)}</p>
                </div>
                <div className="p-3">
                  <div className="flex items-center gap-2 text-xs text-gray-500 mb-1">
                    <Clock className="w-3 h-3" />
                    <span>SLA Deadline</span>
                  </div>
                  <p className="text-sm text-gray-900">{formatDate(ticket.slaDeadline)}</p>
                </div>
              </div>
            </div>

            {/* Priority & Category */}
            <div className="mb-6">
              <label className="text-xs text-gray-500 uppercase mb-2 block">Details</label>
              <div className="bg-white rounded-lg border border-gray-200 p-3 space-y-2">
                <div className="flex justify-between text-sm">
                  <span className="text-gray-500">Priority:</span>
                  <span className={`font-medium ${
                    ticket.priority === 'High' ? 'text-red-600' :
                    ticket.priority === 'Medium' ? 'text-orange-600' : 'text-blue-600'
                  }`}>{ticket.priority}</span>
                </div>
                <div className="flex justify-between text-sm">
                  <span className="text-gray-500">Category:</span>
                  <span className="text-gray-900">{ticket.category}</span>
                </div>
                <div className="flex justify-between text-sm">
                  <span className="text-gray-500">Module:</span>
                  <span className="text-gray-900">{ticket.module}</span>
                </div>
              </div>
            </div>
          </div>

          {/* Right Content - Thread & Reply */}
          <div className="flex-1 flex flex-col overflow-hidden">
            {/* Internal Comment Section */}
            {showInternalComment && (
              <div className="border-b border-yellow-300 bg-yellow-50 p-4">
                <div className="flex items-start gap-3">
                  <div className="flex-1">
                    <label className="text-sm font-medium text-yellow-900 mb-2 block">
                      Internal Comment (Private - Not visible to client)
                    </label>
                    <textarea
                      value={internalComment}
                      onChange={(e) => setInternalComment(e.target.value)}
                      className="w-full p-3 border-2 border-yellow-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-yellow-500 resize-none"
                      rows={3}
                      placeholder="Add internal notes, troubleshooting steps, or team coordination notes..."
                    />
                    <div className="flex gap-2 mt-2">
                      <button className="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors text-sm">
                        Save Internal Note
                      </button>
                      <button 
                        onClick={() => setShowInternalComment(false)}
                        className="px-4 py-2 bg-white text-gray-700 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors text-sm"
                      >
                        Cancel
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            )}

            {/* Conversation Thread */}
            <div className="flex-1 overflow-y-auto p-6 bg-gray-50">
              <div className="max-w-4xl mx-auto space-y-4">
                {/* Existing Messages */}
                {ticket.messages.map((message) => (
                  <div key={message.id} className="bg-white rounded-lg border border-gray-200 p-4 shadow-sm">
                    <div className="flex items-start gap-3 mb-3">
                      <div className={`w-10 h-10 rounded-full flex items-center justify-center ${
                        message.sender === 'client' ? 'bg-blue-100' : 'bg-purple-100'
                      }`}>
                        <User className={`w-5 h-5 ${
                          message.sender === 'client' ? 'text-blue-600' : 'text-purple-600'
                        }`} />
                      </div>
                      <div className="flex-1">
                        <div className="flex items-center gap-2 mb-1">
                          <span className="font-medium text-gray-900">{message.senderName}</span>
                          <span className={`text-xs px-2 py-0.5 rounded ${
                            message.sender === 'client'
                              ? 'bg-blue-100 text-blue-700'
                              : 'bg-purple-100 text-purple-700'
                          }`}>
                            {message.sender === 'client' ? 'Client' : 'HR Support'}
                          </span>
                          <span className="text-xs text-gray-500">{formatTimestamp(message.timestamp)}</span>
                        </div>
                        <p className="text-gray-700 leading-relaxed">{message.content}</p>
                      </div>
                    </div>
                  </div>
                ))}

                {/* Internal Comments */}
                {ticket.internalComments.map((comment) => (
                  <div key={comment.id} className="bg-yellow-50 rounded-lg border-2 border-yellow-300 p-4">
                    <div className="flex items-start gap-3">
                      <div className="w-10 h-10 rounded-full bg-yellow-200 flex items-center justify-center">
                        <User className="w-5 h-5 text-yellow-700" />
                      </div>
                      <div className="flex-1">
                        <div className="flex items-center gap-2 mb-1">
                          <span className="font-medium text-gray-900">{comment.author}</span>
                          <span className="text-xs bg-yellow-200 text-yellow-800 px-2 py-0.5 rounded">
                            Internal Only
                          </span>
                          <span className="text-xs text-gray-500">{formatTimestamp(comment.timestamp)}</span>
                        </div>
                        <p className="text-gray-700">{comment.content}</p>
                      </div>
                    </div>
                  </div>
                ))}
              </div>
            </div>

            {/* Reply Box */}
            <div className="border-t border-gray-200 bg-white p-6">
              <div className="max-w-4xl mx-auto">
                {/* Email Fields */}
                <div className="space-y-3 mb-4">
                  <div className="flex items-center gap-3">
                    <label className="text-sm text-gray-600 w-12">To:</label>
                    <input
                      type="text"
                      value={toEmail}
                      onChange={(e) => setToEmail(e.target.value)}
                      className="flex-1 px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500"
                      placeholder="recipient@email.com"
                    />
                  </div>
                  <div className="flex items-center gap-3">
                    <label className="text-sm text-gray-600 w-12">Cc:</label>
                    <input
                      type="text"
                      value={ccEmail}
                      onChange={(e) => setCcEmail(e.target.value)}
                      className="flex-1 px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500"
                      placeholder="cc@email.com (optional)"
                    />
                  </div>
                  <div className="flex items-center gap-3">
                    <label className="text-sm text-gray-600 w-12">Bcc:</label>
                    <input
                      type="text"
                      value={bccEmail}
                      onChange={(e) => setBccEmail(e.target.value)}
                      className="flex-1 px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500"
                      placeholder="bcc@email.com (optional)"
                    />
                  </div>
                </div>

                {/* Rich Text Editor */}
                <div className="border border-gray-300 rounded-lg overflow-hidden">
                  {/* Toolbar */}
                  <div className="bg-gray-50 border-b border-gray-300 px-3 py-2 flex items-center gap-2">
                    <button className="p-2 hover:bg-gray-200 rounded transition-colors" title="Bold">
                      <Bold className="w-4 h-4 text-gray-600" />
                    </button>
                    <button className="p-2 hover:bg-gray-200 rounded transition-colors" title="Italic">
                      <Italic className="w-4 h-4 text-gray-600" />
                    </button>
                    <button className="p-2 hover:bg-gray-200 rounded transition-colors" title="Insert Link">
                      <Link2 className="w-4 h-4 text-gray-600" />
                    </button>
                    <div className="border-l border-gray-300 h-6 mx-2" />
                    <button className="p-2 hover:bg-gray-200 rounded transition-colors" title="Attach File">
                      <Paperclip className="w-4 h-4 text-gray-600" />
                    </button>
                  </div>
                  
                  {/* Text Area */}
                  <textarea
                    value={replyText}
                    onChange={(e) => setReplyText(e.target.value)}
                    className="w-full p-4 focus:outline-none resize-none"
                    rows={6}
                    placeholder="Type your response to the client here..."
                  />
                  
                  {/* Action Bar */}
                  <div className="p-3 bg-gray-50 flex justify-between items-center">
                    <div className="flex items-center gap-4">
                      <button className="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors text-sm">
                        Save Draft
                      </button>
                      {/* Auto-save indicator */}
                      {autoSaveStatus !== 'idle' && (
                        <div className="flex items-center gap-2 text-xs text-gray-500">
                          {autoSaveStatus === 'saving' && (
                            <>
                              <div className="w-3 h-3 border-2 border-gray-400 border-t-transparent rounded-full animate-spin" />
                              <span>Saving draft...</span>
                            </>
                          )}
                          {autoSaveStatus === 'saved' && lastSaved && (
                            <>
                              <div className="w-2 h-2 bg-green-500 rounded-full" />
                              <span>Draft saved at {lastSaved.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}</span>
                            </>
                          )}
                        </div>
                      )}
                    </div>
                    <div className="flex gap-2">
                      <div className="relative">
                        <button className="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors flex items-center gap-2">
                          <Send className="w-4 h-4" />
                          Send & Update Status
                          <ChevronDown className="w-4 h-4" />
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}