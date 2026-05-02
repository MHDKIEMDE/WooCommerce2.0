@extends('layouts.app')
@section('seo_title', 'Ma liste de souhaits')
@section('noindex')
@section('content')

    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6">Ma liste de souhaits</h1>
        <ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-white">Accueil</a></li>
            <li class="breadcrumb-item"><a href="{{ route('account.profile') }}" class="text-white">Mon compte</a></li>
            <li class="breadcrumb-item active text-white">Wishlist</li>
        </ol>
    </div>

    <div class="container py-5">
        <div class="row g-4">

            {{-- Sidebar --}}
            <div class="col-lg-3">
                <div class="card shadow-sm">
                    <div class="card-body text-center py-4">
                        @if(auth()->user()->avatar)
                            <img src="{{ Storage::url(auth()->user()->avatar) }}" alt="{{ auth()->user()->name }}"
                                class="rounded-circle mb-3" style="width:90px;height:90px;object-fit:cover;">
                        @else
                            <div class="rounded-circle bg-primary d-inline-flex align-items-center justify-content-center mb-3"
                                style="width:90px;height:90px;">
                                <span class="text-white fw-bold fs-2">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                            </div>
                        @endif
                        <h5 class="mb-0 fw-bold">{{ auth()->user()->name }}</h5>
                        <small class="text-muted">{{ auth()->user()->email }}</small>
                    </div>
                    <div class="list-group list-group-flush">
                        <a href="{{ route('account.profile') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-user me-2"></i> Mon profil
                        </a>
                        <a href="{{ route('account.orders') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-box me-2"></i> Mes commandes
                        </a>
                        <a href="{{ route('account.addresses') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-map-marker-alt me-2"></i> Mes adresses
                        </a>
                        <a href="{{ route('account.wishlist') }}" class="list-group-item list-group-item-action active">
                            <i class="fas fa-heart me-2"></i> Ma wishlist
                        </a>
                        <a href="{{ route('account.edit') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-edit me-2"></i> Modifier le profil
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="list-group-item list-group-item-action text-danger border-0 w-100 text-start">
                                <i class="fas fa-sign-out-alt me-2"></i> Déconnexion
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Contenu --}}
            <div class="col-lg-9">

                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="mb-0 fw-bold">Ma liste de souhaits <span class="badge bg-primary">{{ $items->total() }}</span></h4>
                </div>

                @if($items->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-heart fa-3x text-muted mb-3"></i>
                    <p class="text-muted fs-5">Votre liste de souhaits est vide.</p>
                    <a href="{{ route('shop.index') }}" class="btn btn-primary px-4">Découvrir nos produits</a>
                </div>
                @else
                <div class="row g-4">
                    @foreach($items as $item)
                    @php $product = $item->product @endphp
                    @if($product)
                    <div class="col-md-6 col-xl-4">
                        <div class="card h-100 shadow-sm border-0">
                            <div class="position-relative">
                                @if($product->images->first())
                                <img src="{{ Storage::url($product->images->first()->path) }}"
                                    alt="{{ $product->name }}"
                                    class="card-img-top"
                                    style="height:200px;object-fit:cover;">
                                @else
                                <div class="bg-light d-flex align-items-center justify-content-center" style="height:200px;">
                                    <i class="fas fa-image fa-2x text-muted"></i>
                                </div>
                                @endif
                                @if($product->compare_price)
                                <span class="badge bg-danger position-absolute top-0 start-0 m-2">
                                    -{{ round((($product->compare_price - $product->price) / $product->compare_price) * 100) }}%
                                </span>
                                @endif
                            </div>
                            <div class="card-body d-flex flex-column">
                                <h6 class="card-title fw-semibold">
                                    <a href="{{ route('shop.show', $product->slug) }}" class="text-dark text-decoration-none">
                                        {{ $product->name }}
                                    </a>
                                </h6>
                                <div class="mt-auto">
                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                        <div>
                                            <span class="fw-bold text-primary fs-5">{{ number_format($product->price) }} FCFA</span>
                                            @if($product->compare_price)
                                            <small class="text-muted text-decoration-line-through ms-1">{{ number_format($product->compare_price) }}</small>
                                            @endif
                                        </div>
                                        @if($product->stock_quantity > 0)
                                        <span class="badge bg-success-subtle text-success">En stock</span>
                                        @else
                                        <span class="badge bg-danger-subtle text-danger">Rupture</span>
                                        @endif
                                    </div>
                                    <div class="d-flex gap-2">
                                        @if($product->stock_quantity > 0)
                                        <form action="{{ route('cart.add') }}" method="POST" class="flex-grow-1" data-ajax-cart>
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                                            <button type="submit" class="btn btn-primary w-100 btn-sm">
                                                <i class="fas fa-cart-plus me-1"></i> Ajouter au panier
                                            </button>
                                        </form>
                                        @endif
                                        <form action="{{ route('account.wishlist.toggle') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                                            <button type="submit" class="btn btn-outline-danger btn-sm" title="Retirer">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    @endforeach
                </div>

                <div class="mt-4 d-flex justify-content-center">
                    {{ $items->links() }}
                </div>
                @endif

            </div>
        </div>
    </div>

@endsection
