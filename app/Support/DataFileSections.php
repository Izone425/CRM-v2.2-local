<?php

namespace App\Support;

class DataFileSections
{
    public static function map(): array
    {
        return [
            'profile' => [
                'label' => 'Profile',
                'icon' => 'fas fa-user',
                'color' => '#7C3AED',
                'items' => [
                    'import-user' => ['label' => 'Import User', 'file' => 'profile-import-user.xlsx'],
                ],
            ],
            'attendance' => [
                'label' => 'Attendance',
                'icon' => 'fas fa-calendar-check',
                'color' => '#6366F1',
                'items' => [
                    'clocking-schedule' => ['label' => 'Clocking Schedule', 'file' => 'attendance-clocking-schedule.xlsx'],
                ],
            ],
            'leave' => [
                'label' => 'Leave',
                'icon' => 'fas fa-umbrella-beach',
                'color' => '#EF4444',
                'items' => [
                    'leave-policy' => ['label' => 'Leave Policy', 'file' => 'leave-leave-policy.xlsx'],
                ],
            ],
            'claim' => [
                'label' => 'Claim',
                'icon' => 'fas fa-money-bill-wave',
                'color' => '#F59E0B',
                'items' => [
                    'claim-policy' => ['label' => 'Claim Policy', 'file' => 'claim-claim-policy.xlsx'],
                ],
            ],
            'payroll' => [
                'label' => 'Payroll',
                'icon' => 'fas fa-file-invoice-dollar',
                'color' => '#10B981',
                'items' => [
                    'employee-information' => ['label' => 'Payroll Employee Information', 'file' => 'payroll-employee-information.xlsx'],
                    'employee-salary-data' => ['label' => 'Employee Salary Data', 'file' => 'payroll-employee-salary-data.xlsx'],
                    'accumulated-item-ea' => ['label' => 'Accumulated Item EA', 'file' => 'payroll-accumulated-item-ea.xlsx'],
                    'basic-info' => ['label' => 'Payroll Basic Info', 'file' => 'payroll-basic-info.xlsx'],
                ],
            ],
        ];
    }

    public static function isValid(string $section, string $item): bool
    {
        $map = self::map();
        return isset($map[$section]['items'][$item]);
    }
}
