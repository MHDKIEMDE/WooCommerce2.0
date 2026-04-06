@extends('layouts.app')
@section('seo_title', 'Modifier mon profil')
@section('noindex')
@section('content')

    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6">Modifier mon profil</h1>
        <ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-white">Accueil</a></li>
            <li class="breadcrumb-item"><a href="{{ route('account.profile') }}" class="text-white">Mon compte</a></li>
            <li class="breadcrumb-item active text-white">Modifier</li>
        </ol>
    </div>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-7">

                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                <div class="card shadow-sm">
                    <div class="card-header fw-semibold">Informations personnelles</div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('account.update') }}" enctype="multipart/form-data">
                            @csrf @method('PUT')

                            {{-- Avatar --}}
                            <div class="text-center mb-4">
                                @if($user->avatar)
                                    <img src="{{ Storage::url($user->avatar) }}" alt="Avatar"
                                        id="avatar-preview"
                                        class="rounded-circle mb-2" style="width:100px;height:100px;object-fit:cover;">
                                @else
                                    <div class="rounded-circle bg-primary d-inline-flex align-items-center justify-content-center mb-2"
                                        id="avatar-placeholder"
                                        style="width:100px;height:100px;">
                                        <span class="text-white fw-bold fs-1">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                    </div>
                                    <img src="" id="avatar-preview" class="rounded-circle mb-2 d-none"
                                        style="width:100px;height:100px;object-fit:cover;" alt="Aperçu">
                                @endif
                                <div>
                                    <label for="avatar-input" class="btn btn-sm btn-outline-secondary rounded-pill mt-1">
                                        <i class="fas fa-camera me-1"></i> Changer la photo
                                    </label>
                                    <input type="file" id="avatar-input" name="avatar" accept="image/*" class="d-none"
                                        @error('avatar') aria-invalid="true" @enderror>
                                </div>
                                @error('avatar')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Nom complet <sup class="text-danger">*</sup></label>
                                <input type="text" name="name"
                                    class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name', $user->name) }}" required>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control bg-light" value="{{ $user->email }}" disabled>
                                <div class="form-text">L'email ne peut pas être modifié.</div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Téléphone</label>
                                <input type="tel" name="phone"
                                    class="form-control @error('phone') is-invalid @enderror"
                                    value="{{ old('phone', $user->phone) }}">
                                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary rounded-pill px-5">
                                    Enregistrer
                                </button>
                                <a href="{{ route('account.profile') }}" class="btn btn-outline-secondary rounded-pill px-4">
                                    Annuler
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('avatar-input').addEventListener('change', function () {
            const file = this.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = e => {
                const preview = document.getElementById('avatar-preview');
                const placeholder = document.getElementById('avatar-placeholder');
                preview.src = e.target.result;
                preview.classList.remove('d-none');
                if (placeholder) placeholder.classList.add('d-none');
            };
            reader.readAsDataURL(file);
        });
    </script>

@endsection
