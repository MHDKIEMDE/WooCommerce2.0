@extends('dashboard.admin.layout.app')
@section('title', 'Nouveau template')

@section('contents')
<div class="container-fluid px-4">
    <div class="mt-4 mb-2">
        <h1>Nouveau template</h1>
    </div>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.templates.index') }}">Templates</a></li>
        <li class="breadcrumb-item active">Nouveau</li>
    </ol>

    <div class="card shadow-sm" style="max-width:540px;">
        <div class="card-body p-4">
            @if($errors->any())
            <div class="alert alert-danger">
                @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
            </div>
            @endif

            <form method="POST" action="{{ route('admin.templates.store') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label fw-semibold">Nom de la niche <sup class="text-danger">*</sup></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name') }}" placeholder="Ex : Alimentaire" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Icône (emoji)</label>
                    <input type="text" name="icon" class="form-control @error('icon') is-invalid @enderror"
                           value="{{ old('icon') }}" placeholder="🍎" maxlength="10">
                    <div class="form-text">Laissez vide pour utiliser l'icône par défaut.</div>
                    @error('icon')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save me-1"></i> Créer
                    </button>
                    <a href="{{ route('admin.templates.index') }}" class="btn btn-secondary">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
