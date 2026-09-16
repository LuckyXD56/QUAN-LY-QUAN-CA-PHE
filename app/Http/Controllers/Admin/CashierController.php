<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Table;
use App\Models\OrderSession;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CashierController extends Controller
{
    public function index()
    {
        // Load all tables with their active order session and its order items
        $tables = Table::with(['activeSession' => function($q) {
            $q->with(['orders.items.product']);
        }])->get();

        return Inertia::render('Admin/Cashier/Index', [
            'tables' => $tables
        ]);
    }

    public function checkout(Request $request, OrderSession $session)
    {
        $request->validate([
            'payment_method' => 'required|string',
            'discount' => 'numeric|min:0',
        ]);

        // Calculate total
        $totalAmount = 0;
        foreach ($session->orders as $order) {
            // Only charge for items that are not cancelled
            foreach ($order->items as $item) {
                if ($item->status !== 'cancelled') {
                    $totalAmount += $item->price * $item->quantity;
                }
            }
        }

        $discount = $request->discount ?? 0;
        $finalAmount = max(0, $totalAmount - $discount);

        // Create Invoice
        $invoice = Invoice::create([
            'order_session_id' => $session->id,
            'user_id' => auth()->id(),
            'total_amount' => $totalAmount,
            'discount' => $discount,
            'final_amount' => $finalAmount,
            'payment_method' => $request->payment_method,
            'status' => 'paid',
        ]);

        // Close session
        $session->update(['status' => 'closed']);

        return redirect()->route('admin.cashier.index')->with('success', 'Thanh toán thành công. Đã đóng bàn.');
    }
}
