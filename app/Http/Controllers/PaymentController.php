<?php

namespace App\Http\Controllers;
use Carbon\Carbon;

use App\Models\User;
use App\Models\RoleModel;
use App\Models\PaymentModel;
use App\Models\ScheduleModel;
use App\Models\ScoreModel;
use App\Models\TemporaryImageStorageModel;
use App\Models\StoryAudioMasterModel;
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
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Eloquent\Collection;

class PaymentController extends Controller
{
    // ===================================================== user sections
    public function userTestRegistration()
    {
        try {
            $schedules = ScheduleModel::where('status', 1)->get();

            $examClasses = [];
            $examExecutions = [];
            $data = [];
            foreach ($schedules as $s) {
                $payments = PaymentModel::where('schedule_id', $s->id)->where('status', 1)->get();

                array_push($data, array(
                    'id' => $s->id,
                    'name' => $s->name,
                    'class_test' => $s->class_test,
                    'execution' => $s->execution,
                    'open_date' => $s->open_date,
                    'exe_clock' => $s->exe_clock,
                    'capacity' => count($payments) . " / 30",
                ));
            }

            $examClasses = ['Kelas A', 'Kelas B', 'Kelas C'];
            $examExecutions = ['Pagi', 'Siang', 'Malam'];

            return view('homeuser.test-registration.index', [
                'title' => 'Test Registration',
                'active' => 'homeuser/registration',
                'examClasses' => $examClasses,
                'examExecutions' => $examExecutions,
                'datas' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function userViewDetailTestSchedule($id)
    {
        try {
            $schedule = ScheduleModel::where('id', $id)->first();
            $payments = PaymentModel::where('schedule_id', $id)->whereNotIn('status', [2, 3])->get();
            $checkPayments = PaymentModel::where('schedule_id', $id)->get();
            $user = User::where("id", Auth::user()->id)->first();

            $checkData = [];
            foreach ($checkPayments as $p) {
                $checkData[] = $p->user_id;
            }

            $userId = 0;
            $status = "";
            $reason = "";
            if (in_array($user->id, $checkData, TRUE)){
                $userId = $user->id;

                $payment = PaymentModel::where('user_id', $user->id)->where('schedule_id', $id)->first();
                if ($payment->status == 0) {
                    $status = "Unconfirmed";
                } else if ($payment->status == 1) {
                    $status = "Confirmed";
                } else {
                    $status = "Rejected";
                }

                $reason = $payment->reason;
            }

            $data = [];
            foreach ($payments as $p) {
                $user = User::where('id', $p->user_id)->first();
                $userStatus = "";
                if ($p->status == 0) {
                    $userStatus = "Unconfirmed";
                } else if ($p->status == 1) {
                    $userStatus = "Confirmed";
                }

                array_push($data, array(
                    'name' => $user->name,
                    'npm' => $user->npm,
                    'status' => $userStatus,
                ));
            }

            $receiptImg = "";
            if ($userId != 0) {
                $paymentDetail = PaymentModel::where('schedule_id', $id)->where('user_id', $userId)->first();
                $receiptImg = $paymentDetail->image;
            }

            return view('homeuser.test-registration.regist', [
                'title' => 'Test Registration',
                'active' => 'homeuser/registration/final',
                'scheduleName' => $schedule->name,
                'scheduleClass' => $schedule->class_test,
                'scheduleDate' => $schedule->open_date,
                'scheduleTime' => $schedule->exe_clock,
                'datas' => $data,
                'id' => $id,
                'user_id' => $userId,
                'receipt_image' => $receiptImg,
                'status' => $status,
                'reason' => $reason,
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function userTestSaveRegistration(Request $request)
    {
        try {
            DB::beginTransaction();

            $id = $request->input('id');
            $rules =[
                'image' => 'image|file|max:1024',
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                DB::rollBack();
                return response()->json([
                    'errors' => $validator->errors()
                ], 422);
            }
            $validatedData = $validator->validated();

            $schedule = ScheduleModel::where("id", $id)->first();
            $user = User::where('id', Auth::user()->id)->first();
            $dataPayment = PaymentModel::where('schedule_id', $schedule->id)->where('status', 1)->where('deleted_at', null)->get();
            if (count($dataPayment) >= 30) {
                DB::rollBack();
                return response()->json(['message' => 'This schedule is already full of capacity. Please reload the page'], 400);
            }

            try {
                $checkSchedule = ScheduleModel::where("status", 1)->get();
                foreach($checkSchedule as $s) {
                    $payment = PaymentModel::where("schedule_id", $s->id)->where("user_id", $user->id)->where('status', '!=', 2)->where("deleted_at", null)->first();
                    if ($payment) {
                        DB::rollBack();
                        return response()->json(['message' => 'You are already registered in test'], 400);
                    }
                }
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['message' => $e->getMessage()], 500);
            }

            $user_name = Auth::user()->name;
            $cleanedPath = $this->sanitizePath($user_name);
            $fileName = uniqid('payment_') . '.' . $request->file('image')->getClientOriginalExtension();
            $request->file('image')->move(public_path("storage/schedule-registration/{$cleanedPath}/payment-receipt/"), $fileName);

            // delete rejected register
            $payment = PaymentModel::where('schedule_id', $schedule->id)->where('user_id', $user->id)->where('status', 2)->where("deleted_at", null)->first();
            if ($payment) {
                $payment->update(['deleted_at' => Carbon::now('Asia/Jakarta'), 'deleted_by' => Auth::user()->id]);
            }

            $newPayment = new PaymentModel();
            $newPayment->user_id = $user->id;
            $newPayment->schedule_id = $schedule->id;
            $newPayment->image = "storage/schedule-registration/{$cleanedPath}/payment-receipt/{$fileName}";
            $newPayment->created_by = $user->id;
            $newPayment->save();

            // ...
            DB::commit();
            return response()->json(['message' => 'Registration Success. Wait for approval.'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function userUpdateAttachment(Request $request)
    {
        try {
            DB::beginTransaction();

            $rules = [
                'image' => 'image|file|max:1024',
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                DB::rollBack();
                return response()->json([
                    'errors' => $validator->errors()
                ], 422);
            }
            $validatedData = $validator->validated();

            $id = $request->input('id');
            $schedule = ScheduleModel::where("id", $id)->first();
            $user = User::where('id', Auth::user()->id)->first();
            $payment = PaymentModel::where('user_id', $user->id)->where('schedule_id', $schedule->id)->first();

            if ($payment->status == 0) {
                if ($request->hasFile('image')) {
                    $user_name = $user->name;

                    $cleanedPath = $this->sanitizePath($user_name);
                    $fileName = uniqid('payment_') . '.' . $request->file('image')->getClientOriginalExtension();
                    $request->file('image')->move(public_path("storage/schedule-registration/{$cleanedPath}/payment-receipt/"), $fileName);

                    $payment->image = "storage/schedule-registration/{$cleanedPath}/payment-receipt/{$fileName}";
                    $payment->updated_at = Carbon::now('Asia/Jakarta');
                    $payment->updated_by = Auth::user()->id;
                    $payment->update();

                    DB::commit();
                    return response()->json(['message' => 'Success update attachment'], 200);
                }
            } else {
                $documentStatus = "";
                switch ($payment->status) {
                    case 1:
                        $documentStatus = "Confirmed";
                        break;
                    case 2:
                        $documentStatus = "Rejected";
                        break;
                    case 3:
                        $documentStatus = "Canceled";
                        break;
                    default:
                        $documentStatus = "Unknown";
                        break;
                }

                DB::rollBack();
                return response()->json(['message' => 'You are already ' . $documentStatus . ' by admin for this schedule'], 400);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function userCancelTestRegistration(Request $request)
    {
        try {
            DB::beginTransaction();

            $id = $request->input('id');
            $schedule = ScheduleModel::where("id", $id)->first();
            $user = User::where('id', Auth::user()->id)->first();
            $payment = PaymentModel::where('user_id', $user->id)->where('schedule_id', $schedule->id)->whereNotIn('status', [2, 3])->first();
            if ($payment->status == 1) {
                DB::rollBack();
                return response()->json(['message' => 'You are already confirmed by admin for this schedule. Contact the admin for your complain'], 400);
            } else {
                $payment->update(['status' => 3, 'deleted_at' => Carbon::now('Asia/Jakarta'), 'deleted_by' => $user->id]);
    
                DB::commit();
                return response()->json(['message' => 'Success cancel registration'], 200);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function temporaryImagePreview(Request $request)
    {
        try {
            $rules = [
                // 'new-image' => 'image|file|max:1024',
                'image' => 'image|file|max:1024',
            ];
            $validatedData = $request->validate($rules);

            if ($request->hasFile('image')) {
                $fileName = uniqid('payment_') . '.' . $request->file('image')->getClientOriginalExtension();
                $request->file('image')->move(public_path("storage/receipt/temporary-img/"), $fileName);

                $temporary = new TemporaryImageStorageModel();
                $temporary->image = "storage/receipt/temporary-img/{$fileName}";
                $temporary->save($validatedData);
            }

            return json_encode(['code' => 200, 'message' => "success", 'img_preview' => $temporary]);
        } catch (\Exception $e) {
            return json_encode(['message' => $e->getMessage()]);
        }
    }

    private function sanitizePath($path)
    {
        // Replace any unwanted characters with underscores or remove them
        $cleanedPath = preg_replace('/[^\w\d\/._-]/', '', $path);

        // Optionally, convert spaces to underscores (or another character)
        $cleanedPath = str_replace(' ', '_', $cleanedPath);

        return $cleanedPath;
    }

    // ===================================================== admin sections
    public function adminViewTableRegistration()
    {
        try {
            $schedules = ScheduleModel::get();

            $data = [];
            $examClasses = [];
            $executions = [];
            $examStatuses = [];
            foreach ($schedules as $s) {
                $unconfirmedPayments = PaymentModel::where('schedule_id', $s->id)
                ->where('status', 0)
                ->get();
                $totalPayments = PaymentModel::where('schedule_id', $s->id)
                ->whereIn('status', [1, 4])
                ->get();

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
                    'total_capacity' => count($totalPayments) . " / 30",
                    'status' => $status
                ));
            }

            $examClasses = ['Kelas A', 'Kelas B', 'Kelas C'];
            $executions = ['Pagi', 'Siang', 'Malam'];
            $examStatuses = ['Ready To Open', 'Not Started Yet', 'Already Started', 'Done'];

            return view('admin-manage.payment.index', [
                'title' => 'Test Schedule',
                'active' => 'admin-manage/payment',
                'datas' => $data,
                'examClasses' => $examClasses,
                'executions' => $executions,
                'examStatuses' => $examStatuses,
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function adminViewManageRegistration($id)
    {
        try {
            $payments = PaymentModel::where('schedule_id', $id)->withTrashed()->get();
            $currentSchedule = ScheduleModel::where('id', $id)->first();

            $data = [];
            $inviteUrl = "";
            $userStatuses = [];
            foreach ($payments as $p) {
                $user = User::where('id', $p->user_id)->first();
                $schedule = ScheduleModel::where('id', $p->schedule_id)->first();

                if ($schedule) {
                    $status = "";
                    if ($p->status == 0) {
                        $status = "Unconfirmed";
                    } else if ($p->status == 1) {
                        $status = "Confirmed";
                    } else if ($p->status == 2) {
                        $status = "Rejected";
                    } else if ($p->status == 3) {
                        $status = "Canceled";
                    } else {
                        $status = "Complete Exam";
                    }

                    array_push($data, array(
                        'id' => $p->id,
                        'name' => $user->name,
                        'class' => $schedule->class_test,
                        'execution' => $schedule->execution,
                        'test_date' => $schedule->open_date,
                        'date_upload' => $p->created_at,
                        'last_updated' => $p->updated_at,
                        'status' => $status,
                    ));

                    $userStatuses[] = $status;
                }
            }
            $userStatuses = array_unique($userStatuses);

            if (!empty($currentSchedule->join_url)) {
                $inviteUrl = $currentSchedule->join_url;
            }

            return view('admin-manage.payment.list', [
                'title' => 'Manage Payment & Registration',
                'active' => 'admin-manage/payment/list',
                'datas' => $data,
                'schedule_id' => $currentSchedule->id,
                'schedule_name' => $currentSchedule->name,
                'selected_class' => $currentSchedule->class_test,
                'selected_execution' => $currentSchedule->execution,
                'date' => $currentSchedule->open_date,
                'time' => $currentSchedule->exe_clock,
                'status' => $currentSchedule->status,
                'userStatuses' => $userStatuses,
                'joinUrl' => $inviteUrl,
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function adminUpdateInvitationLink(Request $request)
    {
        try {
            DB::beginTransaction();

            $id = $request->input('schedule_id');
            $urlLink = $request->input('invite_link');

            $currentSchedule = ScheduleModel::where('id', $id)->first();
            $currentSchedule->join_url = $urlLink;
            $currentSchedule->update();

            // ...
            DB::commit();
            return response()->json(['message' => 'Invitation link updated'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function adminCloseExamSchedule(Request $request)
    {
        try {
            DB::beginTransaction();

            $id = $request->input('schedule_id');
            $currentSchedule = ScheduleModel::where('id', $id)->first();
            $payments = PaymentModel::where('schedule_id', $currentSchedule->id)->whereNotIn('status', [0, 2, 3])->get();

            if (count($payments) > 0) {
                foreach ($payments as $line) {
                    if ($line->status != 4) {
                        $line->status = 4;
                        $line->updated_at = Carbon::now('Asia/Jakarta');
                        $line->updated_by = Auth::user()->id;
                        $line->update();
                    }
                }
            }

            if ($currentSchedule->status == 3) {
                DB::rollBack();
                return response()->json(['message' => "This schedule is already closed"], 400);
            }

            $currentSchedule->status = 3;
            $currentSchedule->updated_at = Carbon::now('Asia/Jakarta');
            $currentSchedule->updated_by = Auth::user()->id;
            $currentSchedule->update();

            // ...
            DB::commit();
            return response()->json(['message' => "Successfully close this schedule"], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function adminViewDetailRegistration($id)
    {
        try {
            $payment = PaymentModel::where("id", $id)->withTrashed()->first();
            $user = User::where("id", $payment->user_id)->first();
            $schedule = ScheduleModel::where("id", $payment->schedule_id)->first();

            $textStatus = "";
            switch ($payment->status) {
                case 0:
                    $textStatus = "Unconfirmed";
                    break;
                case 1:
                    $textStatus = "Confirmed";
                    break;
                case 2:
                    $textStatus = "Rejected";
                    break;
                case 3:
                    $textStatus = "Canceled";
                    break;
                case 4:
                    $textStatus = "Done";
                    break;
                default:
                    $textStatus = "Unknown";
                    break;
            }

            return view('admin-manage.payment.detail', [
                'title' => 'Registration Approval',
                'active' => 'admin-manage/approval',
                'name' => $user->name,
                'npm' => $user->npm,
                'test_class' => $schedule->class_test . " - " . $schedule->execution,
                'date' => $schedule->open_date,
                'time' => $schedule->exe_clock,
                'image' => $payment->image,
                'id' => $payment->id,
                'status' => $payment->status,
                'reason' => $payment->reason,
                'text_status' => $textStatus,
                'schedule_id' => $schedule->id,
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function adminManageRegistrationApprove(Request $request)
    {
        try {
            DB::beginTransaction();

            $id = $request->input('id');
            $reason = $request->input('reason');
            $payment = PaymentModel::where("id", $id)->first();
            $schedule = ScheduleModel::where('id', $payment->schedule_id)->first();

            // first validation
            $checkCapacity = PaymentModel::where('schedule_id', $schedule->id)->get();
            if (count($checkCapacity) >= 30) {
                DB::rollBack();
                return response()->json(['message' => 'This schedule is already full of capacity'], 400);
            }

            $payment->update(
                [
                    'status' => 1,
                    'reason' => $reason,
                    'updated_at' => Carbon::now('Asia/Jakarta'),
                    'updated_by' => Auth::user()->id,
                ]
            );

            // second validation
            $checkCapacity = PaymentModel::where('schedule_id', $schedule->id)->get();
            if (count($checkCapacity) >= 30) {
                DB::rollBack();
                return response()->json(['message' => 'This schedule is already full of capacity'], 400);
            }

            DB::commit();
            return response()->json(['message' => 'Success approve user registration', 'redirect' => route('admin.manage-registration.list', ['id' => $payment->schedule_id])], 200);
        } catch(\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function adminManageRegistrationReject(Request $request)
    {
        try {
            DB::beginTransaction();

            $id = $request->input('id');
            $reason = $request->input('reason');
            $payment = PaymentModel::where("id", $id)->first();
            $payment->update(
                [
                    'status' => 2,
                    'reason' => $reason,
                    'updated_at' => Carbon::now('Asia/Jakarta'),
                    'updated_by' => Auth::user()->id,
                ]
            );

            // ...
            DB::commit();
            return response()->json(['message' => 'Success reject user registration', 'redirect' => route('admin.manage-registration.list', ['id' => $payment->schedule_id])], 200);
        } catch(\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function adminManageRegistrationCancel(Request $request)
    {
        try {
            DB::beginTransaction();

            $id = $request->input('id');
            $reason = $request->input('reason');
            $payment = PaymentModel::where("id", $id)->first();
            $schedule = ScheduleModel::where('id', $payment->schedule_id)->first();

            if ($schedule->status == 2 || $schedule->status == 3) {
                DB::rollBack();
                return response()->json(['message' => 'This schedule is already started'], 400);
            }

            $payment->update(
                [
                    'status' => 3,
                    'reason' => $reason,
                    'deleted_at' => Carbon::now('Asia/Jakarta'),
                    'deleted_by' => Auth::user()->id,
                ]
            );

            // ...
            DB::commit();
            return response()->json(['message' => 'Success cancel user registration', 'redirect' => route('admin.manage-registration.list', ['id' => $payment->schedule_id])], 200);
        } catch(\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function viewParticipantsTable()
    {
        try {
            $schedules = ScheduleModel::where('status', 3)->get();
            $data = [];

            $participants = [];
            $examClasses = [];
            $examExecutions = [];
            $statuses = [];
            $listeningQuestionMap = [];
            $structureQuestionMap = [];
            $readingQuestionMap = [];

            foreach ($schedules as $line) {
                $doneExam = PaymentModel::where('status', 4)
                ->where('schedule_id', $line->id)
                ->get();

                foreach ($doneExam as $p) {
                    $user = User::where('id', $p->user_id)->first();
                    $score = ScoreModel::where('user_id', $user->id)
                    ->where('schedule_id', $line->id)
                    ->first();

                    $status = "";
                    if (!empty($score)) {
                        if ($score->status == 1) {
                            $status = "Approved";
                        } else {
                            $status = "Waiting Approval";
                        }
                    } else {
                        $status = "Waiting Approval";
                    }

                    array_push($data, array(
                        'id' => $p->id,
                        'name' => $user->name,
                        'npm' => $user->npm,
                        'schedule_name' => $line->name,
                        'class_test' => $line->class_test,
                        'date' => $line->open_date,
                        'execution' => $line->execution,
                        'status' => $status,
                    ));

                    $participants[] = $user->name;

                    // get listening category A question data
                    $listenings = ListeningDataModel::where('schedule_id', $line->id)->where('user_id', $user->id)->pluck('question_id')->toArray();
                    if (!isset($listeningQuestionMap[$line->id][$user->id])) {
                        $listeningQuestionMap[$line->id][$user->id] = [];
                    }
                    $listeningQuestionMap[$line->id][$user->id] = array_merge($listeningQuestionMap[$line->id][$user->id], $listenings);

                    // get listening category B & C question data
                    $masterListeningStoryIDs = MasterListeningWithAudioModel::where('schedule_id', $line->id)->where('user_id', $user->id)->pluck('id');
                    $listeningPart2 = ListeningWithAudioQuestionModel::whereIn('master_audio_listening_id', $masterListeningStoryIDs)->pluck('question_id')->toArray();
                    if (!isset($listeningQuestionMap[$line->id][$user->id])) {
                        $listeningQuestionMap[$line->id][$user->id] = [];
                    }
                    $listeningQuestionMap[$line->id][$user->id] = array_merge($listeningQuestionMap[$line->id][$user->id], $listeningPart2);

                    // get structure question data
                    $structureIDs = StructureDataModel::where('schedule_id', $line->id)->where('user_id', $user->id)->pluck('question_id')->toArray();
                    if (!isset($structureQuestionMap[$line->id][$user->id])) {
                        $structureQuestionMap[$line->id][$user->id] = [];
                    }
                    $structureQuestionMap[$line->id][$user->id] = array_merge($structureQuestionMap[$line->id][$user->id], $structureIDs);

                    // get reading question data
                    $readingIDs = ReadingDataModel::where('schedule_id', $line->id)->where('user_id', $user->id)->pluck('question_id')->toArray();
                    if (!isset($readingQuestionMap[$line->id][$user->id])) {
                        $readingQuestionMap[$line->id][$user->id] = [];
                    }
                    $readingQuestionMap[$line->id][$user->id] = array_merge($readingQuestionMap[$line->id][$user->id], $readingIDs);
                }
            }

            foreach ($listeningQuestionMap as $scheduleId => $users) {
                foreach ($users as $userId => $questions) {
                    $listeningQuestionMap[$scheduleId][$userId] = array_unique($questions);
                }
            }

            foreach ($structureQuestionMap as $scheduleId => $users) {
                foreach ($users as $userId => $questions) {
                    $structureQuestionMap[$scheduleId][$userId] = array_unique($questions);
                }
            }

            foreach ($readingQuestionMap as $scheduleId => $users) {
                foreach ($users as $userId => $questions) {
                    $readingQuestionMap[$scheduleId][$userId] = array_unique($questions);
                }
            }

            $similarityMatrixListening = $this->calculateSimilarityBasedOnScheduleAndCategory($listeningQuestionMap, "Listening");
            $similarityMatrixStructure = $this->calculateSimilarityBasedOnScheduleAndCategory($structureQuestionMap, "Structure");
            $similarityMatrixReading = $this->calculateSimilarityBasedOnScheduleAndCategory($readingQuestionMap, "Reading");

            $participants = array_unique($participants);
            $examClasses = ['Kelas A', 'Kelas B', 'Kelas C'];
            $examExecutions = ['Pagi', 'Siang', 'Malam'];
            $statuses = ["Waiting Approval", "Approved"];

            return view('admin-manage.manage-score.index', [
                'title' => 'Manage Exam Score',
                'active' => 'admin-manage/manage-score',
                'participants' => $participants,
                'examClasses' => $examClasses,
                'examExecutions' => $examExecutions,
                'statuses' => $statuses,
                'datas' => $data,
                'resultListening' => $similarityMatrixListening,
                'resultStructure' => $similarityMatrixStructure,
                'resultReading' => $similarityMatrixReading,
            ]);
        } catch(\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function detailParticipantScore($id)
    {
        try {
            $payment = PaymentModel::where('id', $id)->first();
            $user = User::where('id', $payment->user_id)->first();
            $schedule = ScheduleModel::where('id', $payment->schedule_id)->first();

            $correctListeningAnswer = 0;
            $correctStructureAnswer = 0;
            $correctReadingAnswer = 0;

            // ======================================================================= listening part A
            $listeningPartA = ListeningDataModel::where('user_id', $user->id)
            ->where('schedule_id', $schedule->id)
            ->orderBy('id', 'ASC')
            ->get();

            $dataListeningPartA = [];
            $number = 0;
            foreach ($listeningPartA as $model) {
                $number++;
                $dataAnswerLines = [];
                $question = QuestionModel::where('id', $model->question_id)->first();
                $answerLines = ListeningAnswerLineModel::where('user_id', $user->id)
                ->where('schedule_id', $schedule->id)
                ->where('question_id', $model->question_id)
                ->orderBy('id', 'ASC')
                ->get();

                foreach ($answerLines as $lines) {
                    array_push($dataAnswerLines, array(
                        'id' => $lines->id,
                        'name' => $lines->name,
                        'right_answer' => $lines->right_answer,
                        'chosen' => $lines->is_answered,
                    ));

                    if ($lines->right_answer == true && $lines->is_answered == true) {
                        $correctListeningAnswer++;
                    }
                }

                $isListened = "";
                if ($model->is_listened) {
                    $isListened = "Yes";
                } else {
                    $isListened = "No";
                }

                array_push($dataListeningPartA, array(
                    'number' => $number,
                    'id' => $model->id,
                    'name' => $question->title,
                    'is_listened' => $isListened,
                    'answer_lines' => $dataAnswerLines,
                ));
            }

            // ======================================================================= listening part B
            $masterAudioListeningB = MasterListeningWithAudioModel::where('user_id', $user->id)
            ->where('schedule_id', $schedule->id)
            ->where('category_id', 4)
            ->orderBy('id', 'ASC')
            ->get();

            $dataListeningPartB = [];
            foreach ($masterAudioListeningB as $model) {
                $storyAudio = StoryAudioMasterModel::where('id', $model->story_audio_id)->first();
                $questionData = ListeningWithAudioQuestionModel::where('master_audio_listening_id', $model->id)->orderBy('id', 'ASC')->get();

                $number = 0;
                $questionLists = [];
                foreach ($questionData as $question) {
                    $number++;
                    $questionDetail = QuestionModel::where('id', $question->question_id)->first();
                    $listeningAnswerLines = ListeningWithAudioAnswerLineModel::where('listening_question_id', $question->id)->orderBy('id', 'ASC')->get();

                    $answerLines = [];
                    foreach($listeningAnswerLines as $lines) {
                        array_push($answerLines, array(
                            'id' => $lines->id,
                            'name' => $lines->name,
                            'right_answer' => $lines->right_answer,
                            'chosen' => $lines->is_answered,
                        ));

                        if ($lines->right_answer == true && $lines->is_answered == true) {
                            $correctListeningAnswer++;
                        }
                    }

                    $isListened = "";
                    if ($question->is_listened) {
                        $isListened = "Yes";
                    } else {
                        $isListened = "No";
                    }

                    // ..
                    array_push($questionLists, array(
                        'number' => $number,
                        'id' => $questionDetail->id,
                        'name' => $questionDetail->title,
                        'is_listened' => $isListened,
                        'answer_lines' => $answerLines,
                    ));
                }

                $isStoryListened = "";
                if ($model->is_listened) {
                    $isStoryListened = "Yes";
                } else {
                    $isStoryListened = "No";
                }

                // ...
                array_push($dataListeningPartB, array(
                    'story_id' => $storyAudio->id,
                    'audio_name' => $storyAudio->audio_name,
                    'is_listened' => $isStoryListened,
                    'question_lines' => $questionLists,
                ));
            }

            // ======================================================================= listening part C
            $masterAudioListeningC = MasterListeningWithAudioModel::where('user_id', $user->id)
            ->where('schedule_id', $schedule->id)
            ->where('category_id', 5)
            ->orderBy('id', 'ASC')
            ->get();

            $dataListeningPartC = [];
            foreach ($masterAudioListeningC as $model) {
                $storyAudio = StoryAudioMasterModel::where('id', $model->story_audio_id)->first();
                $questionData = ListeningWithAudioQuestionModel::where('master_audio_listening_id', $model->id)->orderBy('id', 'ASC')->get();

                $number = 0;
                $questionLists = [];
                foreach ($questionData as $question) {
                    $number++;
                    $questionDetail = QuestionModel::where('id', $question->question_id)->first();
                    $listeningAnswerLines = ListeningWithAudioAnswerLineModel::where('listening_question_id', $question->id)->orderBy('id', 'ASC')->get();

                    $answerLines = [];
                    foreach($listeningAnswerLines as $lines) {
                        array_push($answerLines, array(
                            'id' => $lines->id,
                            'name' => $lines->name,
                            'right_answer' => $lines->right_answer,
                            'chosen' => $lines->is_answered,
                        ));

                        if ($lines->right_answer == true && $lines->is_answered == true) {
                            $correctListeningAnswer++;
                        }
                    }

                    $isListened = "";
                    if ($question->is_listened) {
                        $isListened = "Yes";
                    } else {
                        $isListened = "No";
                    }

                    // ..
                    array_push($questionLists, array(
                        'number' => $number,
                        'id' => $questionDetail->id,
                        'name' => $questionDetail->title,
                        'is_listened' => $isListened,
                        'answer_lines' => $answerLines,
                    ));
                }

                $isStoryListened = "";
                if ($model->is_listened) {
                    $isStoryListened = "Yes";
                } else {
                    $isStoryListened = "No";
                }

                // ...
                array_push($dataListeningPartC, array(
                    'story_id' => $storyAudio->id,
                    'audio_name' => $storyAudio->audio_name,
                    'is_listened' => $isStoryListened,
                    'question_lines' => $questionLists,
                ));
            }

            // ======================================================================= structure
            $structureData = StructureDataModel::where('user_id', $user->id)
            ->where('schedule_id', $schedule->id)
            ->orderBy('id', 'ASC')
            ->get();

            $structureQuestions = [];
            $number = 0;
            foreach ($structureData as $model) {
                $number++;
                $dataAnswerLines = [];
                $question = QuestionModel::where('id', $model->question_id)->first();
                $answerLines = StructureAnswerLineModel::where('user_id', $user->id)
                ->where('schedule_id', $schedule->id)
                ->where('question_id', $question->id)
                ->orderBy('id', 'ASC')
                ->get();

                foreach ($answerLines as $lines) {
                    array_push($dataAnswerLines, array(
                        'id' => $lines->id,
                        'name' => $lines->name,
                        'right_answer' => $lines->right_answer,
                        'chosen' => $lines->is_answered,
                    ));

                    if ($lines->right_answer == true && $lines->is_answered == true) {
                        $correctStructureAnswer++;
                    }
                }

                // ...
                array_push($structureQuestions, array(
                    'number' => $number,
                    'id' => $model->id,
                    'name' => $question->title,
                    'question_words' => $question->question_words,
                    'answer_lines' => $dataAnswerLines,
                ));
            }

            // ======================================================================= reading
            $readingData = ReadingDataModel::where('user_id', $user->id)
            ->where('schedule_id', $schedule->id)
            ->orderBy('id', 'ASC')
            ->get();

            $readingQuestions = [];
            $number = 0;
            foreach ($readingData as $model) {
                $number++;
                $dataAnswerLines = [];
                $question = QuestionModel::where('id', $model->question_id)->first();
                $answerLines = ReadingAnswerLineModel::where('user_id', $user->id)
                ->where('schedule_id', $schedule->id)
                ->where('question_id', $question->id)
                ->orderBy('id', 'ASC')
                ->get();

                foreach ($answerLines as $lines) {
                    array_push($dataAnswerLines, array(
                        'id' => $lines->id,
                        'name' => $lines->name,
                        'right_answer' => $lines->right_answer,
                        'chosen' => $lines->is_answered,
                    ));

                    if ($lines->right_answer == true && $lines->is_answered == true) {
                        $correctReadingAnswer++;
                    }
                }

                // ...
                array_push($readingQuestions, array(
                    'number' => $number,
                    'id' => $model->id,
                    'name' => $question->title,
                    'question_words' => $question->question_words,
                    'answer_lines' => $dataAnswerLines,
                ));
            }

            // ======================================================================= score detail
            $scoreTable = ScoreModel::where('user_id', $user->id)->where('schedule_id', $schedule->id)->first();
            $approvalStatus = false;
            $realScore = 0;
            $adminScore = 0;
            $showRealScore = false;
            $showAdminScore = false;

            if ($scoreTable) {
                if ($scoreTable->status == 1) {
                    $approvalStatus = true;
                }

                $realScore = $scoreTable->score;
                $adminScore = $scoreTable->admin_score;
                $showRealScore = $scoreTable->show_real_score;
                $showAdminScore = $scoreTable->show_admin_score;

                // ...
            } else {
                $listeningScore = $this->getScore("Listening", $correctListeningAnswer);
                $structureScore = $this->getScore("Structure", $correctStructureAnswer);
                $readingScore = $this->getScore("Reading", $correctReadingAnswer);

                $tenTimes = ($listeningScore + $structureScore + $readingScore) * 10;
                $divideByThree = $tenTimes / 3;
                $realScore = round($divideByThree);

                // ...
            }

            // ...
            return view('admin-manage.manage-score.detail', [
                'title' => 'Manage Exam Score',
                'active' => 'admin-manage/manage-score',
                'name' => $user->name,
                'npm' => $user->npm,
                'userID' => $user->id,
                'scheduleID' => $schedule->id,
                'scheduleName' => $schedule->name,
                'examClass' => $schedule->class_test,
                'execution' => $schedule->execution,
                'examDate' => $schedule->open_date,
                'examClock' => $schedule->exe_clock,
                'approvalStatus' => $approvalStatus,
                'realScore' => $realScore,
                'adminScore' => $adminScore,
                'showRealScore' => $showRealScore,
                'showAdminScore' => $showAdminScore,
                // ...
                'dataListeningPartA' => $dataListeningPartA,
                'dataListeningPartB' => $dataListeningPartB,
                'dataListeningPartC' => $dataListeningPartC,
                'dataStructureQuestions' => $structureQuestions,
                'dataReadingQuestions' => $readingQuestions,
            ]);
        } catch(\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function approveScoreParticipant(Request $request)
    {
        try {
            DB::beginTransaction();

            $userID = $request->input('user_id');
            $scheduleID = $request->input('schedule_id');
            $realScore = $request->input('real_score');
            $adminScore = $request->input('admin_score');
            $showRealScore = $request->input('show_real_score');
            $showAdminScore = $request->input('show_admin_score');

            $scoreTable = ScoreModel::where('user_id', $userID)->where('schedule_id', $scheduleID)->first();

            if ($scoreTable) {
                $scoreTable->admin_score = $adminScore;
                $scoreTable->show_real_score = $showRealScore;
                $scoreTable->show_admin_score = $showAdminScore;
                $scoreTable->status = 1;
                $scoreTable->updated_at = Carbon::now('Asia/Jakarta');
                $scoreTable->updated_by = Auth::user()->id;
                $scoreTable->update();

                // ...
            } else {
                $newScoreTable = new ScoreModel;
                $newScoreTable->user_id = $userID;
                $newScoreTable->schedule_id = $scheduleID;
                $newScoreTable->score = $realScore;
                $newScoreTable->admin_score = $adminScore;
                $newScoreTable->show_real_score = $showRealScore;
                $newScoreTable->show_admin_score = $showAdminScore;
                $newScoreTable->status = 1;
                $newScoreTable->created_by = Auth::user()->id;
                $newScoreTable->save();

                // ...
            }

            // ...
            DB::commit();
            return response()->json(['message' => "Successfully approved"], 200);
        } catch(\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function updateScoreParticipant(Request $request)
    {
        try {
            DB::beginTransaction();

            $userID = $request->input('user_id');
            $scheduleID = $request->input('schedule_id');
            $realScore = $request->input('real_score');
            $adminScore = $request->input('admin_score');
            $showRealScore = $request->input('show_real_score');
            $showAdminScore = $request->input('show_admin_score');

            $scoreTable = ScoreModel::where('user_id', $userID)->where('schedule_id', $scheduleID)->first();

            if ($scoreTable) {
                $scoreTable->admin_score = $adminScore;
                $scoreTable->show_real_score = $showRealScore;
                $scoreTable->show_admin_score = $showAdminScore;
                $scoreTable->update();

                // ...
            } else {
                $newScoreTable = new ScoreModel;
                $newScoreTable->user_id = $userID;
                $newScoreTable->schedule_id = $scheduleID;
                $newScoreTable->score = $realScore;
                $newScoreTable->admin_score = $adminScore;
                $newScoreTable->show_real_score = $showRealScore;
                $newScoreTable->show_admin_score = $showAdminScore;
                $newScoreTable->save();

                // ...
            }

            // ...
            DB::commit();
            return response()->json(['message' => "Successfully updated"], 200);
        } catch(\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function approveAllParticipants(Request $request)
    {
        try {
            DB::beginTransaction();

            $waitingApprovals = 0;
            $schedules = ScheduleModel::where('status', 3)->get();

            if (count($schedules) < 1) {
                DB::rollBack();
                return response()->json(['message' => "There is no finished exam yet"], 400);
            }

            foreach ($schedules as $model) {
                $doneExams = PaymentModel::where('status', 4)
                ->where('schedule_id', $model->id)
                ->get();

                if (count($doneExams) < 1) {
                    continue;
                }

                foreach ($doneExams as $p) {
                    $user = User::where('id', $p->user_id)->first();
                    $score = ScoreModel::where('user_id', $user->id)
                    ->where('schedule_id', $model->id)
                    ->first();

                    if ($score) {
                        if ($score->status == 0) {
                            $waitingApprovals++;

                            $score->status = 1;
                            $score->updated_at = Carbon::now('Asia/Jakarta');
                            $score->updated_by = Auth::user()->id;
                            $score->update();
                        } else {
                            continue;
                        }

                        // ...
                    } else {
                        $waitingApprovals++;

                        // calculate score
                        $correctListeningAnswer = 0;
                        $correctStructureAnswer = 0;
                        $correctReadingAnswer = 0;

                        // ======================================================================= listening part A
                        $listeningPartA = ListeningDataModel::where('user_id', $user->id)
                        ->where('schedule_id', $model->id)
                        ->orderBy('id', 'ASC')
                        ->get();

                        foreach ($listeningPartA as $data) {
                            $question = QuestionModel::where('id', $data->question_id)->first();
                            $answerLines = ListeningAnswerLineModel::where('user_id', $user->id)
                            ->where('schedule_id', $model->id)
                            ->where('question_id', $data->question_id)
                            ->orderBy('id', 'ASC')
                            ->get();

                            foreach ($answerLines as $lines) {
                                if ($lines->right_answer == true && $lines->is_answered == true) {
                                    $correctListeningAnswer++;
                                }
                            }
                        }

                        // ======================================================================= listening part B
                        $masterAudioListeningB = MasterListeningWithAudioModel::where('user_id', $user->id)
                        ->where('schedule_id', $model->id)
                        ->where('category_id', 4)
                        ->orderBy('id', 'ASC')
                        ->get();

                        foreach ($masterAudioListeningB as $masterModel) {
                            $storyAudio = StoryAudioMasterModel::where('id', $masterModel->story_audio_id)->first();
                            $questionData = ListeningWithAudioQuestionModel::where('master_audio_listening_id', $masterModel->id)->orderBy('id', 'ASC')->get();

                            foreach ($questionData as $question) {
                                $questionDetail = QuestionModel::where('id', $question->question_id)->first();
                                $listeningAnswerLines = ListeningWithAudioAnswerLineModel::where('listening_question_id', $question->id)->orderBy('id', 'ASC')->get();

                                foreach($listeningAnswerLines as $lines) {
                                    if ($lines->right_answer == true && $lines->is_answered == true) {
                                        $correctListeningAnswer++;
                                    }
                                }
                            }
                        }

                        // ======================================================================= listening part C
                        $masterAudioListeningC = MasterListeningWithAudioModel::where('user_id', $user->id)
                        ->where('schedule_id', $model->id)
                        ->where('category_id', 5)
                        ->orderBy('id', 'ASC')
                        ->get();

                        foreach ($masterAudioListeningC as $masterModel) {
                            $storyAudio = StoryAudioMasterModel::where('id', $masterModel->story_audio_id)->first();
                            $questionData = ListeningWithAudioQuestionModel::where('master_audio_listening_id', $masterModel->id)->orderBy('id', 'ASC')->get();

                            foreach ($questionData as $question) {
                                $questionDetail = QuestionModel::where('id', $question->question_id)->first();
                                $listeningAnswerLines = ListeningWithAudioAnswerLineModel::where('listening_question_id', $question->id)->orderBy('id', 'ASC')->get();

                                foreach($listeningAnswerLines as $lines) {
                                    if ($lines->right_answer == true && $lines->is_answered == true) {
                                        $correctListeningAnswer++;
                                    }
                                }
                            }
                        }

                        // ======================================================================= structure
                        $structureData = StructureDataModel::where('user_id', $user->id)
                        ->where('schedule_id', $model->id)
                        ->orderBy('id', 'ASC')
                        ->get();

                        foreach ($structureData as $data) {
                            $question = QuestionModel::where('id', $data->question_id)->first();
                            $answerLines = StructureAnswerLineModel::where('user_id', $user->id)
                            ->where('schedule_id', $model->id)
                            ->where('question_id', $question->id)
                            ->orderBy('id', 'ASC')
                            ->get();

                            foreach ($answerLines as $lines) {
                                if ($lines->right_answer == true && $lines->is_answered == true) {
                                    $correctStructureAnswer++;
                                }
                            }
                        }

                        // ======================================================================= reading
                        $readingData = ReadingDataModel::where('user_id', $user->id)
                        ->where('schedule_id', $model->id)
                        ->orderBy('id', 'ASC')
                        ->get();

                        foreach ($readingData as $data) {
                            $question = QuestionModel::where('id', $data->question_id)->first();
                            $answerLines = ReadingAnswerLineModel::where('user_id', $user->id)
                            ->where('schedule_id', $model->id)
                            ->where('question_id', $question->id)
                            ->orderBy('id', 'ASC')
                            ->get();

                            foreach ($answerLines as $lines) {
                                if ($lines->right_answer == true && $lines->is_answered == true) {
                                    $correctReadingAnswer++;
                                }
                            }
                        }

                        $listeningScore = $this->getScore("Listening", $correctListeningAnswer);
                        $structureScore = $this->getScore("Structure", $correctStructureAnswer);
                        $readingScore = $this->getScore("Reading", $correctReadingAnswer);

                        $tenTimes = ($listeningScore + $structureScore + $readingScore) * 10;
                        $divideByThree = $tenTimes / 3;
                        $realScore = round($divideByThree);
                        // ...

                        $newScoreTable = new ScoreModel;
                        $newScoreTable->user_id = $user->id;
                        $newScoreTable->schedule_id = $model->id;
                        $newScoreTable->score = $realScore;
                        $newScoreTable->admin_score = 0;
                        $newScoreTable->status = 1;
                        $newScoreTable->show_real_score = true;
                        $newScoreTable->created_by = Auth::user()->id;
                        $newScoreTable->save();

                        // ...
                    }
                }
            }

            if ($waitingApprovals == 0) {
                DB::rollBack();
                return response()->json(['message' => "All participants is already approved"], 400);
            }

            // ...
            DB::commit();
            return response()->json(['message' => "All participants are successfully approved"], 200);
        } catch(\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    // ======================================================= TOOLS =======================================================
    private function getScore($category, $correctAnswers)
    {
        try {
            $score = 0;

            if ($category == "Listening") {
                switch ($correctAnswers) {
                    case 1:
                        $score = 25;
                        break;
                    case 2:
                        $score = 26;
                        break;
                    case 3:
                        $score = 27;
                        break;
                    case 4:
                        $score = 28;
                        break;
                    case 5:
                        $score = 29;
                        break;
                    case 6:
                        $score = 30;
                        break;
                    case 7:
                        $score = 31;
                        break;
                    case 8:
                        $score = 32;
                        break;
                    case 9:
                        $score = 32;
                        break;
                    case 10:
                        $score = 33;
                        break;
                    case 11:
                        $score = 35;
                        break;
                    case 12:
                        $score = 37;
                        break;
                    case 13:
                        $score = 38;
                        break;
                    case 14:
                        $score = 39;
                        break;
                    case 15:
                        $score = 41;
                        break;
                    case 16:
                        $score = 41;
                        break;
                    case 17:
                        $score = 42;
                        break;
                    case 18:
                        $score = 43;
                        break;
                    case 19:
                        $score = 44;
                        break;
                    case 20:
                        $score = 45;
                        break;
                    case 21:
                        $score = 45;
                        break;
                    case 22:
                        $score = 46;
                        break;
                    case 23:
                        $score = 47;
                        break;
                    case 24:
                        $score = 47;
                        break;
                    case 25:
                        $score = 48;
                        break;
                    case 26:
                        $score = 48;
                        break;
                    case 27:
                        $score = 49;
                        break;
                    case 28:
                        $score = 49;
                        break;
                    case 29:
                        $score = 50;
                        break;
                    case 30:
                        $score = 51;
                        break;
                    case 31:
                        $score = 52;
                        break;
                    case 32:
                        $score = 52;
                        break;
                    case 33:
                        $score = 53;
                        break;
                    case 34:
                        $score = 53;
                        break;
                    case 35:
                        $score = 54;
                        break;
                    case 36:
                        $score = 54;
                        break;
                    case 37:
                        $score = 55;
                        break;
                    case 38:
                        $score = 56;
                        break;
                    case 39:
                        $score = 57;
                        break;
                    case 40:
                        $score = 57;
                        break;
                    case 41:
                        $score = 58;
                        break;
                    case 42:
                        $score = 59;
                        break;
                    case 43:
                        $score = 60;
                        break;
                    case 44:
                        $score = 61;
                        break;
                    case 45:
                        $score = 62;
                        break;
                    case 46:
                        $score = 63;
                        break;
                    case 47:
                        $score = 65;
                        break;
                    case 48:
                        $score = 66;
                        break;
                    case 49:
                        $score = 67;
                        break;
                    case 50:
                        $score = 68;
                        break;

                    default:
                        $score = 24;
                        break;
                }
            }

            if ($category == "Structure") {
                switch ($correctAnswers) {
                    case 1:
                        $score = 20;
                        break;
                    case 2:
                        $score = 21;
                        break;
                    case 3:
                        $score = 22;
                        break;
                    case 4:
                        $score = 23;
                        break;
                    case 5:
                        $score = 25;
                        break;
                    case 6:
                        $score = 26;
                        break;
                    case 7:
                        $score = 27;
                        break;
                    case 8:
                        $score = 29;
                        break;
                    case 9:
                        $score = 31;
                        break;
                    case 10:
                        $score = 33;
                        break;
                    case 11:
                        $score = 35;
                        break;
                    case 12:
                        $score = 36;
                        break;
                    case 13:
                        $score = 37;
                        break;
                    case 14:
                        $score = 38;
                        break;
                    case 15:
                        $score = 40;
                        break;
                    case 16:
                        $score = 40;
                        break;
                    case 17:
                        $score = 41;
                        break;
                    case 18:
                        $score = 42;
                        break;
                    case 19:
                        $score = 43;
                        break;
                    case 20:
                        $score = 44;
                        break;
                    case 21:
                        $score = 45;
                        break;
                    case 22:
                        $score = 46;
                        break;
                    case 23:
                        $score = 47;
                        break;
                    case 24:
                        $score = 48;
                        break;
                    case 25:
                        $score = 49;
                        break;
                    case 26:
                        $score = 50;
                        break;
                    case 27:
                        $score = 51;
                        break;
                    case 28:
                        $score = 52;
                        break;
                    case 29:
                        $score = 53;
                        break;
                    case 30:
                        $score = 54;
                        break;
                    case 31:
                        $score = 55;
                        break;
                    case 32:
                        $score = 56;
                        break;
                    case 33:
                        $score = 57;
                        break;
                    case 34:
                        $score = 58;
                        break;
                    case 35:
                        $score = 60;
                        break;
                    case 36:
                        $score = 61;
                        break;
                    case 37:
                        $score = 63;
                        break;
                    case 38:
                        $score = 65;
                        break;
                    case 39:
                        $score = 68;
                        break;
                    case 40:
                        $score = 68;
                        break;

                    default:
                        $score = 20;
                        break;
                }
            }

            if ($category == "Reading") {
                switch ($correctAnswers) {
                    case 1:
                        $score = 22;
                        break;
                    case 2:
                        $score = 23;
                        break;
                    case 3:
                        $score = 23;
                        break;
                    case 4:
                        $score = 25;
                        break;
                    case 5:
                        $score = 26;
                        break;
                    case 6:
                        $score = 27;
                        break;
                    case 7:
                        $score = 28;
                        break;
                    case 8:
                        $score = 28;
                        break;
                    case 9:
                        $score = 29;
                        break;
                    case 10:
                        $score = 30;
                        break;
                    case 11:
                        $score = 31;
                        break;
                    case 12:
                        $score = 32;
                        break;
                    case 13:
                        $score = 33;
                        break;
                    case 14:
                        $score = 34;
                        break;
                    case 15:
                        $score = 35;
                        break;
                    case 16:
                        $score = 36;
                        break;
                    case 17:
                        $score = 37;
                        break;
                    case 18:
                        $score = 38;
                        break;
                    case 19:
                        $score = 39;
                        break;
                    case 20:
                        $score = 40;
                        break;
                    case 21:
                        $score = 41;
                        break;
                    case 22:
                        $score = 42;
                        break;
                    case 23:
                        $score = 43;
                        break;
                    case 24:
                        $score = 43;
                        break;
                    case 25:
                        $score = 44;
                        break;
                    case 26:
                        $score = 45;
                        break;
                    case 27:
                        $score = 46;
                        break;
                    case 28:
                        $score = 46;
                        break;
                    case 29:
                        $score = 47;
                        break;
                    case 30:
                        $score = 48;
                        break;
                    case 31:
                        $score = 48;
                        break;
                    case 32:
                        $score = 49;
                        break;
                    case 33:
                        $score = 50;
                        break;
                    case 34:
                        $score = 51;
                        break;
                    case 35:
                        $score = 52;
                        break;
                    case 36:
                        $score = 52;
                        break;
                    case 37:
                        $score = 53;
                        break;
                    case 38:
                        $score = 54;
                        break;
                    case 39:
                        $score = 54;
                        break;
                    case 40:
                        $score = 55;
                        break;
                    case 41:
                        $score = 56;
                        break;
                    case 42:
                        $score = 57;
                        break;
                    case 43:
                        $score = 58;
                        break;
                    case 44:
                        $score = 59;
                        break;
                    case 45:
                        $score = 60;
                        break;
                    case 46:
                        $score = 61;
                        break;
                    case 47:
                        $score = 63;
                        break;
                    case 48:
                        $score = 65;
                        break;
                    case 49:
                        $score = 66;
                        break;
                    case 50:
                        $score = 67;
                        break;

                    default:
                        $score = 21;
                        break;
                }
            }

            return $score;
        } catch(\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    private function calculateSimilarityBasedOnScheduleAndCategory($questionMap, $category)
    {
        $similarityByScheduleAndCategory = [];

        foreach ($questionMap as $scheduleId => $users) {
            $allQuestions = [];

            foreach ($users as $userId => $questions) {
                $allQuestions = array_merge($allQuestions, $questions);
            }

            $questionFrequency = array_count_values($allQuestions);
            $duplicateQuestions = array_filter($questionFrequency, function ($count) {
                return $count > 1;
            });

            $duplicateQuestionIDs = array_keys($duplicateQuestions);
            $totalQuestions = count($allQuestions);
            $totalDuplicateQuestions = count($duplicateQuestionIDs);
            $similarity = $totalQuestions > 0 ? ($totalDuplicateQuestions / $totalQuestions) : 0;
            $percentage = $similarity * 100;
            $roundedSimilarity = round($percentage);

            $scheduleData = ScheduleModel::where('id', $scheduleId)->first();
            $doneExam = PaymentModel::where('status', 4)->where('schedule_id', $scheduleData->id)->count();

            $similarityByScheduleAndCategory[$scheduleId][$category] = [
                'schedule_class' => $scheduleData->class_test,
                'schedule_exec' => $scheduleData->execution,
                'schedule_date' => $scheduleData->open_date,
                'schedule_clock' => $scheduleData->exe_clock,
                'schedule_name' => $scheduleData->name,
                'participants' => $doneExam,
                'total_questions' => $totalQuestions,
                'duplicate_questions' => $totalDuplicateQuestions,
                'similarity' => $roundedSimilarity,
            ];
        }

        return $similarityByScheduleAndCategory;
    }
}