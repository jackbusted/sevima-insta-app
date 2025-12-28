@extends('homeuser.layouts.main')

@section('container')
<script src="https://cdnjs.cloudflare.com/ajax/libs/baguettebox.js/1.11.0/baguetteBox.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/baguettebox.js/1.11.0/baguetteBox.min.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
<link href="https://cdn.datatables.net/v/bs5/dt-1.13.8/b-2.4.2/b-colvis-2.4.2/b-html5-2.4.2/b-print-2.4.2/date-1.5.1/r-2.5.0/sc-2.3.0/sl-1.7.0/datatables.min.css" rel="stylesheet">

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pl-3 pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Complete Your Registration</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2"></div>
    </div>
</div>
<div class="col-lg-10">
    <div class="mb-3">
        <a href="{{ URL::route('homeuser.test-registration.view') }}">
            <button id="back-btn" name="back-btn" class="btn btn-outline-danger" type="button"><i class="bi bi-arrow-left"></i>Back</button>
        </a>
    </div>
</div>

<br>
<div class="col-lg-6">
    <div class="mb-3">
        <label for="schedule-name" class="form-label">Schedule Name</label>
        <input type="text" class="form-control" name="schedule-name" id="schedule-name" value="{{ $scheduleName }}" disabled readonly>
    </div>
    <div class="mb-3">
        <label for="schedule-class" class="form-label">Class</label>
        <input type="text" class="form-control" name="schedule-class" id="schedule-class" value="{{ $scheduleClass }}" disabled readonly>
    </div>
    <div class="mb-3">
        <label for="schedule-date" class="form-label">Date</label>
        <input type="text" class="form-control" name="schedule-date" id="schedule-date" disabled readonly>
    </div>
    <div class="mb-3">
        <label for="schedule-time" class="form-label">Time</label>
        <input type="text" class="form-control" name="schedule-time" id="schedule-time" disabled readonly>
    </div>
</div>

