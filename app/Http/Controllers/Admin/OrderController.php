<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use App\Models\InventoryMovement;
use App\Models\Order;
use App\View\ViewModels\OrderViewModel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * Display live orders list with filtering.
     */
    public function index(Request $request): View
    {
        $status = $request->query('status');
        $isTrashed = $status === 'trashed';
        $channel = $request->query('channel');
        $search = $request->query('search');

        $query = Order::with(['items', 'customer'])->latest();

        if ($isTrashed) {
            $query->onlyTrashed();
        } elseif ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        if ($channel && $channel !== 'all') {
            $query->where('channel', $channel);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('invoice_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_email', 'like', "%{$search}%");
            });
        }

        $trashedCount = Order::onlyTrashed()->count();
        $activeCount = Order::count();
        $totalInDb = Order::withTrashed()->count();

        $orders = $query->paginate(15)->withQueryString();

        if ($totalInDb === 0) {
            $orders = collect(OrderViewModel::all());
        }

        return view('admin.orders.index', [
            'orders' => $orders,
            'selectedStatus' => $status,
            'selectedChannel' => $channel,
            'search' => $search,
            'trashedCount' => $trashedCount,
            'activeCount' => $activeCount,
            'isTrashed' => $isTrashed,
            'currentPage' => is_a($orders, \Illuminate\Pagination\LengthAwarePaginator::class) ? $orders->currentPage() : 1,
            'totalPages' => is_a($orders, \Illuminate\Pagination\LengthAwarePaginator::class) ? $orders->lastPage() : 1,
        ]);
    }

    /**
     * Show single order details.
     */
    public function show(string $id): View
    {
        $order = is_numeric($id) 
            ? Order::with(['items', 'customer'])->find($id) 
            : Order::with(['items', 'customer'])->where('order_number', $id)->first();

        if (!$order) {
            $fallback = OrderViewModel::find($id);
            if ($fallback) {
                $order = $fallback;
            } else {
                abort(404);
            }
        }

        return view('admin.orders.show', [
            'order' => $order,
        ]);
    }

    /**
     * Update order status with inventory rollback if cancelled.
     */
    public function updateStatus(Request $request, string $id): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string'],
        ]);

        /** @var Order $order */
        $order = is_numeric($id) 
            ? Order::with('items')->findOrFail($id) 
            : Order::with('items')->where('order_number', $id)->firstOrFail();

        $oldStatus = strtolower($order->status);
        $newStatus = strtolower($validated['status']);

        if ($oldStatus !== $newStatus) {
            $order->update(['status' => $newStatus]);

            // If order cancelled, return items to inventory
            if ($newStatus === 'cancelled' && $oldStatus !== 'cancelled') {
                $targetLoc = $order->channel === 'offline' ? 'offline' : 'online';

                foreach ($order->items as $item) {
                    if ($item->product_id) {
                        $product = \App\Models\Product::find($item->product_id);
                        if ($product) {
                            \App\Services\InventoryService::adjustStock(
                                product: $product,
                                locationId: $targetLoc,
                                quantityDelta: (int) $item->quantity,
                                movementType: 'Cancelled Order',
                                reason: "Automatic inventory restock due to order cancellation #{$order->order_number}",
                                userName: auth()->user()?->name ?? 'System Admin',
                                sourceOrTarget: "Order #{$order->order_number}"
                            );
                        }
                    }
                }
            }
        }

        return back()->with('success', __('admin.order_status_updated', ['default' => 'Order status updated to :status successfully.', 'status' => $newStatus]));
    }

    public function destroy(int $id): RedirectResponse
    {
        $order = Order::findOrFail($id);
        $orderNumber = $order->order_number;
        $order->delete();

        return redirect()->route('admin.orders.index')
            ->with('success', app()->getLocale() === 'ar'
                ? "تم نقل الطلب [{$orderNumber}] إلى سلة المحذوفات بنجاح."
                : "Order [{$orderNumber}] moved to trash successfully.");
    }

    public function restore(int $id): RedirectResponse
    {
        $order = Order::onlyTrashed()->findOrFail($id);
        $orderNumber = $order->order_number;
        $order->restore();

        return redirect()->route('admin.orders.index', ['status' => 'trashed'])
            ->with('success', app()->getLocale() === 'ar'
                ? "تم استعادة الطلب [{$orderNumber}] بنجاح!"
                : "Order [{$orderNumber}] restored successfully!");
    }

    public function forceDelete(int $id): RedirectResponse
    {
        $order = Order::withTrashed()->findOrFail($id);
        $orderNumber = $order->order_number;
        $order->items()->delete();
        $order->forceDelete();

        return redirect()->route('admin.orders.index', ['status' => 'trashed'])
            ->with('success', app()->getLocale() === 'ar'
                ? "تم حذف الطلب [{$orderNumber}] وسجلاته نهائياً!"
                : "Order [{$orderNumber}] permanently erased!");
    }
}
