@extends('dashboard.admin.layout.app')
@section('title', 'Utilisateurs')
@section('contents')
<div class="container-fluid px-4">
    <h1 class="mt-4">Utilisateurs</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.home-settings.edit') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Utilisateurs</li>
    </ol>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- Filtres --}}
    <form method="GET" class="row g-2 mb-4">
        <div class="col-sm-5">
            <input type="search" name="q" class="form-control" placeholder="Rechercher nom ou email…"
                   value="{{ request('q') }}">
        </div>
        <div class="col-sm-3">
            <select name="role" class="form-select">
                <option value="">Tous les rôles</option>
                <option value="customer"   {{ request('role') === 'customer'   ? 'selected' : '' }}>Client</option>
                <option value="admin"      {{ request('role') === 'admin'      ? 'selected' : '' }}>Admin</option>
                <option value="super-admin"{{ request('role') === 'super-admin'? 'selected' : '' }}>Super Admin</option>
            </select>
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Reset</a>
        </div>
    </form>

    <div class="card shadow-sm">
        <div class="card-header d-flex align-items-center justify-content-between">
            <span class="fw-semibold"><i class="fas fa-users me-2"></i>{{ $users->total() }} utilisateur{{ $users->total() > 1 ? 's' : '' }}</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Nom</th>
                            <th>Email</th>
                            <th>Rôle</th>
                            <th>Commandes</th>
                            <th>Statut</th>
                            <th>Inscrit le</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr>
                            <td class="text-muted small">{{ $user->id }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white fw-bold flex-shrink-0"
                                         style="width:36px;height:36px;font-size:.9rem;">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <span class="fw-semibold">{{ $user->name }}</span>
                                </div>
                            </td>
                            <td class="text-muted small">{{ $user->email }}</td>
                            <td>
                                @switch($user->role)
                                    @case('super-admin')
                                        <span class="badge bg-danger">Super Admin</span>
                                        @break
                                    @case('admin')
                                        <span class="badge bg-warning text-dark">Admin</span>
                                        @break
                                    @default
                                        <span class="badge bg-secondary">Client</span>
                                @endswitch
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ $user->orders_count }}</span>
                            </td>
                            <td>
                                @if($user->is_active ?? true)
                                    <span class="badge bg-success">Actif</span>
                                @else
                                    <span class="badge bg-danger">Suspendu</span>
                                @endif
                            </td>
                            <td class="text-muted small">{{ $user->created_at->format('d/m/Y') }}</td>
                            <td class="d-flex gap-1 flex-wrap">
                                <a href="{{ route('admin.users.edit', $user) }}"
                                   class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if($user->id !== auth()->id())
                                <form method="POST" action="{{ route('admin.users.toggle', $user) }}" class="d-inline">
                                    @csrf @method('PATCH')
                                    <button type="submit"
                                        class="btn btn-sm {{ ($user->is_active ?? true) ? 'btn-outline-warning' : 'btn-outline-success' }} rounded-pill"
                                        title="{{ ($user->is_active ?? true) ? 'Suspendre' : 'Activer' }}">
                                        <i class="fas fa-{{ ($user->is_active ?? true) ? 'ban' : 'check' }}"></i>
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="d-inline"
                                      onsubmit="return confirm('Supprimer {{ addslashes($user->name) }} ?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">Aucun utilisateur trouvé.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($users->hasPages())
        <div class="card-footer">
            {{ $users->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
