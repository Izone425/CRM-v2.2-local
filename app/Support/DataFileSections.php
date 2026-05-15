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
                'icon_component' => 'icons.timetec-profile',
                'color' => '#64748b',
                'items' => [
                    'import-user' => ['label' => 'Import User', 'file' => 'profile-import-user.xlsx'],
                ],
            ],
            'attendance' => [
                'label' => 'Attendance',
                'icon' => 'fas fa-calendar-check',
                'icon_component' => 'icons.timetec-attendance',
                'color' => '#06b6d4',
                'items' => [
                    'clocking-schedule' => ['label' => 'Clocking Schedule', 'file' => 'attendance-clocking-schedule.xlsx'],
                ],
            ],
            'leave' => [
                'label' => 'Leave',
                'icon' => 'fas fa-umbrella-beach',
                'icon_component' => 'icons.timetec-leave',
                'color' => '#22c55e',
                'items' => [
                    'leave-policy' => ['label' => 'Leave Policy', 'file' => 'leave-leave-policy.xlsx'],
                ],
            ],
            'claim' => [
                'label' => 'Claim',
                'icon' => 'fas fa-money-bill-wave',
                'icon_component' => 'icons.timetec-claim',
                'color' => '#eab308',
                'items' => [
                    'claim-policy' => ['label' => 'Claim Policy', 'file' => 'claim-claim-policy.xlsx'],
                ],
            ],
            'payroll' => [
                'label' => 'Payroll',
                'icon' => 'fas fa-file-invoice-dollar',
                'icon_component' => 'icons.timetec-payroll',
                'color' => '#f97316',
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
