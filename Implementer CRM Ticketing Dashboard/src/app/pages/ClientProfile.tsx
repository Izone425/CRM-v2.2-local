import { useParams, useNavigate } from 'react-router';
import { Header } from '../components/Header';
import { mockClients } from '../data/mockClients';
import { mockTickets } from '../data/mockTickets';
import { ArrowLeft, User, Mail, Phone, Briefcase, ExternalLink, Eye } from 'lucide-react';
import { useState } from 'react';
import { TicketDetailView } from '../components/TicketDetailView';

export function ClientProfile() {
  const { clientEmail } = useParams();
  const navigate = useNavigate();
  const [selectedTicket, setSelectedTicket] = useState<any>(null);

  const client = mockClients.find(c => c.email === clientEmail);
  
  if (!client) {
    return (
      <div className="flex-1 flex flex-col">
        <Header />
        <div className="flex-1 flex items-center justify-center">
          <div className="text-center">
            <p className="text-gray-500">Client not found</p>
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

  // Get all tickets from this client
  const clientTickets = mockTickets.filter(ticket => 
    ticket.messages.some(msg => msg.senderName === client.name && msg.sender === 'client')
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

          {/* Client Details Card */}
          <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <div className="flex items-start justify-between mb-6">
              <div className="flex items-center gap-4">
                <div className="w-16 h-16 bg-gradient-to-br from-purple-500 to-blue-500 rounded-full flex items-center justify-center">
                  <User className="w-8 h-8 text-white" />
                </div>
                <div>
                  <h1 className="text-2xl font-semibold text-gray-900">{client.name}</h1>
                  <p className="text-gray-600">{client.position}</p>
                </div>
              </div>
              <button
                onClick={() => navigate(`/company/${client.companyId}`)}
                className="flex items-center gap-2 px-4 py-2 bg-purple-50 text-purple-700 border border-purple-200 rounded-lg hover:bg-purple-100 transition-colors"
              >
                <ExternalLink className="w-4 h-4" />
                View Company CRM
              </button>
            </div>

            <div className="grid grid-cols-3 gap-6">
              <div className="flex items-center gap-3">
                <div className="p-3 bg-blue-50 rounded-lg">
                  <Briefcase className="w-5 h-5 text-blue-600" />
                </div>
                <div>
                  <p className="text-xs text-gray-500">Company</p>
                  <p className="text-sm font-medium text-gray-900">{client.companyName}</p>
                </div>
              </div>
              <div className="flex items-center gap-3">
                <div className="p-3 bg-green-50 rounded-lg">
                  <Mail className="w-5 h-5 text-green-600" />
                </div>
                <div>
                  <p className="text-xs text-gray-500">Email</p>
                  <p className="text-sm font-medium text-gray-900">{client.email}</p>
                </div>
              </div>
              <div className="flex items-center gap-3">
                <div className="p-3 bg-orange-50 rounded-lg">
                  <Phone className="w-5 h-5 text-orange-600" />
                </div>
                <div>
                  <p className="text-xs text-gray-500">Phone</p>
                  <p className="text-sm font-medium text-gray-900">{client.phone}</p>
                </div>
              </div>
            </div>
          </div>

          {/* Tickets Raised by This User */}
          <div className="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div className="px-6 py-4 border-b border-gray-200">
              <h2 className="text-lg font-semibold text-gray-900">
                Tickets Raised by This User
                <span className="ml-2 text-sm text-gray-500">({clientTickets.length} total)</span>
              </h2>
            </div>

            <div className="overflow-x-auto">
              <table className="w-full">
                <thead className="bg-gray-50 border-b border-gray-200">
                  <tr>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ticket ID</th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category & Module</th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Priority</th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Created</th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-gray-200">
                  {clientTickets.length === 0 ? (
                    <tr>
                      <td colSpan={6} className="px-6 py-8 text-center text-gray-500">
                        No tickets found for this user
                      </td>
                    </tr>
                  ) : (
                    clientTickets.map((ticket) => (
                      <tr key={ticket.id} className="hover:bg-gray-50 transition-colors">
                        <td className="px-6 py-4 whitespace-nowrap">
                          <span className="font-medium text-gray-900">{ticket.ticketId}</span>
                        </td>
                        <td className="px-6 py-4">
                          <div className="text-sm">
                            <div className="text-gray-900 font-medium">{ticket.category}</div>
                            <div className="text-gray-500">{ticket.module}</div>
                          </div>
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
                        <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                          {new Date(ticket.createdAt).toLocaleDateString()}
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
