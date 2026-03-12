import { X, Clock, Calendar, AlertCircle, CheckCircle } from 'lucide-react';

interface SLAPolicyModalProps {
  isOpen: boolean;
  onClose: () => void;
}

export function SLAPolicyModal({ isOpen, onClose }: SLAPolicyModalProps) {
  if (!isOpen) return null;

  return (
    <div className="fixed inset-0 bg-black bg-opacity-50 flex items-end sm:items-center justify-center z-50">
      {/* Drawer/Modal */}
      <div className="bg-white w-full sm:max-w-2xl sm:rounded-xl shadow-2xl max-h-[90vh] overflow-hidden flex flex-col">
        {/* Header */}
        <div className="border-b border-gray-200 px-6 py-4 flex items-center justify-between">
          <h2 className="text-xl font-semibold text-gray-900">SLA Policy & Response Logic</h2>
          <button
            onClick={onClose}
            className="p-2 hover:bg-gray-100 rounded-lg transition-colors"
          >
            <X className="w-5 h-5 text-gray-500" />
          </button>
        </div>

        {/* Content */}
        <div className="flex-1 overflow-y-auto p-6">
          {/* Overview */}
          <div className="mb-6">
            <h3 className="text-lg font-semibold text-gray-900 mb-3">Overview</h3>
            <p className="text-gray-600 leading-relaxed">
              Our SLA (Service Level Agreement) policy ensures timely and consistent support to all clients. 
              The system uses automated response logic based on priority levels and submission times.
            </p>
          </div>

          {/* Response Time Matrix */}
          <div className="mb-6">
            <h3 className="text-lg font-semibold text-gray-900 mb-3">Response Time by Priority</h3>
            <div className="bg-white rounded-lg border border-gray-200 overflow-hidden">
              <table className="w-full">
                <thead className="bg-gray-50">
                  <tr>
                    <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Priority</th>
                    <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">First Response</th>
                    <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Resolution Time</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-gray-200">
                  <tr>
                    <td className="px-4 py-3">
                      <span className="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">
                        High
                      </span>
                    </td>
                    <td className="px-4 py-3 text-sm text-gray-900">2 hours</td>
                    <td className="px-4 py-3 text-sm text-gray-900">Same day (by 23:59)</td>
                  </tr>
                  <tr>
                    <td className="px-4 py-3">
                      <span className="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-700">
                        Medium
                      </span>
                    </td>
                    <td className="px-4 py-3 text-sm text-gray-900">4 hours</td>
                    <td className="px-4 py-3 text-sm text-gray-900">Next day (by 12:00 PM)</td>
                  </tr>
                  <tr>
                    <td className="px-4 py-3">
                      <span className="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                        Low
                      </span>
                    </td>
                    <td className="px-4 py-3 text-sm text-gray-900">8 hours</td>
                    <td className="px-4 py-3 text-sm text-gray-900">Within 3 business days</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          {/* Automated Logic */}
          <div className="mb-6">
            <h3 className="text-lg font-semibold text-gray-900 mb-3">Automated Response Logic</h3>
            <div className="space-y-3">
              <div className="flex gap-3 p-4 bg-blue-50 rounded-lg border border-blue-200">
                <Clock className="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" />
                <div>
                  <p className="font-medium text-blue-900 mb-1">Same-Day Cutoff</p>
                  <p className="text-sm text-blue-700">
                    Tickets submitted before 3:00 PM are resolved by 23:59 the same day. 
                    Tickets after 3:00 PM are scheduled for next business day.
                  </p>
                </div>
              </div>

              <div className="flex gap-3 p-4 bg-purple-50 rounded-lg border border-purple-200">
                <Calendar className="w-5 h-5 text-purple-600 flex-shrink-0 mt-0.5" />
                <div>
                  <p className="font-medium text-purple-900 mb-1">Business Hours</p>
                  <p className="text-sm text-purple-700">
                    Monday - Friday: 8:00 AM - 6:00 PM (EST)
                    <br />
                    Weekend tickets are processed on the next business day.
                  </p>
                </div>
              </div>

              <div className="flex gap-3 p-4 bg-orange-50 rounded-lg border border-orange-200">
                <AlertCircle className="w-5 h-5 text-orange-600 flex-shrink-0 mt-0.5" />
                <div>
                  <p className="font-medium text-orange-900 mb-1">Escalation Policy</p>
                  <p className="text-sm text-orange-700">
                    If an implementer cannot resolve a ticket within the SLA window, 
                    it's automatically escalated to R&D with priority notification.
                  </p>
                </div>
              </div>

              <div className="flex gap-3 p-4 bg-green-50 rounded-lg border border-green-200">
                <CheckCircle className="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5" />
                <div>
                  <p className="font-medium text-green-900 mb-1">Auto-Resolution</p>
                  <p className="text-sm text-green-700">
                    If a client doesn't respond within 48 hours after a solution is provided, 
                    the ticket is automatically marked as resolved.
                  </p>
                </div>
              </div>
            </div>
          </div>

          {/* Performance Targets */}
          <div>
            <h3 className="text-lg font-semibold text-gray-900 mb-3">Performance Targets</h3>
            <div className="grid grid-cols-2 gap-4">
              <div className="p-4 bg-gray-50 rounded-lg border border-gray-200">
                <p className="text-sm text-gray-500 mb-1">SLA Compliance Target</p>
                <p className="text-2xl font-semibold text-gray-900">≥ 95%</p>
              </div>
              <div className="p-4 bg-gray-50 rounded-lg border border-gray-200">
                <p className="text-sm text-gray-500 mb-1">First Response Target</p>
                <p className="text-2xl font-semibold text-gray-900">≥ 98%</p>
              </div>
            </div>
          </div>
        </div>

        {/* Footer */}
        <div className="border-t border-gray-200 px-6 py-4 bg-gray-50">
          <button
            onClick={onClose}
            className="w-full sm:w-auto px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors"
          >
            Close
          </button>
        </div>
      </div>
    </div>
  );
}
