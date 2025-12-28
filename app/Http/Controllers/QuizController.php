<?php

namespace App\Http\Controllers;
use Carbon\Carbon;

use App\Models\User;
use App\Models\ScheduleModel;
use App\Models\PaymentModel;
use App\Models\StoryAudioMasterModel;
use App\Models\QuestionGroupModel;
use App\Models\QuestionModel;
use App\Models\AnswerLineModel;
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

class QuizController extends Controller
{
    public function viewSchedule()
    {
        try {
            $schedules = ScheduleModel::get();

            $data = [];
            $examClasses = [];
            $executions = [];
            $examStatuses = [];
            foreach ($schedules as $s) {
                $unconfirmedPayments = PaymentModel::where('schedule_id', $s->id)->where('status', 0)->get();
                $confirmedPayments = PaymentModel::where('schedule_id', $s->id)->whereIn('status', [1, 4])->get();

                $status = "";
                if ($s->status == 0) {
                    $status = "Ready To Open";
                } else if ($s->status == 1) {
                    $status = "Not Started Yet";
                } else if ($s->status == 2) {
                    $status = "Already Started";
                } else {
                    $status = "Done";
                }

                array_push($data, array(
                    'id' => $s->id,
                    'schedule_name' => $s->name,
                    'class_test' => $s->class_test,
                    'execution' => $s->execution,
                    'open_date' => $s->open_date,
                    'exe_clock' => $s->exe_clock,
                    'unconfirmed_capacity' => count($unconfirmedPayments),
                    'confirmed_capacity' => count($confirmedPayments) . " / 30",
                    'status' => $status
                ));
            }

            $examClasses = ['Kelas A', 'Kelas B', 'Kelas C'];
            $executions = ['Pagi', 'Siang', 'Malam'];
            $examStatuses = ['Ready To Open', 'Not Started Yet', 'Already Started', 'Done'];

            return view('admin-manage.create-schedule.index', [
                'title' => 'Exam Schedule',
                'active' => 'admin-manage/create-schedule',
                'datas' => $data,
                'examClasses' => $examClasses,
                'executions' => $executions,
                'examStatuses' => $examStatuses,
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function getOption()
    {
        try {
            $classes = ['Kelas A', 'Kelas B', 'Kelas C'];
            $executions = ['Pagi', 'Siang', 'Malam'];

            return view('admin-manage.create-schedule.create', [
                'title' => 'Create New Exam Schedule',
                'active' => 'admin-manage/create-schedule/create',
                'classes' => $classes,
                'executions' => $executions
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function saveSchedule(Request $request)
    {
        try {
            DB::beginTransaction();

            // check existing schedule
            $existingSchedule = ScheduleModel::whereRaw('LOWER(name) = ?', strtolower($request->input('schedule_name')))
            ->where('class_test', $request->input('class_name'))
            ->where('execution', $request->input('execution_name'))
            ->where('open_date', $request->input('date'))
            ->first();

            if ($existingSchedule != null) {
                DB::rollBack();
                return response()->json(['message' => 'This Schedule is already exists'], 400);
            }

            $schedule = new ScheduleModel();
            $schedule->name = $request->input('schedule_name');
            $schedule->class_test = $request->input('class_name');
            $schedule->execution = $request->input('execution_name');
            $schedule->open_date = $request->input('date');
            $schedule->exe_clock = $request->input('time') . ":00";
            $schedule->created_by = Auth::user()->id;
            $schedule->save();

            DB::commit();
            return response()->json(['message' => 'New Test Schedule Created'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function getEditSchedule($id)
    {
        try {
            $schedule = ScheduleModel::where('id', $id)->first();
            $formattedDate = \Carbon\Carbon::parse($schedule->open_date)->format('Y-m-d');
            $formattedTime = \Carbon\Carbon::parse($schedule->exe_clock)->format('H:i');

            $classes = ['Kelas A', 'Kelas B', 'Kelas C'];
            $executions = ['Pagi', 'Siang', 'Malam'];

            // ...
            return view('admin-manage.create-schedule.create', [
                'title' => 'Edit Exam Schedule',
                'active' => 'admin-manage/create-schedule/create',
                'id' => $id,
                'schedule_name' => $schedule->name,
                'selected_class' => $schedule->class_test,
                'selected_execution' => $schedule->execution,
                'date' => $formattedDate,
                'time' => $formattedTime,
                'classes' => $classes,
                'executions' => $executions
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function updateSchedule(Request $request)
    {
        try {
            DB::beginTransaction();

            $id = $request->input('schedule_id');

            // check existing schedule
            $existingSchedule = ScheduleModel::whereRaw('LOWER(name) = ?', strtolower($request->input('schedule_name')))
            ->where('class_test', $request->input('class_name'))
            ->where('execution', $request->input('execution_name'))
            ->where('open_date', $request->input('date'))
            ->first();

            if ($existingSchedule != null && $existingSchedule->id != $id) {
                DB::rollBack();
                return response()->json(['message' => 'This Schedule is already exists'], 400);
            }

            $schedule = ScheduleModel::where('id', $id)->first();

            if ($schedule->status == 2) {
                DB::rollBack();
                return response()->json(['message' => 'This Schedule is already started'], 400);
            }

            if ($schedule->status == 3) {
                DB::rollBack();
                return response()->json(['message' => 'This Schedule is already done'], 400);
            }

            $schedule->name = $request->input('schedule_name');
            $schedule->class_test = $request->input('class_name');
            $schedule->execution = $request->input('execution_name');
            $schedule->open_date = $request->input('date');
            $schedule->exe_clock = $request->input('time') . ":00";
            $schedule->updated_at = Carbon::now('Asia/Jakarta');
            $schedule->updated_by = Auth::user()->id;
            $schedule->update();

            // ...
            DB::commit();
            return response()->json(['message' => 'Exam Schedule Updated'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function deleteSchedule($id)
    {
        try {
            DB::beginTransaction();

            $schedule = ScheduleModel::where('id', $id)->first();
            $payments = PaymentModel::where('schedule_id', $schedule->id)->get();

            if ($schedule->status == 2) {
                DB::rollBack();
                return response()->json(['message' => 'This Schedule is already started'], 400);
            }

            if ($schedule->status == 3) {
                DB::rollBack();
                return response()->json(['message' => 'This Schedule is already done'], 400);
            }

            $schedule->deleted_at = Carbon::now('Asia/Jakarta');
            $schedule->deleted_by = Auth::user()->id;
            $schedule->update();

            foreach ($payments as $p) {
                $p->update(['status' => 3, 'deleted_at' => Carbon::now('Asia/Jakarta'), 'deleted_by' => Auth::user()->id]);
            }

            // ...
            DB::commit();
            return response()->json(['message' => 'All participants are canceled'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function openTestRegistration($id)
    {
        try {
            DB::beginTransaction();

            $schedule = ScheduleModel::where('id', $id)->first();

            if ($schedule->status == 2) {
                DB::rollBack();
                return response()->json(['message' => 'This Schedule is already started'], 400);
            }

            if ($schedule->status == 3) {
                DB::rollBack();
                return response()->json(['message' => 'This Schedule is already done'], 400);
            }

            $schedule->status = 1;
            $schedule->updated_at = Carbon::now('Asia/Jakarta');
            $schedule->updated_by = Auth::user()->id;
            $schedule->update();

            // ...
            DB::commit();
            return response()->json(['message' => 'This schedule is open registration'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function executeTestSchedule($id)
    {
        try {
            DB::beginTransaction();

            $schedule = ScheduleModel::where('id', $id)->first();
            $payments = PaymentModel::where('schedule_id', $schedule->id)->where('status', 1)->get();

            if ($schedule->status == 2) {
                DB::rollBack();
                return response()->json(['message' => 'This Schedule is already started'], 400);
            }

            if ($schedule->status == 3) {
                DB::rollBack();
                return response()->json(['message' => 'This Schedule is already closed'], 400);
            }

            // check if in range of 6 hours
            $hourValidation = $this->checkCurrentSchedule($schedule->id);
            if ($hourValidation == 0) {
                DB::rollBack();
                return response()->json(['message' => 'The exam execution time is at least 6 hours before the exam starts'], 400);
            }

            if ($hourValidation == 2) {
                DB::rollBack();
                return response()->json(['message' => 'This exam schedule is expired'], 400);
            }

            // check unconfirmed participants
            $ucfPayments = PaymentModel::where('schedule_id', $schedule->id)->where('status', 0)->get();
            if (count($ucfPayments) > 0) {
                DB::rollBack();
                return response()->json(['message' => 'Please confirm all registrations'], 400);
            }

            $questionGroups = QuestionGroupModel::where('active', true)->get();
            if (count($questionGroups) == 0) {
                DB::rollBack();
                return response()->json(['message' => 'Please select at least 1 question group'], 400);
            }

            $countListeningPartA = 0;
            $countListeningPartB = 0;
            $countListeningPartC = 0;
            $countStructure = 0;
            $countReading = 0;
            $questionIDs = [];

            foreach($questionGroups as $group) {
                $listeningPartA = QuestionModel::where('category_id', 1)
                ->where('group_id', $group->id)
                ->get();

                foreach ($listeningPartA as $line) {
                    $countListeningPartA++;
                }

                $listeningPartB = QuestionModel::where("category_id", 4)
                ->where('group_id', $group->id)
                ->distinct()
                ->pluck('story_audio_id')
                ->toArray();

                foreach ($listeningPartB as $line) {
                    $countListeningPartB++;
                }

                $listeningPartC = QuestionModel::where("category_id", 5)
                ->where('group_id', $group->id)
                ->distinct()
                ->pluck('story_audio_id')
                ->toArray();

                foreach ($listeningPartC as $line) {
                    $countListeningPartC++;
                }

                $structureQuestions = QuestionModel::where('category_id', 2)
                ->where('group_id', $group->id)
                ->get();

                foreach ($structureQuestions as $line) {
                    $countStructure++;
                }

                $readingQuestions = QuestionModel::where('category_id', 3)
                ->where('group_id', $group->id)
                ->get();

                foreach ($readingQuestions as $line) {
                    $countReading++;
                }

                $questionIDs[] = $group->id;
            }

            $questionIDs = array_unique($questionIDs);

            if ($countListeningPartA <= 50) {
                DB::rollBack();
                return response()->json(['message' => 'More than 50 questions are needed in the Listening Part A category'], 400);
            }

            if ($countListeningPartB <= 2) {
                DB::rollBack();
                return response()->json(['message' => 'More than 2 questions are needed in the Listening Part B category'], 400);
            }

            if ($countListeningPartC <= 2) {
                DB::rollBack();
                return response()->json(['message' => 'More than 2 questions are needed in the Listening Part C category'], 400);
            }

            if ($countStructure <= 50) {
                DB::rollBack();
                return response()->json(['message' => 'More than 50 questions are needed in the Structure category'], 400);
            }

            if ($countReading <= 50) {
                DB::rollBack();
                return response()->json(['message' => 'More than 50 questions are needed in the Reading category'], 400);
            }

            $userIDs = [];
            foreach ($payments as $p) {
                $userIDs[] = $p->user_id;
            }
            $users = User::whereIn('id', $userIDs)->get();

            $dataQuestionListening = QuestionModel::where("category_id", 1)
            ->whereIn('group_id', $questionIDs)
            ->orderByRaw('RANDOM()')
            ->get();

            $dataQuestionStructure = QuestionModel::where("category_id", 2)
            ->whereIn('group_id', $questionIDs)
            ->orderByRaw('RANDOM()')
            ->get();

            $dataQuestionReading = QuestionModel::where("category_id", 3)
            ->whereIn('group_id', $questionIDs)
            ->orderByRaw('RANDOM()')
            ->get();

            // get part B audio IDs
            $partB_IDs = QuestionModel::where("category_id", 4)
            ->whereIn('group_id', $questionIDs)
            ->distinct()
            ->pluck('story_audio_id')
            ->toArray();
            $dataQuestionListeningPartB = StoryAudioMasterModel::whereIn('id', $partB_IDs)->orderByRaw('RANDOM()')->get();

            // get part C audio IDs
            $partC_IDs = QuestionModel::where("category_id", 5)
            ->whereIn('group_id', $questionIDs)
            ->distinct()
            ->pluck('story_audio_id')
            ->toArray();
            $dataQuestionListeningPartC = StoryAudioMasterModel::whereIn('id', $partC_IDs)->orderByRaw('RANDOM()')->get();

            foreach ($users as $model) {
                // generate listening questions
                $dataQuestionListeningIds = [];
                for ($i = 1; $i <= 10; $i++) {
                    $generate = new ListeningDataModel;
                    $generate->user_id = $model->id;
                    $generate->user_name = $model->name;

                    $maxCommonCount = ceil(0.3 * 10);
                    do {
                        $selectedQuestionId = $dataQuestionListening->random()->id;

                    } while (in_array($selectedQuestionId, $dataQuestionListeningIds));

                    $generate->schedule_id = $schedule->id;
                    $generate->question_id = $selectedQuestionId;
                    $generate->title = $dataQuestionListening->where('id', $selectedQuestionId)->first()->title;
                    $generate->save();

                    $dataAnswer = AnswerLineModel::all()->whereIn("question_id", $generate->question_id);
                    foreach ($dataAnswer as $answer) {
                        $answerLine = new ListeningAnswerLineModel;
                        $answerLine->schedule_id = $schedule->id;
                        $answerLine->question_id = $answer->question_id;
                        $answerLine->name = $answer->name;
                        $answerLine->user_id = $model->id;
                        $answerLine->user_name = $model->name;
                        $answerLine->right_answer = $answer->right_answer;
                        $answerLine->save();
                    }

                    $dataQuestionListeningIds[] = $selectedQuestionId;
                }

                // generate structure questions
                $dataQuestionStructureIds = [];
                for ($i = 1; $i <= 10; $i++) {
                    $generate = new StructureDataModel;
                    $generate->user_id = $model->id;
                    $generate->user_name = $model->name;

                    $maxCommonCount = ceil(0.3 * 10);
                    do {
                        $selectedQuestionId = $dataQuestionStructure->random()->id;

                    } while (in_array($selectedQuestionId, $dataQuestionStructureIds));

                    $generate->schedule_id = $schedule->id;
                    $generate->question_id = $selectedQuestionId;
                    $generate->title = $dataQuestionStructure->where('id', $selectedQuestionId)->first()->title;
                    $generate->save();

                    $dataAnswer = AnswerLineModel::all()->whereIn("question_id", $generate->question_id);
                    foreach ($dataAnswer as $answer) {
                        $answerLine = new StructureAnswerLineModel;
                        $answerLine->schedule_id = $schedule->id;
                        $answerLine->question_id = $answer->question_id;
                        $answerLine->name = $answer->name;
                        $answerLine->user_id = $model->id;
                        $answerLine->user_name = $model->name;
                        $answerLine->right_answer = $answer->right_answer;
                        $answerLine->save();
                    }

                    $dataQuestionStructureIds[] = $selectedQuestionId;
                }

                // generate reading questions
                $dataQuestionReadingIds = [];
                for ($i = 1; $i <= 10; $i++) {
                    $generate = new ReadingDataModel;
                    $generate->user_id = $model->id;
                    $generate->user_name = $model->name;

                    $maxCommonCount = ceil(0.3 * 10);
                    do {
                        $selectedQuestionId = $dataQuestionReading->random()->id;

                    } while (in_array($selectedQuestionId, $dataQuestionReadingIds));

                    $generate->schedule_id = $schedule->id;
                    $generate->question_id = $selectedQuestionId;
                    $generate->title = $dataQuestionReading->where('id', $selectedQuestionId)->first()->title;
                    $generate->save();

                    $dataAnswer = AnswerLineModel::all()->whereIn("question_id", $generate->question_id);
                    foreach ($dataAnswer as $answer) {
                        $answerLine = new ReadingAnswerLineModel;
                        $answerLine->schedule_id = $schedule->id;
                        $answerLine->question_id = $answer->question_id;
                        $answerLine->name = $answer->name;
                        $answerLine->user_id = $model->id;
                        $answerLine->user_name = $model->name;
                        $answerLine->right_answer = $answer->right_answer;
                        $answerLine->save();
                    }

                    $dataQuestionReadingIds[] = $selectedQuestionId;
                }

                // generate listening part B questions
                $dataListeningPartB_Ids = [];
                for ($i = 1; $i <= 1; $i++) {
                    $generateMasterAudio = new MasterListeningWithAudioModel;
                    $generateMasterAudio->user_id = $model->id;
                    $generateMasterAudio->user_name = $model->name;

                    $maxCommonCount = ceil(0.3 * 10);
                    do {
                        $selectedMasterAudioId = $dataQuestionListeningPartB->random()->id;
                    } while (in_array($selectedMasterAudioId, $dataListeningPartB_Ids));

                    $generateMasterAudio->story_audio_id = $selectedMasterAudioId;
                    $generateMasterAudio->schedule_id = $schedule->id;
                    $generateMasterAudio->category_id = 4;
                    $generateMasterAudio->save();

                    $dataQuestions = QuestionModel::where('story_audio_id', $selectedMasterAudioId)->orderBy('id', 'asc')->get();
                    foreach($dataQuestions as $question) {
                        $questionLineData = new ListeningWithAudioQuestionModel;
                        $questionLineData->master_audio_listening_id = $generateMasterAudio->id;
                        $questionLineData->question_id = $question->id;
                        $questionLineData->save();

                        $dataAnswers = AnswerLineModel::where('question_id', $question->id)->get();
                        foreach($dataAnswers as $answer) {
                            $answerLine = new ListeningWithAudioAnswerLineModel;
                            $answerLine->listening_question_id = $questionLineData->id;
                            $answerLine->name = $answer->name;
                            $answerLine->right_answer = $answer->right_answer;
                            $answerLine->save();
                        }
                    }

                    // ...
                    $dataListeningPartB_Ids[] = $selectedMasterAudioId;
                }

                // generate listening part C questions
                $dataListeningPartC_Ids = [];
                for ($i = 1; $i <= 2; $i++) {
                    $generateMasterAudio = new MasterListeningWithAudioModel;
                    $generateMasterAudio->user_id = $model->id;
                    $generateMasterAudio->user_name = $model->name;

                    $maxCommonCount = ceil(0.3 * 10);
                    do {
                        $selectedMasterAudioId = $dataQuestionListeningPartC->random()->id;
                    } while (in_array($selectedMasterAudioId, $dataListeningPartC_Ids));

                    $generateMasterAudio->story_audio_id = $selectedMasterAudioId;
                    $generateMasterAudio->schedule_id = $schedule->id;
                    $generateMasterAudio->category_id = 5;
                    $generateMasterAudio->save();

                    $dataQuestions = QuestionModel::where('story_audio_id', $selectedMasterAudioId)->orderBy('id', 'asc')->get();
                    foreach($dataQuestions as $question) {
                        $questionLineData = new ListeningWithAudioQuestionModel;
                        $questionLineData->master_audio_listening_id = $generateMasterAudio->id;
                        $questionLineData->question_id = $question->id;
                        $questionLineData->save();

                        $dataAnswers = AnswerLineModel::where('question_id', $question->id)->get();
                        foreach($dataAnswers as $answer) {
                            $answerLine = new ListeningWithAudioAnswerLineModel;
                            $answerLine->listening_question_id = $questionLineData->id;
                            $answerLine->name = $answer->name;
                            $answerLine->right_answer = $answer->right_answer;
                            $answerLine->save();
                        }
                    }

                    // ...
                    $dataListeningPartC_Ids[] = $selectedMasterAudioId;
                }
            }

            $schedule->status = 2;
            $schedule->updated_at = Carbon::now('Asia/Jakarta');
            $schedule->updated_by = Auth::user()->id;
            $schedule->update();

            // ...
            DB::commit();
            return response()->json(['message' => 'This schedule was successfully executed'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
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
            } else if ($today->greaterThanOrEqualTo($examStartDate)) {
                return 2;
            }

            return 0;
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
