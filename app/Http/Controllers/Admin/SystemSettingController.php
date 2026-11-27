<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;

class SystemSettingController extends Controller
{
    public function index()
    {
        $settings = [
            'system_name' => SystemSetting::getValue('system_name', 'AI-Q'),
            'allow_registration' => SystemSetting::getValue('allow_registration', '1'),
            'ai_question_builder' => SystemSetting::getValue('ai_question_builder', '1'),
            'ai_insights' => SystemSetting::getValue('ai_insights', '1'),
            'maintenance_notice' => SystemSetting::getValue('maintenance_notice', '0'),
        ];

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request, ActivityLogger $logger)
    {
        $data = $request->validate([
            'system_name' => ['required', 'string', 'max:100'],
            'allow_registration' => ['required', 'boolean'],
            'ai_question_builder' => ['required', 'boolean'],
            'ai_insights' => ['required', 'boolean'],
            'maintenance_notice' => ['required', 'boolean'],
        ]);

        $oldValues = [];

        foreach ($data as $key => $value) {
            $oldValues[$key] = SystemSetting::getValue($key);
            SystemSetting::setValue($key, $value);
        }

        $changes = [];

        foreach ($data as $key => $value) {
            if ((string) $oldValues[$key] !== (string) $value) {
                $changes[$key] = [
                    'old' => $oldValues[$key],
                    'new' => $value,
                ];
            }
        }

        $logger->log(
            'settings.updated',
            'Administrator updated AI-Q system settings.',
            null,
            ['changes' => $changes]
        );

        return redirect()
            ->route('admin.settings.index')
            ->with('success', 'System settings updated successfully.');
    }
}
