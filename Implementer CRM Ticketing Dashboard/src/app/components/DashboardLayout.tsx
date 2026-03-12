import { Outlet } from 'react-router';
import { Sidebar } from './Sidebar';

export function DashboardLayout() {
  return (
    <div className="flex h-screen" style={{ backgroundColor: '#F8FAFC' }}>
      <Sidebar activeItem="tickets" />
      <div className="flex-1 flex flex-col overflow-hidden">
        <Outlet />
      </div>
    </div>
  );
}
