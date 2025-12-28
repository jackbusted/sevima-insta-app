@extends('admin-manage.layouts.main')

@section('container')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
<link href="https://cdn.datatables.net/v/bs5/dt-1.13.8/b-2.4.2/b-colvis-2.4.2/b-html5-2.4.2/b-print-2.4.2/date-1.5.1/r-2.5.0/sc-2.3.0/sl-1.7.0/datatables.min.css" rel="stylesheet">

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2 pl-2">Luaran Hasil Randomize Soal (Listening)</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2"></div>
    </div>
</div>

<div class="col-lg-6">
    <div class="mb-3">
        <label for="className" class="form-label">Test Class</label>
        <select class="form-select" name="className" id="classSelect">
            <option value=""></option>
            @foreach ($class_test as $data)
                <option value="{{ $data }}">{{ $data }}</option>
            @endforeach
        </select>
    </div>
    <div class="mb-3">
        <label for="dateExec" class="form-label">Test Execution</label>
        <select class="form-select" name="dateExec" id="execSelect">
            <option value=""></option>
            @foreach ($class_execution as $data)
                <option value="{{ $data }}">{{ $data }}</option>
            @endforeach
        </select>
    </div>
    <div class="mb-3">
        <label for="dateTest" class="form-label">Test Date</label>
        <select class="form-select" name="dateTest" id="dateSelect">
            <option value=""></option>
            @foreach ($date_test as $data)
                <option value="{{ date('Y-m-d', strtotime($data)) }}">{{ date('Y-m-d', strtotime($data)) }}</option>
            @endforeach
        </select>
    </div>
    <div class="mb-3">
        <label for="userName" class="form-label">Participant</label>
        <select class="form-select" name="userName" id="userSelect">
            <option value=""></option>
            @foreach ($user_name as $data)
                <option value="{{ $data }}">{{ $data }}</option>
            @endforeach
        </select>
    </div>
</div>
<hr>
<div class="table-responsive mb-5 col w-full">
    <table id="table-review-quiz" class="table table-striped table-sm" style="width:100%">
        <thead>
            <tr>
                <th scope="col">Class</th>
                <th scope="col">Execution</th>
                <th scope="col">Date</th>
                <th scope="col">Clock</th>
                <th scope="col">Participant</th>
                <th scope="col">Question ID</th>
                <th scope="col">Title</th>
            </tr>
        </thead>
        <tbody>
            @foreach($datas as $data)
            <tr>
                <td>{{ $data['class_test'] }}</td>
                <td>{{ $data['execution'] }}</td>
                <td>{{ date('Y-m-d', strtotime($data['open_date'])) }}</td>
                <td>{{ $data['exe_clock'] }}</td>
                <td>{{ $data['user_name'] }}</td>
                <td>{{ $data['question_id'] }}</td>
                <td>{{ $data['question_title'] }}</td>
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
$(document).ready(function(){
    // Periksa apakah DataTable sudah diinisialisasi sebelumnya
    if ($.fn.DataTable.isDataTable('#table-review-quiz')) {
        // Hancurkan DataTable sebelum menginisialisasi ulang
        $('#table-review-quiz').DataTable().destroy();
    }

    // Inisialisasi DataTable
    var table = $('#table-review-quiz').DataTable();

    // Handle perubahan filter
    $('#classSelect').change(function() {
        var classTest = $(this).val();
        table.column(0).search(classTest).draw();
    });

    $('#execSelect').change(function() {
        var classExec = $(this).val();
        table.column(1).search(classExec).draw();
    });

    $('#dateSelect').change(function() {
        var classDate = $(this).val();
        table.column(2).search(classDate).draw();
    });

    $('#userSelect').change(function() {
        var userName = $(this).val();
        table.column(4).search(userName).draw();
    });
});
</script>
@endsection