<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use InvalidArgumentException;

class FileUploadService
{
    protected const ALLOWED_IMAGE_MIMES = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    protected const ALLOWED_PDF_MIMES = ['application/pdf'];
    protected const MAX_IMAGE_SIZE = 5120; // 5MB
    protected const MAX_PDF_SIZE = 15360;  // 15MB

    /**
     * Store a product image file.
     */
    public function uploadProductImage(UploadedFile $file, string $sku): string
    {
        if (!in_array($file->getMimeType(), self::ALLOWED_IMAGE_MIMES)) {
            throw new InvalidArgumentException('Invalid image format. Allowed formats: JPEG, PNG, WebP.');
        }

        if ($file->getSize() > self::MAX_IMAGE_SIZE * 1024) {
            throw new InvalidArgumentException('Image file size exceeds maximum limit of 5MB.');
        }

        $filename = 'img_' . Str::slug($sku) . '_' . time() . '_' . Str::random(6) . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('products/images', $filename, 'public');

        return Storage::url($path);
    }

    /**
     * Store a manufacturer PDF datasheet.
     */
    public function uploadDatasheet(UploadedFile $file, string $sku): array
    {
        if (!in_array($file->getMimeType(), self::ALLOWED_PDF_MIMES)) {
            throw new InvalidArgumentException('Invalid document format. Datasheets must be valid PDF documents.');
        }

        if ($file->getSize() > self::MAX_PDF_SIZE * 1024) {
            throw new InvalidArgumentException('Datasheet file size exceeds maximum limit of 15MB.');
        }

        $originalName = $file->getClientOriginalName();
        $filename = 'datasheet_' . Str::slug($sku) . '_' . time() . '.pdf';
        $path = $file->storeAs('products/datasheets', $filename, 'public');

        return [
            'url' => Storage::url($path),
            'filename' => $originalName,
            'storage_path' => $path,
        ];
    }

    /**
     * Delete a stored file from public storage.
     */
    public function deleteFile(?string $url): bool
    {
        if (!$url) return false;

        $relativePath = str_replace('/storage/', '', $url);
        if (Storage::disk('public')->exists($relativePath)) {
            return Storage::disk('public')->delete($relativePath);
        }

        return false;
    }
}
