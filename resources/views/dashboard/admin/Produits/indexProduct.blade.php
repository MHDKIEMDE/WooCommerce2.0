@extends('dashboard.admin.layout.app')

@section('Dashboard - Agribusiness Shop', 'Listes des produit')

@section('contents')
    <div id="layoutAuthentication">
        <div id="layoutAuthentication_content">
            <main>
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-12">
                            <div class="card shadow-lg border-0 rounded-lg mt-5">
                                <div class="card-header">
                                    <h3 class="text-center font-weight-light my-4">Listes des Produits</h3>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Nom du produit</th>
                                                    <th>Prix</th>
                                                    <th>Categories</th>
                                                    {{-- <th>Poids</th> --}}
                                                    <th>Images</th>
                                                    <th>Detail</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($productsWithCategories as $product)
                                                    <tr>

                                                        <td>{{ $product->name }}</td>
                                                        <td>{{ $product->price }}</td>
                                                        @if ($product->categories)
                                                            <td>{{ $product->categories->name }}</td>
                                                        @else
                                                            <td> <i class="fab fa-twitter"></i></a></td>
                                                        @endif
                                                        {{-- <td>{{ $product->poids }}</td> --}}
                                                        <td><img src="{{ asset('storage/images/' . $product->image_path) }}"
                                                                alt="Image de la produits" style="max-width: 100px;"></td>
                                                        <td>
                                                            <a href="{{ route('produits.show', $product->id) }}"
                                                                class="btn btn-info">Voir</a>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
@endsection
