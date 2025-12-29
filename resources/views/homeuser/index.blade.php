@extends('homeuser.layouts.main')

@section('container')
<style>
    .post-header {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 8px;
    }

    .avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
        border: 1px solid #ddd;
    }

    .post-image {
        width: 100%;
        margin-top: 5px;
    }
</style>
<script src="https://cdnjs.cloudflare.com/ajax/libs/baguettebox.js/1.11.0/baguetteBox.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/baguettebox.js/1.11.0/baguetteBox.min.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

<div class="justify-content-between flex-wrap flex-md-nowrap align-items-center pl-3 pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Welcome, {{ auth()->user()->name }}</h1>
</div>

<form action="{{ route('homeuser.posts.new-feed') }}" method="post" id="form-post-feed" enctype="multipart/form-data">
    @csrf
    <div class="row">
        <div class="col-lg-6">
            <div class="mb-3">
                <label for="caption" class="form-label">Post your experience</label>
                <input type="text" class="form-control" id="caption" name="caption" value="">
            </div>
            <div class="mb-3" id="imagePreview" style="display: none;">
                <a href="" id="imageBoxPreview" data-baguettebox>
                    <img class="img-preview img-fluid mb-3 col-sm-5" id="imagePreviewTag">
                </a>
            </div>
            <div class="mb-3">
                <input type="file" class="form-control" id="post-image" name="post-image" accept=".jpeg,.jpg,.png" onchange="previewImage()">
            </div>
            <button id="post-feed" type="submit" class="btn btn-primary mr-2">Post</button>
        </div>
    </div>
</form>

<br><br>

<div id="feed">
    @foreach ($data as $post)
        <div class="post" data-id="{{ $post->id }}">
            <div class="post-header">
                <img
                    src="{{ $post->user->avatar
                        ? asset($post->user->avatar)
                        : asset('images/logo.png') }}"
                    class="avatar"
                >
                <strong>{{ $post->user->username }}</strong>
            </div>
            <img src="{{ asset($post->image) }}" width="100%">
            <p>{{ $post->caption }}</p>

            <button class="btn-like" data-id="{{ $post->id }}" onclick="handleClickLike('{{ $post->id }}')">
                @if ($post->isLikedBy(auth()->id()))
                    ❤️
                @else
                    🤍
                @endif
                <span class="like-count">{{ $post->likes_count }}</span>
            </button>

            <div class="comments">
                @foreach ($post->comments as $comment)
                    <p>
                        <b>{{ $comment->user->username }}</b>
                        {{ $comment->content }}
                    </p>
                @endforeach
            </div>

            <input
                type="text"
                class="comment-input"
                data-id="{{ $post->id }}"
                onkeydown="handleCommentKey(event, '{{ $post->id }}')"
                placeholder="Add a comment..."
            >
        </div>
        <br><br>
    @endforeach
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script>
    let cursor = "{{ $data->count() > 0 ? $data->last()->id : null }}";
    let loading = false;
    let hasMore = true;

    const loader = document.getElementById('loader');

    const observer = new IntersectionObserver(entries => {
        if (entries[0].isIntersecting && !loading && hasMore) {
            loadMore();
        }
    });

    observer.observe(loader);

    async function loadMore() {
        loading = true;
        document.getElementById('loading').style.display = 'block';

        const res = await fetch(`/homeuser/load-feed?cursor=${cursor}`);
        const json = await res.json();

        json.data.forEach(post => {
            document.getElementById('feed').insertAdjacentHTML(
                'beforeend',
                renderPost(post)
            );
        });

        cursor = json.next_cursor;
        hasMore = json.has_more;

        loading = false;
        document.getElementById('loading').style.display = 'none';
    }

    function renderPost(post) {
        let images = `<img src="${post.image}" width="100%">`;

        return `
            <div class="post">
                <strong>${post.user.username}</strong>
                ${images}
                <p>${post.caption}</p>
            </div>
        `;
    }

    document.getElementById('post-feed').addEventListener('click', function(event) {
        event.preventDefault();

        var formData = new FormData(document.getElementById('form-post-feed'));
        formData.append('_token', '{{ csrf_token() }}');

        showLoading()
        $.ajax({
            url: "{{ URL::route('homeuser.posts.new-feed') }}",
            type: 'post',
            data: formData,
            contentType: false,
            processData: false,
            success: function(resp) {
                hideLoading()
                location.reload();
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
        });
    });

    function previewImage() {
        const image = document.querySelector('#post-image');
        const imagePreviewDiv = document.querySelector('#imagePreview');
        const imagePreviewTag = document.querySelector('#imagePreviewTag');
        const preview = document.querySelector('#imageBoxPreview');

        if (image.files && image.files[0]) {
            const oFReader = new FileReader();
            oFReader.readAsDataURL(image.files[0]);
            oFReader.onload = function(oFREvent) {
                imagePreviewTag.src = oFREvent.target.result;
                imagePreviewDiv.style.display = 'block';

                var formData = new FormData(document.getElementById('form-post-feed'));
                formData.append('_token', '{{ csrf_token() }}');
                $.ajax({
                    url: "{{ URL::route('homeuser.posts.image-preview') }}",
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

    function handleClickLike(id) {
        const btn = document.querySelector('.btn-like');

        $.ajax({
            url: `/homeuser/${id}/like`,
            type: 'POST',
            data: {
                _token: "{{ csrf_token() }}"
            },
            success: function (resp) {
                btn.find('.like-count').text(resp.count);
                btn.html((resp.liked ? '❤️ ' : '🤍 ') + `<span class="like-count">${resp.count}</span>`);
                btn.append(`<span class="like-count">${resp.count}</span>`);
            },
            error: function (xhr) {
                console.log(xhr.responseText);
            }
        });
    }

    function handleCommentKey(e, postId) {
        if (e.key === 'Enter') {
            e.preventDefault();

            console.log("enter event")

            const input = e.target;

            if (input.value.trim() === '') return;

            $.ajax({
                url: `/homeuser/${postId}/comment`,
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    comment: input.value.trim()
                },
                success: function (resp) {
                    $('#comments-' + postId)
                        .append(`<p><b>${resp.username}</b> ${resp.comment}</p>`);
                    input.value.trim()
                },
                error: function (xhr) {
                    console.log(xhr.responseText);
                }
            });
        }
    }

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