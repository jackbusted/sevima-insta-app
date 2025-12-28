@extends('admin-manage.layouts.main')

@section('container')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/baguettebox.js/1.11.0/baguetteBox.min.css">

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pl-3 pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Detail, Payment, and Registration</h1>
    <div class="btn-toolbar mb-2 mb-md-0"></div>
</div>
<div class="col-lg-10">
    <div class="mb-3">
        <a href="{{ URL::route('admin.manage-registration.list', ['id' => $schedule_id]) }}">
            <button id="back-btn" name="back-btn" class="btn btn-outline-danger" type="button"><i class="bi bi-arrow-left"></i>Back</button>
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-6">
        <h4>Details</h4>
        <hr>
        <div class="mb-3">
            <label for="name" class="form-label">Nama</label>
            <input type="text" class="form-control" name="name" id="name" value="{{ $name }}" disabled>
        </div>
        <div class="mb-3">
            <label for="npm" class="form-label">NPM</label>
            <input type="text" class="form-control" name="npm" id="npm" value="{{ $npm }}" disabled>
        </div>
        <div class="mb-3">
            <label for="jurusan" class="form-label">Jurusan</label>
            <input type="text" class="form-control" name="jurusan" id="jurusan" value="" disabled>
        </div>
        <div class="mb-3">
            <label for="class-test" class="form-label">Kelas Test</label>
            <input type="text" class="form-control" name="class-test" id="class-test" value="{{ $test_class }}" disabled>
        </div>
        <div class="mb-3">
            <label for="date" class="form-label">Tanggal Pelaksanaan</label>
            <input type="text" class="form-control" name="date" id="date" value="{{ date('Y-m-d', strtotime($date)) }}" disabled>
        </div>
        <div class="mb-3">
            <label for="clock" class="form-label">Jam Pelaksanaan</label>
            <input type="text" class="form-control" name="clock" id="clock" value="{{ $time }}" disabled>
        </div>
    </div>

    <div class="col-lg-6">
        <h4>Information</h4>
        <hr>
        <div>
            <label for="receipt" class="form-label">Bukti Pembayaran</label>
        </div>
        <div class="mb-3" id="imageReceiptPreview" style="display: none;">
            <a href="{{ asset($image) }}" data-baguettebox>
                <img class="img-preview img-fluid mb-3 col-sm-5" id="imgReceipt" style="height: 80px; width: auto;">
            </a>
        </div>

        <div class="mb-3">
            <label for="reason" class="form-label">Approval's Reason</label>
            <textarea class="form-control" name="reason" id="reason" style="resize: both;" rows="4">{{ isset($reason) ? $reason : '' }}</textarea>
        </div>

        @if ($status == 0)
        <div class="mb-3">
            <button value="{{ $id }}" class="btn-approve btn btn-success">Confirm</button>
            <button value="{{ $id }}" class="btn-reject btn btn-danger">Reject</button>
        </div>
        @else
        <label for="text-status" class="form-label">This registration is : <b>{{ $text_status }}</b></label>
        @endif

        @if ($status == 1)
        <div class="mb-3">
            <button value="{{ $id }}" class="btn-cancel btn btn-outline-danger">Cancel This User</button>
        </div>
        @endif
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/baguettebox.js/1.11.0/baguetteBox.min.js"></script>

<script>
    const imageTag = document.querySelector('#imgReceipt');
    imageTag.src = "{{ asset($image) }}";
    const imagePreviewDiv = document.querySelector('#imageReceiptPreview');
    imagePreviewDiv.style.display = 'block';
    baguetteBox.run('#imageReceiptPreview');

    $(document).on('click', '.btn-approve', function() {
        var id = $(this).val();
        var reason = document.getElementById('reason').value;

        Swal.fire({
            title: "Are you sure?",
            text: "Approve this registration?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Sure",
        })
        .then((result) => {
            if (result.isConfirmed) {
                showLoading();
                $.ajax({
                    url: "{{ URL::route('admin.manage-registration.approve') }}",
                    type: 'post',
                    data: {
                        _token: '{{ csrf_token() }}',
                        id: id,
                        reason: reason,
                    },
                    success: function (response) {
                        hideLoading()
                        Swal.fire(
                            'Approved!',
                            response.message,
                            'success'
                        ).then(function () {
                            window.location.href = response.redirect;
                        });
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        hideLoading()
                        Swal.fire({
                            icon: 'error',
                            title: "Failed to approve. Something went wrong",
                            text: xhr.responseJSON.message,
                            showConfirmButton: true,
                        });
                    }
                });
            }
        });
    });

    $(document).on('click', '.btn-reject', function() {
        var id = $(this).val();

        var reason = document.getElementById('reason').value;
        if (!reason) {
            Swal.fire({
                icon: 'error',
                title: 'Empty Reason',
                text: 'Please fill reason for reject action',
                showConfirmButton: true,
            });
            return;
        }

        Swal.fire({
            title: "Are you sure?",
            text: "Reject this registration?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Sure",
        })
        .then((result) => {
            if (result.isConfirmed) {
                showLoading();
                $.ajax({
                    url: "{{ URL::route('admin.manage-registration.reject') }}",
                    type: 'post',
                    data: {
                        _token: '{{ csrf_token() }}',
                        id: id,
                        reason: reason,
                    },
                    success: function (response) {
                        hideLoading()
                        Swal.fire(
                            'Rejected!',
                            response.message,
                            'success'
                        ).then(function () {
                            window.location.href = response.redirect;
                        });
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        hideLoading()
                        Swal.fire({
                            icon: 'error',
                            title: "Failed to reject. Something went wrong",
                            text: xhr.responseJSON.message,
                            showConfirmButton: true,
                        });
                    }
                });
            }
        });
    });

    $(document).on('click', '.btn-cancel', function() {
        var id = $(this).val();

        var reason = document.getElementById('reason').value;
        if (!reason) {
            Swal.fire({
                icon: 'error',
                title: 'Empty Reason',
                text: 'Please fill reason for reject action',
                showConfirmButton: true,
            });
            return;
        }

        Swal.fire({
            title: "Are you sure?",
            text: "Cancel for this user?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Sure",
        })
        .then((result) => {
            if (result.isConfirmed) {
                showLoading();
                $.ajax({
                    url: "{{ URL::route('admin.manage-registration.cancel') }}",
                    type: 'post',
                    data: {
                        _token: '{{ csrf_token() }}',
                        id: id,
                        reason: reason,
                    },
                    success: function (response) {
                        hideLoading()
                        Swal.fire(
                            'Canceled',
                            response.message,
                            'success'
                        ).then(function () {
                            window.location.href = response.redirect;
                        });
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        hideLoading()
                        Swal.fire({
                            icon: 'error',
                            title: "Failed to cancel. Something went wrong",
                            text: xhr.responseJSON.message,
                            showConfirmButton: true,
                        });
                    }
                });
            }
        });
    });
</script>
@endsection