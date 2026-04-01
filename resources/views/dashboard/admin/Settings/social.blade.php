@extends('dashboard.admin.layout.app')

@section('title', 'Réseaux sociaux')

@section('contents')
<div class="container-fluid px-4">
    <h1 class="mt-4">Réseaux sociaux</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Réseaux sociaux</li>
    </ol>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header fw-semibold">Liens des réseaux sociaux</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.social-settings.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 300 300" class="me-2">
                                    <path d="M178.57 127.15 290.27 0h-26.46l-97.03 110.38L89.34 0H0l117.13 166.93L0 300.25h26.46l102.4-116.59 81.8 116.59h89.34M36.01 19.54H76.66l187.13 262.13h-40.66"/>
                                </svg>
                                X (Twitter)
                            </label>
                            <input type="url" name="twitter" class="form-control"
                                placeholder="https://x.com/votrecompte"
                                value="{{ $social['twitter'] ?? '' }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fab fa-facebook-f me-2 text-primary"></i> Facebook
                            </label>
                            <input type="url" name="facebook" class="form-control"
                                placeholder="https://facebook.com/votrepage"
                                value="{{ $social['facebook'] ?? '' }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fab fa-youtube me-2 text-danger"></i> YouTube
                            </label>
                            <input type="url" name="youtube" class="form-control"
                                placeholder="https://youtube.com/@votrechaine"
                                value="{{ $social['youtube'] ?? '' }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fab fa-linkedin-in me-2 text-info"></i> LinkedIn
                            </label>
                            <input type="url" name="linkedin" class="form-control"
                                placeholder="https://linkedin.com/company/votrepage"
                                value="{{ $social['linkedin'] ?? '' }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fab fa-instagram me-2 text-danger"></i> Instagram
                            </label>
                            <input type="url" name="instagram" class="form-control"
                                placeholder="https://instagram.com/votrecompte"
                                value="{{ $social['instagram'] ?? '' }}">
                        </div>

                        <div class="mb-4">
                            <label class="form-label">
                                <i class="fab fa-tiktok me-2"></i> TikTok
                            </label>
                            <input type="url" name="tiktok" class="form-control"
                                placeholder="https://tiktok.com/@votrecompte"
                                value="{{ $social['tiktok'] ?? '' }}">
                        </div>

                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save me-1"></i> Enregistrer
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 bg-light">
                <div class="card-body">
                    <h6 class="fw-bold mb-3"><i class="fas fa-info-circle text-primary me-2"></i>Aperçu footer</h6>
                    <p class="text-muted small mb-3">
                        Les icônes s'affichent uniquement si le lien est renseigné.
                        Laisse un champ vide pour masquer l'icône correspondante.
                    </p>
                    <div class="d-flex gap-2 flex-wrap">
                        @foreach(['twitter','facebook','youtube','linkedin','instagram','tiktok'] as $net)
                            @if(!empty($social[$net] ?? ''))
                                <a href="{{ $social[$net] }}" target="_blank"
                                    class="btn btn-outline-secondary btn-md-square rounded-circle">
                                    @if($net === 'twitter')
                                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" viewBox="0 0 300 300">
                                            <path d="M178.57 127.15 290.27 0h-26.46l-97.03 110.38L89.34 0H0l117.13 166.93L0 300.25h26.46l102.4-116.59 81.8 116.59h89.34M36.01 19.54H76.66l187.13 262.13h-40.66"/>
                                        </svg>
                                    @else
                                        <i class="fab fa-{{ $net === 'facebook' ? 'facebook-f' : ($net === 'linkedin' ? 'linkedin-in' : $net) }}"></i>
                                    @endif
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
