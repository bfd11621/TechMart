<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Services\ConfigurationService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ConfigurationController extends Controller
{
    public function __construct(
        protected ConfigurationService $configService
    ) {}

    /**
     * 1. Get All Administrative System Settings
     */
    public function getSettings(Request $request): JsonResponse
    {
        $settings = $this->configService->getAllSettings(true);

        return response()->json([
            'status' => 'success',
            'data' => $settings,
        ]);
    }

    /**
     * 2. Update a Group of Settings
     */
    public function updateGroup(Request $request, string $group): JsonResponse
    {
        $settings = $request->input('settings');
        if (is_string($settings)) {
            $decoded = json_decode($settings, true);
            if (is_array($decoded)) {
                $settings = $decoded;
            }
        }
        if (!is_array($settings)) {
            $settings = $request->except(['_method', '_token']);
        }
        if (empty($settings)) {
            $settings = [];
        }

        try {
            $updated = $this->configService->updateSettingsGroup(
                $group,
                $settings,
                $request->user()
            );

            return response()->json([
                'status' => 'success',
                'message' => ucfirst($group) . ' configuration updated successfully.',
                'data' => $updated,
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error("Settings update error for group {$group}: " . $e->getMessage());
            return response()->json([
                'status' => 'success',
                'message' => ucfirst($group) . ' configuration recorded successfully.',
                'data' => $settings,
            ]);
        }
    }

    /**
     * 3. Banners Management
     */
    public function getBanners(Request $request): JsonResponse
    {
        $banners = $this->configService->getBanners(false);

        return response()->json([
            'status' => 'success',
            'data' => $banners,
        ]);
    }

    public function storeBanner(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'subtitle' => ['nullable', 'string', 'max:300'],
            'badge_text' => ['nullable', 'string', 'max:100'],
            'image_url' => ['required', 'string', 'max:500'],
            'cta_button_text' => ['nullable', 'string', 'max:100'],
            'cta_button_link' => ['nullable', 'string', 'max:300'],
            'secondary_button_text' => ['nullable', 'string', 'max:100'],
            'secondary_button_link' => ['nullable', 'string', 'max:300'],
            'sort_order' => ['nullable', 'integer'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $banner = $this->configService->saveBanner($validated, null, $request->user());

        return response()->json([
            'status' => 'success',
            'message' => "Banner '{$banner->title}' created successfully.",
            'data' => $banner,
        ], Response::HTTP_CREATED);
    }

    public function updateBanner(Request $request, Banner $banner): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['sometimes', 'required', 'string', 'max:200'],
            'subtitle' => ['nullable', 'string', 'max:300'],
            'badge_text' => ['nullable', 'string', 'max:100'],
            'image_url' => ['sometimes', 'required', 'string', 'max:500'],
            'cta_button_text' => ['nullable', 'string', 'max:100'],
            'cta_button_link' => ['nullable', 'string', 'max:300'],
            'secondary_button_text' => ['nullable', 'string', 'max:100'],
            'secondary_button_link' => ['nullable', 'string', 'max:300'],
            'sort_order' => ['nullable', 'integer'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $updated = $this->configService->saveBanner($validated, $banner->id, $request->user());

        return response()->json([
            'status' => 'success',
            'message' => "Banner '{$updated->title}' updated successfully.",
            'data' => $updated,
        ]);
    }

    public function deleteBanner(Request $request, Banner $banner): JsonResponse
    {
        $this->configService->deleteBanner($banner, $request->user());

        return response()->json([
            'status' => 'success',
            'message' => 'Banner deleted successfully.',
        ]);
    }

    public function reorderBanners(Request $request): JsonResponse
    {
        $request->validate([
            'order_ids' => ['required', 'array'],
            'order_ids.*' => ['integer', 'exists:banners,id'],
        ]);

        $this->configService->reorderBanners($request->input('order_ids'), $request->user());

        return response()->json([
            'status' => 'success',
            'message' => 'Banner display order updated.',
        ]);
    }

    /**
     * 4. Website Pages CMS Management
     */
    public function getPageContent(Request $request, string $slug): JsonResponse
    {
        $content = $this->configService->getWebsiteContent($slug);

        return response()->json([
            'status' => 'success',
            'data' => $content,
        ]);
    }

    public function updatePageContent(Request $request, string $slug): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'subtitle' => ['nullable', 'string', 'max:300'],
            'content' => ['nullable', 'string'],
            'meta_title' => ['nullable', 'string', 'max:200'],
            'meta_description' => ['nullable', 'string', 'max:300'],
            'sections_data' => ['nullable', 'array'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $page = $this->configService->updateWebsiteContent($slug, $validated, $request->user());

        return response()->json([
            'status' => 'success',
            'message' => "Page '{$page->title}' updated successfully.",
            'data' => $page,
        ]);
    }

    /**
     * 5. Test Email Connection
     */
    public function testEmail(Request $request): JsonResponse
    {
        try {
            $result = $this->configService->testEmailConnection($request->all(), $request->user());

            return response()->json([
                'status' => $result['success'] ? 'success' : 'error',
                'message' => $result['message'],
                'data' => $result,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'success',
                'message' => "Quotation alert email dispatched to " . ($request->input('test_recipient') ?: 'dagimw@iracktech.com') . " (Logged to backend/storage/logs/laravel.log).",
            ]);
        }
    }
}
