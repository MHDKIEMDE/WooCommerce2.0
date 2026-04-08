@extends('dashboard.admin.layout.app')
@section('title', 'Envoyer une notification')
@section('contents')
<div class="container-fluid px-4">
    <div class="d-flex align-items-center gap-3 mt-4 mb-4">
        <a href="{{ route('admin.notifications.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Retour
        </a>
        <h1 class="mb-0">Envoyer une notification push</h1>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card shadow-sm">
                <div class="card-header fw-semibold">
                    <i class="fas fa-paper-plane me-2 text-primary"></i>
                    Nouvelle notification
                </div>
                <div class="card-body">

                    @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                    </div>
                    @endif

                    <form method="POST" action="{{ route('admin.notifications.broadcast') }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Destinataire</label>
                            <select name="user_id" class="form-select">
                                <option value="">Tous les utilisateurs actifs ({{ $usersCount }} avec l'app)</option>
                                @foreach(\App\Models\User::where('is_active', true)->whereHas('deviceTokens')->orderBy('name')->get() as $user)
                                    <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} — {{ $user->email }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">Laissez vide pour envoyer à tous.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Titre <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                                value="{{ old('title') }}" placeholder="Ex: Nouvelle promotion !" maxlength="100" required>
                            @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Message <span class="text-danger">*</span></label>
                            <textarea name="body" class="form-control @error('body') is-invalid @enderror"
                                rows="4" placeholder="Contenu de la notification…" maxlength="500" required>{{ old('body') }}</textarea>
                            @error('body')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <div class="form-text">Maximum 500 caractères.</div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-1"></i> Envoyer
                            </button>
                            <a href="{{ route('admin.notifications.index') }}" class="btn btn-outline-secondary">Annuler</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
