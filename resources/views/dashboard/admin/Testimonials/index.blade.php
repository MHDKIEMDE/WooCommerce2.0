@extends('dashboard.admin.layout.app')
@section('Dashboard - Agribusiness Shop', 'Témoignages')
@section('contents')
<div class="container-fluid px-4">
    <h1 class="mt-4">Témoignages</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
        <li class="breadcrumb-item active">Témoignages</li>
    </ol>

    <a href="{{ route('admin.testimonials.create') }}" class="btn btn-primary mb-3">
        <i class="fas fa-plus"></i> Ajouter un témoignage
    </a>

    <div class="card mb-4">
        <div class="card-body">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th style="width:70px">Photo</th>
                        <th>Nom</th>
                        <th>Profession</th>
                        <th>Note</th>
                        <th>Actif</th>
                        <th style="width:140px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($testimonials as $t)
                    <tr>
                        <td>
                            <img src="{{ $t->photo_url }}" alt="{{ $t->name }}"
                                 style="width:50px;height:50px;object-fit:cover;border-radius:50%;">
                        </td>
                        <td>{{ $t->name }}</td>
                        <td>{{ $t->profession ?? '—' }}</td>
                        <td>
                            @for($i=1;$i<=5;$i++)
                            <i class="fas fa-star {{ $i <= $t->rating ? 'text-warning' : 'text-muted' }}" style="font-size:.75rem;"></i>
                            @endfor
                        </td>
                        <td>
                            @if($t->is_active)
                                <span class="badge bg-success">Oui</span>
                            @else
                                <span class="badge bg-secondary">Non</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.testimonials.edit', $t) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.testimonials.destroy', $t) }}" method="POST"
                                  class="d-inline" onsubmit="return confirm('Supprimer ?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted">Aucun témoignage.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
