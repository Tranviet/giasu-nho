<?php

namespace App\Services;

use App\Models\AiInteraction;
use App\Models\Plan;
use App\Models\User;
use Carbon\Carbon;

class QuotaService
{
    /**
     * Get the quota cycle start date for the user.
     */
    public function getCycleStart(User $user): Carbon
    {
        $subscription = $user->activeSubscription;

        if ($subscription && $subscription->current_period_start) {
            return $subscription->current_period_start;
        }

        return now()->startOfMonth();
    }

    /**
     * Get the monthly AI quota limit for the user.
     */
    public function getQuotaLimit(User $user): int
    {
        $subscription = $user->activeSubscription;

        if ($subscription && $subscription->plan) {
            return $subscription->plan->monthly_ai_quota;
        }

        // Fallback: Free plan default or 10 calls
        $freePlan = Plan::where('code', 'free')->first();
        return $freePlan ? $freePlan->monthly_ai_quota : 10;
    }

    /**
     * Get the number of AI requests used in the current billing cycle.
     */
    public function getUsedQuota(User $user): int
    {
        $cycleStart = $this->getCycleStart($user);

        return AiInteraction::where('user_id', $user->id)
            ->where('status', 'success')
            ->where('created_at', '>=', $cycleStart)
            ->count();
    }

    /**
     * Check if user still has remaining AI quota.
     */
    public function hasQuota(User $user): bool
    {
        return $this->getRemainingQuota($user) > 0;
    }

    /**
     * Get the remaining number of AI calls.
     */
    public function getRemainingQuota(User $user): int
    {
        $limit = $this->getQuotaLimit($user);
        $used = $this->getUsedQuota($user);

        return max(0, $limit - $used);
    }

    /**
     * Get a summary of user's quota and subscription status.
     */
    public function getQuotaSummary(User $user): array
    {
        $limit = $this->getQuotaLimit($user);
        $used = $this->getUsedQuota($user);
        $remaining = max(0, $limit - $used);
        $cycleStart = $this->getCycleStart($user);
        $subscription = $user->activeSubscription;

        return [
            'plan_name' => $subscription?->plan?->name ?? 'Miễn phí',
            'plan_code' => $subscription?->plan?->code ?? 'free',
            'limit' => $limit,
            'used' => $used,
            'remaining' => $remaining,
            'cycle_start' => $cycleStart->toIso8601String(),
            'cycle_end' => $subscription?->current_period_end?->toIso8601String() ?? now()->endOfMonth()->toIso8601String(),
        ];
    }
}
