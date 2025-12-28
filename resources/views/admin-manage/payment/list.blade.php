@extends('admin-manage.layouts.main')

@section('container')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
<link href="https://cdn.datatables.net/v/bs5/dt-1.13.8/b-2.4.2/b-colvis-2.4.2/b-html5-2.4.2/b-print-2.4.2/date-1.5.1/r-2.5.0/sc-2.3.0/sl-1.7.0/datatables.min.css" rel="stylesheet">

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pl-3 pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">List Approval and Schedule</h1>
    <div class="btn-toolbar mb-2 mb-md-0"></div>
</div>
<div class="col-lg-10">
    <div class="mb-3">
        <a href="{{ URL::route('admin.manage-registration.view') }}">
            <button id="back-btn" name="back-btn" class="btn btn-outline-danger" type="button"><i class="bi bi-arrow-left"></i>Back</button>
        </a>
    </div>
</div>

<br>
<div class="col-lg-4">
    <div class="mb-3">
        <label for="userStatus" class="form-label">Participant's Status</label>
        <select class="form-select" name="userStatus" id="userStatus">
            <option value="">All</option>
            @foreach ($userStatuses as $data)
                <option value="{{ $data }}">{{ $data }}</option>
            @endforeach
        </select>
    </div>
</div>

<br>
<div class="table-responsive col w-full">
    <table id="table-payment" class="table table-striped table-sm" style="width:100%">
        <thead>
            <tr>
                <th scope="col">Name</th>
                <th scope="col">Class</th>
                <th scope="col">Execution</th>
                <th scope="col">Test Date</th>
                <th scope="col">Date Upload</th>
                <th scope="col">Last Updated</th>
                <th scope="col">Status</th>
                <th scope="col">Approval</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($datas as $data)
            <tr>
                <td style="vertical-align: middle;">{{ $data['name'] }}</td>
                <td style="vertical-align: middle;">{{ $data['class'] }}</td>
                <td style="vertical-align: middle;">{{ $data['execution'] }}</td>
                <td style="vertical-align: middle;">{{ date('Y-m-d', strtotime($data['test_date'])) }}</td>
                <td style="vertical-align: middle;">{{ date('Y-m-d', strtotime($data['date_upload'])) }}</td>
                <td style="vertical-align: middle;">{{ $data['last_updated'] }}</td>
                <td style="vertical-align: middle;">{{ $data['status'] }}</td>
                <td>
                    {{-- tombol detail --}}
                    <div class="d-inline">
                        <a href="{{ URL::route('admin.manage-registration.approval', ['id' => $data['id']]) }}">
                            <input type="hidden" value="{{ $data['id'] }}">
                            <button 
                                title="Detail"
                                type="submit"
                                style="color: rgb(25, 0, 255)"
                                class="btn btn-outline-info"
                            >
                                <i class="bi bi-eye"></i>
                            </button>
                        </a>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<br><br>
<div class="row">
    <div class="col-lg-6">
        <h4>Schedule's Detail</h4>
        <hr>
        <div class="mb-3">
            <label for="name" class="form-label">Schedule's Name</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ isset($schedule_name) ? $schedule_name : '' }}" disabled readonly>
        </div>
        <div class="mb-3">
            <label for="schedule-class" class="form-label">Class</label>
            <input type="text" class="form-control" id="schedule-class" name="schedule-class" value="{{ $selected_class }}" disabled readonly>
        </div>
        <div class="mb-3">
            <label for="schedule-exec" class="form-label">Execution</label>
            <input type="text" class="form-control" id="schedule-exec" name="schedule-exec" value="{{ $selected_execution }}" disabled readonly>
        </div>
        <div class="mb-3">
            <label for="schedule-date" class="form-label">Date</label>
            <input type="text" class="form-control" id="schedule-date" name="schedule-date" value="{{ date('Y-m-d', strtotime($date)) }}" disabled readonly>
        </div>
        <div class="mb-3">
            <label for="schedule-time" class="form-label">Time</label>
            <input type="text" class="form-control" id="schedule-time" name="schedule-time" value="{{ $time }}" disabled readonly>
        </div>
    </div>

    <div class="col-lg-6">
        <h4>More Information</h4>
        <hr>
        <div class="mb-3">
            <label for="schedule-status" class="form-label">Schedule's Status</label>
            <input type="text" class="form-control" name="schedule-status" id="schedule-status" value="" disabled readonly>
        </div>

        @if ($status == 0)
        <div class="mb-3">
            <a href="{{ URL::route('admin.manage-schedule.edit', ['id' => $schedule_id]) }}">
                <button class="btn btn-outline-warning" id="editRegist">Edit Schedule</button>
            </a>
        </div>
        <div class="mb-3">
            <button value="{{ $schedule_id }}" class="btn btn-outline-danger" id="deleteRegist">Delete Schedule</button>
        </div>
        <div class="mb-3">
            <button value="{{ $schedule_id }}" class="btn btn-outline-info" id="openRegist">Open Registration</button>
        </div>
        @endif

        @if ($status == 1)
        <div class="mb-3">
            <a href="{{ URL::route('admin.manage-schedule.edit', ['id' => $schedule_id]) }}">
                <button class="btn btn-outline-warning" id="editRegist">Edit Schedule</button>
            </a>
        </div>
        <div class="mb-3">
            <button value="{{ $schedule_id }}" class="btn btn-outline-danger" id="deleteRegist">Delete Schedule</button>
        </div>
        <div class="mb-3">
            <button value="{{ $schedule_id }}" class="btn btn-outline-success" id="executeSchedule">Execute This Schedule</button>
        </div>
        @endif

        @if ($status == 2)
        <div class="mb-3">
            <label for="invitation-link" class="form-label">Zoom Invitation Link</label>
            <input type="text" class="form-control" name="invitation-link" id="invitation-link" value="{{ isset($joinUrl) ? $joinUrl : '' }}">
        </div>
        <div class="mb-3">
            <button value="{{ $schedule_id }}" id="updateLink" class="btn btn-primary">Update Link</button>
        </div>
        <div class="mb-3">
            <button value="{{ $schedule_id }}" class="btn-close-exam btn btn-warning">Close Exam</button>
        </div>
        @endif
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/v/bs5/dt-1.13.8/b-2.4.2/b-colvis-2.4.2/b-html5-2.4.2/b-print-2.4.2/date-1.5.1/r-2.5.0/sc-2.3.0/sl-1.7.0/datatables.min.js"></script>

