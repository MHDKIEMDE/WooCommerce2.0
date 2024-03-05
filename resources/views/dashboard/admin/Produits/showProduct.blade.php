@extends('dashboard.admin.layout.app')

@section('Dashboard - Agribusiness Shop', "Details d'un produit")

@section('contents')
    <!-- Single Page Header start -->
    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6">Details du produit</h1>
        <ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="#">Accueil</a></li>
            <li class="breadcrumb-item"><a href="#">Pages</a></li>
            <li class="breadcrumb-item active text-white">Details du produit</li>
        </ol>
    </div>
    <!-- Single Page Header End -->
    <div class="col-lg-9">
        <div class="row g-4 justify-content-center">
            @if ($productsWithCategories->isEmpty())
                <div class="col">Aucun produit disponible pour le moment.</div>
            @else
                @foreach ($productsWithCategories as $product)
                    <div class="col-md-6 col-lg-6 col-xl-4">
                        <div class="rounded position-relative fruite-item">
                            <div class="fruite-img">
                                <!-- Ajoutez une classe ou un identifiant pour le produit -->
                                <a href="#" class="product-link" data-product-id="{{ $product->id }}">
                                    <img src="{{ asset('storage/images/' . $product->image_path) }}"
                                        class="img-fluid w-100 rounded-top" alt="{{ $product->name }}">
                                </a>
                            </div>
                            <!-- Utilisez la classe 'product-details' pour afficher les détails du produit -->
                            <div class="product-details text-white bg-secondary px-3 py-1 rounded position-absolute"
                                style="top: 10px; left: 10px; display: none;">
                                @if ($product->categories)
                                    {{ $product->categories->name }}
                                @else
                                    Aucune catégorie définie
                                @endif
                            </div>
                            <div class="p-4 border border-secondary border-top-0 rounded-bottom">
                                <h4>{{ $product->name }}</h4>
                                <p>{{ $product->description }}</p>
                                <div class="d-flex justify-content-between flex-lg-wrap">
                                    <p class="text-dark fs-5 fw-bold mb-0">f{{ $product->price }} / kg</p>
                                    <a href="{{ route('home.cart') }}"
                                        class="btn border border-secondary rounded-pill px-3 text-primary">
                                        <i class="fa fa-shopping-bag me-2 text-primary"></i> Add to cart
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
        <!-- Pagination -->
        <div class="col-12">
            <div class="pagination d-flex justify-content-center mt-5">
                @if ($productsWithCategories->onFirstPage())
                    <a class="rounded disabled">&laquo;</a>
                @else
                    <a href="{{ $productsWithCategories->previousPageUrl() }}" class="rounded">&laquo;</a>
                @endif

                @foreach ($productsWithCategories->getUrlRange(1, $productsWithCategories->lastPage()) as $page => $url)
                    <a href="{{ $url }}"
                        class="rounded{{ $page == $productsWithCategories->currentPage() ? ' active' : '' }}">{{ $page }}</a>
                @endforeach

                @if ($productsWithCategories->hasMorePages())
                    <a href="{{ $productsWithCategories->nextPageUrl() }}" class="rounded">&raquo;</a>
                @else
                    <a class="rounded disabled">&raquo;</a>
                @endif
            </div>
        </div>
    </div>
    <script>
        // JavaScript pour afficher les détails du produit lors du clic sur l'image
        document.querySelectorAll('.product-link').forEach(link => {
            link.addEventListener('click', function(event) {
                event.preventDefault();
                const productId = this.getAttribute('data-product-id');
                const productDetails = document.querySelector(
                    `.product-details[data-product-id="${productId}"]`);
                if (productDetails) {
                    // Masquer tous les autres détails du produit
                    document.querySelectorAll('.product-details').forEach(details => {
                        details.style.display = 'none';
                    });
                    // Afficher les détails du produit cliqué
                    productDetails.style.display = 'block';
                }
            });
        });
    </script>
@endsection