<br>
<div class="col w-full">
    <div class="table-responsive border-bottom">
        <table id="table-user" class="table table-striped table-sm" style="width:100%">
            <thead>
                <tr>
                    <th scope="col">Name</th>
                    <th scope="col">NPM</th>
                    <th scope="col">Jurusan</th>
                    <th scope="col">Fakultas</th>
                    <th scope="col">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($datas as $data)
                <tr>
                    <td style="vertical-align: middle;">{{ $data['name'] }}</td>
                    <td style="vertical-align: middle;">{{ $data['npm'] }}</td>
                    <td style="vertical-align: middle;"></td>
                    <td style="vertical-align: middle;"></td>
                    <td style="vertical-align: middle;">{{ $data['status'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <br><br>
    <form method="post" id="form-upload-receipt" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label for="schedule" class="form-label"><b>Step 2.</b> Upload Your Payment Receipt (.jpeg / .jpg / .png)</label>
            <input type="file" class="form-control col-lg-3" id="image" name="image" accept=".jpeg,.jpg,.png" onchange="previewImage()">
        </div>
        <div class="mb-3" id="imagePreview" style="display: none;">
            <a href="" id="imageBoxPreview" data-baguettebox>
                <img class="img-preview img-fluid mb-3 col-sm-5" style="height: 80px; width: auto;" id="imageBaguettePreview">
            </a>
        </div>

        @if ($user_id != 0)
            <div class="mb-3">
                <label for="status" class="form-label"><b>Step 3.</b> Your Registration Status</label>
                @if ($status == "Confirmed")
                    <div class="input-group flex-nowrap">
                        <span class="input-group-text" id="addon-wrapping" style="border-color: rgb(4, 211, 4)"><i class="bi bi-check-lg" style="color: rgb(4, 211, 4)"></i></span>
                        <input type="text" class="form-control col-lg-3" style="border-color: rgb(4, 211, 4)" id="status" name="status" value="{{ $status }}" disabled>
                    </div>
                @elseif ($status == "Rejected")
                    <div class="input-group flex-nowrap">
                        <span class="input-group-text" id="addon-wrapping" style="border-color: red"><i class="bi bi-exclamation-triangle-fill" style="color: red"></i></span>
                        <input type="text" class="form-control col-lg-3" style="border-color: red" id="status" name="status" value="{{ $status }}" aria-describedby="addon-wrapping" disabled>
                    </div>
                @else
                    <input type="text" class="form-control col-lg-3" id="status" name="status" value="{{ $status }}" disabled>
                @endif
            </div>
            @if ($reason != "")
                <div class="mb-3">
                    <label for="reason" class="form-label">Reason</label>
                    <textarea class="form-control" name="reason" id="reason" style="resize: both; width: 400px;" rows="4" disabled readonly>{{ isset($reason) ? $reason : '' }}</textarea>
                </div>
            @endif
            <div>
                <label for="payment-receipt" class="form-label">Your Payment Receipt</label>
            </div>
            <div class="mb-3" id="previousReceiptPreview" style="display: none;">
                <a href="{{ asset($receipt_image) }}" data-baguettebox>
                    <img class="img-preview img-fluid mb-3 col-sm-5" style="height: 80px; width: auto;" id="previousReceiptBaguettePreview">
                </a>
            </div>

            <script>
                const imgPreviousReceiptTag = document.querySelector('#previousReceiptBaguettePreview');
                imgPreviousReceiptTag.src = "{{ asset($receipt_image) }}";
                const imgPreviousReceiptDiv = document.querySelector('#previousReceiptPreview');
                imgPreviousReceiptDiv.style.display = 'block';
                baguetteBox.run('#previousReceiptPreview');
            </script>
        @endif

        <div class="mb-3">
            <input type="hidden" value="{{ $id }}" name="id">
            <button id="register" name="register" class="btn btn-primary" type="submit">Register</button>
            @if ($user_id != 0 && $status == "Unconfirmed" || $user_id != 0 && $status == "Confirmed")
                <input type="hidden" value="{{ $user_id }}" name="user_id">
                @if ($user_id != 0 && $status == "Unconfirmed")
                    <button id="update" name="update" class="btn btn-warning ml-2" type="submit">Update Attachment</button>
                @endif
                <button id="cancel" name="cancel" class="btn btn-danger ml-2" type="submit">Cancel</button>
            @endif
        </div>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/v/bs5/dt-1.13.8/b-2.4.2/b-colvis-2.4.2/b-html5-2.4.2/b-print-2.4.2/date-1.5.1/r-2.5.0/sc-2.3.0/sl-1.7.0/datatables.min.js"></script>

<script>
var table = $('#table-user').DataTable();

let rawDate = {!! json_encode($scheduleDate) !!};
let rawTime = {!! json_encode($scheduleTime) !!};
scheduleDate = formatDate(rawDate);
scheduleTime = formatTime(rawTime);
const scheduleDateDisplay = document.getElementById('schedule-date');
scheduleDateDisplay.value = scheduleDate;
const scheduleTimeDisplay = document.getElementById('schedule-time');
scheduleTimeDisplay.value = scheduleTime+" wib";

function previewImage(){
    const image = document.querySelector('#image');
    const imgStructureDiv = document.querySelector('#imagePreview');
    const imgStructureTag = document.querySelector('#imageBaguettePreview');
    const preview = document.querySelector('#imageBoxPreview');

    if (image.files && image.files[0]) {
        const oFReader = new FileReader();
        oFReader.readAsDataURL(image.files[0]);
        oFReader.onload = function(oFREvent) {
            imgStructureTag.src = oFREvent.target.result;
            imgStructureDiv.style.display = 'block';

            var formData = new FormData(document.getElementById('form-upload-receipt'));
            formData.append('_token', '{{ csrf_token() }}');
            $.ajax({
                url: "{{ URL::route('homeuser.test-registration.temporary-image') }}",
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
            })
        }
    } else {
        imgStructureDiv.style.display = 'none';
        imgStructureTag.src = '';
    }
};

document.getElementById('register').addEventListener('click', function(event){
    event.preventDefault();

    var image = document.getElementById('image').value;
    if (!image) {
        Swal.fire({
            icon: 'error',
            title: 'Empty Image',
            text: 'Please upload your payment receipt.',
            showConfirmButton: true,
        });
        return;
    }

    var formData = new FormData(document.getElementById('form-upload-receipt'));
    formData.append('_token', '{{ csrf_token() }}');

    Swal.fire({
        title: "Are you sure?",
        text: "Register to this schedule?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Sure",
    })
    .then((result) => {
        if (result.isConfirmed) {
            showLoading()
            $.ajax({
                url: "{{ URL::route('homeuser.test-registration.save') }}",
                type: 'post',
                data: formData,
                contentType: false,
                processData: false,
                success: function (response) {
                    hideLoading()
                    Swal.fire(
                        'Nice!',
                        response.message,
                        'success'
                    ).then(function () {
                        location.reload();
                    });  
                },
                error: function (xhr, ajaxOptions, thrownError) {
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
                            title: 'Something went wrong',
                            text: xhr.responseJSON.message,
                            showConfirmButton: true,
                        });
                    }
                }
            });
        }
    });
});