<script>
var scheduleStatus = "";
let rawStatus = {!! json_encode($status) !!};

switch (rawStatus) {
    case 0:
        scheduleStatus = "Ready to open"
        break;
    case 1:
        scheduleStatus = "Not started yet"
        break;
    case 2:
        scheduleStatus = "Already started"
        break;
    case 3:
        scheduleStatus = "Done"
        break;
    default:
        scheduleStatus = "Unknown"
        break;
}

const scheduleStatusText = document.getElementById('schedule-status');
scheduleStatusText.value = scheduleStatus;

$(document).ready(function() {
    if ($.fn.DataTable.isDataTable('#table-payment')) {
        $('#table-payment').DataTable().destroy();
    }
    var table = $('#table-payment').DataTable();

    $('#userStatus').change(function() {
        var userStatus = $(this).val();
        table.column(6).search(userStatus).draw();
    });
})

$(document).on('click', '.btn-close-exam', function() {
    var id = $(this).val();

    Swal.fire({
        title: "Are you sure?",
        text: "Close this schedule? This will end exam session",
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
                url: "{{ URL::route('admin.manage-schedule.close-exam') }}",
                type: 'post',
                data: {
                    _token: '{{ csrf_token() }}',
                    schedule_id: id,
                },
                success: function(resp) {
                    hideLoading()
                    Swal.fire({
                        icon: 'success',
                        title: 'Closed!',
                        text: resp.message,
                    }).then(function() {
                        location.reload();
                    });
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    hideLoading()
                    Swal.fire({
                        icon: 'error',
                        title: xhr.statusText,
                        text: xhr.responseJSON.message,
                        showConfirmButton: true,
                    });
                }
            })
        }
    })
});

$(document).on('click', '#updateLink', function() {
    var id = $(this).val();
    var joinUrl = document.getElementById('invitation-link').value;

    if (!joinUrl) {
        Swal.fire({
            icon: 'error',
            title: 'Empty Invitation Link',
            text: 'Please fill the provided column',
            showConfirmButton: true,
        });
        return;
    }

    Swal.fire({
        title: "Are you sure?",
        text: "Update zoom invitation link",
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
                url: "{{ URL::route('admin.manage-schedule.update-link') }}",
                type: 'post',
                data: {
                    _token: '{{ csrf_token() }}',
                    schedule_id: id,
                    invite_link: joinUrl,
                },
                success: function(resp) {
                    hideLoading()
                    Swal.fire({
                        icon: 'success',
                        title: 'Updated!',
                        text: resp.message,
                    }).then(function() {
                        location.reload();
                    });
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    hideLoading()
                    Swal.fire({
                        icon: 'error',
                        title: xhr.statusText,
                        text: xhr.responseJSON.message,
                        showConfirmButton: true,
                    });
                }
            })
        }
    })
});

$(document).on('click', '#deleteRegist', function() {
    var id = $(this).val();
    let deleteURL = "{{ route('admin.manage-schedule.delete', ['id'=> ':id']) }}";
    deleteURL = deleteURL.replace(':id', id);

    Swal.fire({
        title: "Are you sure?",
        text: "Delete this schedule?",
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
                url: deleteURL,
                type: 'get',
                data: {
                    _token: '{{ csrf_token() }}',
                },
                success: function(resp) {
                    hideLoading()
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        text: resp.message,
                    }).then(function() {
                        window.location.href = "{{ route('admin.manage-registration.view') }}"
                    });
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    hideLoading()
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

$(document).on('click', '#openRegist', function() {
    var id = $(this).val();
    let openURL = "{{ route('admin.manage-schedule.open', ['id'=> ':id']) }}";
    openURL = openURL.replace(':id', id);

    Swal.fire({
        title: "Are you sure?",
        text: "Open for registration?",
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
                url: openURL,
                type: 'get',
                data: {
                    _token: '{{ csrf_token() }}',
                },
                success: function(resp) {
                    hideLoading()
                    Swal.fire({
                        icon: 'success',
                        title: 'Open registration!',
                        text: resp.message,
                    }).then(function() {
                        location.reload();
                    });
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    hideLoading()
                    Swal.fire({
                        icon: 'error',
                        title: xhr.statusText,
                        text: xhr.responseJSON.message,
                        showConfirmButton: true,
                    });
                }
            })
        }
    })
});

$(document).on('click', '#executeSchedule', function() {
    var id = $(this).val();
    let executeURL = "{{ route('admin.manage-schedule.execute', ['id'=> ':id']) }}";
    executeURL = executeURL.replace(':id', id);

    Swal.fire({
        title: "Are you sure?",
        text: "Execute test schedule?",
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
                url: executeURL,
                type: 'get',
                data: {
                    _token: '{{ csrf_token() }}',
                },
                success: function(resp) {
                    hideLoading()
                    Swal.fire({
                        icon: 'success',
                        title: 'Exam started!',
                        text: resp.message,
                    }).then(function() {
                        location.reload();
                    });
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    hideLoading()
                    Swal.fire({
                        icon: 'error',
                        title: xhr.statusText,
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