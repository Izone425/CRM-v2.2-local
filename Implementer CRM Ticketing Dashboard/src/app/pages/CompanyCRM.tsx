import { useParams, useNavigate } from 'react-router';
import { Header } from '../components/Header';
import { mockClients } from '../data/mockClients';
import { mockTickets } from '../data/mockTickets';
import { ArrowLeft, Building2, Eye } from 'lucide-react';
import { useState } from 'react';
import { TicketDetailView } from '../components/TicketDetailView';

export function CompanyCRM() {
  const { companyId } = useParams();
  const navigate = useNavigate();
  const [activeTab, setActiveTab] = useState('ticketing');
  const [selectedTicket, setSelectedTicket] = useState<any>(null);

  // Get company info from first matching client
  const companyClients = mockClients.filter(c => c.companyId === companyId);
  const company = companyClients[0];

  if (!company) {
    return (
      <div className="flex-1 flex flex-col">
        <Header />
        <div className="flex-1 flex items-center justify-center">
          <div className="text-center">
            <p className="text-gray-500">Company not found</p>
            <button
              onClick={() => navigate('/')}
              className="mt-4 text-purple-600 hover:text-purple-700"
            >
              Back to Dashboard
            </button>
          </div>
        </div>
      </div>
    );
  }

  // Get all tickets from this company's PICs
  const companyPICNames = companyClients.map(c => c.name);
  const companyTickets = mockTickets.filter(ticket => 
    ticket.companyName === company.companyName
  );

  const getStatusColor = (status: string) => {
    switch (status) {
      case 'Open':
        return 'bg-blue-100 text-blue-700 border-blue-200';
      case 'Pending Implementer':
        return 'bg-purple-100 text-purple-700 border-purple-200';
      case 'Pending R&D':
        return 'bg-pink-100 text-pink-700 border-pink-200';
      case 'Resolved':
        return 'bg-green-100 text-green-700 border-green-200';
      default:
        return 'bg-gray-100 text-gray-700 border-gray-200';
    }
  };

  return (
    <div className="flex-1 flex flex-col overflow-hidden">
      <Header />
      
      <div className="flex-1 overflow-y-auto">
        <div className="p-8">
          {/* Back Button */}
          <button
            onClick={() => navigate('/')}
            className="flex items-center gap-2 text-gray-600 hover:text-gray-900 mb-6 transition-colors"
          >
            <ArrowLeft className="w-4 h-4" />
            <span className="text-sm font-medium">Back to Ticketing Dashboard</span>
          </button>

          {/* Company Header */}
          <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <div className="flex items-center gap-4 mb-4">
              <div className="w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-500 rounded-lg flex items-center justify-center">
                <Building2 className="w-8 h-8 text-white" />
              </div>
              <div>
                <h1 className="text-2xl font-semibold text-gray-900">{company.companyName}</h1>
                <p className="text-gray-600">Company CRM Profile</p>
              </div>
            </div>

            {/* Tabs */}
            <div className="border-b border-gray-200">
              <nav className="-mb-px flex gap-6">
                <button
                  onClick={() => setActiveTab('overview')}
                  className={`pb-3 px-1 border-b-2 font-medium text-sm transition-colors ${
                    activeTab === 'overview'
                      ? 'border-purple-500 text-purple-600'
                      : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                  }`}
                >
                  Overview
                </button>
                <button
                  onClick={() => setActiveTab('software')}
                  className={`pb-3 px-1 border-b-2 font-medium text-sm transition-colors ${
                    activeTab === 'software'
                      ? 'border-purple-500 text-purple-600'
                      : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                  }`}
                >
                  Software
                </button>
                <button
                  onClick={() => setActiveTab('hardware')}
                  className={`pb-3 px-1 border-b-2 font-medium text-sm transition-colors ${
                    activeTab === 'hardware'
                      ? 'border-purple-500 text-purple-600'
                      : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                  }`}
                >
                  Hardware
                </button>
                <button
                  onClick={() => setActiveTab('pic')}
                  className={`pb-3 px-1 border-b-2 font-medium text-sm transition-colors ${
                    activeTab === 'pic'
                      ? 'border-purple-500 text-purple-600'
                      : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                  }`}
                >
                  PIC Details
                </button>
                <button
                  onClick={() => setActiveTab('ticketing')}
                  className={`pb-3 px-1 border-b-2 font-medium text-sm transition-colors ${
                    activeTab === 'ticketing'
                      ? 'border-purple-500 text-purple-600'
                      : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                  }`}
                >
                  Ticketing
                  <span className="ml-2 px-2 py-0.5 rounded-full bg-purple-100 text-purple-600 text-xs">
                    {companyTickets.length}
                  </span>
                </button>
              </nav>
            </div>
          </div>

          {/* Tab Content */}
          <div className="bg-white rounded-xl shadow-sm border border-gray-200">
            {activeTab === 'ticketing' ? (
              <div className="overflow-hidden">
                <div className="px-6 py-4 border-b border-gray-200">
                  <h2 className="text-lg font-semibold text-gray-900">
                    All Tickets from {company.companyName}
                  </h2>
                  <p className="text-sm text-gray-500 mt-1">
                    Tickets raised by any PIC associated with this company
                  </p>
                </div>

                <div className="overflow-x-auto">
                  <table className="w-full">
                    <thead className="bg-gray-50 border-b border-gray-200">
                      <tr>
                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ticket ID</th>
                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subject</th>
                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Module</th>
                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Priority</th>
                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Raised By</th>
                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                      </tr>
                    </thead>
                    <tbody className="divide-y divide-gray-200">
                      {companyTickets.length === 0 ? (
                        <tr>
                          <td colSpan={7} className="px-6 py-8 text-center text-gray-500">
                            No tickets found for this company
                          </td>
                        </tr>
                      ) : (
                        companyTickets.map((ticket) => (
                          <tr key={ticket.id} className="hover:bg-gray-50 transition-colors">
                            <td className="px-6 py-4 whitespace-nowrap">
                              <span className="font-medium text-gray-900">{ticket.ticketId}</span>
                            </td>
                            <td className="px-6 py-4">
                              <span className="text-sm text-gray-900">{ticket.category}</span>
                            </td>
                            <td className="px-6 py-4">
                              <span className="text-sm text-gray-600">{ticket.module}</span>
                            </td>
                            <td className="px-6 py-4 whitespace-nowrap">
                              <span className={`inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium border ${getStatusColor(ticket.status)}`}>
                                {ticket.status}
                              </span>
                            </td>
                            <td className="px-6 py-4 whitespace-nowrap">
                              <span className={`text-sm font-medium ${
                                ticket.priority === 'High' ? 'text-red-600' :
                                ticket.priority === 'Medium' ? 'text-orange-600' : 'text-blue-600'
                              }`}>
                                {ticket.priority}
                              </span>
                            </td>
                            <td className="px-6 py-4 whitespace-nowrap">
                              <span className="text-sm text-gray-900">
                                {ticket.messages[0]?.senderName || 'Unknown'}
                              </span>
                            </td>
                            <td className="px-6 py-4 whitespace-nowrap">
                              <button
                                onClick={() => setSelectedTicket(ticket)}
                                className="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                title="View"
                              >
                                <Eye className="w-4 h-4" />
                              </button>
                            </td>
                          </tr>
                        ))
                      )}
                    </tbody>
                  </table>
                </div>
              </div>
            ) : (
              <div className="px-6 py-12 text-center text-gray-500">
                <p>
                  {activeTab === 'overview' && 'Company overview information would be displayed here'}
                  {activeTab === 'software' && 'Software licenses and subscriptions would be displayed here'}
                  {activeTab === 'hardware' && 'Hardware inventory would be displayed here'}
                  {activeTab === 'pic' && 'Person-in-Charge contact details would be displayed here'}
                </p>
              </div>
            )}
          </div>
        </div>
      </div>

      {/* Ticket Detail Modal */}
      {selectedTicket && (
        <TicketDetailView
          ticket={selectedTicket}
          onClose={() => setSelectedTicket(null)}
        />
      )}
    </div>
  );
}
