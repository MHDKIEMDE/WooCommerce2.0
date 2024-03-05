@extends('layouts.app')
@section('Agribusiness Shop', 'Contact')
@section('content')
    <!-- Single Page Header start -->
    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6">Contact</h1>
        <ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="#">Accueil</a></li>
            <li class="breadcrumb-item"><a href="#">Pages</a></li>
            <li class="breadcrumb-item active text-white">Contact</li>
        </ol>
    </div>
    <!-- Single Page Header End -->
    <!-- Contact Start -->
    <div class="container-fluid contact py-5">
        <div class="container py-5">
            <div class="p-5 bg-light rounded">
                <div class="row g-4">
                    <div class="col-12">
                        <div class="text-center mx-auto" style="max-width: 700px;">
                            <h1 class="text-primary">GÉOLOCALISATION</h1>
                            <p class="mb-4">Pour venir à Agribusiness Shop, prendre le boulevard Thomas SANKARA, en
                                quittant l’hôpital pédiatrique Charles de Gaulle pour la présidence. Tourner à gauche au
                                niveau de la station Total, et prendre l’avenue Babanguida. Continuez tout droit jusqu’à la
                                station April Oil, qui se trouvera à votre gauche. Agribusiness Shop est en face de la
                                station, sur la voie rouge. <a href="https://htmlcodex.com/contact-form">Download
                                    Now</a>.</p>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="h-100 rounded">
                            <iframe class="rounded w-100" style="height: 400px;"
                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d124713.46542434252!2d-1.6380599566406562!3d12.36310800000002!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0xe2ebfc3a00c881d%3A0xc4b6ae9efd90a422!2sAgribusiness%20Shop%201200%20Logements!5e0!3m2!1sfr!2sbf!4v1707492706863!5m2!1sfr!2sbf "
                                width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade"></iframe>
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <form action="" class="">
                            <input type="text" class="w-100 form-control border-0 py-3 mb-4" placeholder="Votre nom">
                            <input type="email" class="w-100 form-control border-0 py-3 mb-4"
                                placeholder="Entrer votre mail">
                            <textarea class="w-100 form-control border-0 mb-4" rows="5" cols="10" placeholder="Votre message"></textarea>
                            <button class="w-100 btn form-control border-secondary py-3 bg-white text-primary "
                                type="submit">Soumettre</button>
                        </form>
                    </div>
                    <div class="col-lg-5">
                        <div class="d-flex p-4 rounded mb-4 bg-white">
                            <i class="fas fa-map-marker-alt fa-2x text-primary me-4"></i>
                            <div>
                                <h4>Address</h4>
                                <p class="mb-2">1200 logements, Ouagadougou, Burkina faso</p>
                            </div>
                        </div>
                        <div class="d-flex p-4 rounded mb-4 bg-white">
                            <i class="fas fa-envelope fa-2x text-primary me-4"></i>
                            <div>
                                <h4>mail</h4>
                                <p class="mb-2">agribusiness-shop.com</p>
                            </div>
                        </div>
                        <div class="d-flex p-4 rounded bg-white">
                            <i class="fa fa-phone-alt fa-2x text-primary me-4"></i>
                            <div>
                                <h4>Telephone</h4>
                                <p class="mb-2">(+226) 07443112</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Contact End -->
@endsection
