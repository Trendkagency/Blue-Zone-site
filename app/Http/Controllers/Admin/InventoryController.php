<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\View\ViewModels\InventoryViewModel;
use App\View\ViewModels\ProductViewModel;
use Illuminate\View\View;

class InventoryController extends Controller
{
    public function index(): View
    {
        $stockItems = InventoryViewModel::stockItems();
        $locations = InventoryViewModel::locations();

        return view('admin.inventory.index', [
            'stockItems' => $stockItems,
            'locations' => $locations,
            'currentPage' => 1,
            'totalPages' => 1,
        ]);
    }

    public function show(int $id): View
    {
        $stockItems = InventoryViewModel::stockItems();
        $item = null;
        foreach ($stockItems as $si) {
            if ($si['id'] === $id) {
                $item = $si;
                break;
            }
        }

        $movements = InventoryViewModel::movements();

        return view('admin.inventory.show', [
            'item' => $item ?? $stockItems[0],
            'movements' => $movements,
        ]);
    }

    public function transfers(): View
    {
        $products = ProductViewModel::all();
        $locations = InventoryViewModel::locations();
        $recentTransfers = array_filter(InventoryViewModel::movements(), fn ($m) => $m['movement_type'] === 'Stock Transfer');

        return view('admin.inventory.transfers', [
            'products' => $products,
            'locations' => $locations,
            'transfers' => $recentTransfers,
        ]);
    }

    public function history(): View
    {
        $movements = InventoryViewModel::movements();
        $locations = InventoryViewModel::locations();

        return view('admin.inventory.history', [
            'movements' => $movements,
            'locations' => $locations,
            'currentPage' => 1,
            'totalPages' => 1,
        ]);
    }
}
