@extends('dashboard.admin.layout.app')
@section('title', 'Ajouter une promotion')
@section('contents')
<div class="container-fluid px-4">
    <h1 class="mt-4">Ajouter une promotion</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.promotions.index') }}">Promotions</a></li>
        <li class="breadcrumb-item active">Créer</li>
    </ol>
    <div class="card mb-4" style="max-width:560px">
        <div class="card-body">
            <form action="{{ route('admin.promotions.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Titre <span class="text-danger">*</span></label>
                    <input type="text" name="title" value="{{ old('title') }}"
                           class="form-control @error('title') is-invalid @enderror" required>
                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Sous-titre</label>
                    <input type="text" name="subtitle" value="{{ old('subtitle') }}" class="form-control"
                           placeholder="Ex : 20% DE RÉDUCTION">
                </div>
                <div class="mb-3">
                    <label class="form-label">Lien</label>
                    <input type="text" name="link_url" value="{{ old('link_url') }}" class="form-control"
                           placeholder="/shop">
                </div>
                <div class="mb-3">
                    <label class="form-label">Image <span class="text-danger">*</span></label>
                    <input type="file" name="image" class="form-control @error('image') is-invalid @enderror"
                           accept="image/*" required>
                    @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Couleur de fond</label>
                        <select name="bg_color" class="form-select">
                            <option value="bg-secondary" {{ old('bg_color')=='bg-secondary'?'selected':'' }}>Vert (bg-secondary)</option>
                            <option value="bg-dark" {{ old('bg_color')=='bg-dark'?'selected':'' }}>Sombre (bg-dark)</option>
                            <option value="bg-primary" {{ old('bg_color')=='bg-primary'?'selected':'' }}>Bleu (bg-primary)</option>
                            <option value="bg-danger" {{ old('bg_color')=='bg-danger'?'selected':'' }}>Rouge (bg-danger)</option>
                            <option value="bg-light" {{ old('bg_color')=='bg-light'?'selected':'' }}>Clair (bg-light)</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Thème du texte</label>
                        <select name="text_theme" class="form-select">
                            <option value="light" {{ old('text_theme','light')=='light'?'selected':'' }}>Clair (texte blanc)</option>
                            <option value="dark" {{ old('text_theme')=='dark'?'selected':'' }}>Sombre (texte noir)</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Ordre d'affichage</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}"
                           class="form-control" min="0" style="width:100px">
                </div>
                <div class="mb-3 form-check">
                    <input type="checkbox" name="is_active" id="is_active" class="form-check-input"
                           value="1" checked>
                    <label class="form-check-label" for="is_active">Actif</label>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                    <a href="{{ route('admin.promotions.index') }}" class="btn btn-secondary">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
