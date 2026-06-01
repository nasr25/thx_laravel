<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Services\SettingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function __construct(protected SettingService $settingService) {}

    public function edit(): View
    {
        return view('admin.settings', ['settings' => $this->settingService->getAll()]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'platform_name_en'            => ['nullable', 'string', 'max:255'],
            'platform_name_ar'            => ['nullable', 'string', 'max:255'],
            'primary_color'               => ['nullable', 'string', 'max:9'],
            'secondary_color'             => ['nullable', 'string', 'max:9'],
            'monthly_appreciation_limit'  => ['nullable', 'integer', 'min:1', 'max:100'],
            'max_daily_appreciations'     => ['nullable', 'integer', 'min:1', 'max:50'],
            'max_same_receiver_per_month' => ['nullable', 'integer', 'min:1', 'max:20'],
            'email_notifications_enabled' => ['sometimes', 'boolean'],
        ]);

        $data['email_notifications_enabled'] = $request->boolean('email_notifications_enabled');

        $this->settingService->update($data);

        return back()->with('status', __('messages.settings_updated'));
    }

    public function uploadLogo(Request $request): RedirectResponse
    {
        $request->validate([
            'logo' => ['required', 'image', 'mimes:png,jpg,jpeg,svg', 'max:2048'],
        ]);

        $this->settingService->uploadLogo($request->file('logo'));

        return back()->with('status', __('messages.logo_uploaded'));
    }
}
