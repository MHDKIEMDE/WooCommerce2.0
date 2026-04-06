@extends('dashboard.admin.layout.app')
@section('title', 'Gestion du stock')
@section('contents')
<div class="container-fluid px-4">
    <div class="d-flex align-items-center justify-content-between mt-4 mb-2">
        <h1>Gestion du stock</h1>
    </div>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Stock</li>
    </ol>

    {{-- KPI bandeaux --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-4">
            <a href="{{ route('admin.stock.index', ['filter'=>'low']) }}"
               class="text-decoration-none">
                <div class="card border-warning shadow-sm h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-warning bg-opacity-10 d-flex align-items-center justify-content-center"
                             style="width:48px;height:48px;">
                            <i class="fas fa-exclamation-triangle text-warning fs-5"></i>
                        </div>
                        <div>
                            <div class="fs-3 fw-bold text-warning">{{ $counts['low'] }}</div>
                            <div class="text-muted small">Stock faible</div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-sm-4">
            <a href="{{ route('admin.stock.index', ['filter'=>'out']) }}"
               class="text-decoration-none">
                <div class="card border-danger shadow-sm h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-danger bg-opacity-10 d-flex align-items-center justify-content-center"
                             style="width:48px;height:48px;">
                            <i class="fas fa-times-circle text-danger fs-5"></i>
                        </div>
                        <div>
                            <div class="fs-3 fw-bold text-danger">{{ $counts['out'] }}</div>
                            <div class="text-muted small">Rupture de stock</div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-sm-4">
            <a href="{{ route('admin.stock.index', ['filter'=>'all']) }}"
               class="text-decoration-none">
                <div class="card border-primary shadow-sm h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center"
                             style="width:48px;height:48px;">
                            <i class="fas fa-boxes text-primary fs-5"></i>
                        </div>
                        <div>
                            <div class="fs-3 fw-bold text-primary">{{ $counts['all'] }}</div>
                            <div class="text-muted small">Total produits</div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    {{-- Filtres --}}
    <form method="GET" class="row g-2 mb-3">
        <input type="hidden" name="filter" value="{{ $filter }}">
        <div class="col-sm-5">
            <input type="search" name="q" class="form-control"
                   placeholder="Rechercher nom ou SKU…" value="{{ request('q') }}">
        </div>
        <div class="col-sm-3">
            <select name="filter" class="form-select" onchange="this.form.submit()">
                <option value="low" {{ $filter === 'low' ? 'selected' : '' }}>Stock faible</option>
                <option value="out" {{ $filter === 'out' ? 'selected' : '' }}>Rupture</option>
                <option value="active" {{ $filter === 'active' ? 'selected' : '' }}>Actifs seulement</option>
                <option value="all" {{ $filter === 'all' ? 'selected' : '' }}>Tous les produits</option>
            </select>
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
            <a href="{{ route('admin.stock.index') }}" class="btn btn-outline-secondary">Reset</a>
        </div>
    </form>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 small">
                    <thead class="table-dark">
                        <tr>
                            <th>Produit</th>
                            <th>Catégorie</th>
                            <th>SKU</th>
                            <th class="text-center">Stock actuel</th>
                            <th class="text-center">Seuil alerte</th>
                            <th>Statut</th>
                            <th class="text-center" style="min-width:260px;">Mise à jour</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                        @php
                            $isOut  = $product->stock_quantity <= 0;
                            $isLow  = !$isOut && $product->stock_quantity <= $product->low_stock_threshold;
                        @endphp
                        <tr class="{{ $isOut ? 'table-danger' : ($isLow ? 'table-warning' : '') }}">
                            <td>
                                <div class="fw-semibold">{{ $product->name }}</div>
                            </td>
                            <td class="text-muted">{{ $product->category?->name ?? '—' }}</td>
                            <td class="font-monospace text-muted">{{ $product->sku ?? '—' }}</td>
                            <td class="text-center">
                                @if($isOut)
                                    <span class="badge bg-danger fs-6">0</span>
                                @elseif($isLow)
                                    <span class="badge bg-warning text-dark fs-6">{{ $product->stock_quantity }}</span>
                                @else
                                    <span class="badge bg-success fs-6">{{ $product->stock_quantity }}</span>
                                @endif
                            </td>
                            <td class="text-center text-muted">{{ $product->low_stock_threshold }}</td>
                            <td>
                                @if($product->status === 'active')
                                    <span class="badge bg-success">Actif</span>
                                @else
                                    <span class="badge bg-secondary">Inactif</span>
                                @endif
                            </td>
                            <td>
                                <form method="POST" action="{{ route('admin.stock.update', $product) }}"
                                      class="d-flex align-items-center gap-2">
                                    @csrf @method('PATCH')
                                    <input type="number" name="stock_quantity" min="0"
                                           value="{{ $product->stock_quantity }}"
                                           class="form-control form-control-sm" style="width:80px;">
                                    <input type="number" name="low_stock_threshold" min="0"
                                           value="{{ $product->low_stock_threshold }}"
                                           class="form-control form-control-sm" style="width:70px;"
                                           title="Seuil d'alerte">
                                    <button type="submit" class="btn btn-sm btn-primary rounded-pill px-3">
                                        <i class="fas fa-save me-1"></i>Sauver
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Aucun produit trouvé.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($products->hasPages())
        <div class="card-footer">{{ $products->withQueryString()->links() }}</div>
        @endif
    </div>
</div>
@endsection
