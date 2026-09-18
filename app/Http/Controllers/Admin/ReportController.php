<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index()
    {
        // 1. Revenue by last 7 days
        $revenueData = Invoice::where('status', 'paid')
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(final_amount) as total')
            )
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        // 2. Top selling products
        $topProducts = OrderItem::where('status', '!=', 'cancelled')
            ->select('product_id', DB::raw('SUM(quantity) as total_sold'))
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->take(5)
            ->with('product')
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->product ? $item->product->name : 'Unknown',
                    'sold' => $item->total_sold
                ];
            });

        // 3. Today's summary
        $todayRevenue = Invoice::where('status', 'paid')->whereDate('created_at', Carbon::today())->sum('final_amount');
        $todayOrders = Invoice::where('status', 'paid')->whereDate('created_at', Carbon::today())->count();

        return Inertia::render('Admin/Reports/Index', [
            'revenueData' => $revenueData,
            'topProducts' => $topProducts,
            'todaySummary' => [
                'revenue' => (float) $todayRevenue,
                'orders' => $todayOrders
            ]
        ]);
    }

    public function export()
    {
        $fileName = 'revenue_report_' . date('Y_m_d') . '.csv';
        
        $invoices = Invoice::where('status', 'paid')
            ->orderBy('created_at', 'desc')
            ->get();

        $headers = array(
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = array('ID', 'Order Session ID', 'Total Amount', 'Discount', 'Final Amount', 'Payment Method', 'Date');

        $callback = function() use($invoices, $columns) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for UTF-8 Excel support
            fputs($file, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));
            
            fputcsv($file, $columns);

            foreach ($invoices as $invoice) {
                fputcsv($file, array(
                    $invoice->id,
                    $invoice->order_session_id,
                    $invoice->total_amount,
                    $invoice->discount,
                    $invoice->final_amount,
                    $invoice->payment_method,
                    $invoice->created_at->format('Y-m-d H:i:s')
                ));
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
