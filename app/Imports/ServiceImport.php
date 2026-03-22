<?php

namespace App\Imports;

use App\Models\Service;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ServiceImport implements ToCollection, WithHeadingRow
{
    /**
     * @param Collection<int, Collection<string, mixed>> $rows
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $name = $row['service_name'] ?? $row['name'] ?? null;
            $description = $row['description'] ?? null;
            $type = $row['type'] ?? $row['service_type'] ?? null;
            $price = $row['price'] ?? $row['sell_price'] ?? null;
            $imagePath = $this->resolveImagePath($row['image'] ?? null);

            if (empty($name) || $price === null || $price === '') {
                continue;
            }

            Service::create([
                'name' => trim((string) $name),
                'description' => $description,
                'type' => trim((string) $type) !== '' ? trim((string) $type) : null,
                'price' => $price,
                'img_path' => $imagePath,
                'gallery_paths' => [$imagePath],
            ]);
        }
    }

    private function resolveImagePath($imageValue): string
    {
        $imageValue = trim((string) $imageValue);

        if ($imageValue === '') {
            return 'default.jpg';
        }

        if (str_starts_with($imageValue, 'public/')) {
            $storagePath = str_replace('public/', '', $imageValue);
            return Storage::disk('public')->exists($storagePath) ? $imageValue : 'default.jpg';
        }

        if (str_starts_with($imageValue, 'images/')) {
            return Storage::disk('public')->exists($imageValue) ? 'public/' . $imageValue : 'default.jpg';
        }

        $imagePath = 'images/' . $imageValue;

        return Storage::disk('public')->exists($imagePath)
            ? 'public/' . $imagePath
            : 'default.jpg';
    }
}