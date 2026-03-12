export interface Ticket {
  id: string;
  ticketId: string;
  companyName: string;
  category: string;
  module: string;
  slaStatus: 'On Track' | 'Overdue' | 'At Risk';
  status: 'Open' | 'Pending Implementer' | 'Pending R&D' | 'Resolved';
  priority: 'High' | 'Medium' | 'Low';
  createdAt: string;
  slaDeadline: string;
  assignee: string;
  messages: Message[];
  internalComments: InternalComment[];
}

export interface Message {
  id: string;
  sender: 'client' | 'implementer';
  senderName: string;
  content: string;
  timestamp: string;
  attachments?: string[];
}

export interface InternalComment {
  id: string;
  author: string;
  content: string;
  timestamp: string;
}

export const mockTickets: Ticket[] = [
  {
    id: '1',
    ticketId: 'TKT-2024-001',
    companyName: 'Acme Corporation',
    category: 'Technical Issue',
    module: 'Payroll Processing',
    slaStatus: 'At Risk',
    status: 'Pending Implementer',
    priority: 'High',
    createdAt: '2024-03-02T08:30:00Z',
    slaDeadline: '2024-03-02T18:00:00Z',
    assignee: 'John Smith',
    messages: [
      {
        id: 'm1',
        sender: 'client',
        senderName: 'Jane Cooper',
        content: 'We are experiencing issues with payroll processing. Several employees\' salaries are not being calculated correctly. This is urgent as we need to process payroll by end of day.',
        timestamp: '2024-03-02T08:30:00Z'
      },
      {
        id: 'm2',
        sender: 'implementer',
        senderName: 'John Smith',
        content: 'Thank you for reporting this issue. I am investigating the payroll calculation module. Could you please provide the employee IDs that are affected?',
        timestamp: '2024-03-02T09:15:00Z'
      },
      {
        id: 'm3',
        sender: 'client',
        senderName: 'Jane Cooper',
        content: 'Sure, the affected employee IDs are: EMP-1234, EMP-5678, EMP-9012. All three are showing incorrect overtime calculations.',
        timestamp: '2024-03-02T09:45:00Z'
      }
    ],
    internalComments: [
      {
        id: 'ic1',
        author: 'John Smith',
        content: 'Found the issue - overtime rate configuration was changed in their last update. Working on fix now.',
        timestamp: '2024-03-02T10:00:00Z'
      }
    ]
  },
  {
    id: '2',
    ticketId: 'TKT-2024-002',
    companyName: 'TechStart Inc',
    category: 'Feature Request',
    module: 'Leave Management',
    slaStatus: 'On Track',
    status: 'Open',
    priority: 'Medium',
    createdAt: '2024-03-02T10:00:00Z',
    slaDeadline: '2024-03-03T12:00:00Z',
    assignee: 'Sarah Johnson',
    messages: [
      {
        id: 'm4',
        sender: 'client',
        senderName: 'Mark Stevens',
        content: 'We would like to request a new feature for our leave management system. Can we have the ability to set different leave quotas based on employee tenure?',
        timestamp: '2024-03-02T10:00:00Z'
      }
    ],
    internalComments: []
  },
  {
    id: '3',
    ticketId: 'TKT-2024-003',
    companyName: 'GlobalTech Solutions',
    category: 'Data Migration',
    module: 'Employee Database',
    slaStatus: 'Overdue',
    status: 'Pending R&D',
    priority: 'High',
    createdAt: '2024-03-01T14:00:00Z',
    slaDeadline: '2024-03-02T09:00:00Z',
    assignee: 'Michael Chen',
    messages: [
      {
        id: 'm5',
        sender: 'client',
        senderName: 'Lisa Wang',
        content: 'We need help migrating employee data from our legacy system. We have over 5000 employee records that need to be transferred.',
        timestamp: '2024-03-01T14:00:00Z'
      },
      {
        id: 'm6',
        sender: 'implementer',
        senderName: 'Michael Chen',
        content: 'I have escalated this to our R&D team for a proper data migration strategy. They will provide a migration plan within 24 hours.',
        timestamp: '2024-03-01T16:30:00Z'
      }
    ],
    internalComments: [
      {
        id: 'ic2',
        author: 'Michael Chen',
        content: 'Escalated to R&D - need custom migration script for their legacy format.',
        timestamp: '2024-03-01T16:30:00Z'
      }
    ]
  },
  {
    id: '4',
    ticketId: 'TKT-2024-004',
    companyName: 'Innovate Labs',
    category: 'User Training',
    module: 'Attendance System',
    slaStatus: 'On Track',
    status: 'Pending Implementer',
    priority: 'Low',
    createdAt: '2024-03-02T11:00:00Z',
    slaDeadline: '2024-03-03T12:00:00Z',
    assignee: 'Emily Davis',
    messages: [
      {
        id: 'm7',
        sender: 'client',
        senderName: 'Robert Brown',
        content: 'Our HR team needs training on the new attendance system features. Can you schedule a session?',
        timestamp: '2024-03-02T11:00:00Z'
      }
    ],
    internalComments: []
  },
  {
    id: '5',
    ticketId: 'TKT-2024-005',
    companyName: 'FutureCorp',
    category: 'Configuration',
    module: 'Performance Review',
    slaStatus: 'On Track',
    status: 'Open',
    priority: 'Medium',
    createdAt: '2024-03-02T09:00:00Z',
    slaDeadline: '2024-03-02T23:59:00Z',
    assignee: 'John Smith',
    messages: [
      {
        id: 'm8',
        sender: 'client',
        senderName: 'Amanda Lee',
        content: 'We need to configure custom performance review templates for different departments. Can you guide us through the process?',
        timestamp: '2024-03-02T09:00:00Z'
      }
    ],
    internalComments: []
  },
  {
    id: '6',
    ticketId: 'TKT-2024-006',
    companyName: 'Digital Dynamics',
    category: 'Bug Report',
    module: 'Time Tracking',
    slaStatus: 'On Track',
    status: 'Resolved',
    priority: 'Medium',
    createdAt: '2024-02-28T14:00:00Z',
    slaDeadline: '2024-02-29T12:00:00Z',
    assignee: 'Sarah Johnson',
    messages: [
      {
        id: 'm9',
        sender: 'client',
        senderName: 'Tom Harris',
        content: 'Time tracking is not saving properly for remote employees.',
        timestamp: '2024-02-28T14:00:00Z'
      },
      {
        id: 'm10',
        sender: 'implementer',
        senderName: 'Sarah Johnson',
        content: 'Issue has been fixed. Please test and confirm.',
        timestamp: '2024-02-28T16:30:00Z'
      }
    ],
    internalComments: []
  },
  {
    id: '7',
    ticketId: 'TKT-2024-007',
    companyName: 'Smart Solutions Inc',
    category: 'Access Issue',
    module: 'User Management',
    slaStatus: 'On Track',
    status: 'Resolved',
    priority: 'High',
    createdAt: '2024-02-27T10:00:00Z',
    slaDeadline: '2024-02-27T23:59:00Z',
    assignee: 'Michael Chen',
    messages: [
      {
        id: 'm11',
        sender: 'client',
        senderName: 'Rachel Green',
        content: 'New HR manager cannot access the system. Urgent!',
        timestamp: '2024-02-27T10:00:00Z'
      },
      {
        id: 'm12',
        sender: 'implementer',
        senderName: 'Michael Chen',
        content: 'Access granted. User can now log in with admin privileges.',
        timestamp: '2024-02-27T11:30:00Z'
      }
    ],
    internalComments: []
  },
  {
    id: '8',
    ticketId: 'TKT-2024-008',
    companyName: 'Enterprise Holdings',
    category: 'Report Issue',
    module: 'Analytics Dashboard',
    slaStatus: 'On Track',
    status: 'Resolved',
    priority: 'Low',
    createdAt: '2024-02-26T09:00:00Z',
    slaDeadline: '2024-02-27T12:00:00Z',
    assignee: 'Emily Davis',
    messages: [
      {
        id: 'm13',
        sender: 'client',
        senderName: 'David Miller',
        content: 'Quarterly reports are showing incorrect data for Q4.',
        timestamp: '2024-02-26T09:00:00Z'
      },
      {
        id: 'm14',
        sender: 'implementer',
        senderName: 'Emily Davis',
        content: 'Data has been recalculated and corrected.',
        timestamp: '2024-02-26T14:00:00Z'
      }
    ],
    internalComments: []
  },
  {
    id: '9',
    ticketId: 'TKT-2024-009',
    companyName: 'Nexus Technologies',
    category: 'Integration',
    module: 'Third-party API',
    slaStatus: 'On Track',
    status: 'Resolved',
    priority: 'Medium',
    createdAt: '2024-02-25T15:00:00Z',
    slaDeadline: '2024-02-26T12:00:00Z',
    assignee: 'John Smith',
    messages: [
      {
        id: 'm15',
        sender: 'client',
        senderName: 'Susan Parker',
        content: 'Need to integrate with our existing payroll provider API.',
        timestamp: '2024-02-25T15:00:00Z'
      },
      {
        id: 'm16',
        sender: 'implementer',
        senderName: 'John Smith',
        content: 'Integration completed and tested successfully.',
        timestamp: '2024-02-26T10:00:00Z'
      }
    ],
    internalComments: []
  },
  {
    id: '10',
    ticketId: 'TKT-2024-010',
    companyName: 'Metro Corp',
    category: 'Feature Request',
    module: 'Mobile App',
    slaStatus: 'On Track',
    status: 'Resolved',
    priority: 'Low',
    createdAt: '2024-02-24T11:00:00Z',
    slaDeadline: '2024-02-26T12:00:00Z',
    assignee: 'Sarah Johnson',
    messages: [
      {
        id: 'm17',
        sender: 'client',
        senderName: 'Chris Evans',
        content: 'Can we have dark mode in the mobile app?',
        timestamp: '2024-02-24T11:00:00Z'
      },
      {
        id: 'm18',
        sender: 'implementer',
        senderName: 'Sarah Johnson',
        content: 'Dark mode has been implemented and is now available.',
        timestamp: '2024-02-25T16:00:00Z'
      }
    ],
    internalComments: []
  },
  {
    id: '11',
    ticketId: 'TKT-2024-011',
    companyName: 'Alpha Industries',
    category: 'Cancellation',
    module: 'Contract Management',
    slaStatus: 'On Track',
    status: 'Resolved',
    priority: 'Low',
    createdAt: '2024-02-23T10:00:00Z',
    slaDeadline: '2024-02-24T12:00:00Z',
    assignee: 'Michael Chen',
    messages: [
      {
        id: 'm19',
        sender: 'client',
        senderName: 'Jennifer White',
        content: 'We need to cancel our contract due to business closure.',
        timestamp: '2024-02-23T10:00:00Z'
      },
      {
        id: 'm20',
        sender: 'implementer',
        senderName: 'Michael Chen',
        content: 'Contract cancellation processed. Final invoice sent.',
        timestamp: '2024-02-23T14:00:00Z'
      }
    ],
    internalComments: [
      {
        id: 'ic3',
        author: 'Michael Chen',
        content: 'Client closing business operations. Marked as dead/canceled.',
        timestamp: '2024-02-23T14:00:00Z'
      }
    ]
  }
];