@extends('layouts.logreg')

@section('logreg_content')

<link href="https://fonts.googleapis.com/css?family=Lato:300,400,700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="{{ asset('css/login/style.css') }}">

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-4">
        <div class="login-wrap m-50">
            <h3 class="mb-4 text-center" style="color: #123524">Account Login</h3>
            @if(session('loginError'))
                <div class="alert alert-danger">
                    {{ session('loginError') }}
                </div>
            @endif
            <form action="/login" method="post" class="signin-form">
                @csrf
                <div class="form-group">
                    <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="Email" autofocus required>
                </div>
                <div class="form-group">
                    <input id="password" name="password" type="password" class="form-control" placeholder="Password" required>
                </div>
                <div class="form-group">
                    <button type="submit" class="form-control btn btn-primary submit px-3">Login</button>
                </div>
            </form>
            <p class="w-100 text-center" style="color: #123524">&mdash; Don't Have Account? &mdash;</p>
            <div class="social d-flex text-center">
                <a 
                    href="/register"
                    class="px-2 py-2 mr-md-1 rounded">
                    <span class="ion-logo-facebook mr-2"></span>Register
                </a>
            </div>
            <p style="color: #123524" class="mt-5 mb-3 text-center">&copy; 2025 Satrio Production</p>
        </div>
    </div>
</div>

<script src="{{ asset('js/login/jquery.min.js') }}"></script>
<script src="{{ asset('js/login/popper.js') }}"></script>
<script src="{{ asset('js/login/bootstrap.min.js') }}"></script>
<script src="{{ asset('js/login/main.js') }}"></script>

@endsection