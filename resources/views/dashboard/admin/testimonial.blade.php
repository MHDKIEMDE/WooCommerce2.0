@extends('dashboard.admin.layout.app')
@section('dasboard', 'Témoignage')
@section('contents')
    <!-- Single Page Header start -->
    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6">Témoignage</h1>
        <ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="#">Accuceil</a></li>
            <li class="breadcrumb-item"><a href="#">Pages</a></li>
            <li class="breadcrumb-item active text-white">Témoignage</li>
        </ol>
    </div>
    <!-- Single Page Header End -->
    <!-- Tastimonial Start -->
    {{-- @if ($testimonials->isEmpty())
        <li class="text-center d-none">
        @else --}}
    <div class="container-fluid testimonial py-5">
        <div class="container py-5">
            <div class="testimonial-header text-center">
                <h4 class="text-primary">Témoignages</h4>
                <h1 class="display-5 mb-5 text-dark">Ce que disent nos clients !</h1>
            </div>
            <div class="owl-carousel testimonial-carousel">
                @foreach ($testimonials as $testimonial)
                    <div class="testimonial-item img-border-radius bg-light rounded p-4">
                        <div class="position-relative">
                            <i class="fa fa-quote-right fa-2x text-secondary position-absolute"
                                style="bottom: 30px; right: 0;"></i>
                            <div class="mb-4 pb-4 border-bottom border-secondary">
                                <p class="mb-0">{{ $testimonial->description }}</p>
                            </div>
                            <div class="d-flex align-items-center flex-nowrap">
                                <div class="bg-secondary rounded">
                                    <img src="img/testimonial-1.jpg" class="img-fluid rounded"
                                        style="width: 100px; height: 100px;" alt="">
                                </div>
                                <div class="ms-4 d-block">
                                    <h4 class="text-dark">{{ $testimonial->name }}</h4>
                                    {{-- {{ auth()->user()->name }} --}}
                                    <p class="m-0 pb-3">{{ $testimonial->profession }}</p>
                                    <div class="d-flex pe-5">
                                        <i class="fas fa-star text-primary"></i>
                                        <i class="fas fa-star text-primary"></i>
                                        <i class="fas fa-star text-primary"></i>
                                        <i class="fas fa-star text-primary"></i>
                                        <i class="fas fa-star"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    {{-- @endif --}}
    <!-- Tastimonial End -->
@endsection
