<?php

namespace App\Imports;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Models\Item;
use App\Models\Stock;

class ItemStockImport implements ToCollection, WithHeadingRow
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $description = $row['product_name'] ?? null;
            $category = $row['category'] ?? 'General';
            $brand = $row['brand'] ?? null;
            $costPrice = $row['cost_price'] ?? null;
            $sellPrice = $row['sell_price'] ?? null;
            $quantity = (int) ($row['quantity'] ?? 0);
            $imagePath = $this->resolveImagePath($row['image'] ?? null);

            if (empty($description) || $costPrice === null || $sellPrice === null) {
                continue;
            }

            $item = Item::create([
                'description' => trim((string) $description),
                'category' => trim((string) $category) !== '' ? trim((string) $category) : 'General',
                'brand' => trim((string) $brand) !== '' ? trim((string) $brand) : null,
                'cost_price' => $costPrice,
                'sell_price' => $sellPrice,
                'img_path' => $imagePath,
                'gallery_paths' => [$imagePath],
            ]);

            $stock = new Stock();
            $stock->item_id = $item->item_id;
            $stock->quantity = $quantity;
            $stock->save();
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
