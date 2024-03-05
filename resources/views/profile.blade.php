@extends('layouts.app')
@section('Agribusiness Shop', 'Mon Profile')
@section('content')
    <!-- Single Page Header start -->
    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6">Mon profiles</h1>
        <ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="#">Accueil</a></li>
            <li class="breadcrumb-item"><a href="#">Pages</a></li>
            <li class="breadcrumb-item active text-white">Mon profiles</li>
        </ol>
    </div>
    <!-- End of Navbar -->
    <div class="container-fluid fruite py-5">

        <div class="sectionTitle fw-bold h1 text-blanc mb-5 mt-5"></div>

        <div class="container bg-blanc p-5">
            <div class="row justify-content-center">
                <div class="col-6">
                    @if ($user->profile_image)
                        <!-- Si l'entreprise a une image, affichez-le -->
                        <img src="{{ asset('storage/' . $user->profile_image) }}" class="w-100 img-fluid"
                            style="object-fit: cover; height: 300px" />
                    @else
                        <!-- Si l'entreprise n'a pas de logo, affichez l'image par défaut -->
                        <div> <img src="{{ asset('assets/images/image_Defaut.png') }}" class="w-100 img-fluid"
                                style="object-fit: cover; height: 300px" /></div>
                    @endif
                </div>
                <div class="col-md-8">
                    <div class="col-12">
                        <h1 class="flamaSemiBold text-capitalize firstNameTitleMobile">{{ $user->last_name }}</h1>
                        <h1 class="flamaBold text-uppercase nameTitleMobile">{{ $user->name }}</h1>
                    </div>
                    <div class="col-12">
                        <div>
                            <h3>{{ $user->phone_number }}
                            </h3>
                            <h3>{{ $user->secondary_phone_number }}</h3>
                            <h3>{{ $user->email }}</h3>
                            <h3><span class="text-capitalize">{{ $user->address }}</span></h3>
                        </div>
                    </div>
                    <div class="col-12" style="margin-top:39px ">
                        <h1 class="bg-bleu text-blanc text-center p-3 text-uppercase flamaBold fs-1">
                            {{ $user->quarter }}
                        </h1>
                    </div>
                </div>
            </div>
        </div>
        <div class="container
                bg-blanc p-5 mt-3">
            <div class="row">
                <div class="col-12 col-md-4">
                    <a class="btn w-100 bg-vert text-blanc flamaSemiBold fs-4" href="{{ route('user.editProfile') }}"><i
                            class="fa-solid fa-pencil fa-shake me-2" style="color: #ffffff;"></i>Modifier mon profil</a>
                </div>
                <div class="col-12 col-md-4">
                    <a class="btn w-100 bg-rouge text-blanc flamaSemiBold fs-4"href=""><i
                            class="fa-solid fa-pencil fa-shake me-2" style="color: #ffffff;"></i>Modifier mot de
                        passe</a>
                </div>
            </div>
        </div>
    </div>
@endsection
