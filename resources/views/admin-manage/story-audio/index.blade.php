@extends('admin-manage.layouts.main')

@section('container')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
<link href="https://cdn.datatables.net/v/bs5/dt-1.13.8/b-2.4.2/b-colvis-2.4.2/b-html5-2.4.2/b-print-2.4.2/date-1.5.1/r-2.5.0/sc-2.3.0/sl-1.7.0/datatables.min.css" rel="stylesheet">

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pl-3 pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Manage Story Audio</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2"></div>
    </div>
</div>

<div class="col-lg-10">
    <div class="mb-3">
        <a href="{{ URL::route('admin.manage-question.view-created-question') }}">
            <button
                id="back-btn"
                name="back-btn"
                class="btn btn-outline-danger"
                type="button"
            ><i class="bi bi-arrow-left"></i>Back</button>
        </a>
        <a href="{{ URL::route('admin.manage-question.story-audio.create') }}" style="padding-left: 10px">
            <button type="button" class="btn btn-outline-primary">
                <i class="bi bi-file-earmark-plus-fill"></i> Create New Story Audio
            </button>
        </a>
    </div>
</div>
<div class="table-responsive col w-full">
    <table id="table-story-audio" class="table table-striped table-sm" style="width:100%">
        <thead>
            <tr>
                <th scope="col" style="width: 40%">Name</th>
                <th scope="col">Last Updated</th>
                <th scope="col">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($datas as $data)
                <tr>
                    <td style="vertical-align: middle;">{{ $data['name'] }}</td>
                    <td style="vertical-align: middle;">{{ $data['last_updated'] }}</td>
                    <td>
                        {{-- tombol edit --}}
                        <a href="{{ URL::route('admin.manage-question.story-audio.detail', ['id' => $data['id']]) }}">
                            <input type="hidden" name="edit_id" value="{{ $data['id'] }}">
                            <button title="Edit" type="submit" class="btn btn-outline-success mb-1" style="color: rgb(28, 248, 138);">
                                <i class="bi bi-pencil-fill"></i>
                            </button>
                        </a>

                        {{-- tombol delete --}}
                        <button title="Delete" value="{{ $data['id'] }}" class="btn button-delete btn-outline-danger mb-1" style="color: rgb(255, 0, 0);">
                            <i class="bi bi-trash"></i>
                        </button>
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
        if ($.fn.DataTable.isDataTable('#table-story-audio')) {
            $('#table-story-audio').DataTable().destroy();
        }

        var table = $('#table-story-audio').DataTable();
    })

    $(document).on('click', '.button-delete', function() {
        var id = $(this).val();

        Swal.fire({
            title: "Are you sure?",
            text: "Delete this story audio?",
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
                    url: "{{ URL::route('admin.manage-question.story-audio.delete') }}",
                    type: 'post',
                    data: {
                        _token: '{{ csrf_token() }}',
                        id: id,
                    },
                    success: function(resp) {
                        hideLoading();
                        Swal.fire({
                            title: 'Story audio data deleted!',
                            text: resp.message,
                            icon: 'success'
                        }).then(function() {
                            window.location.reload();
                        });
                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                        hideLoading();
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
    })
</script>
@endsection