<!DOCTYPE html>
<html lang="en" data-bs-theme="auto">

<head>
    <script src="{{ asset('js/color-modes.js') }}"></script>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="viewport" content="initial-scale=1, maximum-scale=1">
    <title>ITATS's TEFL - START EXAM</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">

    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700,800,900" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/baguettebox.js/1.11.0/baguetteBox.min.css">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    <style>
        .bd-placeholder-img {
            font-size: 1.125rem;
            text-anchor: middle;
            -webkit-user-select: none;
            -moz-user-select: none;
            user-select: none;
        }

        @media (min-width: 768px) {
            .bd-placeholder-img-lg {
                font-size: 3.5rem;
            }
        }

        .b-example-divider {
            width: 100%;
            height: 3rem;
            background-color: rgba(0, 0, 0, .1);
            border: solid rgba(0, 0, 0, .15);
            border-width: 1px 0;
            box-shadow: inset 0 .5em 1.5em rgba(0, 0, 0, .1), inset 0 .125em .5em rgba(0, 0, 0, .15);
        }

        .b-example-vr {
            flex-shrink: 0;
            width: 1.5rem;
            height: 100vh;
        }

        .bi {
            vertical-align: -.125em;
            fill: currentColor;
        }

        .btn-bd-primary {
            --bd-violet-bg: #712cf9;
            --bd-violet-rgb: 112.520718, 44.062154, 249.437846;

            --bs-btn-font-weight: 600;
            --bs-btn-color: var(--bs-white);
            --bs-btn-bg: var(--bd-violet-bg);
            --bs-btn-border-color: var(--bd-violet-bg);
            --bs-btn-hover-color: var(--bs-white);
            --bs-btn-hover-bg: #6528e0;
            --bs-btn-hover-border-color: #6528e0;
            --bs-btn-focus-shadow-rgb: var(--bd-violet-rgb);
            --bs-btn-active-color: var(--bs-btn-hover-color);
            --bs-btn-active-bg: #5a23c8;
            --bs-btn-active-border-color: #5a23c8;
        }

        .bd-mode-toggle {
            z-index: 1500;
        }

        .bd-mode-toggle .dropdown-menu .active .bi {
            display: block !important;
        }

        /* button for question number */
        .btn-question {
            align-items: center;
            background-image: linear-gradient(144deg, #AF40FF, #5B42F3 50%,#00DDEB);
            border: 0;
            border-radius: 8px;
            box-shadow: rgba(151, 65, 252, 0.2) 0 15px 30px -5px;
            box-sizing: border-box;
            color: #FFFFFF;
            display: flex;
            font-family: Phantomsans, sans-serif;
            font-size: 18px;
            justify-content: center;
            max-width: 100%;
            min-width: 50px;
            max-height: 100%;
            min-height: 50px;
            padding: 3px;
            text-decoration: none;
            user-select: none;
            -webkit-user-select: none;
            touch-action: manipulation;
            white-space: nowrap;
            cursor: pointer;
            transition: all .3s;
        }

        .btn-question:hover {
            outline: 0;
        }

        .btn-question span {
            background-color: rgb(5, 6, 45);
            border-radius: 6px;
            width: 100%;
            height: 100%;
            transition: 300ms;
        }

        .btn-question:hover span {
            background: none;
        }

        .btn-question.active {
            align-items: center;
            background-image: #4CAF50;
            color: white;
        }

        /* button for play audio */
        .btn-audio,
        .btn-audio *,
        .btn-audio :after,
        .btn-audio :before,
        .btn-audio:after,
        .btn-audio:before {
            border: 0 solid;
            box-sizing: border-box;
        }

        .btn-audio {
            -webkit-tap-highlight-color: transparent;
            -webkit-appearance: button;
            background-color: #053742;
            background-image: none;
            color: #fff;
            cursor: pointer;
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont,
            Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif,
            Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji;
            font-size: 100%;
            font-weight: 900;
            line-height: 1.5;
            margin: 0;
            -webkit-mask-image: -webkit-radial-gradient(#000, #fff);
            padding: 0;
            text-transform: uppercase;
        }

        .btn-audio:disabled {
            cursor: default;
        }

        .btn-audio:-moz-focusring {
            outline: auto;
        }

        .btn-audio svg {
            display: block;
            vertical-align: middle;
        }

        .btn-audio [hidden] {
            display: none;
        }

        .btn-audio {
            border-radius: 99rem;
            border-width: 2px;
            padding: 0.8rem 3rem;
            z-index: 0;
        }

        .btn-audio,
        .btn-audio .text-container {
            overflow: hidden;
            position: relative;
        }

        .btn-audio .text-container {
            display: block;
            mix-blend-mode: difference;
        }

        .btn-audio .text {
            display: block;
            position: relative;
        }

        .btn-audio:hover .text {
            -webkit-animation: move-up-alternate 0.3s forwards;
            animation: move-up-alternate 0.3s forwards;
        }

        @-webkit-keyframes move-up-alternate {
            0% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(80%);
            }

            51% {
                transform: translateY(-80%);
            }

            to {
                transform: translateY(0);
            }
        }

        @keyframes move-up-alternate {
            0% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(80%);
            }

            51% {
                transform: translateY(-80%);
            }

            to {
                transform: translateY(0);
            }
        }

        .btn-audio:after,
        .btn-audio:before {
            --skew: 0.2;
            background: #E8F0F2;
            content: "";
            display: block;
            height: 102%;
            left: calc(-50% - 50% * var(--skew));
            pointer-events: none;
            position: absolute;
            top: -104%;
            transform: skew(calc(150deg * var(--skew))) translateY(var(--progress, 0));
            transition: transform 0.2s ease;
            width: 100%;
        }

        .btn-audio:after {
            --progress: 0%;
            left: calc(50% + 50% * var(--skew));
            top: 102%;
            z-index: -1;
        }

        .btn-audio:hover:before {
            --progress: 100%;
        }

        .btn-audio:hover:after {
            --progress: -102%;
        }

        /* radio button for answer lines */
        .radio-input {
            display: flex;
            flex-direction: row;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
            font-size: 16px;
            font-weight: 600;
            color: white;
        }

        .radio-input input[type="radio"] {
            display: none;
        }

        .radio-input label {
            display: flex;
            align-items: center;
            padding: 10px;
            border: 1px solid #ccc;
            background-color: #212121;
            border-radius: 5px;
            margin-right: 12px;
            cursor: pointer;
            position: relative;
            transition: all 0.3s ease-in-out;
        }

        .radio-input label:before {
            content: "";
            display: block;
            position: absolute;
            top: 50%;
            left: 0;
            transform: translate(-50%, -50%);
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background-color: #fff;
            border: 2px solid #ccc;
            transition: all 0.3s ease-in-out;
        }

        .radio-input input[type="radio"]:checked + label:before {
            background-color: green;
            top: 0;
        }

        .radio-input input[type="radio"]:checked + label {
            background-color: royalblue;
            color: #fff;
            border-color: rgb(129, 235, 129);
            animation: radio-translate 0.5s ease-in-out;
        }

        @keyframes radio-translate {
            0% {
            transform: translateX(0);
            }

            50% {
            transform: translateY(-10px);
            }

            100% {
            transform: translateX(0);
            }
        }

        /* button for next exam type */
        .button-next {
            all: unset;
            display: flex;
            align-items: center;
            position: relative;
            padding: 0.6em 2em;
            border: mediumspringgreen solid 0.15em;
            border-radius: 0.25em;
            color: mediumspringgreen;
            font-size: 1.5em;
            font-weight: 600;
            cursor: pointer;
            overflow: hidden;
            transition: border 300ms, color 300ms;
            user-select: none;
        }

        .button-next p {
           margin: auto;
           transition: color 300ms;
        }

        .button-next:hover {
            color: #212121;
        }

        .button-next:hover p {
            z-index: 1;
            color: #212121;
        }

        .button-next:active {
            border-color: teal;
        }

        .button-next::after, .button-next::before {
            content: "";
            position: absolute;
            width: 9em;
            aspect-ratio: 1;
            background: mediumspringgreen;
            opacity: 50%;
            border-radius: 50%;
            transition: transform 500ms, background 300ms;
        }

        .button-next::before {
            left: 0;
            transform: translateX(-8em);
        }

        .button-next::after {
            right: 0;
            transform: translateX(8em);
        }

        .button-next:hover:before {
            transform: translateX(-1em);
        }

        .button-next:hover:after {
            transform: translateX(1em);
        }

        .button-next:active:before,
        .button-next:active:after {
            background: teal;
        }

        /* vertical line */
        .vl {
            border: 2px solid rgb(122, 122, 122);
            height: 500px;
        }

        /* center image */
        img {
            display: block;
            margin-left: auto;
            margin-right: auto;
        }
    </style>

    {{-- Custom styles for this template --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.addEventListener('contextmenu', function(e) {
                e.preventDefault();
            });

            document.addEventListener('selectstart', function(e) {
                e.preventDefault();
            });

            document.addEventListener('mousedown', function(e) {
                if (e.detail > 1) {
                    e.preventDefault();
                }
            });

            document.addEventListener('keydown', function(e) {
                if (e.ctrlKey || e.metaKey || e.altKey) {
                    e.preventDefault();
                }

                const disabledKeys = [123, 73, 85, 67, 116]; // F12, Ctrl+Shift+I, Ctrl+U, Ctrl+C, F5
                // const disabledKeys = [73, 85, 67, 116]; // Ctrl+Shift+I, Ctrl+U, Ctrl+C, F5
                if (disabledKeys.includes(e.keyCode) || (e.ctrlKey && disabledKeys.includes(e.which))) {
                    e.preventDefault();
                }
            });
        })
    </script>
