@extends('dashboard.admin.layout.app')
@section('title', 'Modifier le témoignage')
@section('contents')
<div class="container-fluid px-4">
    <h1 class="mt-4">Modifier le témoignage</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.testimonials.index') }}">Témoignages</a></li>
        <li class="breadcrumb-item active">Modifier</li>
    </ol>

    <div class="card mb-4" style="max-width:560px">
        <div class="card-body">
            <form action="{{ route('admin.testimonials.update', $testimonial) }}" method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')

                @if($testimonial->photo)
                <div class="mb-3">
                    <img src="{{ $testimonial->photo_url }}" alt="photo"
                         style="width:80px;height:80px;object-fit:cover;border-radius:50%;margin-bottom:8px">
                </div>
                @endif

                <div class="mb-3">
                    <label class="form-label">Nom <span class="text-danger">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $testimonial->name) }}"
                           class="form-control @error('name') is-invalid @enderror" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Profession</label>
                    <input type="text" name="profession" value="{{ old('profession', $testimonial->profession) }}"
                           class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">Témoignage <span class="text-danger">*</span></label>
                    <textarea name="description" rows="4"
                              class="form-control @error('description') is-invalid @enderror"
                              required>{{ old('description', $testimonial->description) }}</textarea>
                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Note</label>
                    <select name="rating" class="form-select w-auto">
                        @for($i=5;$i>=1;$i--)
                        <option value="{{ $i }}" {{ $testimonial->rating==$i?'selected':'' }}>
                            {{ $i }} étoile{{ $i>1?'s':'' }}
                        </option>
                        @endfor
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Nouvelle photo <small class="text-muted">(laisser vide pour conserver)</small></label>
                    <input type="file" name="photo" class="form-control" accept="image/*">
                </div>

                <div class="mb-3 form-check">
                    <input type="checkbox" name="is_active" id="is_active" class="form-check-input"
                           value="1" {{ $testimonial->is_active?'checked':'' }}>
                    <label class="form-check-label" for="is_active">Actif</label>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Mettre à jour</button>
                    <a href="{{ route('admin.testimonials.index') }}" class="btn btn-secondary">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
