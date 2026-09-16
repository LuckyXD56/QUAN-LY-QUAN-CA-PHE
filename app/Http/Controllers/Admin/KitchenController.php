<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Inertia\Inertia;

class KitchenController extends Controller
{
    public function index()
    {
        // Get all pending and cooking items
        $items = OrderItem::with(['order.session.table', 'product'])
            ->whereIn('status', ['pending', 'cooking'])
            ->orderBy('created_at', 'asc')
            ->get();

        return Inertia::render('Admin/Kitchen/Index', [
            'initialItems' => $items
        ]);
    }

    public function updateStatus(Request $request, OrderItem $item)
    {
        $request->validate([
            'status' => 'required|in:pending,cooking,ready,served,cancelled'
        ]);

        $item->update(['status' => $request->status]);

        return back();
    }
}
