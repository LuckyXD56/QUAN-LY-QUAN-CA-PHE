<?php

namespace App\Http\Controllers;

use App\Models\Table;
use App\Models\Category;
use App\Models\Product;
use App\Models\OrderSession;
use App\Models\Order;
use App\Models\OrderItem;
use App\Events\OrderPlaced;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;

class CustomerController extends Controller
{
    public function menu(Request $request, $qr_token)
    {
        $table = Table::where('qr_token', $qr_token)->where('is_active', true)->firstOrFail();
        
        // Find or create an active session for this table
        $activeSession = $table->activeSession;
        if (!$activeSession) {
            $activeSession = OrderSession::create([
                'table_id' => $table->id,
                'session_token' => Str::uuid()->toString(),
                'status' => 'active'
            ]);
        }

        // Save session token to HTTP session to allow customer to see their own orders (optional security)
        $request->session()->put('order_session_token', $activeSession->session_token);

        // Get categories with their available products
        $categories = Category::with(['products' => function($q) {
            $q->where('is_available', true);
        }])->orderBy('sort_order')->get();

        // Get existing placed orders
        $placedOrders = [];
        if ($activeSession) {
            $placedOrders = OrderItem::with('product')
                ->whereHas('order', function($q) use ($activeSession) {
                    $q->where('order_session_id', $activeSession->id);
                })
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return Inertia::render('Customer/Menu', [
            'table' => $table,
            'categories' => $categories,
            'session_token' => $activeSession->session_token,
            'session_id' => $activeSession->id,
            'placedOrders' => $placedOrders
        ]);
    }

    public function placeOrder(Request $request, $qr_token)
    {
        $table = Table::where('qr_token', $qr_token)->where('is_active', true)->firstOrFail();
        $activeSession = $table->activeSession;

        if (!$activeSession) {
            return back()->withErrors(['message' => 'Phiên gọi món đã kết thúc. Vui lòng quét lại mã QR.']);
        }

        $request->validate([
            'cart' => 'required|array|min:1',
            'cart.*.id' => 'required|exists:products,id',
            'cart.*.quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string'
        ]);

        // Create the order batch
        $order = Order::create([
            'order_session_id' => $activeSession->id,
            'status' => 'pending',
            'notes' => $request->notes
        ]);

        // Create order items
        foreach ($request->cart as $item) {
            $product = Product::find($item['id']);
            if ($product && $product->is_available) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                    'notes' => $item['notes'] ?? null,
                    'status' => 'pending'
                ]);
            }
        }

        // Fire event to notify kitchen
        $order->load('items.product', 'session.table');
        broadcast(new OrderPlaced($order))->toOthers();

        return back()->with('success', 'Đã gửi yêu cầu gọi món tới bếp!');
    }
}
