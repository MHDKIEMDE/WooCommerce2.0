@extends('layouts.app')
@section('Agribusiness Shop', 'Home')
@section('content')
    <!-- Hero Start -->
    <div class="container-fluid py-5 mb-5 hero-header">
        <div class="container py-5">
            <div class="row g-5 align-items-center">
                <div class="col-md-12 col-lg-7">
                    <h4 class="mb-3 text-secondary">Aliments 100 % biologiques</h4>
                    <h1 class="mb-5 display-3 text-primary">Aliments biologiques à base de fruits</h1>


                    <div class="position-relative mx-auto">
                        <form action="/recherche" method="get" class="position-relative mx-auto d-flex align-items-center">
                            <input class="form-control border-2 border-secondary w-75 py-3 px-4 rounded-pill" type="text"
                                name="q" placeholder="Recherche">

                            <button type="submit"
                                class="btn btn-primary border-2 border-secondary py-3 px-4 position-absolute rounded-pill text-white h-100"
                                style="top: 0; right: 25%;">Soumettre</button>
                        </form>
                    </div>

                    @if (isset($query))
                        @if (!empty($results))
                            <h2>Résultats de la recherche :</h2>
                            <ol>
                                @foreach ($query as $result)
                                    <li>{{ $result->name }}</li>
                                @endforeach
                            </ol>
                        @else
                            <p>Aucun résultat trouvé pour "{{ $query }}"</p>
                        @endif
                    @else
                        <div class="d-none">Résultats de la recherche</div>
                    @endif
                    
                </div>
                <div class="col-md-12 col-lg-5">
                    <div id="carouselId" class="carousel slide position-relative" data-bs-ride="carousel">
                        <div class="carousel-inner" role="listbox">
                            @foreach ($categories->chunk(1) as $chunk)
                                <div class="carousel-item {{ $loop->first ? 'active' : '' }} rounded">
                                    <div class="row justify-content-center align-items-center">
                                        @foreach ($chunk as $category)
                                            {{-- @if (isset($category->image_path)) --}}
                                            <div class="col-md-6">
                                                <div class="d-flex flex-column align-items-center">
                                                    {{-- <h5 class="mt-2">{{ $category->name }}</h5> --}}
                                                    <img src="{{ asset($category->image_path) }}"
                                                        alt="{{ $category->name }}" style="max-width: 300px;">
                                                </div>
                                            </div>
                                            {{-- @endif --}}
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#carouselId"
                            data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Précédent</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#carouselId"
                            data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Suivant</span>
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!-- Hero End -->
    <!-- Featurs Section Start -->
    <div class="container-fluid featurs py-5">
        <div class="container py-5">
            <div class="row g-4">
                <div class="col-md-6 col-lg-3">
                    <div class="featurs-item text-center rounded bg-light p-4">
                        <div class="featurs-icon btn-square rounded-circle bg-secondary mb-5 mx-auto">
                            <i class="fas fa-car-side fa-3x text-white"></i>
                        </div>
                        <div class="featurs-content text-center">
                            <h5>Livraison Possible</h5>
                            <p class="mb-0">Gratuit à partir de 300 $ d'achat</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="featurs-item text-center rounded bg-light p-4">
                        <div class="featurs-icon btn-square rounded-circle bg-secondary mb-5 mx-auto">
                            <i class="fas fa-user-shield fa-3x text-white"></i>
                        </div>
                        <div class="featurs-content text-center">
                            <h5>Paiement de sécurité</h5>
                            <p class="mb-0">Paiement sécurisé à 100</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="featurs-item text-center rounded bg-light p-4">
                        <div class="featurs-icon btn-square rounded-circle bg-secondary mb-5 mx-auto">
                            <i class="fas fa-exchange-alt fa-3x text-white"></i>
                        </div>
                        <div class="featurs-content text-center">
                            <h5>Retour sous 1 jours</h5>
                            <p class="mb-0">Garantie de 1h</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="featurs-item text-center rounded bg-light p-4">
                        <div class="featurs-icon btn-square rounded-circle bg-secondary mb-5 mx-auto">
                            <i class="fa fa-phone-alt fa-3x text-white"></i>
                        </div>
                        <div class="featurs-content text-center">
                            <h5>Assistance 24/7</h5>
                            <p class="mb-0">Un soutien toujours rapide</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Featurs Section End -->
    <!-- Fruits Shop Start-->
    <div class="container-fluid fruite py-5">
        <div class="container py-5">
            <div class="tab-class text-center">
                <div class="row g-4">
                    <div class="col-lg-4 text-start">
                        <h1>Our Organic Products</h1>
                    </div>
                    <div class="col-lg-8 text-end">
                        <ul class="nav nav-pills d-inline-flex text-center mb-5">
                            <li class="nav-item">
                                <a class="d-flex m-2 py-2 bg-light rounded-pill active" data-bs-toggle="pill"
                                    href="#tab-1">
                                    <span class="text-dark" style="width: 130px;"> Tous les produits </span>
                                </a>
                            </li>
                            @foreach ($categories as $category)
                                <li class="nav-item">
                                    <a id="category-{{ $category->id }}"
                                        class="d-flex py-2 m-2 bg-light rounded-pill category-link" data-bs-toggle="pill"
                                        href="#">
                                        <span class="text-dark" style="width: 130px;">{{ $category->name }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="tab-content">
                    <div id="tab-1" class="tab-pane fade show p-0 active">
                        <div class="row g-4">
                            <div class="col-lg-12">
                                <div class="row g-4 justify-content-center">
                                    @if ($productsWithCategories->isEmpty())
                                        <div class="col">Aucun produit disponible pour le moment.</div>
                                    @else
                                        @foreach ($productsWithCategories as $product)
                                            <div class="col-md-3 col-lg-3 col-xl-3 col-sm-8">
                                                <div class="rounded position-relative fruite-item">
                                                    <div class="fruite-img">
                                                        @if ($product->images->isNotEmpty())
                                                            <a href="{{ route('produits.show', $product->id) }}">
                                                                <img src="{{ asset('storage/images/' . $product->images->first()->image_path) }}"
                                                                    class="img-fluid rounded-top product-image"
                                                                    alt="{{ $product->name }}"
                                                                    style="width: 300px; height: 200px; object-fit: cover;"></a>
                                                        @else
                                                            <img src="{{ asset('storage/images/image_default.png') }}"
                                                                class="img-fluid rounded-top product-image"
                                                                alt="Image par défaut"
                                                                style="width: 300px; height: 200px; object-fit: cover;">

                                                            </a>
                                                        @endif
                                                    </div>
                                                    {{-- <div class="text-white bg-secondary px-3 py-1 rounded position-absolute"
                                                        style="top: 10px; left: 10px;">
                                                        @if ($product->categories)
                                                            {{ $product->categories->name }}
                                                        @else
                                                            Aucune catégorie définie
                                                        @endif
                                                    </div> --}}
                                                    <div class="p-4 border border-secondary border-top-0 rounded-bottom">
                                                        <h4>{{ $product->name }}</h4>
                                                        <p>{{ $product->description }}</p>
                                                        <div class="d-flex justify-content-between flex-lg-wrap">
                                                            <p class="text-dark fs-5 fw-bold mb-0">f{{ $product->price }}
                                                                /
                                                                kg
                                                            </p>
                                                            <a href="{{ route('home.cart') }}"
                                                                class="btn border border-secondary rounded-pill px-3 text-primary">
                                                                <i class="fa fa-shopping-bag me-2 text-primary"></i> Add to
                                                                cart
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
                                            <a href="{{ $productsWithCategories->previousPageUrl() }}"
                                                class="rounded">&laquo;</a>
                                        @endif
                                        @foreach ($productsWithCategories->getUrlRange(1, $productsWithCategories->lastPage()) as $page => $url)
                                            <a href="{{ $url }}"
                                                class="rounded{{ $page == $productsWithCategories->currentPage() ? ' active' : '' }}">{{ $page }}</a>
                                        @endforeach
                                        @if ($productsWithCategories->hasMorePages())
                                            <a href="{{ $productsWithCategories->nextPageUrl() }}"
                                                class="rounded">&raquo;</a>
                                        @else
                                            <a class="rounded disabled">&raquo;</a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="tab-2" class="tab-pane fade show p-0">
                        <div class="row g-4">
                            <div class="col-lg-12">
                                <div class="row g-4">
                                    <div class="col-md-6 col-lg-4 col-xl-3">
                                        <div class="rounded position-relative fruite-item">
                                            <div class="fruite-img">
                                                <img src="img/fruite-item-5.jpg" class="img-fluid w-100 rounded-top"
                                                    alt="">
                                            </div>
                                            <div class="text-white bg-secondary px-3 py-1 rounded position-absolute"
                                                style="top: 10px; left: 10px;">Fruits</div>
                                            <div class="p-4 border border-secondary border-top-0 rounded-bottom">
                                                <h4>Raisins</h4>
                                                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit sed do eiusmod te
                                                    incididunt</p>
                                                <div class="d-flex justify-content-between flex-lg-wrap">
                                                    <p class="text-dark fs-5 fw-bold mb-0">$4.99 / kg</p>
                                                    <a href="#"
                                                        class="btn border border-secondary rounded-pill px-3 text-primary"><i
                                                            class="fa fa-shopping-bag me-2 text-primary"></i> Ajouter au
                                                        panier</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-4 col-xl-3">
                                        <div class="rounded position-relative fruite-item">
                                            <div class="fruite-img">
                                                <img src="img/fruite-item-2.jpg" class="img-fluid w-100 rounded-top"
                                                    alt="">
                                            </div>
                                            <div class="text-white bg-secondary px-3 py-1 rounded position-absolute"
                                                style="top: 10px; left: 10px;">Fruits</div>
                                            <div class="p-4 border border-secondary border-top-0 rounded-bottom">
                                                <h4>Framboises</h4>
                                                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit sed do eiusmod te
                                                    incididunt</p>
                                                <div class="d-flex justify-content-between flex-lg-wrap">
                                                    <p class="text-dark fs-5 fw-bold mb-0">$4.99 / kg</p>
                                                    <a href="#"
                                                        class="btn border border-secondary rounded-pill px-3 text-primary"><i
                                                            class="fa fa-shopping-bag me-2 text-primary"></i> Ajouter au
                                                        panier</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="tab-3" class="tab-pane fade show p-0">
                        <div class="row g-4">
                            <div class="col-lg-12">
                                <div class="row g-4">
                                    <div class="col-md-6 col-lg-4 col-xl-3">
                                        <div class="rounded position-relative fruite-item">
                                            <div class="fruite-img">
                                                <img src="img/fruite-item-1.jpg" class="img-fluid w-100 rounded-top"
                                                    alt="">
                                            </div>
                                            <div class="text-white bg-secondary px-3 py-1 rounded position-absolute"
                                                style="top: 10px; left: 10px;">Fruits</div>
                                            <div class="p-4 border border-secondary border-top-0 rounded-bottom">
                                                <h4>Oranges</h4>
                                                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit sed do eiusmod te
                                                    incididunt</p>
                                                <div class="d-flex justify-content-between flex-lg-wrap">
                                                    <p class="text-dark fs-5 fw-bold mb-0">$4.99 / kg</p>
                                                    <a href="#"
                                                        class="btn border border-secondary rounded-pill px-3 text-primary"><i
                                                            class="fa fa-shopping-bag me-2 text-primary"></i>Ajouter au
                                                        panier</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-4 col-xl-3">
                                        <div class="rounded position-relative fruite-item">
                                            <div class="fruite-img">
                                                <img src="img/fruite-item-6.jpg" class="img-fluid w-100 rounded-top"
                                                    alt="">
                                            </div>
                                            <div class="text-white bg-secondary px-3 py-1 rounded position-absolute"
                                                style="top: 10px; left: 10px;">Fruits</div>
                                            <div class="p-4 border border-secondary border-top-0 rounded-bottom">
                                                <h4>Pomme</h4>
                                                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit sed do eiusmod te
                                                    incididunt</p>
                                                <div class="d-flex justify-content-between flex-lg-wrap">
                                                    <p class="text-dark fs-5 fw-bold mb-0">$4.99 / kg</p>
                                                    <a href="#"
                                                        class="btn border border-secondary rounded-pill px-3 text-primary"><i
                                                            class="fa fa-shopping-bag me-2 text-primary"></i> Ajouter au
                                                        panier</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="tab-4" class="tab-pane fade show p-0">
                        <div class="row g-4">
                            <div class="col-lg-12">
                                <div class="row g-4">
                                    <div class="col-md-6 col-lg-4 col-xl-3">
                                        <div class="rounded position-relative fruite-item">
                                            <div class="fruite-img">
                                                <img src="img/fruite-item-5.jpg" class="img-fluid w-100 rounded-top"
                                                    alt="">
                                            </div>
                                            <div class="text-white bg-secondary px-3 py-1 rounded position-absolute"
                                                style="top: 10px; left: 10px;">Fruits</div>
                                            <div class="p-4 border border-secondary border-top-0 rounded-bottom">
                                                <h4>Raisins</h4>
                                                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit sed do eiusmod te
                                                    incididunt</p>
                                                <div class="d-flex justify-content-between flex-lg-wrap">
                                                    <p class="text-dark fs-5 fw-bold mb-0">$4.99 / kg</p>
                                                    <a href="#"
                                                        class="btn border border-secondary rounded-pill px-3 text-primary"><i
                                                            class="fa fa-shopping-bag me-2 text-primary"></i> Ajouter au
                                                        panier</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-4 col-xl-3">
                                        <div class="rounded position-relative fruite-item">
                                            <div class="fruite-img">
                                                <img src="img/fruite-item-4.jpg" class="img-fluid w-100 rounded-top"
                                                    alt="">
                                            </div>
                                            <div class="text-white bg-secondary px-3 py-1 rounded position-absolute"
                                                style="top: 10px; left: 10px;">Fruits</div>
                                            <div class="p-4 border border-secondary border-top-0 rounded-bottom">
                                                <h4>Abricots</h4>
                                                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit sed do eiusmod te
                                                    incididunt</p>
                                                <div class="d-flex justify-content-between flex-lg-wrap">
                                                    <p class="text-dark fs-5 fw-bold mb-0">$4.99 / kg</p>
                                                    <a href="#"
                                                        class="btn border border-secondary rounded-pill px-3 text-primary"><i
                                                            class="fa fa-shopping-bag me-2 text-primary"></i> Ajouter au
                                                        panier</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="tab-5" class="tab-pane fade show p-0">
                        <div class="row g-4">
                            <div class="col-lg-12">
                                <div class="row g-4">
                                    <div class="col-md-6 col-lg-4 col-xl-3">
                                        <div class="rounded position-relative fruite-item">
                                            <div class="fruite-img">
                                                <img src="img/fruite-item-3.jpg" class="img-fluid w-100 rounded-top"
                                                    alt="">
                                            </div>
                                            <div class="text-white bg-secondary px-3 py-1 rounded position-absolute"
                                                style="top: 10px; left: 10px;">Fruits</div>
                                            <div class="p-4 border border-secondary border-top-0 rounded-bottom">
                                                <h4>Banane</h4>
                                                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit sed do eiusmod te
                                                    incididunt</p>
                                                <div class="d-flex justify-content-between flex-lg-wrap">
                                                    <p class="text-dark fs-5 fw-bold mb-0">$4.99 / kg</p>
                                                    <a href="#"
                                                        class="btn border border-secondary rounded-pill px-3 text-primary"><i
                                                            class="fa fa-shopping-bag me-2 text-primary"></i> Ajouter au
                                                        panier</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-4 col-xl-3">
                                        <div class="rounded position-relative fruite-item">
                                            <div class="fruite-img">
                                                <img src="img/fruite-item-2.jpg" class="img-fluid w-100 rounded-top"
                                                    alt="">
                                            </div>
                                            <div class="text-white bg-secondary px-3 py-1 rounded position-absolute"
                                                style="top: 10px; left: 10px;">Fruits</div>
                                            <div class="p-4 border border-secondary border-top-0 rounded-bottom">
                                                <h4>Framboises</h4>
                                                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit sed do eiusmod te
                                                    incididunt</p>
                                                <div class="d-flex justify-content-between flex-lg-wrap">
                                                    <p class="text-dark fs-5 fw-bold mb-0">$4.99 / kg</p>
                                                    <a href="#"
                                                        class="btn border border-secondary rounded-pill px-3 text-primary"><i
                                                            class="fa fa-shopping-bag me-2 text-primary"></i> Ajouter au
                                                        chariot</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-4 col-xl-3">
                                        <div class="rounded position-relative fruite-item">
                                            <div class="fruite-img">
                                                <img src="img/fruite-item-1.jpg" class="img-fluid w-100 rounded-top"
                                                    alt="">
                                            </div>
                                            <div class="text-white bg-secondary px-3 py-1 rounded position-absolute"
                                                style="top: 10px; left: 10px;">Fruits</div>
                                            <div class="p-4 border border-secondary border-top-0 rounded-bottom">
                                                <h4>Oranges</h4>
                                                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit sed do eiusmod te
                                                    incididunt</p>
                                                <div class="d-flex justify-content-between flex-lg-wrap">
                                                    <p class="text-dark fs-5 fw-bold mb-0">$4.99 / kg</p>
                                                    <a href="#"
                                                        class="btn border border-secondary rounded-pill px-3 text-primary"><i
                                                            class="fa fa-shopping-bag me-2 text-primary"></i> Ajouter au
                                                        panier</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Fruits Shop End-->
    <!-- Featurs Start -->
    <div class="container-fluid service py-5">
        <div class="container py-5">
            <div class="row g-4 justify-content-center">
                <div class="col-md-6 col-lg-4">
                    <a href="#">
                        <div class="service-item bg-secondary rounded border border-secondary">
                            <img src="img/featur-1.jpg" class="img-fluid rounded-top w-100" alt="">
                            <div class="px-4 rounded-bottom">
                                <div class="service-content bg-primary text-center p-4 rounded">
                                    <h5 class="text-white">Pommes fraîches</h5>
                                    <h3 class="mb-0">20 % DE RÉDUCTION</h3>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-6 col-lg-4">
                    <a href="#">
                        <div class="service-item bg-dark rounded border border-dark">
                            <img src="img/featur-2.jpg" class="img-fluid rounded-top w-100" alt="">
                            <div class="px-4 rounded-bottom">
                                <div class="service-content bg-light text-center p-4 rounded">
                                    <h5 class="text-primary">Fruits savoureux</h5>
                                    <h3 class="mb-0">Livraison Possible</h3>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-6 col-lg-4">
                    <a href="#">
                        <div class="service-item bg-primary rounded border border-primary">
                            <img src="img/featur-3.jpg" class="img-fluid rounded-top w-100" alt="">
                            <div class="px-4 rounded-bottom">
                                <div class="service-content bg-secondary text-center p-4 rounded">
                                    <h5 class="text-white">Légumes exotiques</h5>
                                    <h3 class="mb-0">Remise de 30$</h3>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <!-- Featurs End -->
    <!-- Vesitable Shop Start-->
    <div class="container-fluid vesitable py-5">
        <div class="container py-5">
            <h1 class="mb-0">Légumes frais biologiques</h1>

            <div class="owl-carousel vegetable-carousel justify-content-center">
                @if ($productsWithCategories->isEmpty())
                    <div class="col">Aucun produit disponible pour le moment.</div>
                @else
                    @foreach ($productsWithCategories as $product)
                        <div class="border border-primary rounded position-relative vesitable-item">
                            <div class="vesitable-img">
                                @if ($product->images->isNotEmpty())
                                    <a href="{{ route('produits.show', $product->id) }}">
                                        <img src="{{ asset('storage/images/' . $product->images->first()->image_path) }}"
                                            class="img-fluid rounded-top product-image" alt="{{ $product->name }}"
                                            style="width: 300px; height: 200px; object-fit: cover;">
                                    </a>
                                @else
                                    <a href=""><img src="{{ asset('storage/images/image_default.png') }}"
                                            class="img-fluid rounded-top product-image" alt="Image par défaut"
                                            style="width: 300px; height: 200px; object-fit: cover;">
                                    </a>
                                @endif
                            </div>

                            <div class="text-white bg-primary px-3 py-1 rounded position-absolute"
                                style="top: 10px; right: 10px;">
                                @if ($product->categories)
                                    {{ $product->category->name }}
                                @else
                                    Aucune
                                @endif

                            </div>
                            <div class="p-4 rounded-bottom">
                                <h4>{{ $product->name }}</h4>
                                <p>{{ $product->description }}</p>
                                <div class="d-flex justify-content-between flex-lg-wrap">
                                    <p class="text-dark fs-5 fw-bold mb-0">f{{ $product->price }}</p>
                                    <a href="#"
                                        class="btn border border-secondary rounded-pill px-3 text-primary"><i
                                            class="fa fa-shopping-bag me-2 text-primary"></i> </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>


        </div>
    </div>
    <!-- Vesitable Shop End -->
    <!-- Banner Section Start-->
    <div class="container-fluid banner bg-secondary my-5">
        <div class="container py-5">
            <div class="row g-4 align-items-center">
                <div class="col-lg-6">
                    <div class="py-4">
                        <h1 class="display-3 text-white">Fruits frais exotiques</h1>
                        <p class="fw-normal display-3 text-dark mb-4">dans notre magasin</p>
                        <p class="mb-4 text-dark">The generated Lorem Ipsum is therefore always free from repetition
                            injected humour, or non-characteristic words etc.</p>
                        <a href="#"
                            class="banner-btn btn border-2 border-white rounded-pill text-dark py-3 px-5">ACHETER</a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="position-relative">
                        <img src="img/baner-1.png" class="img-fluid w-100 rounded" alt="">
                        <div class="d-flex align-items-center justify-content-center bg-white rounded-circle position-absolute"
                            style="width: 140px; height: 140px; top: 0; left: 0;">
                            <h1 style="font-size: 100px;">1</h1>
                            <div class="d-flex flex-column">
                                <span class="h2 mb-0">50$</span>
                                <span class="h4 text-muted mb-0">kg</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Banner Section End -->
    <!-- Bestsaler Product Start -->
    <div class="container-fluid py-5">
        <div class="container py-5">
            <div class="text-center mx-auto mb-5" style="max-width: 700px;">
                <h1 class="display-4">Les produits les plus vendus</h1>
                <p>Latin words, combined with a handful of model sentence structures, to generate Lorem Ipsum which looks
                    reasonable.</p>
            </div>
            <div class="row g-4">
                @if ($productsWithCategories->isEmpty())
                    <div class="col">Aucun produit disponible pour le moment.</div>
                @else
                    @foreach ($productsWithCategories as $product)
                        <div class="col-lg-6 col-xl-4">

                            <div class="p-4 rounded bg-light">
                                <div class="row align-items-center">
                                    <div class="col-6">
                                        <img src="img/best-product-1.jpg" class="img-fluid rounded-circle w-100"
                                            alt="">
                                    </div>
                                    {{-- @if ($product->images->isNotEmpty())
                                    <a href="{{ route('produits.show', $product->id) }}">
                                       
                                        <img src="{{ asset('storage/images/' . $product->images->first()->image_path) }}"
                                            class="img-fluid rounded-top product-image" alt="{{ $product->name }}"
                                            style="width: 300px; height: 200px; object-fit: cover;">
                                    </a>
                                @else
                                    <a href=""><img src="{{ asset('storage/images/image_default.png') }}"
                                            class="img-fluid rounded-top product-image" alt="Image par défaut"
                                            style="width: 300px; height: 200px; object-fit: cover;">
                                    </a>
                                @endif --}}
                                    <div class="col-6">

                                        <a href="#" class="h5">{{ $product->name }}</a>
                                        <div class="d-flex my-3">
                                            <i class="fas fa-star text-primary"></i>
                                            <i class="fas fa-star text-primary"></i>
                                            <i class="fas fa-star text-primary"></i>
                                            <i class="fas fa-star text-primary"></i>
                                            <i class="fas fa-star"></i>
                                        </div>
                                        <h4 class="mb-3">{{ $product->price }}</h4>
                                        <a href="#"
                                            class="btn border border-secondary rounded-pill px-3 text-primary"><i
                                                class="fa fa-shopping-bag me-2 text-primary"></i> Ajouter au panier</a>
                                    </div>
                                </div>


                            </div>

                        </div>
                    @endforeach
                @endif
            </div>
            <br>
            <div class="row g-4">
                @if ($categories->isEmpty())
                    <li>Aucune catégorie n'est disponible pour le moment.</li>
                @else
                    @foreach ($categories as $category)
                        <div class="col-md-6 col-lg-6 col-xl-3">
                            <div class="text-center fruite-img">
                                <img src="{{ asset($category->image_path) }}" alt="{{ $category->name }}"
                                    style="width: 300px; height: 200px; object-fit: cover;">
                                <div class="py-4">
                                    <a href="#" class="h5">{{ $category->name }}</a>
                                    <div class="d-flex my-3 justify-content-center">
                                        <i class="fas fa-star text-primary"></i>
                                        <i class="fas fa-star text-primary"></i>
                                        <i class="fas fa-star text-primary"></i>
                                        <i class="fas fa-star text-primary"></i>
                                        <i class="fas fa-star"></i>
                                    </div>
                                    <h4 class="mb-3">({{ $category->products->count() }})</h4>
                                    <a href="#"
                                        class="btn border border-secondary rounded-pill px-3 text-primary"><i
                                            class="fa fa-shopping-bag me-2 text-primary"></i> Ajouter au panier</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
    </div>
    <!-- Bestsaler Product End -->
    <!-- Fact Start -->
    <div class="container-fluid py-5">
        <div class="container">
            <div class="bg-light p-5 rounded">
                <div class="row g-4 justify-content-center">
                    <div class="col-md-6 col-lg-6 col-xl-3">
                        <div class="counter bg-white rounded p-5">
                            <i class="fa fa-users text-secondary"></i>
                            <h4>Des clients satisfaits</h4>
                            <h1>1963</h1>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-6 col-xl-3">
                        <div class="counter bg-white rounded p-5">
                            <i class="fa fa-users text-secondary"></i>
                            <h4>la qualité du service</h4>
                            <h1>99%</h1>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-6 col-xl-3">
                        <div class="counter bg-white rounded p-5">
                            <i class="fa fa-users text-secondary"></i>
                            <h4>Certificats de qualité</h4>
                            <h1>33</h1>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-6 col-xl-3">
                        <div class="counter bg-white rounded p-5">
                            <i class="fa fa-users text-secondary"></i>
                            <h4>Produits disponibles</h4>
                            <h1>789</h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Fact Start -->
    <!-- Tastimonial Start -->
    {{-- @if ($testimonials->isEmpty())
        <li class="text-center d-none">
        @else  --}}
    <div class="container-fluid testimonial py-5">
        <div class="container py-5">
            <div class="testimonial-header text-center">
                <h4 class="text-primary">Témoignage</h4>
                <h1 class="display-5 mb-5 text-dark">Ce que disent nos clients !</h1>
            </div>
            <div class="owl-carousel testimonial-carousel">
                @foreach ($testimonials as $testimonial)
                    <div class="testimonial-item img-border-radius bg-light rounded p-4">
                        <div class="position-relative">
                            <i class="fa fa-quote-right fa-2x text-secondary position-absolute"
                                style="bottom: 30px; right: 0;"></i>
                            <div class="mb-4 pb-4 border-bottom border-secondary">
                                <p class="mb-0">{{ $testimonial->description }}
                                </p>
                            </div>
                            <div class="d-flex align-items-center flex-nowrap">
                                <div class="bg-secondary rounded">
                                    <img src="img/testimonial-1.jpg" class="img-fluid rounded"
                                        style="width: 100px; height: 100px;" alt="">
                                </div>
                                <div class="ms-4 d-block">
                                    <h4 class="text-dark">{{ $testimonial->name }}</a></h4>
                                    <p class="m-0 pb-3">{{ $testimonial->profession }}</p>
                                    <div class="d-flex pe-5">
                                        <i class="fas fa-star text-primary"></i>
                                        <i class="fas fa-star text-primary"></i>
                                        <i class="fas fa-star text-primary"></i>
                                        <i class="fas fa-star text-primary"></i>
                                        <i class="fas fa-star"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    {{-- @endif --}}
    <!-- Tastimonial End -->
@endsection
