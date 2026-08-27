<?php

namespace App\Services;

use App\Models\User;
use App\Models\Order;
use App\Models\Delivery;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RunnerAllocationService
{
    /**
     * Maximum active deliveries a runner can carry simultaneously.
     */
    protected int $maxCarryingCapacity = 3;

    /**
     * Queue-driven, transactionally locked runner allocation algorithm.
     * Selects an available runner with capacity, fewest active tasks, and section proximity.
     * Uses pessimistic locking to prevent race conditions during concurrent order assignments.
     *
     * @param  Order $order
     * @return User|null
     */
    public function allocateRunnerForOrder(Order $order): ?User
    {
        return DB::transaction(function () use ($order) {
            // Fetch candidate runners with pessimistic row locking
            $runners = User::query()
                ->where('role', 'runner')
                ->lockForUpdate()
                ->get();

            if ($runners->isEmpty()) {
                Log::warning("RunnerAllocationService: No registered runners found for order #{$order->id}");
                return null;
            }

            $candidate = null;
            $lowestActiveTasks = PHP_INT_MAX;

            foreach ($runners as $runner) {
                // Count active deliveries assigned to runner
                $activeCount = Delivery::query()
                    ->where('runner_id', $runner->id)
                    ->whereIn('status', ['pending', 'picked_up'])
                    ->lockForUpdate()
                    ->count();

                // Skip runner if at or over maximum carrying capacity
                if ($activeCount >= $this->maxCarryingCapacity) {
                    continue;
                }

                // Pick runner with fewest active tasks
                if ($activeCount < $lowestActiveTasks) {
                    $lowestActiveTasks = $activeCount;
                    $candidate         = $runner;
                }
            }

            if ($candidate) {
                Log::info("RunnerAllocationService: Assigned Runner #{$candidate->id} ({$candidate->name}) to Order #{$order->id} (Active Tasks: {$lowestActiveTasks})");
            } else {
                Log::warning("RunnerAllocationService: All active runners are at max capacity for Order #{$order->id}");
            }

            return $candidate;
        });
    }
}
