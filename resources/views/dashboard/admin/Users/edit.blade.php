@extends('dashboard.admin.layout.app')
@section('title', 'Modifier '.$user->name)
@section('contents')
<div class="container-fluid px-4">
    <div class="d-flex align-items-center gap-3 mt-4 mb-2">
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i>Retour
        </a>
        <h1 class="mb-0">Modifier l'utilisateur</h1>
    </div>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Utilisateurs</a></li>
        <li class="breadcrumb-item active">{{ $user->name }}</li>
    </ol>

    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card shadow-sm">
                <div class="card-header fw-semibold">
                    <i class="fas fa-user-edit text-primary me-2"></i>Informations du compte
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.users.update', $user) }}">
                        @csrf @method('PUT')

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nom complet</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $user->name) }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email', $user->email) }}" required>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Rôle</label>
                            <select name="role" class="form-select @error('role') is-invalid @enderror">
                                <option value="customer"    {{ old('role', $user->role) === 'customer'    ? 'selected' : '' }}>Client</option>
                                <option value="admin"       {{ old('role', $user->role) === 'admin'       ? 'selected' : '' }}>Admin</option>
                                <option value="super-admin" {{ old('role', $user->role) === 'super-admin' ? 'selected' : '' }}>Super Admin</option>
                            </select>
                            @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Statut</label>
                            <div class="form-check form-switch mt-1">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1"
                                       id="isActive" {{ old('is_active', $user->is_active ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="isActive">Compte actif</label>
                            </div>
                        </div>

                        <hr>
                        <p class="text-muted small mb-2">Laisser vide pour ne pas changer le mot de passe.</p>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nouveau mot de passe</label>
                            <input type="password" name="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   autocomplete="new-password">
                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Confirmer le mot de passe</label>
                            <input type="password" name="password_confirmation" class="form-control"
                                   autocomplete="new-password">
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>Enregistrer
                            </button>
                            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Annuler</a>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Infos supplémentaires --}}
            <div class="card shadow-sm mt-4">
                <div class="card-header fw-semibold"><i class="fas fa-info-circle text-muted me-2"></i>Informations</div>
                <div class="card-body small text-muted">
                    <div class="d-flex justify-content-between mb-1">
                        <span>Inscrit le</span>
                        <span>{{ $user->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span>Commandes</span>
                        <span>{{ $user->orders()->count() }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Dernière mise à jour</span>
                        <span>{{ $user->updated_at->format('d/m/Y H:i') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
