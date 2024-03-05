@extends('dashboard.admin.layout.app')

@section('Dashboard - Agribusiness Shop', "")

@section('contents')
    <div class="container-fluid px-4">
        <h1 class="mt-4">Creation d'un compte utilisateur</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('admin.adminHome') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Create User</li>
        </ol>
        <div class="card mb-4">
            <div class="card-body">
                <form action="{{ route('admin.loginUsers') }}" method="POST">
                    @csrf
                    <div class="row justify-content-center">
                        <div class="col-md-5">
                            <div class="mb-3">
                                <label for="inputFirstName" class="form-label">First Name</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="inputFirstName" name="name" value="{{ old('name') }}" required autofocus>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="mb-3">
                                <label for="inputLastName" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="inputLastName" name="last_name"
                                    value="{{ old('last_name') }}" required>
                                @error('last_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row justify-content-center">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="inputEmail" class="form-label">Email address</label>
                                <input type="email" class="form-control" id="inputEmail" name="email"
                                    value="{{ old('email') }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row justify-content-center">
                        <div class="col-md-5">
                            <div class="mb-3">
                                <label for="inputPassword" class="form-label">Password</label>
                                <input type="password" class="form-control" id="inputPassword" name="password" required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="mb-3">
                                <label for="inputPasswordConfirm" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" id="inputPasswordConfirm"
                                    name="password_confirmation" required>
                                @error('password_confirmation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Create Account</button>
                </form>
            </div>
        </div>
    </div>
@endsection
