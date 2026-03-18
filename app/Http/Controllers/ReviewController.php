<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Review;
use App\Models\Service;
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

    public function storeService(Request $request, int $serviceId): RedirectResponse
    {
        $user = Auth::user();
        $service = Service::findOrFail($serviceId);

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        if (!$this->hasPurchasedService((int) $user->id, (int) $service->service_id)) {
            return redirect()->back()->with('error', 'You can only review services you have purchased.');
        }

        Review::updateOrCreate(
            [
                'user_id' => $user->id,
                'item_id' => null,
                'service_id' => $service->service_id,
            ],
            [
                'rating' => $validated['rating'],
                'comment' => trim((string) ($validated['comment'] ?? '')) ?: null,
                'verified_purchase' => true,
            ]
        );

        return redirect()->back()->with('success', 'Your service review was submitted successfully.');
    }

    public function destroy(int $reviewId): RedirectResponse
    {
        $review = Review::findOrFail($reviewId);
        $review->delete();

        return redirect()->route('admin.reviews')->with('success', 'Review deleted successfully.');
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

    private function hasPurchasedService(int $userId, int $serviceId): bool
    {
        return DB::table('service_orderline')
            ->join('orderinfo', 'service_orderline.orderinfo_id', '=', 'orderinfo.orderinfo_id')
            ->join('customer', 'orderinfo.customer_id', '=', 'customer.customer_id')
            ->where('customer.user_id', $userId)
            ->where('service_orderline.service_id', $serviceId)
            ->where('orderinfo.status', '!=', 'Canceled')
            ->exists();
    }
}
