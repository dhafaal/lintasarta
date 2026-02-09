<?php

namespace App\Traits;

use App\Models\AdminActivityLog;
use App\Models\AuthActivityLog;
use App\Models\UserActivityLog;

trait LogsActivity
{
    /**
     * Log admin activity
     */
    public function logAdminActivity(
        string $action,
        string $resourceType,
        ?int $resourceId = null,
        ?string $resourceName = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?string $description = null
    ): void {
        AdminActivityLog::log(
            $action,
            $resourceType,
            $resourceId,
            $resourceName,
            $oldValues,
            $newValues,
            $description
        );
    }

    /**
     * Log user activity
     */
    public function logUserActivity(
        string $action,
        ?string $resourceType = null,
        ?int $resourceId = null,
        ?string $resourceName = null,
        ?array $additionalData = null,
        ?string $description = null
    ): void {
        UserActivityLog::log(
            $action,
            $resourceType,
            $resourceId,
            $resourceName,
            $additionalData,
            $description
        );
    }

    /**
     * Log authentication activity
     */
    public function logAuthActivity(
        string $action,
        string $status,
        ?string $email = null,
        ?int $userId = null,
        ?string $description = null
    ): void {
        AuthActivityLog::log(
            $action,
            $status,
            $email,
            $userId,
            $description
        );
    }
}
