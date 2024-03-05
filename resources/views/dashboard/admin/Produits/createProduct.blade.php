@extends('dashboard.admin.layout.app')

@section('Dashboard - Agribusiness Shop', "Ajout d'un produit")

@section('contents')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <h1 class="mt-4 p-3">Formulaire d'ajout de produits</h1>
                <ol class="breadcrumb mb-4 p-3">
                    <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                    <li class="breadcrumb-item active">Formulaire</li>
                </ol>
                <div class="card-body">
                    <form method="POST" action="{{ route('produits.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">Nom du produit</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="price" class="form-label">Prix</label>
                            <input type="text" class="form-control" id="price" name="price" required>
                        </div>
                        <div class="mb-3">
                            <label for="category" class="form-label">Catégorie</label>
                            <select class="form-select" id="category" name="category_id" required>
                                <option value="">Sélectionner une catégorie</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="images" class="form-label">Images</label>
                            <input type="file" class="form-control" id="images" name="images[]" multiple required>
                        </div>
                        <button type="submit" class="btn btn-primary">Ajouter</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
