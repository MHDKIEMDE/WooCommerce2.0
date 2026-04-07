@extends('dashboard.admin.layout.app')

@section('title', 'Ajouter un slide')

@section('contents')
<div class="container-fluid px-4">
    <h1 class="mt-4">Ajouter un slide</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.slides.index') }}">Slides</a></li>
        <li class="breadcrumb-item active">Créer</li>
    </ol>

    <div class="card mb-4" style="max-width:600px">
        <div class="card-body">
            <form action="{{ route('admin.slides.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Image <span class="text-danger">*</span></label>
                    <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/*" required>
                    @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Titre</label>
                    <input type="text" name="title" value="{{ old('title') }}"
                           class="form-control @error('title') is-invalid @enderror"
                           placeholder="Ex : Aliments 100% biologiques">
                    @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Sous-titre</label>
                    <input type="text" name="subtitle" value="{{ old('subtitle') }}"
                           class="form-control @error('subtitle') is-invalid @enderror"
                           placeholder="Ex : Frais du producteur au consommateur">
                    @error('subtitle') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Texte du bouton</label>
                        <input type="text" name="button_text" value="{{ old('button_text') }}"
                               class="form-control" placeholder="Ex : Voir les produits">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Lien du bouton</label>
                        <input type="text" name="button_url" value="{{ old('button_url') }}"
                               class="form-control" placeholder="/shop">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Ordre d'affichage</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}"
                           class="form-control" min="0" style="width:100px">
                </div>

                <div class="mb-3 form-check">
                    <input type="checkbox" name="is_active" id="is_active" class="form-check-input"
                           value="1" {{ old('is_active', '1') ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">Actif (visible sur le site)</label>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                    <a href="{{ route('admin.slides.index') }}" class="btn btn-secondary">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
