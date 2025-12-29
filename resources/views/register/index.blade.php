@extends('layouts.logreg')

@section('logreg_content')

<link href="https://fonts.googleapis.com/css?family=Lato:300,400,700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="{{ asset('css/login/style.css') }}">

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-4">
        <div class="login-wrap m-50">
            <h3 class="mb-4 text-center" style="color: #123524">Account Register</h3>
            <form method="post" class="signin-form" id="register-form">
                @csrf
                <div class="form-group">
                    <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="Name" autofocus required>
                </div>
                <div class="form-group">
                    <input type="text" id="username" name="username" class="form-control @error('username') is-invalid @enderror" placeholder="Username" required>
                </div>
                <div class="form-group">
                    <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="Email" required>
                </div>
                <div class="form-group">
                    <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Password" required>
                </div>
                <div class="form-group">
                    <button id="btn-register" type="submit" class="form-control btn btn-primary submit px-3">Register</button>
                </div>
            </form>
            <p class="w-100 text-center" style="color: #123524">&mdash; Have An Account? &mdash;</p>
            <div class="social d-flex text-center">
                <a 
                    href="/login"
                    class="px-2 py-2 mr-md-1 rounded">
                    <span class="ion-logo-facebook mr-2"></span>Login
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

<script>
document.getElementById('btn-register').addEventListener('click', function(event) {
    event.preventDefault();

    var formData = new FormData(document.getElementById('register-form'));
    formData.append('_token', '{{ csrf_token() }}');

    $.ajax({
        url: "{{ URL::route('register.save') }}",
        type: 'post',
        data: formData,
        contentType: false,
        processData: false,
        success: function(resp) {
            Swal.fire({
                icon: 'success',
                title: 'Registered',
                text: resp.message,
            }).then(function() {
                window.location.href = "{{ URL::route('login') }}";
            });
        },
        error: function(xhr, ajaxOptions, thrownError) {
            if (xhr.status === 422) {
                var errors = xhr.responseJSON.errors;
                var errorMessage = '';

                $.each(errors, function(key, value) {
                    errorMessage += value + ' ';
                });

                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: errorMessage,
                    showConfirmButton: true,
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: xhr.statusText,
                    text: xhr.responseJSON.message,
                    showConfirmButton: true,
                });
            }
        }
    });
});
</script>

@endsection