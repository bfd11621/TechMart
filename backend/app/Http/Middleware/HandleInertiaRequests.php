<?php

namespace App\Http\Middleware;

use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'company' => [
                'name' => SystemSetting::get('company_name', 'iRACK IT Solution') ?: 'iRACK IT Solution',
                'phone' => SystemSetting::get('company_phone', '+251 94 642 8560') ?: '+251 94 642 8560',
                'email' => SystemSetting::get('company_email', 'dagimw@iracktech.com') ?: 'dagimw@iracktech.com',
                'logo_url' => SystemSetting::get('company_logo_url', '') ?: '',
            ],
            'staffPortalUrl' => rtrim((string) config('app.frontend_url'), '/').'/admin/login',
        ];
    }
}