</head>

<body>
    <svg xmlns="http://www.w3.org/2000/svg" class="d-none">
        <symbol id="check2" viewBox="0 0 16 16">
            <path
                d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z" />
        </symbol>
        <symbol id="circle-half" viewBox="0 0 16 16">
            <path d="M8 15A7 7 0 1 0 8 1v14zm0 1A8 8 0 1 1 8 0a8 8 0 0 1 0 16z" />
        </symbol>
        <symbol id="moon-stars-fill" viewBox="0 0 16 16">
            <path
                d="M6 .278a.768.768 0 0 1 .08.858 7.208 7.208 0 0 0-.878 3.46c0 4.021 3.278 7.277 7.318 7.277.527 0 1.04-.055 1.533-.16a.787.787 0 0 1 .81.316.733.733 0 0 1-.031.893A8.349 8.349 0 0 1 8.344 16C3.734 16 0 12.286 0 7.71 0 4.266 2.114 1.312 5.124.06A.752.752 0 0 1 6 .278z" />
            <path
                d="M10.794 3.148a.217.217 0 0 1 .412 0l.387 1.162c.173.518.579.924 1.097 1.097l1.162.387a.217.217 0 0 1 0 .412l-1.162.387a1.734 1.734 0 0 0-1.097 1.097l-.387 1.162a.217.217 0 0 1-.412 0l-.387-1.162A1.734 1.734 0 0 0 9.31 6.593l-1.162-.387a.217.217 0 0 1 0-.412l1.162-.387a1.734 1.734 0 0 0 1.097-1.097l.387-1.162zM13.863.099a.145.145 0 0 1 .274 0l.258.774c.115.346.386.617.732.732l.774.258a.145.145 0 0 1 0 .274l-.774.258a1.156 1.156 0 0 0-.732.732l-.258.774a.145.145 0 0 1-.274 0l-.258-.774a1.156 1.156 0 0 0-.732-.732l-.774-.258a.145.145 0 0 1 0-.274l.774-.258c.346-.115.617-.386.732-.732L13.863.1z" />
        </symbol>
        <symbol id="sun-fill" viewBox="0 0 16 16">
            <path
                d="M8 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM8 0a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 0zm0 13a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 13zm8-5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2a.5.5 0 0 1 .5.5zM3 8a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2A.5.5 0 0 1 3 8zm10.657-5.657a.5.5 0 0 1 0 .707l-1.414 1.415a.5.5 0 1 1-.707-.708l1.414-1.414a.5.5 0 0 1 .707 0zm-9.193 9.193a.5.5 0 0 1 0 .707L3.05 13.657a.5.5 0 0 1-.707-.707l1.414-1.414a.5.5 0 0 1 .707 0zm9.193 2.121a.5.5 0 0 1-.707 0l-1.414-1.414a.5.5 0 0 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .707zM4.464 4.465a.5.5 0 0 1-.707 0L2.343 3.05a.5.5 0 1 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .708z" />
        </symbol>
    </svg>

    <div class="dropdown position-fixed bottom-0 end-0 mb-3 me-3 bd-mode-toggle">
        <button class="btn btn-bd-primary py-2 dropdown-toggle d-flex align-items-center" id="bd-theme" type="button"
            aria-expanded="false" data-bs-toggle="dropdown" aria-label="Toggle theme (auto)">
            <svg class="bi my-1 theme-icon-active" width="1em" height="1em">
                <use href="#circle-half"></use>
            </svg>
            <span class="visually-hidden" id="bd-theme-text">Toggle theme</span>
        </button>

        <!-- change theme (error) -->
        <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="bd-theme-text">
            <li>
                <button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="light"
                    aria-pressed="false">
                    <svg class="bi me-2 opacity-50 theme-icon" width="1em" height="1em">
                        <use href="#sun-fill"></use>
                    </svg>
                    Light
                    <svg class="bi ms-auto d-none" width="1em" height="1em">
                        <use href="#check2"></use>
                    </svg>
                </button>
            </li>
            <li>
                <button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="dark"
                    aria-pressed="false">
                    <svg class="bi me-2 opacity-50 theme-icon" width="1em" height="1em">
                        <use href="#moon-stars-fill"></use>
                    </svg>
                    Dark
                    <svg class="bi ms-auto d-none" width="1em" height="1em">
                        <use href="#check2"></use>
                    </svg>
                </button>
            </li>
            <li>
                <button type="button" class="dropdown-item d-flex align-items-center active" data-bs-theme-value="auto"
                    aria-pressed="true">
                    <svg class="bi me-2 opacity-50 theme-icon" width="1em" height="1em">
                        <use href="#circle-half"></use>
                    </svg>
                    Auto
                    <svg class="bi ms-auto d-none" width="1em" height="1em">
                        <use href="#check2"></use>
                    </svg>
                </button>
            </li>
        </ul>
    </div>

    <div class="row pl-3">
        <div class="col-3 mb-4 ml-2 mt-2 pl-2 pr-2 vl" style="position: sticky;">
            <h3 style="font-size: 22px; margin-top: 20px; margin-bottom: 15px; text-align: center;">Question Number</h3>
            <hr>
            <div class="row w-full pr-4 pl-4" id="question-numbers-container">
                @foreach ($datas as $data)
                    <div class="col-3 mr-4 mb-4" style="height: 40px; width: 40px">
                        <button class="btn-question" id="{{ 'btn-question-' . $data['number'] }}" onclick="handleClick('{{ $data['id'] }}')">
                            <span class="text" id="{{ 'span-question-' . $data['number'] }}">{{ $data['number'] }}</span>
                        </button>
                    </div>
                @endforeach
            </div>
            <br><br>
            <div class="row w-full pr-4 pl-4" id="next-session-button" style="display: none;">
                <button class="button-next" style="height: 40px; width: 200px; position: relative; left: 50%; -ms-transform: translate(-50%, -50%); transform: translate(-50%, -50%); top: 1cm;" onclick="handleChangeType('{{ $type }}')">
                    <p id="next-btn-text">Next Session</p>
                </button>
            </div>
        </div>
        <div class="col-8 mb-4 ml-2 mt-2 pl-2">
            <h3 id="exam-title" style="font-size: 22px; margin-top: 20px; margin-bottom: 15px; text-align: center;">{{ 'Question Detail for ' . $title }}</h3>
            <hr>
            <div id="question-details-container">
                @foreach ($datas as $data)
                    <div id="{{ 'div-' . $data['id'] }}" style="display: none; padding-left: 0.5cm; padding-right: 0.5cm;">
                        <div class="mb-3" id="{{ 'audioDiv-' . $data['id'] }}" style="display: none;">
                            <button class="btn-audio" id="{{ 'playBtn-' . $data['id'] }}" onclick="playAudio('{{ $data['id'] }}')" style="display: block; position: relative; left: 50%; -ms-transform: translate(-50%, -50%); transform: translate(-50%, -50%); top: 1cm;">
                                <span class="text-container">
                                    <span class="text"><i class="bi bi-play-fill"></i> Play audio</span>
                                </span>
                            </button>
                            <button class="btn-audio" id="{{ 'playingBtn-' . $data['id'] }}" style="display: none; position: relative; left: 50%; -ms-transform: translate(-50%, -50%); transform: translate(-50%, -50%); top: 1cm;">
                                <span class="text-container">
                                    <span class="text"><i class="bi bi-play-fill"></i> Audio is playing ...</span>
                                </span>
                            </button>
                            <button class="btn-audio" id="{{ 'playedBtn-' . $data['id'] }}" style="display: none; position: relative; left: 50%; -ms-transform: translate(-50%, -50%); transform: translate(-50%, -50%); top: 1cm;">
                                <span class="text-container">
                                    <span class="text"><i class="bi bi-play-fill"></i> Audio is already played</span>
                                </span>
                            </button>
                            <audio preload="auto" id="{{ 'audio-' . $data['id'] }}" src="{{ asset($data['audio']) }}" controls class="audio-preview col-sm-5" style="display: none;">
                        </div>
                        <div class="mb-3" id="{{ 'imageDiv-' . $data['id'] }}" style="display: none;">
                            <a href="" data-baguettebox>
                                <img class="img-preview img-fluid mb-3 col-sm-5" style="height: auto;" id="{{ 'imageQuestionPreview-' . $data['id'] }}">
                            </a>
                        </div>
                        <div class="mb-3">
                            <h4 for="title">{{ $data['question_words'] }}</h4>
                        </div>
                        <br>
                        @foreach ($data['answer_lines'] as $lines)
                            <div class="radio-input mt-2">
                                <input
                                    type="radio"
                                    id="{{ 'question-' . $data['id'] . '-answer-' . $lines['id'] }}"
                                    value="{{ 'question-' . $data['id'] . '-answer-' . $lines['id'] }}"
                                    name="{{ 'question-' . $data['id'] }}"
                                    onclick="answerClick('{{ $data['id'] }}', '{{ $lines['id'] }}')"
                                >
                                <label for="{{ 'question-' . $data['id'] . '-answer-' . $lines['id'] }}">{{ $lines['name'] }}</label><br>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- script section -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/baguettebox.js/1.11.0/baguetteBox.min.js"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/feather.js') }}"></script>
    <script src="{{ asset('js/popper.js') }}"></script>
    <script src="{{ asset('js/main.js') }}"></script>
    <script>
        var userID = {!! json_encode($user_id) !!};
        var scheduleID = {!! json_encode($schedule_id) !!};
        var isComplete = false;
        let answers = [];
        var checkData = {!! json_encode($datas) !!};
        checkData.forEach(val => {
            if (val.is_answered == 1) {
                const btnQuestion = document.getElementById('span-question-' + val.number);
                btnQuestion.innerHTML = "✔";

                val.answer_lines.forEach(line => {
                    if (line.choosen == true) {
                        const radioButton = document.getElementById('question-' + val.id + '-answer-' + line.id);
                        radioButton.checked = true;

                        answers.push({
                            userID: val.user_id,
                            scheduleID: val.schedule_id,
                            questionID: val.id,
                            answerID: line.id,
                        });
                    }
                })
            }

            if (val.category == "Listening Part A") {
                if (val.is_listened) {
                    const playBtn = document.getElementById('playBtn-' + val.id);
                    const playingBtn = document.getElementById('playingBtn-' + val.id);
                    const playedBtn = document.getElementById('playedBtn-' + val.id);

                    playBtn.style.display = 'none';
                    playingBtn.style.display = 'none';
                    playedBtn.style.display = 'block';
                }
            }
        })

        if (answers.length >= checkData.length) {
            isComplete = true;
        }

        const nextButton = document.getElementById('next-session-button');
        if (isComplete) {
            nextButton.style.display = 'block';
        }

        let draw = 0;
        var newData = null;
        var newDataLines = null;
        var previousMasterAudioDiv = null;
        var previousCategoryDiv = null;
        var previousImageDiv = null;
        var previousDiv = null;
        var itemElement = null;
        var masterAudioElement = null;

        function handleClick(id) {
            if (previousDiv != null) {
                previousDiv.style.display = 'none';
            }

            if (previousMasterAudioDiv != null) {
                previousMasterAudioDiv = null;
            }

            if (previousCategoryDiv != null) {
                previousCategoryDiv.style.display = 'none';
            }

            if (previousImageDiv != null) {
                previousImageDiv.src = "";
            }

            if (masterAudioElement != null) {
                masterAudioElement = null;
            }

            if (itemElement != null) {
                itemElement = null;
            }

            var rawDatas = {!! json_encode($datas) !!};
            let data = {}
            if (draw > 0) {
                newDataLines.forEach(val => {
                    if (val.id == id) {
                        data = val;
                    }
                })
            } else {
                rawDatas.forEach(val => {
                    if (val.id == id) {
                        data = val;
                    }
                });
            }

            var div = document.getElementById('div-' + id);
            if (div) {
                div.style.display = 'block';

                if (data.category == "Listening Part A") {
                    const audioPreviewDiv = document.getElementById('audioDiv-' + id);
                    audioPreviewDiv.style.display = 'block';

                    // ...
                    previousCategoryDiv = audioPreviewDiv;
                } else if (data.category == "Listening Part B" || data.category == "Listening Part C") {
                    const masterAudioPreviewDiv = document.getElementById('masterAudioDiv-' + id);
                    const audioPreviewDiv = document.getElementById('audioDiv-' + id);
                    masterAudioPreviewDiv.style.display = 'block';
                    audioPreviewDiv.style.display = 'block';

                    // ...
                    previousMasterAudioDiv = masterAudioPreviewDiv;
                    previousCategoryDiv = audioPreviewDiv;
                } else {
                    const imagePreviewDiv = document.getElementById('imageDiv-' + id);
                    imagePreviewDiv.style.display = 'block';

                    if (data.image_question != "") {
                        let cleanedImagePath = "";
                        if (data.image_question != null) {
                            cleanedImagePath = data.image_question.replace(/['"]+/g, '');
                        }

                        const imgQuestionPreview = document.getElementById('imageQuestionPreview-' + id);
                        imgQuestionPreview.src = cleanedImagePath;
                        baguetteBox.run("#imageDiv-"+id);

                        // ...
                        previousImageDiv = imgQuestionPreview;
                    }

                    // ...
                    previousCategoryDiv = imagePreviewDiv;
                }
            }

            // ...
            previousDiv = div;
        }

        function playAudio(id) {
            const playBtn = document.getElementById('playBtn-' + id);
            const playingBtn = document.getElementById('playingBtn-' + id);
            const playedBtn = document.getElementById('playedBtn-' + id);
            const audioElements = document.getElementById('audio-' + id);
            if (audioElements) {
                audioElements.play();
                audioElements.addEventListener('play', () => {
                    playBtn.style.display = 'none';
                    playingBtn.style.display = 'block';
                    playedBtn.style.display = 'none';
                });

                audioElements.addEventListener('ended', () => {
                    playBtn.style.display = 'none';
                    playingBtn.style.display = 'none';
                    playedBtn.style.display = 'block';
                });

                var rawDatas = {!! json_encode($datas) !!};
                let data = {}
                if (draw > 0) {
                    newDataLines.forEach(val => {
                        if (val.id == id) {
                            data = val;
                        }
                    })
                } else {
                    rawDatas.forEach(val => {
                        if (val.id == id) {
                            data = val;
                        }
                    });
                }

                $.ajax({
                    url: "{{ URL::route('homeuser.update-audio-status') }}",
                    type: 'post',
                    data: {
                        _token: '{{ csrf_token() }}',
                        master_audio_id: data.master_audio_id,
                        question_id: id,
                        user_id: data.user_id,
                        schedule_id: data.schedule_id,
                    },
                    success: function(response) {
                        console.log("audio played")
                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                        console.log(xhr.responseJSON.message);
                    }
                });

                // ...
                itemElement = audioElements;
            }
        }

        function playMasterAudio(id) {
            let data = {}
            newDataLines.forEach(val => {
                if (val.master_audio_id == id) {
                    data = val;
                }
            })

            const playBtns = document.querySelectorAll(`#masterPlayBtn-${id}`);
            const playingBtns = document.querySelectorAll(`#masterPlayingBtn-${id}`);
            const playedBtns = document.querySelectorAll(`#masterPlayedBtn-${id}`);
            const audioElements = document.querySelectorAll(`#masterAudio-${id}`);
            if (audioElements.length > 0) {
                audioElements[0].play();
                audioElements[0].addEventListener('play', () => {
                    playBtns.forEach(btn => btn.style.display = 'none');
                    playingBtns.forEach(btn => btn.style.display = 'block');
                    playedBtns.forEach(btn => btn.style.display = 'none');
                });

                audioElements[0].addEventListener('ended', () => {
                    playBtns.forEach(btn => btn.style.display = 'none');
                    playingBtns.forEach(btn => btn.style.display = 'none');
                    playedBtns.forEach(btn => btn.style.display = 'block');
                });

                $.ajax({
                    url: "{{ URL::route('homeuser.update-master-audio-status') }}",
                    type: 'post',
                    data: {
                        _token: '{{ csrf_token() }}',
                        master_audio_id: data.master_audio_id,
                        question_id: data.id,
                        user_id: data.user_id,
                        schedule_id: data.schedule_id,
                    },
                    success: function(response) {
                        console.log("master audio played")
                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                        console.log(xhr.responseJSON.message);
                    }
                });

                // ...
                masterAudioElement = audioElements;
            }
        }

        function answerClick(qID, id) {
            var rawDatas = {!! json_encode($datas) !!};
            let data = {}

            if (draw > 0) {
                newDataLines.forEach(val => {
                    if (val.id == qID) {
                        data = val;
                    }
                })
            } else {
                rawDatas.forEach(val => {
                    if (val.id == qID) {
                        data = val;
                    }
                });
            }

            const btnQuestion = document.getElementById('span-question-' + data.number);

            answers = answers.filter(item => item.questionID !== Number(qID));
            answers.push({
                userID: data.user_id,
                scheduleID: data.schedule_id,
                questionID: Number(qID),
                answerID: Number(id),
            });

            $.ajax({
                url: "{{ URL::route('homeuser.update-answer-line') }}",
                type: 'post',
                data: {
                    _token: '{{ csrf_token() }}',
                    question_id: qID,
                    answer_id: id,
                    user_id: data.user_id,
                    schedule_id: data.schedule_id,
                    category: data.category,
                },
                success: function(response) {
                    if (response.message == "Exam Closed") {
                        var url = "{{ URL::route('homeuser.complete-exam') }}?user_id=" + userID + "&schedule_id=" + scheduleID;
                        var newTab = window.open(url, "_blank");
                        if (newTab) {
                            newTab.focus();
                            window.close();
                        } else {
                            alert("Pop-up blocker is enabled! Please allow pop-ups for this site.");
                        }
                    }

                    btnQuestion.innerHTML = "✔";

                    if (draw > 0) {
                        if (answers.length >= newDataLines.length) {
                            nextButton.style.display = 'block';
                            if (newData.exam_status == "Final") {
                                $("#next-btn-text").text('Complete Exam');
                            }
                        }
                    } else {
                        if (answers.length >= rawDatas.length) {
                            nextButton.style.display = 'block';
                        }
                    }
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    console.log(xhr.responseJSON.message);
                }
            });
        }

        function handleChangeType(type) {
            var examType = "";
            var examStatus = "";

            if (draw > 0) {
                examType = newData.current_type
                examStatus = newData.exam_status
            } else {
                examType = type
            }

            if (examStatus == "Final") {
                var url = "{{ URL::route('homeuser.complete-exam') }}?user_id=" + userID + "&schedule_id=" + scheduleID;
                var newTab = window.open(url, "_blank");
                if (newTab) {
                    newTab.focus();
                    window.close();
                } else {
                    alert("Pop-up blocker is enabled! Please allow pop-ups for this site.");
                }
            } else {
                $.ajax({
                    url: "{{ URL::route('homeuser.get-next-exam') }}",
                    type: 'get',
                    data: {
                        _token: '{{ csrf_token() }}',
                        user_id: userID,
                        schedule_id: scheduleID,
                        type: examType,
                    },
                    success: function(response) {
                        updateElements(response);
                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                        console.log(xhr.responseJSON.message);
                    }
                });
            }
        }

        function updateElements(response) {
            draw++;
            isComplete = false;
            answers = [];
            newData = JSON.parse(response);
            const newType = newData.current_type;
            newDataLines = newData.datas;

            $("#exam-title").text('Question Detail for '+newData.title);
            $("#question-numbers-container").empty();
            $("#question-details-container").empty();
            nextButton.style.display = 'none';

            newDataLines.forEach(val => {
                if (val.is_answered == 1) {
                    val.answer_lines.forEach(line => {
                        if (line.choosen == true) {
                            val.number = "✔";

                            answers.push({
                                userID: val.user_id,
                                scheduleID: val.schedule_id,
                                questionID: val.id,
                                answerID: line.id,
                            });
                        }
                    });
                }
            });

            if (answers.length >= newDataLines.length) {
                isComplete = true;
            }

            if (isComplete) {
                nextButton.style.display = 'block';
                if (newData.exam_status == "Final") {
                    $("#next-btn-text").text('Complete Exam');
                }
            }

            $.each(newDataLines, function(index, item) {
                let cleanedImagePath = ""
                if (item.image_question != "") {
                    cleanedImagePath = item.image_question.replace(/['"]+/g, '');
                }

                let cleanedMasterAudioPath = ""
                if (item.master_audio != "") {
                    cleanedMasterAudioPath = item.master_audio.replace(/['"]+/g, '');
                }

                let cleanedAudioPath = ""
                if (item.audio != "") {
                    cleanedAudioPath = item.audio.replace(/['"]+/g, '');
                }

                $('#question-numbers-container').append(`
                    <div class="col-3 mr-4 mb-4" style="height: 40px; width: 40px">
                        <button class="btn-question" id="btn-question-${item.number}" onclick="handleClick('${item.id}')">
                            <span class="text" id="span-question-${item.number}">${item.number}</span>
                        </button>
                    </div>
                `);

                var questionDiv = `
                    <div id="div-${item.id}" style="display: none; padding-left: 0.5cm; padding-right: 0.5cm;">
                `;

                if (cleanedMasterAudioPath != "") {
                    if (item.is_master_audio_listened) {
                        questionDiv += `
                        <div class="mb-3" id="masterAudioDiv-${item.id}" style="display: none;">
                            <button class="btn-audio" id="masterPlayBtn-${item.master_audio_id}" onclick="playMasterAudio(${item.master_audio_id})" style="display: none; position: relative; left: 50%; -ms-transform: translate(-50%, -50%); transform: translate(-50%, -50%); top: 1cm;">
                                <span class="text-container">
                                    <span class="text"><i class="bi bi-play-fill"></i> Play Conversation Audio</span>
                                </span>
                            </button>
                            <button class="btn-audio" id="masterPlayingBtn-${item.master_audio_id}" style="display: none; position: relative; left: 50%; -ms-transform: translate(-50%, -50%); transform: translate(-50%, -50%); top: 1cm;">
                                <span class="text-container">
                                    <span class="text"><i class="bi bi-play-fill"></i> Conversation Audio is playing ...</span>
                                </span>
                            </button>
                            <button class="btn-audio" id="masterPlayedBtn-${item.master_audio_id}" style="display: block; position: relative; left: 50%; -ms-transform: translate(-50%, -50%); transform: translate(-50%, -50%); top: 1cm;">
                                <span class="text-container">
                                    <span class="text"><i class="bi bi-play-fill"></i> Conversation Audio is already played</span>
                                </span>
                            </button>
                            <audio preload="auto" id="masterAudio-${item.master_audio_id}" src="${cleanedMasterAudioPath}" controls class="audio-preview col-sm-5" style="display: none;">
                        </div>
                        `
                    } else {
                        questionDiv += `
                        <div class="mb-3" id="masterAudioDiv-${item.id}" style="display: none;">
                            <button class="btn-audio" id="masterPlayBtn-${item.master_audio_id}" onclick="playMasterAudio(${item.master_audio_id})" style="display: block; position: relative; left: 50%; -ms-transform: translate(-50%, -50%); transform: translate(-50%, -50%); top: 1cm;">
                                <span class="text-container">
                                    <span class="text"><i class="bi bi-play-fill"></i> Play Conversation Audio</span>
                                </span>
                            </button>
                            <button class="btn-audio" id="masterPlayingBtn-${item.master_audio_id}" style="display: none; position: relative; left: 50%; -ms-transform: translate(-50%, -50%); transform: translate(-50%, -50%); top: 1cm;">
                                <span class="text-container">
                                    <span class="text"><i class="bi bi-play-fill"></i> Conversation Audio is playing ...</span>
                                </span>
                            </button>
                            <button class="btn-audio" id="masterPlayedBtn-${item.master_audio_id}" style="display: none; position: relative; left: 50%; -ms-transform: translate(-50%, -50%); transform: translate(-50%, -50%); top: 1cm;">
                                <span class="text-container">
                                    <span class="text"><i class="bi bi-play-fill"></i> Conversation Audio is already played</span>
                                </span>
                            </button>
                            <audio preload="auto" id="masterAudio-${item.master_audio_id}" src="${cleanedMasterAudioPath}" controls class="audio-preview col-sm-5" style="display: none;">
                        </div>
                        `
                    }
                }

                if (cleanedAudioPath != "") {
                    if (item.is_listened) {
                        questionDiv += `
                        <div class="mb-3" id="audioDiv-${item.id}" style="display: none;">
                            <button class="btn-audio" id="playBtn-${item.id}" onclick="playAudio(${item.id})" style="display: none; position: relative; left: 50%; -ms-transform: translate(-50%, -50%); transform: translate(-50%, -50%); top: 1cm;">
                                <span class="text-container">
                                    <span class="text"><i class="bi bi-play-fill"></i> Play audio</span>
                                </span>
                            </button>
                            <button class="btn-audio" id="playingBtn-${item.id}" style="display: none; position: relative; left: 50%; -ms-transform: translate(-50%, -50%); transform: translate(-50%, -50%); top: 1cm;">
                                <span class="text-container">
                                    <span class="text"><i class="bi bi-play-fill"></i> Audio is playing ...</span>
                                </span>
                            </button>
                            <button class="btn-audio" id="playedBtn-${item.id}" style="display: block; position: relative; left: 50%; -ms-transform: translate(-50%, -50%); transform: translate(-50%, -50%); top: 1cm;">
                                <span class="text-container">
                                    <span class="text"><i class="bi bi-play-fill"></i> Audio is already played</span>
                                </span>
                            </button>
                            <audio preload="auto" id="audio-${item.id}" src="${cleanedAudioPath}" controls class="audio-preview col-sm-5" style="display: none;">
                        </div>
                        `;
                    } else {
                        questionDiv += `
                        <div class="mb-3" id="audioDiv-${item.id}" style="display: none;">
                            <button class="btn-audio" id="playBtn-${item.id}" onclick="playAudio(${item.id})" style="display: block; position: relative; left: 50%; -ms-transform: translate(-50%, -50%); transform: translate(-50%, -50%); top: 1cm;">
                                <span class="text-container">
                                    <span class="text"><i class="bi bi-play-fill"></i> Play audio</span>
                                </span>
                            </button>
                            <button class="btn-audio" id="playingBtn-${item.id}" style="display: none; position: relative; left: 50%; -ms-transform: translate(-50%, -50%); transform: translate(-50%, -50%); top: 1cm;">
                                <span class="text-container">
                                    <span class="text"><i class="bi bi-play-fill"></i> Audio is playing ...</span>
                                </span>
                            </button>
                            <button class="btn-audio" id="playedBtn-${item.id}" style="display: none; position: relative; left: 50%; -ms-transform: translate(-50%, -50%); transform: translate(-50%, -50%); top: 1cm;">
                                <span class="text-container">
                                    <span class="text"><i class="bi bi-play-fill"></i> Audio is already played</span>
                                </span>
                            </button>
                            <audio preload="auto" id="audio-${item.id}" src="${cleanedAudioPath}" controls class="audio-preview col-sm-5" style="display: none;">
                        </div>
                        `;
                    }
                }

                if (cleanedImagePath != "") {
                    questionDiv += `
                        <div class="mb-3" id="imageDiv-${item.id}" style="display: none;">
                            <a href="${cleanedImagePath}" data-baguettebox>
                                <img class="img-preview img-fluid mb-3 col-sm-5" style="height: auto;" id="imageQuestionPreview-${item.id}">
                            </a>
                        </div>
                    `;
                }

                questionDiv += `
                        <div class="mb-3">
                            <h4 style="font-size: 20px;" for="title">${item.question_words}</h4>
                        </div>
                        <br>
                `;

                $.each(item.answer_lines, function(i, line) {
                    if (line.choosen == true) {
                        questionDiv += `
                            <div class="radio-input mt-2">
                                <input
                                    checked="true"
                                    type="radio"
                                    id="question-${item.id}-answer-${line.id}"
                                    value="question-${item.id}-answer-${line.id}"
                                    name="question-${item.id}"
                                    onclick="answerClick('${item.id}', '${line.id}')"
                                >
                                <label for="question-${item.id}-answer-${line.id}">${line.name}</label><br>
                            </div>
                        `;
                    } else {
                        questionDiv += `
                            <div class="radio-input mt-2">
                                <input
                                    type="radio"
                                    id="question-${item.id}-answer-${line.id}"
                                    value="question-${item.id}-answer-${line.id}"
                                    name="question-${item.id}"
                                    onclick="answerClick('${item.id}', '${line.id}')"
                                >
                                <label for="question-${item.id}-answer-${line.id}">${line.name}</label><br>
                            </div>
                        `;
                    }
                });

                questionDiv += `</div>`;
                $('#question-details-container').append(questionDiv);
            });
        }
    </script>
</body>

</html>