@if ($user_id != 0 && $status == "Unconfirmed")
document.getElementById('update').addEventListener('click', function(event){
    event.preventDefault();

    var image = document.getElementById('image').value;
    if (!image) {
        Swal.fire({
            icon: 'error',
            title: 'Empty Image',
            text: 'Please upload your payment receipt.',
            showConfirmButton: true,
        });
        return;
    }

    var formData = new FormData(document.getElementById('form-upload-receipt'));
    formData.append('_token', '{{ csrf_token() }}');

    Swal.fire({
        title: "Are you sure?",
        text: "Update attachment?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Sure",
    })
    .then((result) => {
        if (result.isConfirmed) {
            showLoading()
            $.ajax({
                url: "{{ URL::route('homeuser.test-registration.update') }}",
                type: 'post',
                data: formData,
                contentType: false,
                processData: false,
                success: function (response) {
                    hideLoading()
                    Swal.fire(
                        'Updated!',
                        response.message,
                        'success'
                    ).then(function () {
                        location.reload();
                    });  
                },
                error: function (xhr, ajaxOptions, thrownError) {
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
                            title: 'Something went wrong',
                            text: xhr.responseJSON.message,
                            showConfirmButton: true,
                        });
                    }
                }
            });
        }
    });
});
@endif

@if ($user_id != 0 && $status == "Unconfirmed" || $user_id != 0 && $status == "Confirmed")
document.getElementById('cancel').addEventListener('click', function(event){
    event.preventDefault();

    var formData = new FormData(document.getElementById('form-upload-receipt'));
    formData.append('_token', '{{ csrf_token() }}');

    Swal.fire({
        title: "Are you sure?",
        text: "Cancel registration to this schedule?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Sure",
    })
    .then((result) => {
        if (result.isConfirmed) {
            showLoading()
            $.ajax({
                url: "{{ URL::route('homeuser.test-registration.cancel') }}",
                type: 'post',
                data: formData,
                contentType: false,
                processData: false,
                success: function (response) {
                    hideLoading()
                    Swal.fire(
                        'Canceled!',
                        response.message,
                        'success'
                    ).then(function () {
                        location.reload();
                    });  
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    hideLoading()
                    Swal.fire({
                        icon: 'error',
                        title: "Failed to cancel",
                        text: xhr.responseJSON.message,
                        showConfirmButton: true,
                    });
                }
            });
        }
    });
}); 
@endif

function formatDate(dateString) {
    let date = new Date(dateString);
    let options = {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    };

    return date.toLocaleDateString('en-US', options);
}

function formatTime(timeString) {
    let timeParts = timeString.split(':');
    let formattedTime = timeParts[0] + ':' + timeParts[1];

    return formattedTime;
}
</script>
@endsection