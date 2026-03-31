@extends('dashboard.admin.layout.app')
@section('Dashboard - Agribusiness Shop', 'Ajouter un témoignage')
@section('contents')
<div class="container-fluid px-4">
    <h1 class="mt-4">Ajouter un témoignage</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.testimonials.index') }}">Témoignages</a></li>
        <li class="breadcrumb-item active">Créer</li>
    </ol>

    <div class="card mb-4" style="max-width:560px">
        <div class="card-body">
            <form action="{{ route('admin.testimonials.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Nom <span class="text-danger">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}"
                           class="form-control @error('name') is-invalid @enderror" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Profession</label>
                    <input type="text" name="profession" value="{{ old('profession') }}" class="form-control"
                           placeholder="Ex : Client fidèle, Agriculteur…">
                </div>

                <div class="mb-3">
                    <label class="form-label">Témoignage <span class="text-danger">*</span></label>
                    <textarea name="description" rows="4"
                              class="form-control @error('description') is-invalid @enderror"
                              required>{{ old('description') }}</textarea>
                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Note</label>
                    <select name="rating" class="form-select w-auto">
                        @for($i=5;$i>=1;$i--)
                        <option value="{{ $i }}" {{ old('rating',5)==$i?'selected':'' }}>
                            {{ $i }} étoile{{ $i>1?'s':'' }}
                        </option>
                        @endfor
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Photo</label>
                    <input type="file" name="photo" class="form-control" accept="image/*">
                </div>

                <div class="mb-3 form-check">
                    <input type="checkbox" name="is_active" id="is_active" class="form-check-input"
                           value="1" {{ old('is_active','1')?'checked':'' }}>
                    <label class="form-check-label" for="is_active">Actif (visible sur le site)</label>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                    <a href="{{ route('admin.testimonials.index') }}" class="btn btn-secondary">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
