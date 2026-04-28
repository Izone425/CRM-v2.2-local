<?php

namespace App\Services;

use App\Models\ProjectPlan;
use App\Models\ProjectTask;
use App\Models\SoftwareHandover;
use Illuminate\Support\Facades\Log;

class ProjectProgressService
{
    public static function getProjectProgressData(int $leadId): array
    {
        $progressData = [
            'leadId' => $leadId,
            'selectedModules' => [],
            'swId' => null,
            'progressOverview' => [],
            'overallSummary' => [
                'totalTasks' => 0,
                'completedTasks' => 0,
                'overallProgress' => 0,
                'modules' => [],
            ],
            'projectPlanGeneratedAt' => null,
        ];

        try {
            $softwareHandovers = SoftwareHandover::where('lead_id', $leadId)
                ->where('status_handover', '!=', 'Closed')
                ->orderBy('created_at', 'desc')
                ->get();

            if ($softwareHandovers->isEmpty()) {
                return $progressData;
            }

            $progressData['projectPlanGeneratedAt'] = $softwareHandovers
                ->whereNotNull('project_plan_generated_at')
                ->max('project_plan_generated_at');

            $allSelectedModules = [];
            $swIds = [];

            foreach ($softwareHandovers as $handover) {
                $handoverModules = $handover->getSelectedModules();
                $allSelectedModules = array_merge($allSelectedModules, $handoverModules);
                $swIds[] = $handover->id;
            }

            $progressData['selectedModules'] = array_unique(array_merge(['phase 1', 'phase 2'], $allSelectedModules));
            $progressData['swIds'] = $swIds;

            usort($progressData['selectedModules'], function ($a, $b) {
                return ProjectTask::getModuleOrder($a) - ProjectTask::getModuleOrder($b);
            });

            $totalTasksAll = 0;
            $completedTasksAll = 0;

            foreach ($progressData['selectedModules'] as $module) {
                $moduleNames = ProjectTask::where('module', $module)
                    ->where('is_active', true)
                    ->select('module_name')
                    ->distinct()
                    ->get()
                    ->pluck('module_name')
                    ->toArray();

                usort($moduleNames, function ($a, $b) {
                    $orderA = ProjectTask::where('module_name', $a)->value('module_order') ?? 999;
                    $orderB = ProjectTask::where('module_name', $b)->value('module_order') ?? 999;
                    return $orderA - $orderB;
                });

                foreach ($moduleNames as $moduleName) {
                    $modulePlans = ProjectPlan::where('lead_id', $leadId)
                        ->whereIn('sw_id', $swIds)
                        ->whereHas('projectTask', function ($query) use ($moduleName) {
                            $query->where('module_name', $moduleName)
                                ->where('is_active', true);
                        })
                        ->with('projectTask')
                        ->get();

                    if ($modulePlans->isNotEmpty()) {
                        $totalTasks = $modulePlans->count();
                        $completedTasks = $modulePlans->where('status', 'completed')->count();
                        $overallProgress = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;

                        $totalTasksAll += $totalTasks;
                        $completedTasksAll += $completedTasks;

                        $sortedPlans = $modulePlans->sortBy(function ($plan) {
                            return $plan->projectTask->order ?? 0;
                        });

                        $tasksArray = $sortedPlans->map(function ($plan) {
                            return [
                                'id' => $plan->id,
                                'task_name' => $plan->projectTask->task_name ?? 'N/A',
                                'order' => $plan->projectTask->order ?? 0,
                                'module' => $plan->projectTask->module ?? '',
                                'module_name' => $plan->projectTask->module_name ?? '',
                                'percentage' => $plan->projectTask->task_percentage ?? 0,
                                'status' => $plan->status ?? 'pending',
                                'plan_start_date' => $plan->plan_start_date,
                                'plan_end_date' => $plan->plan_end_date,
                                'actual_start_date' => $plan->actual_start_date,
                                'actual_end_date' => $plan->actual_end_date,
                                'remarks' => $plan->remarks ?? '',
                            ];
                        })->values()->toArray();

                        $moduleOrder = ProjectTask::where('module_name', $moduleName)->value('module_order') ?? 999;

                        $progressData['progressOverview'][$moduleName] = [
                            'tasks' => $tasksArray,
                            'totalTasks' => $totalTasks,
                            'completedTasks' => $completedTasks,
                            'overallProgress' => $overallProgress,
                            'module_order' => $moduleOrder,
                            'module_name' => $moduleName,
                        ];

                        $progressData['overallSummary']['modules'][] = [
                            'module' => $module,
                            'module_name' => $moduleName,
                            'module_order' => $moduleOrder,
                            'progress' => $overallProgress,
                            'completed' => $completedTasks,
                            'total' => $totalTasks,
                        ];
                    }
                }
            }

            usort($progressData['overallSummary']['modules'], function ($a, $b) {
                return $a['module_order'] - $b['module_order'];
            });

            $progressData['overallSummary']['totalTasks'] = $totalTasksAll;
            $progressData['overallSummary']['completedTasks'] = $completedTasksAll;
            $progressData['overallSummary']['overallProgress'] = $totalTasksAll > 0
                ? round(($completedTasksAll / $totalTasksAll) * 100)
                : 0;
        } catch (\Exception $e) {
            Log::error('Error getting project progress data: ' . $e->getMessage());
        }

        return $progressData;
    }
}
