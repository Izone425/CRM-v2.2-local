import { Eye, MessageSquare, Plus } from 'lucide-react';
import { Ticket } from '../data/mockTickets';
import { useNavigate } from 'react-router';
import { mockClients } from '../data/mockClients';

interface TicketsTableProps {
  tickets: Ticket[];
  onViewTicket: (ticket: Ticket) => void;
}

export function TicketsTable({ tickets, onViewTicket }: TicketsTableProps) {
  const navigate = useNavigate();

  const getClientEmail = (senderName: string) => {
    const client = mockClients.find(c => c.name === senderName);
    return client?.email || null;
  };

  const handleClientClick = (ticket: Ticket) => {
    const senderName = ticket.messages[0]?.senderName;
    if (senderName) {
      const clientEmail = getClientEmail(senderName);
      if (clientEmail) {
        navigate(`/client/${clientEmail}`);
      }
    }
  };

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

  const getSLAStatusColor = (slaStatus: string) => {
    switch (slaStatus) {
      case 'On Track':
        return 'text-green-600 bg-green-50';
      case 'At Risk':
        return 'text-orange-600 bg-orange-50';
      case 'Overdue':
        return 'text-red-600 bg-red-50';
      default:
        return 'text-gray-600 bg-gray-50';
    }
  };

  const getPriorityColor = (priority: string) => {
    switch (priority) {
      case 'High':
        return 'text-red-600';
      case 'Medium':
        return 'text-orange-600';
      case 'Low':
        return 'text-blue-600';
      default:
        return 'text-gray-600';
    }
  };

  const calculateTimeRemaining = (deadline: string) => {
    const now = new Date();
    const deadlineDate = new Date(deadline);
    const diff = deadlineDate.getTime() - now.getTime();
    const hours = Math.floor(diff / (1000 * 60 * 60));
    const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
    
    if (diff < 0) {
      return <span className="text-red-600 font-medium">Overdue</span>;
    } else if (hours < 2) {
      return <span className="text-orange-600 font-medium">{hours}h {minutes}m</span>;
    } else {
      return <span className="text-green-600 font-medium">{hours}h {minutes}m</span>;
    }
  };

  return (
    <div className="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
      <div className="overflow-x-auto">
        <table className="w-full">
          <thead className="bg-gray-50 border-b border-gray-200">
            <tr>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Ticket ID
              </th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Company Name
              </th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Category & Module
              </th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Implementer
              </th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                SLA Status
              </th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Status
              </th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Time Remaining
              </th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Actions
              </th>
            </tr>
          </thead>
          <tbody className="divide-y divide-gray-200">
            {tickets.map((ticket) => (
              <tr key={ticket.id} className="hover:bg-gray-50 transition-colors">
                <td className="px-6 py-4 whitespace-nowrap">
                  <div className="flex items-center gap-2">
                    <span className="font-medium text-gray-900">{ticket.ticketId}</span>
                    <span className={`text-xs font-medium ${getPriorityColor(ticket.priority)}`}>
                      {ticket.priority}
                    </span>
                  </div>
                </td>
                <td className="px-6 py-4 whitespace-nowrap">
                  <button
                    onClick={() => handleClientClick(ticket)}
                    className="text-sm text-blue-600 hover:text-blue-800 hover:underline cursor-pointer transition-colors"
                  >
                    {ticket.companyName}
                  </button>
                </td>
                <td className="px-6 py-4">
                  <div className="text-sm">
                    <div className="text-gray-900 font-medium">{ticket.category}</div>
                    <div className="text-gray-500">{ticket.module}</div>
                  </div>
                </td>
                <td className="px-6 py-4 whitespace-nowrap">
                  <div className="flex items-center gap-2">
                    <div className="w-8 h-8 bg-gradient-to-br from-purple-500 to-blue-500 rounded-full flex items-center justify-center">
                      <span className="text-white text-xs font-medium">
                        {ticket.assignee.split(' ').map(n => n[0]).join('')}
                      </span>
                    </div>
                    <span className="text-sm text-gray-900">{ticket.assignee}</span>
                  </div>
                </td>
                <td className="px-6 py-4 whitespace-nowrap">
                  <span className={`inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium ${getSLAStatusColor(ticket.slaStatus)}`}>
                    {ticket.slaStatus}
                  </span>
                </td>
                <td className="px-6 py-4 whitespace-nowrap">
                  <span className={`inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium border ${getStatusColor(ticket.status)}`}>
                    {ticket.status}
                  </span>
                </td>
                <td className="px-6 py-4 whitespace-nowrap text-sm">
                  {calculateTimeRemaining(ticket.slaDeadline)}
                </td>
                <td className="px-6 py-4 whitespace-nowrap">
                  <div className="flex items-center gap-2">
                    <button
                      onClick={() => onViewTicket(ticket)}
                      className="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                      title="View"
                    >
                      <Eye className="w-4 h-4" />
                    </button>
                    <button
                      className="p-2 text-purple-600 hover:bg-purple-50 rounded-lg transition-colors"
                      title="Reply"
                    >
                      <MessageSquare className="w-4 h-4" />
                    </button>
                    <button
                      className="p-2 text-orange-600 hover:bg-orange-50 rounded-lg transition-colors"
                      title="Add Comment"
                    >
                      <Plus className="w-4 h-4" />
                    </button>
                  </div>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  );
}