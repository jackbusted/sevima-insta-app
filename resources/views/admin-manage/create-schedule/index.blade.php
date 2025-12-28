@extends('admin-manage.layouts.main')

@section('container')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
<link href="https://cdn.datatables.net/v/bs5/dt-1.13.8/b-2.4.2/b-colvis-2.4.2/b-html5-2.4.2/b-print-2.4.2/date-1.5.1/r-2.5.0/sc-2.3.0/sl-1.7.0/datatables.min.css" rel="stylesheet">

<style>
    .table-responsive {
        overflow: visible !important;
    }
</style>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pl-3 pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Manage Exam Schedule</h1>
    <div class="btn-toolbar mb-2 mb-md-0"></div>
</div>

<div class="col-lg-10">
    <div class="mb-3">
        <a href="/admin-manage/create-schedule/create">
            <button type="button" class="btn btn-outline-primary"><i class="bi bi-file-earmark-plus-fill"></i> Create</button>
        </a>
    </div>
</div>
<br>

<div class="col-lg-4">
    <div class="mb-3">
        <label for="examClass" class="form-label">Exam Class</label>
        <select class="form-select" name="examClass" id="examClass">
            <option value="">All</option>
            @foreach ($examClasses as $data)
                <option value="{{ $data }}">{{ $data }}</option>
            @endforeach
        </select>
    </div>
    <div class="mb-3">
        <label for="executions" class="form-label">Execution</label>
        <select class="form-select" name="executions" id="executions">
            <option value="">All</option>
            @foreach ($executions as $data)
                <option value="{{ $data }}">{{ $data }}</option>
            @endforeach
        </select>
    </div>
    <div class="mb-3">
        <label for="examStatus" class="form-label">Exam Status</label>
        <select class="form-select" name="examStatus" id="examStatus">
            <option value="">All</option>
            @foreach ($examStatuses as $data)
                <option value="{{ $data }}">{{ $data }}</option>
            @endforeach
        </select>
    </div>
</div>
<br>

<div class="table-responsive col w-full">
    <table id="table-schedule" class="table table-striped table-sm" style="width:100%">
        <thead>
            <tr>
                <th scope="col">Name</th>
                <th scope="col">Class</th>
                <th scope="col">Execution</th>
                <th scope="col">Test Date</th>
                <th scope="col">Time Execution</th>
                <th scope="col">Unconfirmed</th>
                <th scope="col">Confirmed</th>
                <th scope="col">Status</th>
                <th scope="col">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($datas as $data)
            <tr>
                <td style="vertical-align: middle;">{{ $data['schedule_name'] }}</td>
                <td style="vertical-align: middle;">{{ $data['class_test'] }}</td>
                <td style="vertical-align: middle;">{{ $data['execution'] }}</td>
                <td style="vertical-align: middle;">{{ date('Y-m-d', strtotime($data['open_date'])) }}</td>
                <td style="vertical-align: middle;">{{ $data['exe_clock'] }}</td>
                <td style="vertical-align: middle;">{{ $data['unconfirmed_capacity'] }}</td>
                <td style="vertical-align: middle;">{{ $data['confirmed_capacity'] }}</td>
                <td style="vertical-align: middle;">{{ $data['status'] }}</td>
                <td>
                    <div class="btn-group">
                        <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            More...
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a href="{{ URL::route('admin.manage-schedule.edit', ['id' => $data['id']]) }}" class="dropdown-item @if($data['status'] == "Already Started" || $data['status'] == "Done") disabled @endif">Edit
                                    <input type="hidden" name="edit_id" value="{{ $data['id'] }}">
                                </a>
                            </li>
                            <li>
                                <button value="{{ $data['id'] }}" class="delete-btn dropdown-item @if($data['status'] == "Already Started" || $data['status'] == "Done") disabled @endif">Delete</button>
                            </li>
                            <li>
                                <button value="{{ $data['id'] }}" class="open-btn dropdown-item @if($data['status'] == "Not Started Yet" || $data['status'] == "Already Started" || $data['status'] == "Done") disabled @endif">Open Registration</button>
                            </li>
                            <li>
                                <button value="{{ $data['id'] }}" class="execute-btn dropdown-item @if($data['status'] == "Ready To Open" || $data['status'] == "Already Started" || $data['status'] == "Done") disabled @endif">Execute</button>
                            </li>
                            <li>
                                <a href="{{ URL::route('admin.manage-registration.list', ['id' => $data['id']]) }}" class="dropdown-item">More Info</a>
                            </li>
                        </ul>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/v/bs5/dt-1.13.8/b-2.4.2/b-colvis-2.4.2/b-html5-2.4.2/b-print-2.4.2/date-1.5.1/r-2.5.0/sc-2.3.0/sl-1.7.0/datatables.min.js"></script>

<script>
$(document).ready(function() {
    if ($.fn.DataTable.isDataTable('#table-schedule')) {
        $('#table-schedule').DataTable().destroy();
    }
    var table = $('#table-schedule').DataTable();

    $('#examClass').change(function() {
        var examClass = $(this).val();
        table.column(1).search(examClass).draw();
    });

    $('#executions').change(function() {
        var executions = $(this).val();
        table.column(2).search(executions).draw();
    });

    $('#examStatus').change(function() {
        var examStatus = $(this).val();
        table.column(7).search(examStatus).draw();
    });
})

$(document).on('click', '.delete-btn', function() {
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
                contentType: false,
                processData: false,
                success: function (response) {
                    hideLoading()
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        text: response.message,
                    }).then(function() {
                        window.location.reload();
                    });
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    hideLoading()
                    Swal.fire({
                        icon: 'error',
                        title: "Failed to delete. Something went wrong",
                        text: xhr.responseJSON.message,
                        showConfirmButton: true,
                    });
                }
            });
        }
    });
});

$(document).on('click', '.open-btn', function() {
    var id = $(this).val();
    let openURL = "{{ route('admin.manage-schedule.open', ['id'=> ':id']) }}";
    openURL = openURL.replace(':id', id);

    Swal.fire({
        title: "Are you sure?",
        text: "Open this schedule for registration?",
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
                url: openURL,
                type: 'get',
                contentType: false,
                processData: false,
                success: function (response) {
                    hideLoading();
                    Swal.fire({
                        icon: 'success',
                        title: 'Open registration!',
                        text: response.message,
                    }).then(function () {
                        window.location.reload();
                    });
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    hideLoading();
                    Swal.fire({
                        icon: 'error',
                        title: "Failed to open. Something went wrong",
                        text: xhr.responseJSON.message,
                        showConfirmButton: true,
                    });
                }
            });
        }
    });
});

$(document).on('click', '.execute-btn', function() {
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
    .then((result) => {
        if (result.isConfirmed) {
            showLoading();
            $.ajax({
                url: executeURL,
                type: 'get',
                contentType: false,
                processData: false,
                success: function (response) {
                    hideLoading();
                    Swal.fire({
                        icon: 'success',
                        title: 'Exam started!',
                        text: response.message,
                    }).then(function () {
                        window.location.reload();
                    });
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    hideLoading();
                    Swal.fire({
                        icon: 'error',
                        title: "Failed to execute. Something went wrong",
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