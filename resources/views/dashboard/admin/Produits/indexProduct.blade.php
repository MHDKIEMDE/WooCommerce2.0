@extends('dashboard.admin.layout.app')
@section('title', 'Produits')
@section('contents')
<div class="container-fluid px-4">
    <h1 class="mt-4">Produits</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
        <li class="breadcrumb-item active">Produits</li>
    </ol>

    <div class="d-flex gap-2 mb-3">
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Ajouter un produit
        </a>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th style="width:70px">Image</th>
                        <th>Nom</th>
                        <th>Catégorie</th>
                        <th>Prix</th>
                        <th>Stock</th>
                        <th>Statut</th>
                        <th style="width:140px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                    @php $img = $product->images->firstWhere('is_primary', true) ?? $product->images->first(); @endphp
                    <tr>
                        <td>
                            <img src="{{ $img ? $img->url : asset('img/fruite-item-1.jpg') }}"
                                 style="width:55px;height:45px;object-fit:cover;border-radius:4px;" alt="">
                        </td>
                        <td>
                            <a href="{{ route('shop.show', $product->slug) }}" target="_blank" class="text-decoration-none">
                                {{ $product->name }}
                            </a>
                        </td>
                        <td>{{ $product->category->name ?? '—' }}</td>
                        <td>{{ number_format($product->price, 0, ',', ' ') }} FCFA</td>
                        <td>
                            @if($product->stock_quantity <= 0)
                                <span class="badge bg-danger">Rupture</span>
                            @elseif($product->stock_quantity <= $product->low_stock_threshold)
                                <span class="badge bg-warning text-dark">{{ $product->stock_quantity }} (faible)</span>
                            @else
                                <span class="badge bg-success">{{ $product->stock_quantity }}</span>
                            @endif
                        </td>
                        <td>
                            @match($product->status)
                                'active'   => '<span class="badge bg-success">Actif</span>',
                                'draft'    => '<span class="badge bg-secondary">Brouillon</span>',
                                'archived' => '<span class="badge bg-dark">Archivé</span>',
                                default    => '<span class="badge bg-light text-dark">—</span>',
                            @endmatch
                        </td>
                        <td>
                            <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.products.destroy', $product) }}" method="POST"
                                  class="d-inline" onsubmit="return confirm('Supprimer ce produit ?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center text-muted">Aucun produit.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-3">{{ $products->links() }}</div>
        </div>
    </div>
</div>
@endsection
