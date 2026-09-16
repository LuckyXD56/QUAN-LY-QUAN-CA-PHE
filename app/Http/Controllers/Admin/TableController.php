<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Table;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;

class TableController extends Controller
{
    public function index()
    {
        $tables = Table::all();
        return Inertia::render('Admin/Tables/Index', [
            'tables' => $tables
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'is_active' => 'boolean'
        ]);

        $table = new Table();
        $table->name = $request->name;
        $table->is_active = $request->is_active ?? true;
        $table->qr_token = Str::random(32);
        $table->save();

        return redirect()->route('admin.tables.index')->with('success', 'Đã thêm bàn thành công.');
    }

    public function update(Request $request, Table $table)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'is_active' => 'boolean'
        ]);

        $table->update([
            'name' => $request->name,
            'is_active' => $request->is_active ?? true,
        ]);

        return redirect()->route('admin.tables.index')->with('success', 'Đã cập nhật bàn thành công.');
    }

    public function destroy(Table $table)
    {
        $table->delete();
        return redirect()->route('admin.tables.index')->with('success', 'Đã xóa bàn thành công.');
    }

    public function regenerateQr(Table $table)
    {
        $table->update(['qr_token' => Str::random(32)]);
        return back()->with('success', 'Đã tạo lại mã QR thành công.');
    }
}
