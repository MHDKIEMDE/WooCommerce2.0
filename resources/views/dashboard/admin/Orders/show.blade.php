@extends('dashboard.admin.layout.app')
@section('title', 'Commande '.$order->order_number)
@section('contents')
<div class="container-fluid px-4">
    <div class="d-flex align-items-center gap-3 mt-4 mb-2">
        <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i>Retour
        </a>
        <h1 class="mb-0">{{ $order->order_number }}</h1>
        <a href="{{ route('admin.orders.invoice', $order) }}" class="btn btn-outline-danger btn-sm ms-auto">
            <i class="bi bi-file-earmark-pdf me-1"></i>Télécharger la facture
        </a>
        @php $sColors = ['pending'=>'warning','processing'=>'info','shipped'=>'primary','delivered'=>'success','cancelled'=>'danger']; @endphp
        <span class="badge bg-{{ $sColors[$order->status] ?? 'secondary' }} fs-6">{{ ucfirst($order->status) }}</span>
    </div>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.orders.index') }}">Commandes</a></li>
        <li class="breadcrumb-item active">{{ $order->order_number }}</li>
    </ol>

    <div class="row g-4">

        {{-- Colonne principale --}}
        <div class="col-lg-8">

            {{-- Articles --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header fw-semibold">
                    <i class="fas fa-shopping-bag text-primary me-2"></i>Articles ({{ $order->items->count() }})
                </div>
                <div class="card-body p-0">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Produit</th>
                                <th class="text-center">Qté</th>
                                <th class="text-end">P.U.</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                            @php $snap = $item->product_snapshot ?? []; @endphp
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        @if(!empty($snap['image']))
                                        <img src="{{ $snap['image'] }}" style="width:44px;height:44px;object-fit:cover;border-radius:6px;" alt="">
                                        @endif
                                        <div>
                                            <div class="fw-semibold">{{ $item->product_name }}</div>
                                            @if($item->variant_id)
                                            <small class="text-muted">Variante #{{ $item->variant_id }}</small>
                                            @endif
                                            <div class="text-muted" style="font-size:.78rem;">SKU : {{ $item->product_sku }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center fw-semibold">{{ $item->quantity }}</td>
                                <td class="text-end">{{ fmt_price($item->unit_price) }}</td>
                                <td class="text-end fw-bold text-primary">{{ fmt_price($item->total_price) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="border-top">
                            <tr>
                                <td colspan="3" class="text-end text-muted">Sous-total</td>
                                <td class="text-end">{{ fmt_price($order->subtotal) }}</td>
                            </tr>
                            @if($order->discount_amount > 0)
                            <tr class="text-success">
                                <td colspan="3" class="text-end">Réduction</td>
                                <td class="text-end">− {{ fmt_price($order->discount_amount) }}</td>
                            </tr>
                            @endif
                            @if($order->shipping_cost > 0)
                            <tr>
                                <td colspan="3" class="text-end text-muted">Livraison</td>
                                <td class="text-end">{{ fmt_price($order->shipping_cost) }}</td>
                            </tr>
                            @endif
                            @if($order->tax_amount > 0)
                            <tr>
                                <td colspan="3" class="text-end text-muted">TVA</td>
                                <td class="text-end">{{ fmt_price($order->tax_amount) }}</td>
                            </tr>
                            @endif
                            <tr class="fw-bold border-top">
                                <td colspan="3" class="text-end fs-6">Total TTC</td>
                                <td class="text-end fs-5 text-primary">{{ fmt_price($order->total) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            {{-- Adresse livraison --}}
            @php $addr = $order->shipping_address ?? $order->billing_address ?? []; @endphp
            @if(!empty($addr))
            <div class="card shadow-sm mb-4">
                <div class="card-header fw-semibold">
                    <i class="fas fa-map-marker-alt text-primary me-2"></i>Adresse de livraison
                </div>
                <div class="card-body">
                    <p class="mb-1 fw-semibold">{{ ($addr['first_name'] ?? '').' '.($addr['last_name'] ?? '') }}</p>
                    <p class="mb-1">{{ $addr['address'] ?? '' }}{{ !empty($addr['city']) ? ', '.$addr['city'] : '' }}</p>
                    @if(!empty($addr['country']))<p class="mb-1">{{ $addr['country'] }}</p>@endif
                    @if(!empty($addr['phone']))<p class="mb-0"><i class="fas fa-phone me-1 text-muted"></i>{{ $addr['phone'] }}</p>@endif
                    @if(!empty($addr['email']))<p class="mb-0"><i class="fas fa-envelope me-1 text-muted"></i>{{ $addr['email'] }}</p>@endif
                </div>
            </div>
            @endif

            {{-- Notes --}}
            @if($order->notes)
            <div class="card shadow-sm mb-4">
                <div class="card-header fw-semibold"><i class="fas fa-sticky-note text-warning me-2"></i>Notes</div>
                <div class="card-body text-muted">{{ $order->notes }}</div>
            </div>
            @endif
        </div>

        {{-- Colonne droite --}}
        <div class="col-lg-4">

            {{-- Changer statut commande --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header fw-semibold">
                    <i class="fas fa-exchange-alt text-primary me-2"></i>Statut de la commande
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.orders.status', $order) }}">
                        @csrf @method('PATCH')
                        <select name="status" class="form-select mb-3">
                            @foreach(['pending','processing','shipped','delivered','cancelled'] as $s)
                            <option value="{{ $s }}" {{ $order->status === $s ? 'selected':'' }}>
                                {{ ['pending'=>'En attente','processing'=>'En cours','shipped'=>'Expédiée','delivered'=>'Livrée','cancelled'=>'Annulée'][$s] }}
                            </option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-save me-1"></i>Mettre à jour
                        </button>
                    </form>
                </div>
            </div>

            {{-- Changer statut paiement --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header fw-semibold">
                    <i class="fas fa-credit-card text-success me-2"></i>Statut du paiement
                </div>
                <div class="card-body">
                    @php $pColors = ['pending'=>'warning','paid'=>'success','failed'=>'danger','refunded'=>'secondary']; @endphp
                    <div class="mb-3">
                        <span class="badge bg-{{ $pColors[$order->payment_status] ?? 'secondary' }} fs-6">
                            {{ ucfirst($order->payment_status) }}
                        </span>
                        <div class="text-muted small mt-1">
                            Mode : {{ $order->payment_method === 'cash_on_delivery' ? 'Paiement à la livraison' : 'Virement bancaire' }}
                        </div>
                    </div>
                    <form method="POST" action="{{ route('admin.orders.payment', $order) }}">
                        @csrf @method('PATCH')
                        <select name="payment_status" class="form-select mb-2">
                            @foreach(['pending'=>'En attente','paid'=>'Payé','failed'=>'Échoué','refunded'=>'Remboursé'] as $k => $v)
                            <option value="{{ $k }}" {{ $order->payment_status === $k ? 'selected':'' }}>{{ $v }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-success w-100 btn-sm">Valider</button>
                    </form>
                </div>
            </div>

            {{-- Infos client --}}
            @if($order->user)
            <div class="card shadow-sm mb-4">
                <div class="card-header fw-semibold"><i class="fas fa-user text-info me-2"></i>Client</div>
                <div class="card-body">
                    <p class="fw-semibold mb-1">{{ $order->user->name }}</p>
                    <p class="text-muted small mb-1">{{ $order->user->email }}</p>
                    <p class="text-muted small mb-0">
                        <i class="fas fa-shopping-bag me-1"></i>
                        {{ $order->user->orders()->count() }} commande(s)
                    </p>
                </div>
            </div>
            @endif

            {{-- Méta --}}
            <div class="card shadow-sm">
                <div class="card-header fw-semibold"><i class="fas fa-info-circle text-muted me-2"></i>Informations</div>
                <div class="card-body small text-muted">
                    <div class="d-flex justify-content-between mb-1">
                        <span>Créée le</span>
                        <span>{{ $order->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Mise à jour</span>
                        <span>{{ $order->updated_at->format('d/m/Y H:i') }}</span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
