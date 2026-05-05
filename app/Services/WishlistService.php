<?php

namespace App\Services;
use App\Models\Wishlist;

class WishlistService
{

    public function wishlistCount(): int
    {
        return Wishlist::where(
            auth('customer')->check()
            ? ['customers_id' => auth('customer')->id()]
            : ['session_id' => session()->getId()]
        )->count();
    }
}
