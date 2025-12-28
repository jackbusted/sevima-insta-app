@extends('admin-manage.layouts.main')

@section('container')
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/baguettebox.js/1.11.0/baguetteBox.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/baguettebox.js/1.11.0/baguetteBox.min.css">

<div class="justify-content-between flex-wrap flex-md-nowrap align-items-center pl-3 pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Profile Setting</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2"></div>
    </div>
</div>

<form action="" method="post" id="update-profile" enctype="multipart/form-data">
    @csrf
    <div class="row">
        <div class="col-lg-6">
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" required class="form-control" name="name" id="name" value="{{ isset($name) ? $name : '' }}">
            </div>
            <div class="mb-3">
                <label for="username" class="form-label">User Name</label>
                <input type="text" required class="form-control" name="username" id="username" value="{{ isset($username) ? $username : '' }}">
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" required class="form-control" id="email" name="email" value="{{ isset($email) ? $email : '' }}">
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">New Password</label>
                <div class="input-group">
                    <input type="password" class="form-control" id="password" name="password" placeholder="(Optional)">
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                            <i class="bi bi-eye-slash" id="togglePasswordIcon"></i>
                        </button>
                    </div>
                </div>
            </div>
            <br>
            <button id="update-data" type="submit" class="btn btn-primary mr-2">Update</button>
            <button id="delete-avatar" type="submit" class="btn btn-danger" {{ $avatarImage ? '' : 'disabled' }}>Delete Avatar</button>
        </div>

        <div class="col-lg-6">
            <div class="mb-3">
                <label for="avatar-image" class="form-label">Profile's Avatar</label>
                <input type="file" class="form-control" id="avatar-image" name="avatar-image" accept=".jpeg,.jpg,.png" onchange="previewImage()">
            </div>
            <div class="mb-3" id="imagePreview" style="display: none;">
                <a href="" id="imageBoxPreview" data-baguettebox>
                    <img class="img-preview img-fluid mb-3 col-sm-5" style="height: 80px; width: auto;" id="imagePreviewTag">
                </a>
            </div>
            @if ($avatarImage)
                <div>
                    <label for="current-avatar" class="form-label">Current Avatar</label>
                </div>
                <div class="mb-3" id="avatarPreview" style="display: none;">
                    <a href="{{ asset($avatarImage) }}" data-baguettebox>
                        <img class="img-preview img-fluid mb-3 col-sm-5" style="height: 80px; width: auto;" id="currentAvatar">
                    </a>
                </div>

                <script>
                    const currentAvatar = document.querySelector('#currentAvatar');
                    currentAvatar.src = "{{ asset($avatarImage) }}";
                    const avatarPreview = document.querySelector('#avatarPreview');
                    avatarPreview.style.display = 'block';
                    baguetteBox.run('#avatarPreview');
                </script>
            @endif
        </div>
    </div>
</form>

<script>
const passwordField = document.getElementById('password');
const togglePasswordButton = document.getElementById('togglePassword');
const togglePasswordIcon = document.getElementById('togglePasswordIcon');

togglePasswordButton.addEventListener('click', function () {
    const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
    passwordField.setAttribute('type', type);

    if (type === 'password') {
        togglePasswordIcon.classList.remove('bi-eye');
        togglePasswordIcon.classList.add('bi-eye-slash');
    } else {
        togglePasswordIcon.classList.remove('bi-eye-slash');
        togglePasswordIcon.classList.add('bi-eye');
    }
});

function previewImage() {
    const image = document.querySelector('#avatar-image');
    const imagePreviewDiv = document.querySelector('#imagePreview');
    const imagePreviewTag = document.querySelector('#imagePreviewTag');
    const preview = document.querySelector('#imageBoxPreview');

    if (image.files && image.files[0]) {
        const oFReader = new FileReader();
        oFReader.readAsDataURL(image.files[0]);
        oFReader.onload = function(oFREvent) {
            imagePreviewTag.src = oFREvent.target.result;
            imagePreviewDiv.style.display = 'block';

            var formData = new FormData(document.getElementById('update-profile'));
            formData.append('_token', '{{ csrf_token() }}');
            $.ajax({
                url: "{{ URL::route('admin.setting-profile.avatar-preview') }}",
                type: 'post',
                data: formData,
                contentType: false,
                processData: false,
                success: function(resp) {
                    var parsedResp = $.parseJSON(resp);
                    var tempImagePreview = '{{ asset('') }}' + parsedResp.img_preview.image;
                    preview.href = tempImagePreview
                    baguetteBox.run('#imagePreview');
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    Swal.fire({
                        icon: 'error',
                        title: xhr.statusText,
                        text: xhr.responseJSON.message,
                        showConfirmButton: true,
                    });
                }
            });
        }
    } else {
        imagePreviewTag.src = '';
        imagePreviewDiv.style.display = 'none';
    }
}

document.getElementById('update-data').addEventListener('click', function(event) {
    event.preventDefault();

    var formData = new FormData(document.getElementById('update-profile'));
    formData.append('_token', '{{ csrf_token() }}');

    Swal.fire({
        title: "Are you sure?",
        text: "Update your profile",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Sure",
    })
    .then(result => {
        if (result.isConfirmed) {
            showLoading()
            $.ajax({
                url: "{{ URL::route('admin.setting-profile.update') }}",
                type: 'post',
                data: formData,
                contentType: false,
                processData: false,
                success: function(resp) {
                    hideLoading()
                    Swal.fire({
                        icon: 'success',
                        title: 'Nice!',
                        text: resp.message,
                    }).then(function() {
                        location.reload();
                    });
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    hideLoading()
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
                            title: "Something went wrong",
                            text: xhr.responseJSON.message,
                            showConfirmButton: true,
                        });
                    }
                }
            })
        }
    })
});

document.getElementById('delete-avatar').addEventListener('click', function(event) {
    event.preventDefault();

    var formData = new FormData(document.getElementById('update-profile'));
    formData.append('_token', '{{ csrf_token() }}');

    Swal.fire({
        title: "Are you sure?",
        text: "Delete your avatar",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Sure",
    })
    .then(result => {
        if (result.isConfirmed) {
            showLoading()
            $.ajax({
                url: "{{ URL::route('admin.setting-profile.delete-avatar') }}",
                type: 'post',
                data: formData,
                contentType: false,
                processData: false,
                success: function(resp) {
                    hideLoading()
                    Swal.fire({
                        icon: 'success',
                        title: 'Nice!',
                        text: resp.message,
                    }).then(function() {
                        location.reload();
                    });
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    hideLoading()
                    Swal.fire({
                        icon: 'error',
                        title: "Something went wrong",
                        text: xhr.responseJSON.message,
                        showConfirmButton: true,
                    });
                }
            })
        }
    })
});
</script>
@endsection