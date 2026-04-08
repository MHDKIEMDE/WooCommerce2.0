<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 13px; color: #333; }
        .container { padding: 40px; }

        /* Header */
        .header { display: flex; justify-content: space-between; margin-bottom: 40px; border-bottom: 3px solid #81C408; padding-bottom: 20px; }
        .shop-name { font-size: 28px; font-weight: bold; color: #81C408; }
        .shop-info { font-size: 11px; color: #666; margin-top: 5px; }
        .invoice-title { text-align: right; }
        .invoice-title h2 { font-size: 22px; color: #333; text-transform: uppercase; letter-spacing: 2px; }
        .invoice-title p { font-size: 11px; color: #666; margin-top: 4px; }

        /* Infos */
        .info-row { display: flex; justify-content: space-between; margin-bottom: 30px; }
        .info-box { width: 48%; }
        .info-box h4 { font-size: 11px; text-transform: uppercase; color: #81C408; letter-spacing: 1px; margin-bottom: 8px; border-bottom: 1px solid #eee; padding-bottom: 4px; }
        .info-box p { font-size: 12px; color: #444; line-height: 1.6; }

        /* Table */
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        thead tr { background: #81C408; color: white; }
        thead th { padding: 10px 12px; text-align: left; font-size: 12px; font-weight: bold; }
        tbody tr:nth-child(even) { background: #f9f9f9; }
        tbody td { padding: 10px 12px; font-size: 12px; border-bottom: 1px solid #eee; }

        /* Totaux */
        .totals { display: flex; justify-content: flex-end; margin-top: 10px; }
        .totals-box { width: 280px; }
        .totals-row { display: flex; justify-content: space-between; padding: 6px 0; font-size: 12px; border-bottom: 1px solid #eee; }
        .totals-row.total { font-weight: bold; font-size: 15px; color: #81C408; border-bottom: none; border-top: 2px solid #81C408; padding-top: 10px; margin-top: 5px; }

        /* Statut */
        .badge { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: bold; }
        .badge-paid { background: #d4edda; color: #155724; }
        .badge-pending { background: #fff3cd; color: #856404; }

        /* Footer */
        .footer { margin-top: 50px; text-align: center; font-size: 11px; color: #999; border-top: 1px solid #eee; padding-top: 20px; }
    </style>
</head>
<body>
<div class="container">

    <!-- Header -->
    <div class="header">
        <div>
            <div class="shop-name">{{ $shopName }}</div>
            @if($shopAddress)<div class="shop-info">{{ $shopAddress }}</div>@endif
            @if($shopPhone)<div class="shop-info">{{ $shopPhone }}</div>@endif
            @if($shopEmail)<div class="shop-info">{{ $shopEmail }}</div>@endif
        </div>
        <div class="invoice-title">
            <h2>Facture</h2>
            <p>N° {{ $order->order_number }}</p>
            <p>Date : {{ $order->created_at->format('d/m/Y') }}</p>
            <p>
                <span class="badge {{ $order->payment_status === 'paid' ? 'badge-paid' : 'badge-pending' }}">
                    {{ $order->payment_status === 'paid' ? 'Payée' : 'En attente' }}
                </span>
            </p>
        </div>
    </div>

    <!-- Infos client & livraison -->
    <div class="info-row">
        <div class="info-box">
            <h4>Client</h4>
            <p>{{ $order->user->name }}</p>
            <p>{{ $order->user->email }}</p>
            @if($order->user->phone)<p>{{ $order->user->phone }}</p>@endif
        </div>
        <div class="info-box">
            <h4>Adresse de livraison</h4>
            @if($order->shipping_address)
                <p>{{ $order->shipping_address['first_name'] ?? '' }} {{ $order->shipping_address['last_name'] ?? '' }}</p>
                <p>{{ $order->shipping_address['street'] ?? '' }}</p>
                <p>{{ $order->shipping_address['zip'] ?? '' }} {{ $order->shipping_address['city'] ?? '' }}</p>
                <p>{{ $order->shipping_address['country'] ?? '' }}</p>
            @endif
        </div>
    </div>

    <!-- Articles -->
    <table>
        <thead>
            <tr>
                <th>Produit</th>
                <th>Variante</th>
                <th style="text-align:center">Qté</th>
                <th style="text-align:right">Prix unitaire</th>
                <th style="text-align:right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
            @php $snap = $item->product_snapshot ?? []; @endphp
            <tr>
                <td>{{ $snap['name'] ?? $item->product_name ?? '—' }}</td>
                <td>{{ $item->variant_label ?? '—' }}</td>
                <td style="text-align:center">{{ $item->quantity }}</td>
                <td style="text-align:right">{{ number_format($item->unit_price, 0, ',', ' ') }} FCFA</td>
                <td style="text-align:right">{{ number_format($item->quantity * $item->unit_price, 0, ',', ' ') }} FCFA</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Totaux -->
    <div class="totals">
        <div class="totals-box">
            <div class="totals-row">
                <span>Sous-total</span>
                <span>{{ number_format($order->subtotal, 0, ',', ' ') }} FCFA</span>
            </div>
            @if($order->discount_amount > 0)
            <div class="totals-row">
                <span>Remise</span>
                <span>- {{ number_format($order->discount_amount, 0, ',', ' ') }} FCFA</span>
            </div>
            @endif
            <div class="totals-row">
                <span>Livraison</span>
                <span>{{ $order->shipping_cost > 0 ? number_format($order->shipping_cost, 0, ',', ' ').' FCFA' : 'Gratuite' }}</span>
            </div>
            <div class="totals-row total">
                <span>Total TTC</span>
                <span>{{ number_format($order->total, 0, ',', ' ') }} FCFA</span>
            </div>
        </div>
    </div>

    <!-- Méthode de paiement -->
    <p style="margin-top:20px; font-size:12px; color:#666;">
        <strong>Mode de paiement :</strong>
        {{ $order->payment_method === 'cash' ? 'Paiement à la livraison' : ($order->payment_method === 'bank_transfer' ? 'Virement bancaire' : ucfirst($order->payment_method)) }}
    </p>

    <!-- Footer -->
    <div class="footer">
        <p>Merci pour votre commande — {{ $shopName }}</p>
        @if($shopEmail)<p>{{ $shopEmail }}</p>@endif
    </div>

</div>
</body>
</html>
