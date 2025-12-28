@extends('homeuser.layouts.main')

@section('container')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
<link href="https://cdn.datatables.net/v/bs5/dt-1.13.8/b-2.4.2/b-colvis-2.4.2/b-html5-2.4.2/b-print-2.4.2/date-1.5.1/r-2.5.0/sc-2.3.0/sl-1.7.0/datatables.min.css" rel="stylesheet">

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pl-3 pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Your Exam History</h1>
    <div class="btn-toolbar mb-2 mb-md-0"></div>
</div>

<div class="col-lg-4">
    <div class="mb-3">
        <label for="examClasses" class="form-label">Exam Class</label>
        <select class="form-select" name="examClasses" id="examClass">
            <option value="">All</option>
            @foreach ($examClasses as $data)
                <option value="{{ $data }}">{{ $data }}</option>
            @endforeach
        </select>
    </div>
    <div class="mb-3">
        <label for="examExecutions" class="form-label">Exam Execution</label>
        <select class="form-select" name="examExecutions" id="examExecution">
            <option value="">All</option>
            @foreach ($examExecutions as $data)
                <option value="{{ $data }}">{{ $data }}</option>
            @endforeach
        </select>
    </div>
</div>

<br>
<div class="table-responsive col w-full">
    <table id="table-history" class="table table-striped table-sm" style="width:100%">
        <thead>
            <tr>
                <th scope="col">Exam Name</th>
                <th scope="col">Date</th>
                <th scope="col">Class</th>
                <th scope="col">Execution</th>
                <th scope="col">Time</th>
                <th scope="col">Score</th>
            </tr>
        </thead>
        <tbody>
            @foreach($datas as $data)
            <tr>
                <td>{{ $data['examName'] }}</td>
                <td>{{ $data['examDate'] }}</td>
                <td>{{ $data['examClass'] }}</td>
                <td>{{ $data['examExecution'] }}</td>
                <td>{{ $data['examTime'] }}</td>
                <td>{{ $data['score'] != null ? $data['score'] : 'Waiting Approval' }}</td>
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
        if ($.fn.DataTable.isDataTable('#table-history')) {
            $('#table-history').DataTable().destroy();
        }
        var table = $('#table-history').DataTable();

        $('#examClass').change(function() {
            var category = $(this).val();
            table.column(2).search(category).draw();
        });

        $('#examExecution').change(function() {
            var group = $(this).val();
            table.column(3).search(group).draw();
        });
    })
</script>
@endsection