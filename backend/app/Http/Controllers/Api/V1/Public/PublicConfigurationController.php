<?php

namespace App\Http\Controllers\Api\V1\Public;

use App\Http\Controllers\Controller;
use App\Services\ConfigurationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PublicConfigurationController extends Controller
{
    public function __construct(
        protected ConfigurationService $configService
    ) {}

    /**
     * Get Public Website Configuration (Branding, Navigation, Socials, Currency)
     */
    public function settings(): JsonResponse
    {
        $data = $this->configService->getPublicSettings();

        return response()->json([
            'status' => 'success',
            'data' => $data,
        ]);
    }

    /**
     * Get Active Homepage Promotional Banners
     */
    public function banners(): JsonResponse
    {
        $banners = $this->configService->getBanners(true);

        return response()->json([
            'status' => 'success',
            'data' => $banners,
        ]);
    }

    /**
     * Get Public Page Content by Slug
     */
    public function content(string $slug): JsonResponse
    {
        $content = $this->configService->getWebsiteContent($slug);

        if (!$content || !$content->is_published) {
            return response()->json([
                'status' => 'error',
                'message' => 'Page content not found or unpublished.',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $content,
        ]);
    }
}
