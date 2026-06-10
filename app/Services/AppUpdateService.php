<?php

namespace App\Services;

use App\Models\AppUpdate;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AppUpdateService
{
    /**
     * Get all app updates
     *
     * @return Collection
     */
    public function getAllAppUpdates(): Collection
    {
        return AppUpdate::all();
    }

    /**
     * Get app update by ID
     *
     * @param int $id
     * @return AppUpdate|null
     */
    public function findAppUpdate(int $id): ?AppUpdate
    {
        return AppUpdate::findOrFail($id);
    }

    /**
     * Create new app update
     *
     * @param array $data
     * @return AppUpdate
     */
    public function createAppUpdate(array $data): AppUpdate
    {
        return AppUpdate::create($data);
    }

    /**
     * Update app update
     *
     * @param AppUpdate $appUpdate
     * @param array $data
     * @return bool
     */
    public function updateAppUpdate(AppUpdate $appUpdate, array $data): bool
    {
        return $appUpdate->update($data);
    }

    /**
     * Delete app update
     *
     * @param int $id
     * @return bool
     */
    public function deleteAppUpdate(int $id): bool
    {
        $appUpdate = AppUpdate::find($id);
        if ($appUpdate) {
            return $appUpdate->delete();
        }
        return false;
    }

    /**
     * Check for app updates (API logic)
     *
     * @param Request $request
     * @return array
     */
    public function checkForUpdates(Request $request): array
    {
        $clientVersion = $request->input('version');
        $platform = $request->input('platform');
        $deviceDate = $request->input('device_date');

        // Convert to Carbon for compare
        $deviceDateTime = Carbon::parse($deviceDate);
        $serverDateTime = Carbon::now();

        $timeDifference = abs($deviceDateTime->diffInMinutes($serverDateTime));
        $maxAllowedDifference = 300; // By Minutes

        $latest = AppUpdate::where('platform', $platform)
            ->orderByDesc('version')
            ->first();

        if (!$latest) {
            return [
                'has_update' => false,
                'latest_version' => null,
                'update_url' => null,
                'changelog' => null,
                'device_date' => $deviceDateTime->toDateTimeString(),
                'server_date' => $serverDateTime->toDateTimeString(),
                'time_difference_minutes' => $timeDifference,
                'is_force_update' => false,
                'is_clock_incorrect' => $timeDifference > $maxAllowedDifference,
            ];
        }

        $isNewAvailable = version_compare($clientVersion, $latest->version, '<');

        return [
            'has_update' => $isNewAvailable,
            'latest_version' => $latest->version,
            'update_url' => $latest->update_url,
            'changelog' => $latest->changelog,
            'device_date' => $deviceDateTime->toDateTimeString(),
            'server_date' => $serverDateTime->toDateTimeString(),
            'time_difference_minutes' => $timeDifference,
            'is_force_update' => $latest->is_force_update,
            'is_clock_incorrect' => $timeDifference > $maxAllowedDifference,
        ];
    }

    /**
     * Get latest app update for platform
     *
     * @param string $platform
     * @return AppUpdate|null
     */
    public function getLatestAppUpdate(string $platform): ?AppUpdate
    {
        return AppUpdate::where('platform', $platform)
            ->orderByDesc('version')
            ->first();
    }

    /**
     * Validate device time synchronization
     *
     * @param string $deviceDate
     * @param int $maxAllowedDifference
     * @return array
     */
    public function validateDeviceTime(string $deviceDate, int $maxAllowedDifference = 300): array
    {
        $deviceDateTime = Carbon::parse($deviceDate);
        $serverDateTime = Carbon::now();
        $timeDifference = abs($deviceDateTime->diffInMinutes($serverDateTime));

        return [
            'device_date' => $deviceDateTime->toDateTimeString(),
            'server_date' => $serverDateTime->toDateTimeString(),
            'time_difference_minutes' => $timeDifference,
            'is_clock_incorrect' => $timeDifference > $maxAllowedDifference,
        ];
    }

    /**
     * Compare versions and return if update is needed
     *
     * @param string $currentVersion
     * @param string $latestVersion
     * @return bool
     */
    public function isUpdateNeeded(string $currentVersion, string $latestVersion): bool
    {
        return version_compare($currentVersion, $latestVersion, '<');
    }
}
