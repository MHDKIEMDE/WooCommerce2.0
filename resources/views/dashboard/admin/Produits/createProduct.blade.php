@extends('dashboard.admin.layout.app')

@section('title', "Ajouter un produit")

@section('contents')
<div class="container-fluid px-4">
    <h1 class="mt-4">Ajouter un produit</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">Produits</a></li>
        <li class="breadcrumb-item active">Nouveau produit</li>
    </ol>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="row">
            {{-- Colonne gauche --}}
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header"><strong>Informations générales</strong></div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Nom du produit <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name') }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description courte</label>
                            <textarea name="short_description" rows="2"
                                class="form-control @error('short_description') is-invalid @enderror">{{ old('short_description') }}</textarea>
                            @error('short_description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description complète <span class="text-danger">*</span></label>
                            <textarea name="description" rows="6"
                                class="form-control @error('description') is-invalid @enderror" required>{{ old('description') }}</textarea>
                            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header"><strong>Images</strong></div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Photos du produit</label>
                            <input type="file" name="images[]" multiple accept="image/*"
                                class="form-control @error('images.*') is-invalid @enderror">
                            <div class="form-text">Formats acceptés : JPG, PNG, WebP. Max 2 Mo par image. La première image sera l'image principale.</div>
                            @error('images.*')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Colonne droite --}}
            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header"><strong>Prix & Stock</strong></div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Prix (FCFA) <span class="text-danger">*</span></label>
                            <input type="number" name="price" step="0.01" min="0"
                                class="form-control @error('price') is-invalid @enderror"
                                value="{{ old('price') }}" required>
                            @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Prix barré (FCFA)</label>
                            <input type="number" name="compare_price" step="0.01" min="0"
                                class="form-control @error('compare_price') is-invalid @enderror"
                                value="{{ old('compare_price') }}">
                            @error('compare_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Stock <span class="text-danger">*</span></label>
                            <input type="number" name="stock_quantity" min="0"
                                class="form-control @error('stock_quantity') is-invalid @enderror"
                                value="{{ old('stock_quantity', 0) }}" required>
                            @error('stock_quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Unité</label>
                            <input type="text" name="unit" placeholder="ex: kg, L, pièce"
                                class="form-control @error('unit') is-invalid @enderror"
                                value="{{ old('unit') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">SKU</label>
                            <input type="text" name="sku"
                                class="form-control @error('sku') is-invalid @enderror"
                                value="{{ old('sku') }}">
                            @error('sku')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header"><strong>Organisation</strong></div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Catégorie <span class="text-danger">*</span></label>
                            <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                                <option value="">— Choisir —</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        @if ($brands->isNotEmpty())
                        <div class="mb-3">
                            <label class="form-label">Marque</label>
                            <select name="brand_id" class="form-select">
                                <option value="">— Aucune —</option>
                                @foreach ($brands as $brand)
                                    <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>
                                        {{ $brand->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                        <div class="mb-3">
                            <label class="form-label">Statut</label>
                            <select name="status" class="form-select">
                                <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Actif</option>
                                <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Brouillon</option>
                                <option value="archived" {{ old('status') === 'archived' ? 'selected' : '' }}>Archivé</option>
                            </select>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="featured" value="1" id="featured"
                                {{ old('featured') ? 'checked' : '' }}>
                            <label class="form-check-label" for="featured">Produit mis en avant</label>
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-success btn-lg">
                        <i class="fas fa-save me-1"></i> Enregistrer le produit
                    </button>
                    <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">Annuler</a>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
