import { 
  LayoutDashboard, 
  Users, 
  ClipboardList, 
  Bell, 
  Ticket,
  Calendar,
  Settings
} from 'lucide-react';

interface SidebarProps {
  activeItem?: string;
  onItemClick?: (item: string) => void;
}

export function Sidebar({ activeItem = 'tickets', onItemClick }: SidebarProps) {
  const menuItems = [
    { id: 'dashboard', icon: LayoutDashboard, color: 'text-purple-500', label: 'Dashboard' },
    { id: 'clients', icon: Users, color: 'text-blue-500', label: 'Clients' },
    { id: 'projects', icon: ClipboardList, color: 'text-green-500', label: 'Projects' },
    { id: 'reminders', icon: Bell, color: 'text-orange-500', label: 'Follow Up Reminder' },
    { id: 'tickets', icon: Ticket, color: 'text-pink-500', label: 'Client Ticketing' },
    { id: 'calendar', icon: Calendar, color: 'text-blue-400', label: 'Calendar' },
    { id: 'settings', icon: Settings, color: 'text-gray-400', label: 'Settings' },
  ];

  return (
    <div className="w-20 bg-white border-r border-gray-200 flex flex-col items-center py-6 gap-6 shadow-sm">
      {/* Logo */}
      <div className="w-10 h-10 bg-gradient-to-br from-purple-500 to-pink-500 rounded-lg flex items-center justify-center mb-4">
        <span className="text-white font-bold text-sm">HR</span>
      </div>

      {/* Menu Items */}
      {menuItems.map((item) => {
        const Icon = item.icon;
        const isActive = activeItem === item.id;
        
        return (
          <button
            key={item.id}
            onClick={() => onItemClick?.(item.id)}
            className={`group relative w-12 h-12 rounded-xl flex items-center justify-center transition-all duration-200 ${
              isActive 
                ? 'bg-gray-50 shadow-md' 
                : 'hover:bg-gray-50'
            }`}
            title={item.label}
          >
            <Icon 
              className={`w-6 h-6 ${item.color} ${
                isActive ? 'scale-110' : 'group-hover:scale-110'
              } transition-transform duration-200`}
              strokeWidth={1.5}
            />
            
            {/* Active Indicator */}
            {isActive && (
              <div className={`absolute left-0 w-1 h-8 ${item.color.replace('text-', 'bg-')} rounded-r-full`} />
            )}
          </button>
        );
      })}
    </div>
  );
}
