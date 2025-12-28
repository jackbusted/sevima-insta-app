<?php

namespace App\Http\Controllers;
use Carbon\Carbon;

use App\Models\User;
use App\Models\PaymentModel;
use App\Models\ScheduleModel;
use App\Models\ScoreModel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Eloquent\Collection;

class HistoryController extends Controller
{
    public function viewHistory()
    {
        try {
            $payments = PaymentModel::where('user_id', Auth::user()->id)
            ->where('status', 4)
            ->orderBy('id', 'desc')
            ->get();

            $examClasses = [];
            $examExecutions = [];
            $data = [];
            foreach ($payments as $model) {
                $finalScore = null;
                $score = ScoreModel::where('user_id', $model->user_id)
                ->where('schedule_id', $model->schedule_id)
                ->first();
                if ($score) {
                    if ($score->status == 1) {
                        if ($score->show_real_score) {
                            $finalScore = $score->score;
                        } else {
                            $finalScore = $score->admin_score;
                        }
                    }
                }

                $schedule = ScheduleModel::where('id', $model->schedule_id)->first();
                $formattedExamDate = Carbon::parse($schedule->open_date)
                ->locale('en')
                ->isoFormat('dddd, MMMM Do YYYY');

                array_push($data, array(
                    'examName' => $schedule->name,
                    'examDate' => $formattedExamDate,
                    'examClass' => $schedule->class_test,
                    'examExecution' => $schedule->execution,
                    'examTime' => $schedule->exe_clock,
                    'score' => $finalScore,
                ));

                $examClasses[] = $schedule->class_test;
                $examExecutions[] = $schedule->execution;
            }

            $examClasses = array_unique($examClasses);
            $examExecutions = array_unique($examExecutions);

            return view('homeuser.history.index', [
                'title' => 'Test History',
                'active' => 'homeuser/history',
                'examClasses' => $examClasses,
                'examExecutions' => $examExecutions,
                'datas' => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
