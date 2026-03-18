<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{
    public function storeItem(Request $request, int $itemId): RedirectResponse
    {
        $user = Auth::user();
        $item = Item::findOrFail($itemId);

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        if (!$this->hasPurchasedItem((int) $user->id, (int) $item->item_id)) {
            return redirect()->back()->with('error', 'You can only review items you have purchased.');
        }

        Review::updateOrCreate(
            [
                'user_id' => $user->id,
                'item_id' => $item->item_id,
                'service_id' => null,
            ],
            [
                'rating' => $validated['rating'],
                'comment' => trim((string) ($validated['comment'] ?? '')) ?: null,
                'verified_purchase' => true,
            ]
        );

        return redirect()->back()->with('success', 'Your review was submitted successfully.');
    }

    private function hasPurchasedItem(int $userId, int $itemId): bool
    {
        return DB::table('orderline')
            ->join('orderinfo', 'orderline.orderinfo_id', '=', 'orderinfo.orderinfo_id')
            ->join('customer', 'orderinfo.customer_id', '=', 'customer.customer_id')
            ->where('customer.user_id', $userId)
            ->where('orderline.item_id', $itemId)
            ->where('orderinfo.status', '!=', 'Canceled')
            ->exists();
    }
}
