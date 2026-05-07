<?php
namespace App\Http\Controllers\Website;
use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    private function owner(): array
    {
        if (auth('customer')->check()) {
            return ['customer_id' => auth('customer')->id()];
        }

        return ['session_id' => session()->getId()];
    }

    public function toggle($productId)
    {
        $owner = $this->owner();

        $exists = Wishlist::where('product_id', $productId)
            ->where($owner)
            ->first();

        if ($exists) {
            $exists->delete();
            $status = 'removed';
        } else {
            Wishlist::create(array_merge(
                ['product_id' => $productId],
                $owner
            ));
            $status = 'added';
        }

        return response()->json([
            'status' => 200,
            'action' => $status,
            'count' => $this->count()
        ]);
    }

    public function index()
    {
        $items = Wishlist::with('product')
            ->where($this->owner())
            ->latest()
            ->get();

        return view('website.wishlist', compact('items'));
    }

    public function count(): int
    {
        return Wishlist::where($this->owner())->count();
    }

    // Call this after login
    public function mergeAfterLogin()
    {
        $sessionId = session()->getId();
        $customerId = auth('customer')->id();

        $guestItems = Wishlist::where('session_id', $sessionId)
            ->whereNull('customer_id')
            ->get();

        foreach ($guestItems as $item) {

            $exists = Wishlist::where('customer_id', $customerId)
                ->where('product_id', $item->product_id)
                ->exists();

            if (!$exists) {
                $item->update([
                    'customer_id' => $customerId,
                    'session_id' => null
                ]);
            } else {
                $item->delete();
            }
        }
    }
}
