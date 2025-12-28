@extends('homeuser.layouts.main')

@section('container')
<div class="justify-content-between flex-wrap flex-md-nowrap align-items-center pl-3 pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Bind Your Email to Zoom Meeting Conference</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2"></div>
    </div>
</div>

<div class="justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3">
    <div class="col-lg-10">
        <form method="post" id="form-bind-zoom-email">
            @csrf
            <div class="mb-3">
                <label for="email" class="form-label">Your Email</label>
                <input type="email" required class="form-control" id="email" name="email" value="{{ isset($zoomData) ? $zoomData->email : '' }}">
            </div>
            <div class="mb-3">
                <label for="first_name" class="form-label">First Name</label>
                <input type="text" required class="form-control" id="first_name" name="first_name" value="{{ isset($zoomData) ? $zoomData->first_name : '' }}">
            </div>
            <div class="mb-3">
                <label for="last_name" class="form-label">Last Name</label>
                <input type="text" required class="form-control" id="last_name" name="last_name" value="{{ isset($zoomData) ? $zoomData->last_name : '' }}">
            </div>
            <div class="mb-3">
                <label for="bound-status" class="form-label">Zoom's Bind Status</label>
                @if (isset($zoomData))
                    <div class="input-group flex-nowrap">
                        <span class="input-group-text" id="addon-wrapping" style="border-color: rgb(4, 211, 4)"><i class="bi bi-check-lg" style="color: rgb(4, 211, 4)"></i></span>
                        <input type="text" class="form-control col-lg-3" style="border-color: rgb(4, 211, 4)" id="status" name="status" value="Bound" disabled>
                    </div>
                @else
                    <div class="input-group flex-nowrap">
                        <span class="input-group-text" id="addon-wrapping" style="border-color: red"><i class="bi bi-exclamation-triangle-fill" style="color: red"></i></span>
                        <input type="text" class="form-control col-lg-3" style="border-color: red" id="status" name="status" value="Not Bound" aria-describedby="addon-wrapping" disabled>
                    </div>
                @endif
            </div>
            @if (isset($zoomData))
                <div class="mb-3">
                    <label for="account-status" class="form-label">Account Status</label>
                    <input type="text" class="form-control col-lg-3" id="account-status" name="account-status" value="{{ $zoomStatus }}" disabled>
                    @if (isset($statusMessage))
                        <span>{{ $statusMessage }}</span>
                    @endif
                </div>
            @endif

            <br>
            @if (isset($zoomData))
                <button id="updateToZoom" type="submit" class="btn btn-primary">Update</button>
            @else
                <button id="bindToZoom" type="submit" class="btn btn-primary">Bind</button>
            @endif
        </form>
    </div>
</div>

<script>
    var isEdit = false;
    @if (isset($zoomData))
        isEdit = true;
    @endif

    if (isEdit) {
        document.getElementById('updateToZoom').addEventListener('click', function(event) {
            event.preventDefault();

            var userEmail = document.getElementById('email').value;
            var firstName = document.getElementById('first_name').value;
            var lastName = document.getElementById('last_name').value;

            if (!userEmail) {
                Swal.fire({
                    icon: 'error',
                    title: 'Empty Email',
                    text: 'Please fill Email column.',
                    showConfirmButton: true,
                });
                return;
            }

            if (!firstName) {
                Swal.fire({
                    icon: 'error',
                    title: 'Empty First Name',
                    text: 'Please fill First Name column.',
                    showConfirmButton: true,
                });
                return;
            }

            if (!lastName) {
                Swal.fire({
                    icon: 'error',
                    title: 'Empty Last Name',
                    text: 'Please fill Last Name column.',
                    showConfirmButton: true,
                });
                return;
            }

            var formData = new FormData(document.getElementById('form-bind-zoom-email'));
            formData.append('_token', '{{ csrf_token() }}');

            Swal.fire({
                title: "Are you sure?",
                text: "Update Zoom's data",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Sure",
            })
            .then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ URL::route('homeuser.bind-zoom.update') }}",
                        type: 'post',
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function(resp) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Nice!',
                                text: resp.message,
                            }).then(function() {
                                location.reload();
                            });
                        },
                        error: function(xhr, ajaxOptions, thrownError) {
                            Swal.fire({
                                icon: 'error',
                                title: xhr.statusText,
                                text: thrownError,
                                showConfirmButton: true,
                            });
                        }
                    });
                }
            });
        });
    } else {
        document.getElementById('bindToZoom').addEventListener('click', function(event) {
            event.preventDefault();

            var userEmail = document.getElementById('email').value;
            var firstName = document.getElementById('first_name').value;
            var lastName = document.getElementById('last_name').value;

            if (!userEmail) {
                Swal.fire({
                    icon: 'error',
                    title: 'Empty Email',
                    text: 'Please fill Email column.',
                    showConfirmButton: true,
                });
                return;
            }

            if (!firstName) {
                Swal.fire({
                    icon: 'error',
                    title: 'Empty First Name',
                    text: 'Please fill First Name column.',
                    showConfirmButton: true,
                });
                return;
            }

            if (!lastName) {
                Swal.fire({
                    icon: 'error',
                    title: 'Empty Last Name',
                    text: 'Please fill Last Name column.',
                    showConfirmButton: true,
                });
                return;
            }

            var formData = new FormData(document.getElementById('form-bind-zoom-email'));
            formData.append('_token', '{{ csrf_token() }}');

            Swal.fire({
                title: "Are you sure?",
                text: "Bind to Zoom",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Sure",
            })
            .then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ URL::route('homeuser.bind-zoom.save') }}",
                        type: 'post',
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function(resp) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Nice!',
                                text: resp.message,
                            }).then(function() {
                                location.reload();
                            });
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
            });
        });
    }
</script>
@endsection