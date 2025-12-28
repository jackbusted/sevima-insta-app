<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
use Jubaer\Zoom\Facades\Zoom;

use App\Models\User;
use App\Models\UserZoomBoundModel;
use App\Models\StoryAudioMasterModel;
use App\Models\QuestionModel;
use App\Models\CategoryModel;
use App\Models\AnswerLineModel;
use App\Models\PaymentModel;
use App\Models\ScheduleModel;
use App\Models\ListeningDataModel;
use App\Models\ListeningAnswerLineModel;
use App\Models\StructureDataModel;
use App\Models\StructureAnswerLineModel;
use App\Models\ReadingDataModel;
use App\Models\ReadingAnswerLineModel;
use App\Models\MasterListeningWithAudioModel;
use App\Models\ListeningWithAudioQuestionModel;
use App\Models\ListeningWithAudioAnswerLineModel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Eloquent\Collection;

class HomeUserController extends Controller
{
    public function index()
    {
        try {
            $showScheduleNotice = false;
            $showIncomingNotice = false;
            $isAlreadyStarted = false;
            $showNpmNotice = false;
            $user = User::where('id', Auth::user()->id)->first();

            if (!$user->npm) {
                $showNpmNotice = true;
            }

            // check registered schedule
            $payment = PaymentModel::where('user_id', $user->id)
            ->orderBy('id', 'desc')
            ->first();

            $date = null;
            $time = null;
            $paymentStatus = "";
            if ($payment) {
                $schedule = ScheduleModel::where('id', $payment->schedule_id)
                ->where('status', 1)
                ->first();

                if ($schedule) {
                    $showScheduleNotice = true;
                    $date = $schedule->open_date;
                    $time = $schedule->exe_clock;

                    if ($payment->status == 0) {
                        $paymentStatus = "Unconfirmed";
                    }

                    if ($payment->status == 1) {
                        $paymentStatus = "Confirmed";
                    }

                    if ($payment->status == 2) {
                        $paymentStatus = "Rejected";
                    }

                    if ($payment->status == 3) {
                        $paymentStatus = "Canceled";
                    }
                }
            }

            // check incoming exam
            $today = Carbon::now('Asia/Jakarta');
            $todayPayment = PaymentModel::where('user_id', $user->id)
            ->where('status', 1)
            ->orderBy('id', 'desc')
            ->first();

            $incomingDate = null;
            $incomingTime = null;
            $joinUrl = null;
            if ($todayPayment) {
                $todaySchedule = ScheduleModel::where('id', $todayPayment->schedule_id)
                ->whereIn('status', [1, 2])
                ->first();

                if ($todaySchedule && $todaySchedule->status == 1) {
                    $hourValidation = $this->checkCurrentSchedule($todaySchedule->id);
                    if ($hourValidation > 0) {
                        $showIncomingNotice = true;
                        $incomingDate = $todaySchedule->open_date;
                        $incomingTime = $todaySchedule->exe_clock;
                    }
                } else if ($todaySchedule && $todaySchedule->status == 2) {
                    $examStartDate = Carbon::createFromFormat('Y-m-d H:i:s', $todaySchedule->open_date);
                    if ($today->isSameDay($examStartDate)) {
                        $hourValidation = $this->checkCurrentSchedule($todaySchedule->id);
                        if ($hourValidation == 1) {
                            $showIncomingNotice = true;
                            $incomingDate = $todaySchedule->open_date;
                            $incomingTime = $todaySchedule->exe_clock;
                            $joinUrl = $todaySchedule->join_url;
                        } else if ($hourValidation == 2) {
                            $showIncomingNotice = true;
                            $isAlreadyStarted = true;
                            $incomingDate = $todaySchedule->open_date;
                            $incomingTime = $todaySchedule->exe_clock;
                            $joinUrl = $todaySchedule->join_url;
                        }
                    }
                }
            }

            // ...
            return view('homeuser.index', [
                'title' => 'Dashboard',
                'active' => 'homeuser',
                'showScheduleNotice' => $showScheduleNotice,
                'date' => $date,
                'time' => $time,
                'paymentStatus' => $paymentStatus,
                'showIncomingNotice' => $showIncomingNotice,
                'isAlreadyStarted' => $isAlreadyStarted,
                'incomingDate' => $incomingDate,
                'incomingTime' => $incomingTime,
                'joinUrl' => $joinUrl,
                'showNpmNotice' => $showNpmNotice,
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function checkCurrentSchedule($id)
    {
        try {
            $schedule = ScheduleModel::where('id', $id)->first();
            $today = Carbon::now('Asia/Jakarta');

            $examStartDate = Carbon::createFromFormat('Y-m-d H:i:s', $schedule->open_date);
            if ($today->isSameDay($examStartDate)) {
                list($hour, $minute, $second) = explode(':', $schedule->exe_clock);
                $examStartTime = Carbon::createFromTime($hour, $minute, $second);
                $hoursBefore = $examStartTime->copy()->subHours(6);
                $now = Carbon::now('Asia/Jakarta');

                if ($now->greaterThanOrEqualTo($hoursBefore)) {
                    if ($now->greaterThanOrEqualTo($examStartTime)) {
                        return 2;
                    }
                    return 1;
                }
            }

            return 0;
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function checkExamSchedule()
    {
        try {
            $user = User::where('id', Auth::user()->id)->first();
            $payments = PaymentModel::where('user_id', $user->id)->get();
            $isActive = false;
            $scheduleID = 0;

            if (count($payments) > 0) {
                $today = Carbon::now('Asia/Jakarta');
                foreach($payments as $data) {
                    if ($data->status == 1) {
                        $checkSchedule = ScheduleModel::where('id', $data->schedule_id)->first();
                        if ($checkSchedule) {
                            $examStartDate = Carbon::createFromFormat('Y-m-d H:i:s', $checkSchedule->open_date);

                            if ($today->isSameDay($examStartDate)) {
                                list($hour, $minute, $second) = explode(':', $checkSchedule->exe_clock);
                                $examStartTime = Carbon::createFromTime($hour, $minute, $second);
                                $examEndTime = $examStartTime->copy()->addHours(3);
                                $now = Carbon::now('Asia/Jakarta')->format('H:i:s');

                                if ($now >= $examStartTime->format('H:i:s') && $now <= $examEndTime->format('H:i:s')) {
                                    $scheduleID = $data->schedule_id;
                                    $isActive = true;
                                    break;
                                }

                                // kebutuhan development
                                /* if ($now >= $examStartTime->format('H:i:s')) {
                                    $scheduleID = $data->schedule_id;
                                    $isActive = true;
                                    break;
                                } */
                            }
                        }
                    }
                }
            }

            if ($isActive) {
                return $this->getUserQuestions($scheduleID);
            } else {
                return view('notice', [
                    'title' => "Empty schedule",
                    'message' => "There is no active exam schedule",
                ]);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function getUserQuestions($scheduleID)
    {
        try {
            $user = User::where('id', Auth::user()->id)->first();
            $schedule = ScheduleModel::where('id', $scheduleID)->first();
            $payment = PaymentModel::where('user_id', $user->id)
            ->where('schedule_id', $schedule->id)
            ->whereIn('status', [1, 4])
            ->first();

            if ($payment->status == 1) {
                $listeningData = ListeningDataModel::where('schedule_id', $schedule->id)
                ->where('user_id', $user->id)
                ->orderByRaw('RANDOM()')
                ->get();

                $questionPack = [];
                $number = 0;
                foreach ($listeningData as $data) {
                    $number++;
                    $answerLinesPack = [];
                    $isAnswered = 0;
                    $question = QuestionModel::where('id', $data->question_id)->first();
                    $category = CategoryModel::where('id', $question->category_id)->first();
                    $answerLines = ListeningAnswerLineModel::where('schedule_id', $schedule->id)
                    ->where('question_id', $question->id)
                    ->where('user_id', $user->id)
                    ->orderByRaw('RANDOM()')
                    ->get();

                    foreach ($answerLines as $lines) {
                        array_push($answerLinesPack, array(
                            'id' => $lines->id,
                            'name' => $lines->name,
                            'right_answer' => $lines->right_answer,
                            'choosen' => $lines->is_answered,
                        ));

                        if ($lines->is_answered == true) {
                            $isAnswered = 1;
                        }
                    }

                    array_push($questionPack, array(
                        'number' => $number,
                        'master_audio_id' => 0,
                        'id' => $question->id,
                        'category' => $category->name_ctg,
                        'title' => $question->title,
                        'audio' => $question->audio,
                        'image_question' => "",
                        'question_words' => "",
                        'answer_lines' => $answerLinesPack,
                        'is_answered' => $isAnswered,
                        'is_listened' => $data->is_listened,
                        'user_id' => $user->id,
                        'schedule_id' => $schedule->id,
                    ));
                }

                return view('homeuser.start-test.index', [
                    'title' => "Listening Test",
                    'type' => "Listening Part A",
                    'datas' => $questionPack,
                    'user_id' => $user->id,
                    'schedule_id' => $schedule->id,
                    'meeting' => $schedule->join_url,
                ]);
            } else {
                return view('notice', [
                    'title' => 'Exam Completed',
                    'message' => "Your exam is already completed",
                ]);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function changeExamType(Request $request)
    {
        try {
            $previousType = $request->input('type');
            $userID = $request->input('user_id');
            $scheduleID = $request->input('schedule_id');

            $currentTitle = "";
            $currentType = "";
            $examStatus = "";
            $questionPack = [];
            $number = 0;

            // get listening part B
            if ($previousType == "Listening Part A") {
                $masterListeningAudio = MasterListeningWithAudioModel::where('schedule_id', $scheduleID)
                ->where('user_id', $userID)
                ->where('category_id', 4)
                ->get();

                foreach ($masterListeningAudio as $model) {
                    $storyAudio = StoryAudioMasterModel::where('id', $model->story_audio_id)->first();
                    $questionData = ListeningWithAudioQuestionModel::where('master_audio_listening_id', $model->id)->orderBy('id', 'asc')->get();

                    foreach ($questionData as $data) {
                        $number++;
                        $answerLinesPack = [];
                        $isAnswered = 0;
                        $question = QuestionModel::where('id', $data->question_id)->first();
                        $category = CategoryModel::where('id', $question->category_id)->first();
                        $answerLines = ListeningWithAudioAnswerLineModel::where('listening_question_id', $data->id)->orderByRaw('RANDOM()')->get();

                        foreach ($answerLines as $lines) {
                            array_push($answerLinesPack, array(
                                'id' => $lines->id,
                                'name' => $lines->name,
                                'right_answer' => $lines->right_answer,
                                'choosen' => $lines->is_answered,
                            ));

                            if ($lines->is_answered == true) {
                                $isAnswered = 1;
                            }
                        }

                        array_push($questionPack, array(
                            'number' => $number,
                            'master_audio_id' => $model->id,
                            'id' => $question->id,
                            'category' => $category->name_ctg,
                            'title' => $question->title,
                            'master_audio' => asset($storyAudio->audio_file),
                            'audio' => asset($question->audio),
                            'image_question' => "",
                            'question_words' => "",
                            'answer_lines' => $answerLinesPack,
                            'is_answered' => $isAnswered,
                            'is_listened' => $data->is_listened,
                            'is_master_audio_listened' => $model->is_listened,
                            'user_id' => $userID,
                            'schedule_id' => $scheduleID,
                        ));
                    }
                }

                $currentTitle = "Listening Part B Test";
                $currentType = "Listening Part B";
            }

            // get listening part C
            if ($previousType == "Listening Part B") {
                $masterListeningAudio = MasterListeningWithAudioModel::where('schedule_id', $scheduleID)
                ->where('user_id', $userID)
                ->where('category_id', 5)
                ->orderBy('id', 'asc')
                ->get();

                foreach ($masterListeningAudio as $model) {
                    $storyAudio = StoryAudioMasterModel::where('id', $model->story_audio_id)->first();
                    $questionData = ListeningWithAudioQuestionModel::where('master_audio_listening_id', $model->id)->orderBy('id', 'asc')->get();

                    foreach ($questionData as $data) {
                        $number++;
                        $answerLinesPack = [];
                        $isAnswered = 0;
                        $question = QuestionModel::where('id', $data->question_id)->first();
                        $category = CategoryModel::where('id', $question->category_id)->first();
                        $answerLines = ListeningWithAudioAnswerLineModel::where('listening_question_id', $data->id)->orderByRaw('RANDOM()')->get();

                        foreach ($answerLines as $lines) {
                            array_push($answerLinesPack, array(
                                'id' => $lines->id,
                                'name' => $lines->name,
                                'right_answer' => $lines->right_answer,
                                'choosen' => $lines->is_answered,
                            ));

                            if ($lines->is_answered == true) {
                                $isAnswered = 1;
                            }
                        }

                        array_push($questionPack, array(
                            'number' => $number,
                            'master_audio_id' => $model->id,
                            'id' => $question->id,
                            'category' => $category->name_ctg,
                            'title' => $question->title,
                            'master_audio' => asset($storyAudio->audio_file),
                            'audio' => asset($question->audio),
                            'image_question' => "",
                            'question_words' => "",
                            'answer_lines' => $answerLinesPack,
                            'is_answered' => $isAnswered,
                            'is_listened' => $data->is_listened,
                            'is_master_audio_listened' => $model->is_listened,
                            'user_id' => $userID,
                            'schedule_id' => $scheduleID,
                        ));
                    }
                }

                $currentTitle = "Listening Part C Test";
                $currentType = "Listening Part C";
            }

            // get structure section
            if ($previousType == "Listening Part C") {
                $structureData = StructureDataModel::where('schedule_id', $scheduleID)
                ->where('user_id', $userID)
                ->orderByRaw('RANDOM()')
                ->get();

                foreach ($structureData as $data) {
                    $number++;
                    $answerLinesPack = [];
                    $isAnswered = 0;
                    $questionWords = "";

                    $question = QuestionModel::where('id', $data->question_id)->first();
                    $category = CategoryModel::where('id', $question->category_id)->first();
                    $answerLines = StructureAnswerLineModel::where('schedule_id', $scheduleID)
                    ->where('question_id', $question->id)
                    ->where('user_id', $userID)
                    ->orderByRaw('RANDOM()')
                    ->get();

                    foreach ($answerLines as $lines) {
                        array_push($answerLinesPack, array(
                            'id' => $lines->id,
                            'name' => $lines->name,
                            'right_answer' => $lines->right_answer,
                            'choosen' => $lines->is_answered,
                        ));
    
                        if ($lines->is_answered == true) {
                            $isAnswered = 1;
                        }
                    }

                    if ($question->question_words != null) {
                        $questionWords = $question->question_words;
                    }

                    array_push($questionPack, array(
                        'number' => $number,
                        'master_audio_id' => 0,
                        'id' => $question->id,
                        'category' => $category->name_ctg,
                        'title' => $question->title,
                        'master_audio' => "",
                        'audio' => "",
                        'image_question' => asset($question->image),
                        'question_words' => $questionWords,
                        'answer_lines' => $answerLinesPack,
                        'is_answered' => $isAnswered,
                        'is_listened' => false,
                        'is_master_audio_listened' => false,
                        'user_id' => $userID,
                        'schedule_id' => $scheduleID,
                    ));
                }

                $currentTitle = "Structure Test";
                $currentType = "Structure";
            }

            // get reading section
            if ($previousType == "Structure") {
                $readingData = ReadingDataModel::where('schedule_id', $scheduleID)
                ->where('user_id', $userID)
                ->orderByRaw('RANDOM()')
                ->get();

                foreach ($readingData as $data) {
                    $number++;
                    $answerLinesPack = [];
                    $isAnswered = 0;
                    $questionWords = "";

                    $question = QuestionModel::where('id', $data->question_id)->first();
                    $category = CategoryModel::where('id', $question->category_id)->first();
                    $answerLines = ReadingAnswerLineModel::where('schedule_id', $scheduleID)
                    ->where('question_id', $question->id)
                    ->where('user_id', $userID)
                    ->orderByRaw('RANDOM()')
                    ->get();

                    foreach ($answerLines as $lines) {
                        array_push($answerLinesPack, array(
                            'id' => $lines->id,
                            'name' => $lines->name,
                            'right_answer' => $lines->right_answer,
                            'choosen' => $lines->is_answered,
                        ));

                        if ($lines->is_answered == true) {
                            $isAnswered = 1;
                        }
                    }

                    if ($question->question_words != null) {
                        $questionWords = $question->question_words;
                    }

                    array_push($questionPack, array(
                        'number' => $number,
                        'master_audio_id' => 0,
                        'id' => $question->id,
                        'category' => $category->name_ctg,
                        'title' => $question->title,
                        'master_audio' => "",
                        'audio' => "",
                        'image_question' => asset($question->image),
                        'question_words' => $questionWords,
                        'answer_lines' => $answerLinesPack,
                        'is_answered' => $isAnswered,
                        'is_listened' => false,
                        'is_master_audio_listened' => false,
                        'user_id' => $userID,
                        'schedule_id' => $scheduleID,
                    ));
                }

                $currentTitle = "Reading Test";
                $currentType = "Reading";
                $examStatus = "Final";
            }

            // ...
            return json_encode(
                [
                    'message' => 'success get next exam',
                    'title' => $currentTitle,
                    'previous_type' => $previousType,
                    'current_type' => $currentType,
                    'datas' => $questionPack,
                    'user_id' => $userID,
                    'schedule_id' => $scheduleID,
                    'exam_status' => $examStatus,
                ]
            );
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function completeExam(Request $request)
    {
        try {
            DB::beginTransaction();

            $userID = $request->input('user_id');
            $scheduleID = $request->input('schedule_id');

            $payment = PaymentModel::where('user_id', $userID)
            ->where('schedule_id', $scheduleID)
            ->where('status', 1)
            ->first();

            if ($payment) {
                $payment->status = 4;
                $payment->updated_at = Carbon::now('Asia/Jakarta');
                $payment->updated_by = Auth::user()->id;
                $payment->update();
            }

            // ...
            DB::commit();
            return view('notice', [
                'title' => 'Exam Completed',
                'message' => "Thank you for completing ITATS's TEFL Exam",
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function updateMasterListeningAudio(Request $request)
    {
        try {
            DB::beginTransaction();

            $masterAudioID = $request->input('master_audio_id');
            $scheduleID = $request->input('schedule_id');
            $questionID = $request->input('question_id');
            $userID = $request->input('user_id');

            $masterAudioQuestion = MasterListeningWithAudioModel::where('id', $masterAudioID)->first();
            $masterAudioQuestion->is_listened = true;
            $masterAudioQuestion->update();

            // ...
            DB::commit();
            return response()->json(['message' => 'master audio status updated'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function updateListeningAudio(Request $request)
    {
        try {
            DB::beginTransaction();

            $masterAudioID = $request->input('master_audio_id');
            $scheduleID = $request->input('schedule_id');
            $questionID = $request->input('question_id');
            $userID = $request->input('user_id');

            if ($masterAudioID > 0) {
                $questionModel = ListeningWithAudioQuestionModel::where('master_audio_listening_id', $masterAudioID)
                ->where('question_id', $questionID)
                ->first();

                $questionModel->is_listened = true;
                $questionModel->update();

                // ...
            } else {
                $listeningQuestion = ListeningDataModel::where('schedule_id', $scheduleID)
                ->where('question_id', $questionID)
                ->where('user_id', $userID)
                ->first();
    
                $listeningQuestion->is_listened = true;
                $listeningQuestion->update();

                // ...
            }

            // ...
            DB::commit();
            return response()->json(['message' => 'listening audio status updated'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function updateUserAnswerLines(Request $request)
    {
        try {
            DB::beginTransaction();

            $scheduleID = $request->input('schedule_id');
            $questionID = $request->input('question_id');
            $answerID = $request->input('answer_id');
            $userID = $request->input('user_id');
            $category = $request->input('category');

            $checkStatus = $this->checkScheduleStatus($scheduleID);
            if ($checkStatus == 1) {
                DB::rollBack();
                return response()->json(['message' => "Exam Closed"], 200);
            }

            switch ($category) {
                case 'Listening Part A':
                    $previousListeningAnswerLines = ListeningAnswerLineModel::where('schedule_id', $scheduleID)
                    ->where('question_id', $questionID)
                    ->where('user_id', $userID)
                    ->get();

                    foreach ($previousListeningAnswerLines as $line) {
                        $line->is_answered = false;
                        $line->update();
                    }

                    $listeningAnswerLine = ListeningAnswerLineModel::where('id', $answerID)->first();
                    $listeningAnswerLine->is_answered = true;
                    $listeningAnswerLine->update();

                    // ...
                    break;
                case 'Listening Part B':
                    $masterAudioData = MasterListeningWithAudioModel::where('schedule_id', $scheduleID)
                    ->where('user_id', $userID)
                    ->get();

                    foreach($masterAudioData as $master) {
                        $questionData = ListeningWithAudioQuestionModel::where('master_audio_listening_id', $master->id)
                        ->where('question_id', $questionID)
                        ->first();

                        if ($questionData) {
                            $previousReadingAnswerLines = ListeningWithAudioAnswerLineModel::where('listening_question_id', $questionData->id)->get();
                            foreach($previousReadingAnswerLines as $line) {
                                $line->is_answered = false;
                                $line->update();
                            }

                            $answerLine = ListeningWithAudioAnswerLineModel::where('id', $answerID)->first();
                            $answerLine->is_answered = true;
                            $answerLine->update();
                        }
                    }

                    // ...
                    break;
                case 'Listening Part C':
                    $masterAudioData = MasterListeningWithAudioModel::where('schedule_id', $scheduleID)
                    ->where('user_id', $userID)
                    ->get();

                    foreach($masterAudioData as $master) {
                        $questionData = ListeningWithAudioQuestionModel::where('master_audio_listening_id', $master->id)
                        ->where('question_id', $questionID)
                        ->first();

                        if ($questionData) {
                            $previousReadingAnswerLines = ListeningWithAudioAnswerLineModel::where('listening_question_id', $questionData->id)->get();
                            foreach($previousReadingAnswerLines as $line) {
                                $line->is_answered = false;
                                $line->update();
                            }

                            $answerLine = ListeningWithAudioAnswerLineModel::where('id', $answerID)->first();
                            $answerLine->is_answered = true;
                            $answerLine->update();
                        }
                    }

                    // ...
                    break;
                case 'Structure':
                    $previousStructureAnswerLines = StructureAnswerLineModel::where('schedule_id', $scheduleID)
                    ->where('question_id', $questionID)
                    ->where('user_id', $userID)
                    ->get();

                    foreach ($previousStructureAnswerLines as $line) {
                        $line->is_answered = false;
                        $line->update();
                    }

                    $structureAnswerLine = StructureAnswerLineModel::where('id', $answerID)->first();
                    $structureAnswerLine->is_answered = true;
                    $structureAnswerLine->update();

                    // ...
                    break;
                case 'Reading':
                    $previousReadingAnswerLines = ReadingAnswerLineModel::where('schedule_id', $scheduleID)
                    ->where('question_id', $questionID)
                    ->where('user_id', $userID)
                    ->get();

                    foreach ($previousReadingAnswerLines as $line) {
                        $line->is_answered = false;
                        $line->update();
                    }

                    $readingAnswerLine = ReadingAnswerLineModel::where('id', $answerID)->first();
                    $readingAnswerLine->is_answered = true;
                    $readingAnswerLine->update();

                    // ...
                    break;
                
                default:
                    # code...
                    break;
            }

            // ...
            DB::commit();
            return response()->json(['message' => 'answer line status updated', 'category' => $category], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    private function checkScheduleStatus($scheduleID)
    {
        try {
            $currentSchedule = ScheduleModel::find($scheduleID);

            if ($currentSchedule && $currentSchedule->status == 3) {
                return 1;
            }

            return 0;
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}