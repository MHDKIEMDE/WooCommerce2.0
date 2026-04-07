@extends('dashboard.admin.layout.app')

@section('title', 'Modifier le slide')

@section('contents')
<div class="container-fluid px-4">
    <h1 class="mt-4">Modifier le slide</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.slides.index') }}">Slides</a></li>
        <li class="breadcrumb-item active">Modifier</li>
    </ol>

    <div class="card mb-4" style="max-width:600px">
        <div class="card-body">
            <form action="{{ route('admin.slides.update', $slide) }}" method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Image actuelle</label><br>
                    <img src="{{ Storage::url($slide->image_path) }}" alt="slide"
                         style="width:200px;height:120px;object-fit:cover;border-radius:6px;margin-bottom:8px">
                </div>

                <div class="mb-3">
                    <label class="form-label">Nouvelle image <small class="text-muted">(laisser vide pour conserver)</small></label>
                    <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/*">
                    @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Titre</label>
                    <input type="text" name="title" value="{{ old('title', $slide->title) }}"
                           class="form-control @error('title') is-invalid @enderror">
                    @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Sous-titre</label>
                    <input type="text" name="subtitle" value="{{ old('subtitle', $slide->subtitle) }}"
                           class="form-control @error('subtitle') is-invalid @enderror">
                    @error('subtitle') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Texte du bouton</label>
                        <input type="text" name="button_text" value="{{ old('button_text', $slide->button_text) }}"
                               class="form-control">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Lien du bouton</label>
                        <input type="text" name="button_url" value="{{ old('button_url', $slide->button_url) }}"
                               class="form-control">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Ordre d'affichage</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', $slide->sort_order) }}"
                           class="form-control" min="0" style="width:100px">
                </div>

                <div class="mb-3 form-check">
                    <input type="checkbox" name="is_active" id="is_active" class="form-check-input"
                           value="1" {{ $slide->is_active ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">Actif (visible sur le site)</label>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Mettre à jour</button>
                    <a href="{{ route('admin.slides.index') }}" class="btn btn-secondary">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
