@extends('dashboard.admin.layout.app')
@section('title', 'Produits — ' . $shop->name)

@section('contents')
<div class="container-fluid px-4">

    {{-- En-tête --}}
    <div class="d-flex justify-content-between align-items-center mt-4 mb-2 flex-wrap gap-2">
        <div>
            <h1 class="mb-0">
                @if($shop->template?->icon)<span class="me-2">{{ $shop->template->icon }}</span>@endif
                {{ $shop->name }}
            </h1>
            <small class="text-muted">
                Vendeur : {{ $shop->owner?->name }} &bull;
                <span class="badge bg-{{ ['active'=>'success','pending'=>'warning','suspended'=>'danger'][$shop->status] ?? 'secondary' }}">
                    {{ ucfirst($shop->status) }}
                </span>
            </small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('marketplace.show', $shop->slug) }}" target="_blank"
               class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-external-link-alt me-1"></i> Voir la boutique
            </a>
            <a href="{{ route('admin.shops.index') }}" class="btn btn-sm btn-outline-primary">
                <i class="fas fa-arrow-left me-1"></i> Retour
            </a>
        </div>
    </div>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.shops.index') }}">Boutiques</a></li>
        <li class="breadcrumb-item active">{{ $shop->name }}</li>
    </ol>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- KPIs --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card text-center border-0 shadow-sm">
                <div class="card-body py-3">
                    <div class="fs-2 fw-bold text-primary">{{ $products->total() }}</div>
                    <div class="small text-muted">Total produits</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center border-0 shadow-sm">
                <div class="card-body py-3">
                    <div class="fs-2 fw-bold text-success">
                        {{ $products->getCollection()->where('status','active')->count() }}
                        <small class="fs-6 text-muted">/ {{ $products->total() }}</small>
                    </div>
                    <div class="small text-muted">Actifs (page)</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center border-0 shadow-sm">
                <div class="card-body py-3">
                    <div class="fs-2 fw-bold text-warning">
                        {{ $products->getCollection()->sum('stock_quantity') }}
                    </div>
                    <div class="small text-muted">Stock (page)</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center border-0 shadow-sm">
                <div class="card-body py-3">
                    <div class="fs-2 fw-bold text-info">
                        {{ number_format($products->getCollection()->avg('price') ?? 0, 0, ',', ' ') }}
                    </div>
                    <div class="small text-muted">Prix moy. FCFA</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Table produits --}}
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width:60px">Image</th>
                            <th>Produit</th>
                            <th>Catégorie</th>
                            <th class="text-end">Prix</th>
                            <th class="text-center">Stock</th>
                            <th class="text-center">Note</th>
                            <th class="text-center">Statut</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                        @php $img = $product->images->first(); @endphp
                        <tr class="{{ $product->status !== 'active' ? 'table-warning' : '' }}">
                            <td>
                                @if($img)
                                <img src="{{ $img->url }}" alt="{{ $product->name }}"
                                     style="width:48px;height:48px;object-fit:cover;" class="rounded">
                                @else
                                <div class="bg-light rounded d-flex align-items-center justify-content-center"
                                     style="width:48px;height:48px;">
                                    <i class="fas fa-image text-muted"></i>
                                </div>
                                @endif
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $product->name }}</div>
                                <small class="text-muted font-monospace">{{ $product->sku }}</small>
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ $product->category?->name ?? '—' }}</span>
                            </td>
                            <td class="text-end fw-semibold">
                                {{ number_format($product->price, 0, ',', ' ') }} FCFA
                                @if($product->compare_price && $product->compare_price > $product->price)
                                <div class="text-muted text-decoration-line-through small">
                                    {{ number_format($product->compare_price, 0, ',', ' ') }}
                                </div>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($product->stock_quantity <= 0)
                                <span class="badge bg-danger">Rupture</span>
                                @elseif($product->stock_quantity <= ($product->low_stock_threshold ?? 5))
                                <span class="badge bg-warning text-dark">{{ $product->stock_quantity }}</span>
                                @else
                                <span class="badge bg-success">{{ $product->stock_quantity }}</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($product->rating_count > 0)
                                <div class="d-flex align-items-center justify-content-center gap-1">
                                    <i class="fas fa-star text-warning" style="font-size:.75rem;"></i>
                                    <span class="small fw-semibold">{{ number_format($product->rating_avg, 1) }}</span>
                                    <span class="text-muted small">({{ $product->rating_count }})</span>
                                </div>
                                @else
                                <span class="text-muted small">—</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge bg-{{ $product->status === 'active' ? 'success' : 'secondary' }}">
                                    {{ $product->status === 'active' ? 'Actif' : 'Inactif' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex gap-1 justify-content-center">
                                    <a href="{{ route('shop.show', $product->slug) }}" target="_blank"
                                       class="btn btn-sm btn-outline-secondary" title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <form method="POST"
                                          action="{{ route('admin.shops.products.toggle', [$shop, $product]) }}">
                                        @csrf @method('PATCH')
                                        <button class="btn btn-sm btn-outline-{{ $product->status === 'active' ? 'warning' : 'success' }}"
                                                title="{{ $product->status === 'active' ? 'Désactiver' : 'Activer' }}">
                                            <i class="fas fa-{{ $product->status === 'active' ? 'pause' : 'play' }}"></i>
                                        </button>
                                    </form>
                                    <form method="POST"
                                          action="{{ route('admin.shops.products.destroy', [$shop, $product]) }}"
                                          onsubmit="return confirm('Supprimer ce produit définitivement ?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-5">
                                <i class="fas fa-box-open fa-2x mb-2 d-block"></i>
                                Cette boutique n'a aucun produit.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($products->hasPages())
        <div class="card-footer">
            {{ $products->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
