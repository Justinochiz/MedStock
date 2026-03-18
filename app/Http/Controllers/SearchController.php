<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Searchable\Search;
use App\Models\Item;
use App\Models\Service;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $term = trim((string) $request->input('term', ''));
        $type = (string) $request->input('type', 'all');

        if (!in_array($type, ['all', 'product', 'service'], true)) {
            $type = 'all';
        }

        $search = new Search();

        if (in_array($type, ['all', 'product'], true)) {
            $search->registerModel(Item::class, ['description', 'category', 'sell_price']);
        }

        if (in_array($type, ['all', 'service'], true)) {
            $search->registerModel(Service::class, ['name', 'description', 'price']);
        }

        $searchResults = $term === ''
            ? collect()
            : $search->search($term);

        return view('search', compact('searchResults', 'term', 'type'));
    }
}
