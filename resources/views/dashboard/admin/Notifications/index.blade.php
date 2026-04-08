@extends('dashboard.admin.layout.app')
@section('title', 'Notifications')
@section('contents')
<div class="container-fluid px-4">
    <div class="d-flex align-items-center justify-content-between mt-4 mb-3">
        <h1 class="mb-0">Notifications</h1>
        <a href="{{ route('admin.notifications.send') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-paper-plane me-1"></i> Envoyer une notification
        </a>
    </div>

    {{-- Stats --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm text-center py-3">
                <div class="fs-2 fw-bold text-primary">{{ $stats['total'] }}</div>
                <div class="text-muted small">Total</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm text-center py-3">
                <div class="fs-2 fw-bold text-warning">{{ $stats['unread'] }}</div>
                <div class="text-muted small">Non lues</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm text-center py-3">
                <div class="fs-2 fw-bold text-success">{{ $stats['read'] }}</div>
                <div class="text-muted small">Lues</div>
            </div>
        </div>
    </div>

    {{-- Filtres --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label class="form-label small fw-semibold">Recherche</label>
                    <input type="text" name="q" class="form-control form-control-sm"
                        placeholder="Titre, message, utilisateur…" value="{{ request('q') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Type</label>
                    <select name="type" class="form-select form-select-sm">
                        <option value="">Tous les types</option>
                        @foreach($types as $type)
                            <option value="{{ $type }}" {{ request('type') === $type ? 'selected' : '' }}>
                                {{ $type }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button class="btn btn-primary btn-sm w-100">Filtrer</button>
                    <a href="{{ route('admin.notifications.index') }}" class="btn btn-outline-secondary btn-sm w-100">Reset</a>
                </div>
            </form>
        </div>
    </div>

    {{-- Table --}}
    <div class="card shadow-sm">
        <div class="card-body p-0">
            @if($notifications->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-bell-slash fa-3x mb-3"></i>
                    <p>Aucune notification trouvée.</p>
                </div>
            @else
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Utilisateur</th>
                            <th>Type</th>
                            <th>Titre</th>
                            <th>Message</th>
                            <th>Statut</th>
                            <th>Date</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($notifications as $notif)
                        <tr class="{{ $notif->read_at ? '' : 'table-warning' }}">
                            <td>
                                <span class="fw-semibold">{{ $notif->user->name ?? '—' }}</span><br>
                                <small class="text-muted">{{ $notif->user->email ?? '' }}</small>
                            </td>
                            <td><span class="badge bg-secondary">{{ $notif->type }}</span></td>
                            <td>{{ Str::limit($notif->title, 40) }}</td>
                            <td>{{ Str::limit($notif->body, 60) }}</td>
                            <td>
                                @if($notif->read_at)
                                    <span class="badge bg-success">Lu</span>
                                    <small class="text-muted d-block">{{ $notif->read_at->format('d/m H:i') }}</small>
                                @else
                                    <span class="badge bg-warning text-dark">Non lu</span>
                                @endif
                            </td>
                            <td>{{ $notif->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <form method="POST" action="{{ route('admin.notifications.destroy', $notif->id) }}"
                                    onsubmit="return confirm('Supprimer cette notification ?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-outline-danger btn-sm">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($notifications->hasPages())
            <div class="p-3">{{ $notifications->links() }}</div>
            @endif
            @endif
        </div>
    </div>
</div>
@endsection
