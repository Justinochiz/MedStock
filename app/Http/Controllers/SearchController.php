<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Service;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $term = trim((string) $request->input('term', ''));
        $type = (string) $request->input('type', 'all');
        $perPage = 10;

        $minPrice = $this->toNullableFloat($request->input('min_price'));
        $maxPrice = $this->toNullableFloat($request->input('max_price'));
        $category = trim((string) $request->input('category', ''));
        $brand = trim((string) $request->input('brand', ''));
        $serviceType = trim((string) $request->input('service_type', ''));

        if ($minPrice !== null && $maxPrice !== null && $minPrice > $maxPrice) {
            [$minPrice, $maxPrice] = [$maxPrice, $minPrice];
        }

        $hasItemBrandColumn = Schema::hasColumn('item', 'brand');
        $hasServiceTypeColumn = Schema::hasColumn('service', 'type');

        $hasActiveFilters = $minPrice !== null
            || $maxPrice !== null
            || $category !== ''
            || ($hasItemBrandColumn && $brand !== '')
            || ($hasServiceTypeColumn && $serviceType !== '');

        if (!in_array($type, ['all', 'product', 'service'], true)) {
            $type = 'all';
        }

        $products = null;
        $services = null;

        if ($term !== '' || $hasActiveFilters) {
            if (in_array($type, ['all', 'product'], true)) {
                if ($term !== '') {
                    $products = Item::search($term)
                        ->query(function (Builder $query) use ($minPrice, $maxPrice, $category, $brand, $hasItemBrandColumn) {
                            $this->applyItemFilters($query, $minPrice, $maxPrice, $category, $brand, $hasItemBrandColumn);
                        })
                        ->paginate($perPage, 'products_page');
                } else {
                    $productQuery = Item::query();
                    $this->applyItemFilters($productQuery, $minPrice, $maxPrice, $category, $brand, $hasItemBrandColumn);
                    $products = $productQuery->paginate($perPage, ['*'], 'products_page');
                }
            }

            if (in_array($type, ['all', 'service'], true)) {
                if ($term !== '') {
                    $services = Service::search($term)
                        ->query(function (Builder $query) use ($minPrice, $maxPrice, $serviceType, $hasServiceTypeColumn) {
                            $this->applyServiceFilters($query, $minPrice, $maxPrice, $serviceType, $hasServiceTypeColumn);
                        })
                        ->paginate($perPage, 'services_page');
                } else {
                    $serviceQuery = Service::query();
                    $this->applyServiceFilters($serviceQuery, $minPrice, $maxPrice, $serviceType, $hasServiceTypeColumn);
                    $services = $serviceQuery->paginate($perPage, ['*'], 'services_page');
                }
            }
        }

        $categories = Item::query()
            ->whereNull('deleted_at')
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        $brands = collect();
        if ($hasItemBrandColumn) {
            $brands = Item::query()
                ->whereNull('deleted_at')
                ->whereNotNull('brand')
                ->where('brand', '!=', '')
                ->distinct()
                ->orderBy('brand')
                ->pluck('brand');
        }

        $serviceTypes = collect();
        if ($hasServiceTypeColumn) {
            $serviceTypes = Service::query()
                ->whereNull('deleted_at')
                ->whereNotNull('type')
                ->where('type', '!=', '')
                ->distinct()
                ->orderBy('type')
                ->pluck('type');
        }

        return view('search', compact(
            'products',
            'services',
            'term',
            'type',
            'minPrice',
            'maxPrice',
            'category',
            'brand',
            'serviceType',
            'categories',
            'brands',
            'serviceTypes',
            'hasActiveFilters',
            'hasItemBrandColumn',
            'hasServiceTypeColumn'
        ));
    }

    private function applyItemFilters(
        Builder $query,
        ?float $minPrice,
        ?float $maxPrice,
        string $category,
        string $brand,
        bool $hasItemBrandColumn
    ): void {
        $query->whereNull('deleted_at');

        if ($minPrice !== null) {
            $query->where('sell_price', '>=', $minPrice);
        }

        if ($maxPrice !== null) {
            $query->where('sell_price', '<=', $maxPrice);
        }

        if ($category !== '') {
            $query->where('category', $category);
        }

        if ($hasItemBrandColumn && $brand !== '') {
            $query->where('brand', $brand);
        }

        $query->orderBy('description');
    }

    private function applyServiceFilters(
        Builder $query,
        ?float $minPrice,
        ?float $maxPrice,
        string $serviceType,
        bool $hasServiceTypeColumn
    ): void {
        $query->whereNull('deleted_at');

        if ($minPrice !== null) {
            $query->where('price', '>=', $minPrice);
        }

        if ($maxPrice !== null) {
            $query->where('price', '<=', $maxPrice);
        }

        if ($hasServiceTypeColumn && $serviceType !== '') {
            $query->where('type', $serviceType);
        }

        $query->orderBy('name');
    }

    private function toNullableFloat($value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        return is_numeric($value) ? (float) $value : null;
    }
}
