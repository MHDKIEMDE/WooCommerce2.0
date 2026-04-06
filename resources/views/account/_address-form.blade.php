{{-- Partial réutilisé pour ajout ET édition. Variable : $a (Address|null) --}}
<div class="row g-3">
    <div class="col-12">
        <label class="form-label">Libellé (ex: Maison, Bureau)</label>
        <input type="text" name="label" class="form-control"
            value="{{ old('label', $a?->label) }}" placeholder="Optionnel">
    </div>
    <div class="col-sm-6">
        <label class="form-label">Prénom <sup class="text-danger">*</sup></label>
        <input type="text" name="first_name"
            class="form-control @error('first_name') is-invalid @enderror"
            value="{{ old('first_name', $a?->first_name) }}" required>
        @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-sm-6">
        <label class="form-label">Nom <sup class="text-danger">*</sup></label>
        <input type="text" name="last_name"
            class="form-control @error('last_name') is-invalid @enderror"
            value="{{ old('last_name', $a?->last_name) }}" required>
        @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-12">
        <label class="form-label">Rue / Quartier <sup class="text-danger">*</sup></label>
        <input type="text" name="street"
            class="form-control @error('street') is-invalid @enderror"
            value="{{ old('street', $a?->street) }}" required>
        @error('street')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-sm-6">
        <label class="form-label">Ville <sup class="text-danger">*</sup></label>
        <input type="text" name="city"
            class="form-control @error('city') is-invalid @enderror"
            value="{{ old('city', $a?->city) }}" required>
        @error('city')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-sm-6">
        <label class="form-label">Code postal</label>
        <input type="text" name="zip" class="form-control"
            value="{{ old('zip', $a?->zip) }}">
    </div>
    <div class="col-sm-6">
        <label class="form-label">Pays <sup class="text-danger">*</sup></label>
        <input type="text" name="country"
            class="form-control @error('country') is-invalid @enderror"
            value="{{ old('country', $a?->country ?? 'Côte d\'Ivoire') }}" required>
        @error('country')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-sm-6">
        <label class="form-label">Téléphone</label>
        <input type="tel" name="phone" class="form-control"
            value="{{ old('phone', $a?->phone) }}">
    </div>
</div>
