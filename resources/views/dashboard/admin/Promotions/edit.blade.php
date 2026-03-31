@extends('dashboard.admin.layout.app')
@section('Dashboard - Agribusiness Shop', 'Modifier la promotion')
@section('contents')
<div class="container-fluid px-4">
    <h1 class="mt-4">Modifier la promotion</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.promotions.index') }}">Promotions</a></li>
        <li class="breadcrumb-item active">Modifier</li>
    </ol>
    <div class="card mb-4" style="max-width:560px">
        <div class="card-body">
            <form action="{{ route('admin.promotions.update', $promotion) }}" method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')
                <div class="mb-3">
                    <img src="{{ Storage::url($promotion->image_path) }}" alt="{{ $promotion->title }}"
                         style="width:150px;height:90px;object-fit:cover;border-radius:6px;margin-bottom:8px">
                </div>
                <div class="mb-3">
                    <label class="form-label">Titre <span class="text-danger">*</span></label>
                    <input type="text" name="title" value="{{ old('title', $promotion->title) }}"
                           class="form-control @error('title') is-invalid @enderror" required>
                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Sous-titre</label>
                    <input type="text" name="subtitle" value="{{ old('subtitle', $promotion->subtitle) }}"
                           class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Lien</label>
                    <input type="text" name="link_url" value="{{ old('link_url', $promotion->link_url) }}"
                           class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Nouvelle image <small class="text-muted">(optionnel)</small></label>
                    <input type="file" name="image" class="form-control" accept="image/*">
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Couleur de fond</label>
                        <select name="bg_color" class="form-select">
                            @foreach(['bg-secondary'=>'Vert','bg-dark'=>'Sombre','bg-primary'=>'Bleu','bg-danger'=>'Rouge','bg-light'=>'Clair'] as $val=>$label)
                            <option value="{{ $val }}" {{ $promotion->bg_color==$val?'selected':'' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Thème du texte</label>
                        <select name="text_theme" class="form-select">
                            <option value="light" {{ $promotion->text_theme=='light'?'selected':'' }}>Clair</option>
                            <option value="dark" {{ $promotion->text_theme=='dark'?'selected':'' }}>Sombre</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Ordre</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', $promotion->sort_order) }}"
                           class="form-control" min="0" style="width:100px">
                </div>
                <div class="mb-3 form-check">
                    <input type="checkbox" name="is_active" id="is_active" class="form-check-input"
                           value="1" {{ $promotion->is_active?'checked':'' }}>
                    <label class="form-check-label" for="is_active">Actif</label>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Mettre à jour</button>
                    <a href="{{ route('admin.promotions.index') }}" class="btn btn-secondary">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
