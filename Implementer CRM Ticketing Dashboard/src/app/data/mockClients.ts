export interface Client {
  email: string;
  name: string;
  position: string;
  phone: string;
  companyId: string;
  companyName: string;
}

export const mockClients: Client[] = [
  {
    email: 'jane.cooper@acmecorporation.com',
    name: 'Jane Cooper',
    position: 'HR Director',
    phone: '+1 (555) 123-4567',
    companyId: 'acme-corp',
    companyName: 'Acme Corporation'
  },
  {
    email: 'mark.stevens@techstartinc.com',
    name: 'Mark Stevens',
    position: 'Chief People Officer',
    phone: '+1 (555) 234-5678',
    companyId: 'techstart-inc',
    companyName: 'TechStart Inc'
  },
  {
    email: 'lisa.wang@globaltechsolutions.com',
    name: 'Lisa Wang',
    position: 'IT Manager',
    phone: '+1 (555) 345-6789',
    companyId: 'globaltech',
    companyName: 'GlobalTech Solutions'
  },
  {
    email: 'robert.brown@innovatelabs.com',
    name: 'Robert Brown',
    position: 'HR Manager',
    phone: '+1 (555) 456-7890',
    companyId: 'innovate-labs',
    companyName: 'Innovate Labs'
  },
  {
    email: 'amanda.lee@futurecorp.com',
    name: 'Amanda Lee',
    position: 'Operations Manager',
    phone: '+1 (555) 567-8901',
    companyId: 'futurecorp',
    companyName: 'FutureCorp'
  },
];
