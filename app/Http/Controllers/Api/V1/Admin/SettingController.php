<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Api\V1\BaseApiController;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SettingController extends BaseApiController
{
    public function index(): JsonResponse
    {
        $settings = Cache::remember('admin:settings', 3600, fn () =>
            Setting::all()->groupBy('group')->map(fn ($group) =>
                $group->pluck('value', 'key')
            )
        );

        return $this->success($settings);
    }

    public function update(Request $request): JsonResponse
    {
        $request->validate([
            'settings'         => 'required|array',
            'settings.*.key'   => 'required|string|max:100',
            'settings.*.value' => 'nullable|string',
            'settings.*.group' => 'nullable|string|max:50',
        ]);

        foreach ($request->settings as $item) {
            Setting::updateOrCreate(
                ['key' => $item['key']],
                ['value' => $item['value'] ?? null, 'group' => $item['group'] ?? 'general']
            );
        }

        Cache::forget('admin:settings');

        return $this->success(null, 'Paramètres mis à jour.');
    }
}
