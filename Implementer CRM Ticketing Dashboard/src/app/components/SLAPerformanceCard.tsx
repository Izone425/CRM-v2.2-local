import { TrendingUp, Clock, CheckCircle2 } from 'lucide-react';

export function SLAPerformanceCard() {
  return (
    <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow cursor-pointer">
      <div className="flex items-center justify-between mb-6">
        <h3 className="text-lg font-semibold text-gray-900">SLA Performance</h3>
        <span className="text-xs text-gray-500">Click to view detailed reports</span>
      </div>
      
      <div className="grid grid-cols-3 gap-8">
        {/* Compliance Rate */}
        <div className="flex flex-col items-center text-center cursor-pointer hover:bg-gray-50 p-4 rounded-lg transition-colors">
          <div className="p-3 bg-green-50 rounded-full mb-3">
            <CheckCircle2 className="w-6 h-6 text-green-500" />
          </div>
          <p className="text-sm text-gray-500 mb-1">Compliance Rate</p>
          <p className="text-3xl font-semibold text-gray-900 mb-1">94.2%</p>
          <div className="flex items-center gap-1 text-green-600">
            <TrendingUp className="w-3 h-3" />
            <span className="text-xs font-medium">+2.3% this month</span>
          </div>
        </div>

        {/* Average Resolution Time */}
        <div className="flex flex-col items-center text-center cursor-pointer hover:bg-gray-50 p-4 rounded-lg transition-colors border-l border-r border-gray-100">
          <div className="p-3 bg-blue-50 rounded-full mb-3">
            <Clock className="w-6 h-6 text-blue-500" />
          </div>
          <p className="text-sm text-gray-500 mb-1">Avg Resolution Time</p>
          <p className="text-3xl font-semibold text-gray-900 mb-1">4.2 hrs</p>
          <div className="flex items-center gap-1 text-blue-600">
            <TrendingUp className="w-3 h-3" />
            <span className="text-xs font-medium">15% faster</span>
          </div>
        </div>

        {/* First Response Performance */}
        <div className="flex flex-col items-center text-center cursor-pointer hover:bg-gray-50 p-4 rounded-lg transition-colors">
          <div className="p-3 bg-purple-50 rounded-full mb-3">
            <TrendingUp className="w-6 h-6 text-purple-500" />
          </div>
          <p className="text-sm text-gray-500 mb-1">First Response Rate</p>
          <p className="text-3xl font-semibold text-gray-900 mb-1">96.8%</p>
          <div className="flex items-center gap-1 text-purple-600">
            <TrendingUp className="w-3 h-3" />
            <span className="text-xs font-medium">+1.5% improvement</span>
          </div>
        </div>
      </div>
    </div>
  );
}