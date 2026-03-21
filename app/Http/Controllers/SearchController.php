<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Service;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $term = trim((string) $request->input('term', ''));
        $type = (string) $request->input('type', 'all');
        $perPage = 10;

        if (!in_array($type, ['all', 'product', 'service'], true)) {
            $type = 'all';
        }

        $products = null;
        $services = null;

        if ($term !== '') {
            if (in_array($type, ['all', 'product'], true)) {
                $products = Item::search($term)
                    ->query(function ($query) {
                        $query->whereNull('deleted_at')->orderBy('description');
                    })
                    ->paginate($perPage, 'products_page');
            }

            if (in_array($type, ['all', 'service'], true)) {
                $services = Service::search($term)
                    ->query(function ($query) {
                        $query->whereNull('deleted_at')->orderBy('name');
                    })
                    ->paginate($perPage, 'services_page');
            }
        }

        return view('search', compact('products', 'services', 'term', 'type'));
    }
}
