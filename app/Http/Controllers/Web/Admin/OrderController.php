<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class OrderController extends Controller
{
    private const STATUSES = ['pending','processing','shipped','delivered','cancelled'];

    public function index(Request $request)
    {
        $query = Order::with('user')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('payment')) {
            $query->where('payment_status', $request->payment);
        }
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(fn ($w) =>
                $w->where('order_number', 'like', "%$q%")
                  ->orWhereHas('user', fn ($u) => $u->where('name','like',"%$q%")->orWhere('email','like',"%$q%"))
            );
        }

        $orders = $query->paginate(20)->withQueryString();

        $counts = [
            'all'        => Order::count(),
            'pending'    => Order::where('status','pending')->count(),
            'processing' => Order::where('status','processing')->count(),
            'shipped'    => Order::where('status','shipped')->count(),
            'delivered'  => Order::where('status','delivered')->count(),
            'cancelled'  => Order::where('status','cancelled')->count(),
        ];

        return view('dashboard.admin.Orders.index', compact('orders','counts'));
    }

    public function show(Order $order)
    {
        $order->load(['items', 'user']);
        return view('dashboard.admin.Orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:'.implode(',', self::STATUSES),
        ]);

        $order->update(['status' => $request->status]);

        return back()->with('success', "Statut mis à jour : {$request->status}");
    }

    public function downloadInvoice(Order $order): Response
    {
        $order->load(['items', 'user']);
        $shop = Setting::getGroup('shop');

        $pdf = Pdf::loadView('pdf.invoice', [
            'order'       => $order,
            'shopName'    => $shop['shop_name'] ?? config('app.name'),
            'shopAddress' => $shop['shop_address'] ?? null,
            'shopPhone'   => $shop['shop_phone'] ?? null,
            'shopEmail'   => $shop['shop_email'] ?? null,
        ])->setPaper('a4', 'portrait');

        return $pdf->download("facture-{$order->order_number}.pdf");
    }

    public function updatePayment(Request $request, Order $order)
    {
        $request->validate([
            'payment_status' => 'required|in:pending,paid,failed,refunded',
        ]);

        $order->update(['payment_status' => $request->payment_status]);

        return back()->with('success', 'Statut paiement mis à jour.');
    }
}
