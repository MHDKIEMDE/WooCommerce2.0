@extends('dashboard.admin.layout.app')
@section('title','Commandes')
@section('contents')
<div class="container-fluid px-4">
    <div class="d-flex align-items-center justify-content-between mt-4 mb-2">
        <h1>Commandes</h1>
    </div>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Commandes</li>
    </ol>

    {{-- Onglets statuts --}}
    <div class="d-flex flex-wrap gap-2 mb-4">
        @php
            $tabs = ['all'=>'Toutes','pending'=>'En attente','processing'=>'En cours','shipped'=>'Expédiées','delivered'=>'Livrées','cancelled'=>'Annulées'];
            $colors = ['all'=>'secondary','pending'=>'warning','processing'=>'info','shipped'=>'primary','delivered'=>'success','cancelled'=>'danger'];
        @endphp
        @foreach($tabs as $key => $label)
        <a href="{{ route('admin.orders.index', array_merge(request()->query(), ['status' => $key === 'all' ? null : $key])) }}"
           class="btn btn-sm {{ request('status', 'all') === $key ? 'btn-'.$colors[$key] : 'btn-outline-'.$colors[$key] }} rounded-pill">
            {{ $label }}
            <span class="badge bg-white text-dark ms-1">{{ $counts[$key] }}</span>
        </a>
        @endforeach
    </div>

    {{-- Filtres --}}
    <form method="GET" class="row g-2 mb-3">
        @if(request('status'))<input type="hidden" name="status" value="{{ request('status') }}">@endif
        <div class="col-sm-5">
            <input type="search" name="q" class="form-control" placeholder="N° commande, nom, email…" value="{{ request('q') }}">
        </div>
        <div class="col-sm-3">
            <select name="payment" class="form-select">
                <option value="">Tout paiement</option>
                <option value="pending"  {{ request('payment')==='pending'  ? 'selected':'' }}>En attente</option>
                <option value="paid"     {{ request('payment')==='paid'     ? 'selected':'' }}>Payé</option>
                <option value="failed"   {{ request('payment')==='failed'   ? 'selected':'' }}>Échoué</option>
                <option value="refunded" {{ request('payment')==='refunded' ? 'selected':'' }}>Remboursé</option>
            </select>
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
            <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary">Reset</a>
        </div>
    </form>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 small">
                    <thead class="table-dark">
                        <tr>
                            <th>N° Commande</th>
                            <th>Client</th>
                            <th>Total</th>
                            <th>Statut</th>
                            <th>Paiement</th>
                            <th>Date</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                        @php
                            $sColors = ['pending'=>'warning','processing'=>'info','shipped'=>'primary','delivered'=>'success','cancelled'=>'danger'];
                            $pColors = ['pending'=>'warning','paid'=>'success','failed'=>'danger','refunded'=>'secondary'];
                        @endphp
                        <tr>
                            <td class="font-monospace fw-semibold">{{ $order->order_number }}</td>
                            <td>
                                <div class="fw-semibold">{{ $order->user?->name ?? (($order->billing_address['first_name'] ?? '').' '.($order->billing_address['last_name'] ?? '')) }}</div>
                                <div class="text-muted" style="font-size:.78rem;">{{ $order->user?->email ?? ($order->billing_address['email'] ?? '') }}</div>
                            </td>
                            <td class="fw-bold text-primary">{{ fmt_price($order->total) }}</td>
                            <td>
                                <span class="badge bg-{{ $sColors[$order->status] ?? 'secondary' }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $pColors[$order->payment_status] ?? 'secondary' }}">
                                    {{ ucfirst($order->payment_status) }}
                                </span>
                            </td>
                            <td class="text-muted">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                            <td class="text-center">
                                <a href="{{ route('admin.orders.show', $order) }}"
                                   class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                    <i class="fas fa-eye me-1"></i>Voir
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">Aucune commande.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($orders->hasPages())
        <div class="card-footer">{{ $orders->withQueryString()->links() }}</div>
        @endif
    </div>
</div>
@endsection
