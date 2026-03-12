import { useState } from 'react';
import { Header } from '../components/Header';
import { StatsCard } from '../components/StatsCard';
import { SLAPerformanceCard } from '../components/SLAPerformanceCard';
import { TicketsTable } from '../components/TicketsTable';
import { TicketDetailView } from '../components/TicketDetailView';
import { SLAPolicyModal } from '../components/SLAPolicyModal';
import { CreateTicketModal } from '../components/CreateTicketModal';
import { mockTickets, Ticket } from '../data/mockTickets';
import { 
  Ticket as TicketIcon, 
  Clock, 
  AlertTriangle, 
  Users,
  Plus 
} from 'lucide-react';

export function TicketingDashboard() {
  const [selectedTicket, setSelectedTicket] = useState<Ticket | null>(null);
  const [activeTab, setActiveTab] = useState('pending');
  const [statusFilter, setStatusFilter] = useState<string | null>(null);
  const [showSLAPolicy, setShowSLAPolicy] = useState(false);
  const [showCreateTicket, setShowCreateTicket] = useState(false);

  // Calculate stats
  const openTickets = mockTickets.filter(t => t.status === 'Open').length;
  const pendingImplementer = mockTickets.filter(t => t.status === 'Pending Implementer').length;
  const pendingRD = mockTickets.filter(t => t.status === 'Pending R&D').length;
  const overdueTickets = mockTickets.filter(t => t.slaStatus === 'Overdue').length;

  // Filter tickets based on tab and status filter
  let filteredTickets = mockTickets;
  
  if (activeTab === 'pending') {
    filteredTickets = filteredTickets.filter(t => 
      t.status === 'Open' || t.status === 'Pending Implementer'
    );
  }

  if (statusFilter) {
    if (statusFilter === 'Overdue') {
      filteredTickets = filteredTickets.filter(t => t.slaStatus === 'Overdue');
    } else {
      filteredTickets = filteredTickets.filter(t => t.status === statusFilter);
    }
  }

  const handleStatusCardClick = (filter: string) => {
    if (statusFilter === filter) {
      // If clicking the same filter, clear it
      setStatusFilter(null);
    } else {
      setStatusFilter(filter);
      // Switch to "All Tickets" tab when filtering
      setActiveTab('all');
    }
  };

  const handleCreateTicket = (ticketData: any) => {
    console.log('New ticket created:', ticketData);
    // Here you would typically send the data to your backend
  };

  return (
    <>
      <Header onSLAPolicyClick={() => setShowSLAPolicy(true)} />

      {/* Content Area */}
      <div className="flex-1 overflow-y-auto">
        <div className="p-8">
          {/* Top Actions Bar */}
          <div className="flex justify-between items-center mb-6">
            <div>
              <h1 className="text-2xl font-semibold text-gray-900">Ticket Management</h1>
              <p className="text-sm text-gray-600 mt-1">Manage and track all client support tickets</p>
            </div>
            <button
              onClick={() => setShowCreateTicket(true)}
              className="flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-purple-600 to-blue-600 text-white rounded-lg hover:from-purple-700 hover:to-blue-700 transition-all shadow-md hover:shadow-lg"
            >
              <Plus className="w-5 h-5" />
              Create New Ticket
            </button>
          </div>

          {/* Horizontal Status Row - Clickable Cards */}
          <div className="grid grid-cols-4 gap-4 mb-6">
            <button
              onClick={() => handleStatusCardClick('Open')}
              className={`text-left transition-all ${
                statusFilter === 'Open' ? 'ring-2 ring-purple-500 scale-105' : 'hover:scale-105'
              }`}
            >
              <StatsCard
                title="Open Tickets"
                value={openTickets}
                borderColor={`border-l-4 border-purple-500 ${statusFilter === 'Open' ? 'shadow-lg' : ''}`}
                icon={<TicketIcon className="w-7 h-7 text-purple-500" />}
              />
            </button>
            <button
              onClick={() => handleStatusCardClick('Pending Implementer')}
              className={`text-left transition-all ${
                statusFilter === 'Pending Implementer' ? 'ring-2 ring-blue-500 scale-105' : 'hover:scale-105'
              }`}
            >
              <StatsCard
                title="Pending Implementer"
                value={pendingImplementer}
                borderColor={`border-l-4 border-blue-500 ${statusFilter === 'Pending Implementer' ? 'shadow-lg' : ''}`}
                icon={<Users className="w-7 h-7 text-blue-500" />}
              />
            </button>
            <button
              onClick={() => handleStatusCardClick('Pending R&D')}
              className={`text-left transition-all ${
                statusFilter === 'Pending R&D' ? 'ring-2 ring-pink-500 scale-105' : 'hover:scale-105'
              }`}
            >
              <StatsCard
                title="Pending R&D"
                value={pendingRD}
                borderColor={`border-l-4 border-pink-500 ${statusFilter === 'Pending R&D' ? 'shadow-lg' : ''}`}
                icon={<Clock className="w-7 h-7 text-pink-500" />}
              />
            </button>
            <button
              onClick={() => handleStatusCardClick('Overdue')}
              className={`text-left transition-all ${
                statusFilter === 'Overdue' ? 'ring-2 ring-red-500 scale-105' : 'hover:scale-105'
              }`}
            >
              <StatsCard
                title="Overdue Tickets"
                value={overdueTickets}
                borderColor={`border-l-4 border-red-500 ${statusFilter === 'Overdue' ? 'shadow-lg' : ''}`}
                icon={<AlertTriangle className="w-7 h-7 text-red-500" />}
              />
            </button>
          </div>

          {/* Active Filter Indicator */}
          {statusFilter && (
            <div className="mb-6 flex items-center gap-3">
              <span className="text-sm text-gray-600">Filtered by:</span>
              <span className="px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-sm font-medium">
                {statusFilter}
              </span>
              <button
                onClick={() => setStatusFilter(null)}
                className="text-sm text-purple-600 hover:text-purple-700 font-medium"
              >
                Clear filter
              </button>
            </div>
          )}

          {/* SLA Performance Panel */}
          <div className="mb-8">
            <SLAPerformanceCard />
          </div>

          {/* Tabs */}
          <div className="mb-6">
            <div className="border-b border-gray-200">
              <nav className="-mb-px flex gap-6">
                <button
                  onClick={() => {
                    setActiveTab('pending');
                    setStatusFilter(null);
                  }}
                  className={`pb-4 px-1 border-b-2 font-medium text-sm transition-colors ${
                    activeTab === 'pending'
                      ? 'border-purple-500 text-purple-600'
                      : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                  }`}
                >
                  Pending Client Actions
                  <span className="ml-2 px-2 py-0.5 rounded-full bg-purple-100 text-purple-600 text-xs">
                    {mockTickets.filter(t => t.status === 'Open' || t.status === 'Pending Implementer').length}
                  </span>
                </button>
                <button
                  onClick={() => setActiveTab('all')}
                  className={`pb-4 px-1 border-b-2 font-medium text-sm transition-colors ${
                    activeTab === 'all'
                      ? 'border-purple-500 text-purple-600'
                      : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                  }`}
                >
                  All Tickets
                  <span className="ml-2 px-2 py-0.5 rounded-full bg-gray-100 text-gray-600 text-xs">
                    {mockTickets.length}
                  </span>
                </button>
              </nav>
            </div>
          </div>

          {/* Tickets Table */}
          <TicketsTable 
            tickets={filteredTickets} 
            onViewTicket={setSelectedTicket}
          />
        </div>
      </div>

      {/* Ticket Detail Modal */}
      {selectedTicket && (
        <TicketDetailView
          ticket={selectedTicket}
          onClose={() => setSelectedTicket(null)}
        />
      )}

      {/* SLA Policy Modal */}
      <SLAPolicyModal
        isOpen={showSLAPolicy}
        onClose={() => setShowSLAPolicy(false)}
      />

      {/* Create Ticket Modal */}
      <CreateTicketModal
        isOpen={showCreateTicket}
        onClose={() => setShowCreateTicket(false)}
        onSubmit={handleCreateTicket}
      />
    </>
  );
}