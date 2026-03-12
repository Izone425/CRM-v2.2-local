import { createBrowserRouter } from "react-router";
import { DashboardLayout } from "./components/DashboardLayout";
import { TicketingDashboard } from "./pages/TicketingDashboard";
import { ClientProfile } from "./pages/ClientProfile";
import { CompanyCRM } from "./pages/CompanyCRM";

export const router = createBrowserRouter([
  {
    path: "/",
    Component: DashboardLayout,
    children: [
      { index: true, Component: TicketingDashboard },
      { path: "client/:clientEmail", Component: ClientProfile },
      { path: "company/:companyId", Component: CompanyCRM },
    ],
  },
]);
