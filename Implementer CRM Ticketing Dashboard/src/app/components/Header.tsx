import { ChevronRight, ChevronDown, Search, User, FileText } from 'lucide-react';
import { useState } from 'react';

interface HeaderProps {
  onSLAPolicyClick?: () => void;
}

export function Header({ onSLAPolicyClick }: HeaderProps = {}) {
  const [selectedImplementer, setSelectedImplementer] = useState('All Implementers');
  const [dropdownOpen, setDropdownOpen] = useState(false);

  const implementers = [
    'All Implementers',
    'John Smith',
    'Sarah Johnson',
    'Michael Chen',
    'Emily Davis'
  ];

  return (
    <div className="bg-white border-b border-gray-200 px-8 py-4">
      <div className="flex items-center justify-between">
        {/* Breadcrumb */}
        <div className="flex items-center gap-2 text-sm">
          <span className="text-gray-500 hover:text-gray-700 cursor-pointer">CRM</span>
          <ChevronRight className="w-4 h-4 text-gray-400" />
          <span className="text-gray-900 font-medium">Client Ticketing</span>
        </div>

        {/* Right Side Actions */}
        <div className="flex items-center gap-4">
          {/* Search */}
          <div className="relative">
            <Search className="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" />
            <input
              type="text"
              placeholder="Search tickets..."
              className="pl-10 pr-4 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent w-64"
            />
          </div>

          {/* SLA Policy Button */}
          {onSLAPolicyClick && (
            <button
              onClick={onSLAPolicyClick}
              className="flex items-center gap-2 px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors text-sm font-medium"
            >
              <FileText className="w-4 h-4" />
              SLA Policy
            </button>
          )}

          {/* Implementer Filter */}
          <div className="relative">
            <button
              onClick={() => setDropdownOpen(!dropdownOpen)}
              className="flex items-center gap-2 px-4 py-2 border border-gray-200 rounded-lg text-sm hover:bg-gray-50 transition-colors"
            >
              <User className="w-4 h-4 text-gray-500" />
              <span className="text-gray-700">{selectedImplementer}</span>
              <ChevronDown className="w-4 h-4 text-gray-400" />
            </button>

            {/* Dropdown */}
            {dropdownOpen && (
              <>
                <div 
                  className="fixed inset-0 z-10" 
                  onClick={() => setDropdownOpen(false)}
                />
                <div className="absolute right-0 mt-2 w-56 bg-white border border-gray-200 rounded-lg shadow-lg z-20 py-2">
                  {implementers.map((impl) => (
                    <button
                      key={impl}
                      onClick={() => {
                        setSelectedImplementer(impl);
                        setDropdownOpen(false);
                      }}
                      className={`w-full text-left px-4 py-2 text-sm hover:bg-gray-50 transition-colors ${
                        selectedImplementer === impl ? 'text-purple-600 bg-purple-50' : 'text-gray-700'
                      }`}
                    >
                      {impl}
                    </button>
                  ))}
                </div>
              </>
            )}
          </div>
        </div>
      </div>
    </div>
  );
}