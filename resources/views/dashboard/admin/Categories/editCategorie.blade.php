@extends('dashboard.admin.layout.app')

@section('title', 'Modifier la catégorie')

@section('contents')
<div class="container-fluid px-4">
    <h1 class="mt-4">Modifier la catégorie</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.categories.index') }}">Catégories</a></li>
        <li class="breadcrumb-item active">{{ $category->name }}</li>
    </ol>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-7">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.categories.update', $category) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">Nom <span class="text-danger">*</span></label>
                            <input type="text" name="name"
                                class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', $category->name) }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" rows="3" class="form-control">{{ old('description', $category->description) }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Catégorie parente</label>
                            <select name="parent_id" class="form-select">
                                <option value="">— Aucune (catégorie principale) —</option>
                                @foreach ($parents as $parent)
                                    <option value="{{ $parent->id }}"
                                        {{ old('parent_id', $category->parent_id) == $parent->id ? 'selected' : '' }}>
                                        {{ $parent->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Image</label>
                            @if ($category->image_url)
                                <div class="mb-2">
                                    <img src="{{ $category->image_url }}" alt="{{ $category->name }}"
                                        style="height:80px;object-fit:cover;" class="rounded border">
                                    <div class="form-text">Image actuelle. Choisissez une nouvelle image pour la remplacer.</div>
                                </div>
                            @endif
                            <input type="file" name="image" accept="image/*"
                                class="form-control @error('image') is-invalid @enderror">
                            @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Ordre d'affichage</label>
                                <input type="number" name="sort_order" min="0" class="form-control"
                                    value="{{ old('sort_order', $category->sort_order ?? 0) }}">
                            </div>
                            <div class="col-md-6 mb-3 d-flex align-items-end">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active"
                                        {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">Catégorie active</label>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-1"></i> Enregistrer les modifications
                            </button>
                            <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">Annuler</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
