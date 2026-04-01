@extends('dashboard.admin.layout.app')

@section('title', "Modifier le produit")

@section('contents')
<div class="container-fluid px-4">
    <h1 class="mt-4">Modifier un produit</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">Produits</a></li>
        <li class="breadcrumb-item active">{{ $product->name }}</li>
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

    <form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row">
            {{-- Colonne gauche --}}
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header"><strong>Informations générales</strong></div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Nom du produit <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', $product->name) }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description courte</label>
                            <textarea name="short_description" rows="2"
                                class="form-control @error('short_description') is-invalid @enderror">{{ old('short_description', $product->short_description) }}</textarea>
                            @error('short_description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description complète <span class="text-danger">*</span></label>
                            <textarea name="description" rows="6"
                                class="form-control @error('description') is-invalid @enderror" required>{{ old('description', $product->description) }}</textarea>
                            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header"><strong>Images</strong></div>
                    <div class="card-body">
                        @if ($product->images->isNotEmpty())
                            <div class="row g-2 mb-3">
                                @foreach ($product->images as $img)
                                    <div class="col-auto">
                                        <img src="{{ $img->url }}" alt="" style="width:80px;height:80px;object-fit:cover;"
                                            class="rounded border {{ $img->is_primary ? 'border-success border-3' : '' }}">
                                        @if ($img->is_primary)
                                            <div class="text-center"><small class="text-success">Principale</small></div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif
                        <div class="mb-1">
                            <label class="form-label">Ajouter de nouvelles images</label>
                            <input type="file" name="images[]" multiple accept="image/*"
                                class="form-control @error('images.*') is-invalid @enderror">
                            <div class="form-text">Les nouvelles images s'ajoutent aux existantes. Formats : JPG, PNG, WebP. Max 2 Mo.</div>
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
                                value="{{ old('price', $product->price) }}" required>
                            @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Prix barré (FCFA)</label>
                            <input type="number" name="compare_price" step="0.01" min="0"
                                class="form-control @error('compare_price') is-invalid @enderror"
                                value="{{ old('compare_price', $product->compare_price) }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Stock <span class="text-danger">*</span></label>
                            <input type="number" name="stock_quantity" min="0"
                                class="form-control @error('stock_quantity') is-invalid @enderror"
                                value="{{ old('stock_quantity', $product->stock_quantity) }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Unité</label>
                            <input type="text" name="unit" placeholder="ex: kg, L, pièce"
                                class="form-control"
                                value="{{ old('unit', $product->unit) }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">SKU</label>
                            <input type="text" name="sku" class="form-control @error('sku') is-invalid @enderror"
                                value="{{ old('sku', $product->sku) }}">
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
                                    <option value="{{ $cat->id }}"
                                        {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>
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
                                    <option value="{{ $brand->id }}"
                                        {{ old('brand_id', $product->brand_id) == $brand->id ? 'selected' : '' }}>
                                        {{ $brand->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                        <div class="mb-3">
                            <label class="form-label">Statut</label>
                            <select name="status" class="form-select">
                                <option value="active" {{ old('status', $product->status) === 'active' ? 'selected' : '' }}>Actif</option>
                                <option value="draft" {{ old('status', $product->status) === 'draft' ? 'selected' : '' }}>Brouillon</option>
                                <option value="archived" {{ old('status', $product->status) === 'archived' ? 'selected' : '' }}>Archivé</option>
                            </select>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="featured" value="1" id="featured"
                                {{ old('featured', $product->featured) ? 'checked' : '' }}>
                            <label class="form-check-label" for="featured">Produit mis en avant</label>
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-success btn-lg">
                        <i class="fas fa-save me-1"></i> Enregistrer les modifications
                    </button>
                    <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">Annuler</a>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
