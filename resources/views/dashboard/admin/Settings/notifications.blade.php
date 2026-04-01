@extends('dashboard.admin.layout.app')

@section('title', 'Notifications')

@section('contents')
<div class="container-fluid px-4">
    <h1 class="mt-4">Paramètres de notification</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Notifications</li>
    </ol>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-7">

            {{-- ── Carte WhatsApp ── --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header d-flex align-items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#25D366"
                        viewBox="0 0 16 16">
                        <path d="M13.601 2.326A7.85 7.85 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.9 7.9 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.9 7.9 0 0 0 13.6 2.326zM7.994 14.521a6.6 6.6 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.56 6.56 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592zm3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.73.73 0 0 0-.529.247c-.182.198-.691.677-.691 1.654s.71 1.916.81 2.049c.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232z"/>
                    </svg>
                    <strong>WhatsApp — Réception des commandes</strong>
                </div>
                <div class="card-body">

                    <form method="POST" action="{{ route('admin.notification-settings.update') }}">
                        @csrf
                        @method('PUT')

                        {{-- Activation --}}
                        <div class="form-check form-switch mb-4">
                            <input class="form-check-input" type="checkbox" name="whatsapp_enabled"
                                id="whatsappEnabled" value="1"
                                {{ ($settings['whatsapp_enabled'] ?? '0') === '1' ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="whatsappEnabled">
                                Activer les notifications WhatsApp
                            </label>
                        </div>

                        {{-- Numéro --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Numéro WhatsApp de réception
                                <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                <input type="text" name="whatsapp_phone" class="form-control"
                                    placeholder="+2250102030405"
                                    value="{{ $settings['whatsapp_phone'] ?? '' }}">
                            </div>
                            <div class="form-text">
                                Numéro avec indicatif pays (ex: <code>+2250102030405</code>).
                                Les commandes seront envoyées sur ce numéro.
                            </div>
                        </div>

                        {{-- Clé API --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Clé API CallMeBot</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-key"></i></span>
                                <input type="text" name="whatsapp_apikey" class="form-control"
                                    placeholder="123456"
                                    value="{{ $settings['whatsapp_apikey'] ?? '' }}">
                                <button class="btn btn-outline-secondary" type="button"
                                    onclick="this.previousElementSibling.type = this.previousElementSibling.type === 'password' ? 'text' : 'password'">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="form-text">
                                Clé fournie par CallMeBot après activation.
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-1"></i> Enregistrer
                            </button>
                        </div>
                    </form>

                    {{-- Bouton test séparé --}}
                    @if (!empty($settings['whatsapp_phone']) && !empty($settings['whatsapp_apikey']))
                    <hr>
                    <form method="POST" action="{{ route('admin.notification-settings.test') }}">
                        @csrf
                        <p class="text-muted small mb-2">
                            Envoie un message de test sur <strong>{{ $settings['whatsapp_phone'] }}</strong>.
                        </p>
                        <button type="submit" class="btn btn-outline-success btn-sm">
                            <i class="fas fa-paper-plane me-1"></i> Envoyer un message test
                        </button>
                    </form>
                    @endif

                </div>
            </div>

        </div>

        {{-- ── Colonne droite : guide d'activation ── --}}
        <div class="col-lg-5">
            <div class="card border-0 bg-light mb-4">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">
                        <i class="fas fa-info-circle text-primary me-2"></i>
                        Comment activer CallMeBot (gratuit)
                    </h6>
                    <ol class="ps-3 mb-0" style="line-height:2">
                        <li>
                            Depuis le numéro WhatsApp de réception, envoie ce message à
                            <strong>+34 644 60 49 16</strong> :
                            <div class="bg-white border rounded px-3 py-2 my-2 font-monospace small">
                                I allow callmebot to send me messages
                            </div>
                        </li>
                        <li>CallMeBot te répond avec ta <strong>clé API</strong> (ex: <code>123456</code>).</li>
                        <li>Colle cette clé dans le champ ci-contre et enregistre.</li>
                        <li>Clique sur <em>"Envoyer un message test"</em> pour vérifier.</li>
                    </ol>
                    <hr>
                    <p class="small text-muted mb-0">
                        <i class="fas fa-shield-alt me-1 text-success"></i>
                        Service gratuit, sans abonnement. Aucune donnée de commande n'est stockée chez CallMeBot.
                    </p>
                </div>
            </div>

            <div class="card border-0 bg-light">
                <div class="card-body">
                    <h6 class="fw-bold mb-2">
                        <i class="fas fa-bell text-warning me-2"></i>
                        Ce que tu reçois à chaque commande
                    </h6>
                    <div class="bg-white border rounded p-3 font-monospace small" style="white-space:pre-line">🛒 <strong>NOUVELLE COMMANDE — CMD-XYZ123</strong>

👤 Client : Moussa Koné
📞 Téléphone : +2250102030405
📍 Adresse : Cocody, Abidjan
💳 Paiement : Paiement à la livraison

Articles :
• Tomates fraîches x2 — 2 500 FCFA
• Ananas Bio x1 — 1 800 FCFA

💰 Total : 4 300 FCFA

⚡ Répondre au client : https://wa.me/…</div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
