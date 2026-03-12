interface StatsCardProps {
  title: string;
  value: number;
  borderColor: string;
  icon?: React.ReactNode;
}

export function StatsCard({ title, value, borderColor, icon }: StatsCardProps) {
  return (
    <div className={`bg-white rounded-xl shadow-sm border ${borderColor} p-5 hover:shadow-md transition-shadow`}>
      <div className="flex items-center justify-between">
        <div className="flex-1">
          <p className="text-xs text-gray-500 mb-1">{title}</p>
          <p className="text-2xl font-semibold text-gray-900">{value}</p>
        </div>
        {icon && (
          <div className="ml-4">
            {icon}
          </div>
        )}
      </div>
    </div>
  );
}