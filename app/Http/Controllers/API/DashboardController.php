<?php

namespace App\Http\Controllers\API;

use App\Models\EmAppeal;
use App\Repositories\CitizenCaseCountRepository;
use Illuminate\Http\Request;
// use Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Repositories\AppealRepository;
use Illuminate\Support\Facades\Validator;
use App\Repositories\PeshkarNoteRepository;
use App\Http\Controllers\API\BaseController as BaseController;

// use Validator;


class DashboardController extends BaseController
{
    public function test()
    {
        // $data = array();
        // Counter
        //$data['total_case'] = DB::table('case_register')->count();
        $data[] = "Test";
        // dd($data);
        // echo 'Hellollll'; exit;
        return $this->sendResponse($data, 'test successfully.');
    }

    // use AuthenticatesUsers;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $roleID = globalUserInfo()->role_id; // $request->role_id;
        $userID = globalUserInfo()->user_id; // $request->user_id;
        $court_id = globalUserInfo()->court_id; //$request->court_id;
        $district_id = globalUserInfo()->district_id;
        //   dd($roleID);

        $data = [];
        // $data['rm_case_status'] = [];


        if ($roleID == 1) {
            // Superadmi dashboard

            // Counter
            $data['total_case'] = EmAppeal::whereNotIn('appeal_status', ['DRAFT'])->count();
            $data['running_case'] = EmAppeal::where('appeal_status', 'ON_TRIAL')->count();
            $data['pending_case'] = EmAppeal::whereIn('appeal_status', ['SEND_TO_EM', 'SEND_TO_DM'])->count();
            $data['completed_case'] = EmAppeal::where('appeal_status', 'CLOSED')->count();
            $data['rejected_case'] = EmAppeal::where('appeal_status', 'REJECTED')->count();
            $data['postpond_case'] = EmAppeal::where('appeal_status', 'POSTPONED')->count();

            return $this->sendResponse($data, 'সুপার অ্যাডমিন ড্যাশবোর্ড.');
        } elseif ($roleID == 2) {
            // Superadmin dashboard
            // Counter
            $data['total_case'] = EmAppeal::whereNotIn('appeal_status', ['DRAFT'])->count();
            $data['pending_case'] = EmAppeal::whereIn('appeal_status', ['SEND_TO_EM', 'SEND_TO_DM'])->count();
            $data['running_case'] = EmAppeal::where('appeal_status', 'ON_TRIAL')->count();
            $data['completed_case'] = EmAppeal::where('appeal_status', 'CLOSED')->count();
            $data['rejected_case'] = EmAppeal::where('appeal_status', 'REJECTED')->count();
            $data['postpond_case'] = EmAppeal::where('appeal_status', 'POSTPONED')->count();
            $data['draft_case'] = EmAppeal::where('appeal_status', 'DRAFT')->count();

            $data['total_office'] = DB::table('office')->where('is_gcc', 1)->whereNotIn('id', [1, 2, 7])->count();
            $data['total_user'] = DB::table('users')->count();
            $data['total_court'] = DB::table('court')->whereNotIn('id', [1, 2])->count();
            // $data['total_mouja'] = DB::table('mouja')->count();
            // $data['total_ct'] = DB::table('case_type')->count();

            return $this->sendResponse($data, 'অ্যাডমিন ড্যাশবোর্ড.');
        } elseif ($roleID == 6) {
            // Superadmin dashboard

            // Counter
            $data['total_case'] = EmAppeal::whereNotIn('appeal_status', ['DRAFT'])->count();
            $data['running_case'] = EmAppeal::whereIn('appeal_status', ['ON_TRIAL', 'SEND_TO_GCO'])->count();
            $data['completed_case'] = EmAppeal::where('appeal_status', 'CLOSED')->count();
            $data['draft_case'] = EmAppeal::where('appeal_status', 'DRAFT')->count();
            $data['rejected_case'] = EmAppeal::where('appeal_status', 'REJECTED')->count();
            $data['postpond_case'] = EmAppeal::where('appeal_status', 'POSTPONED')->count();

            $data['total_office'] = DB::table('office')->where('is_gcc', 1)->whereNotIn('id', [1, 2, 7])->count();
            $data['total_user'] = DB::table('users')->count();
            $data['total_court'] = DB::table('court')->whereNotIn('id', [1, 2])->count();
            // $data['total_mouja'] = DB::table('mouja')->count();
            // $data['total_ct'] = DB::table('case_type')->count();

            return $this->sendResponse($data, 'জেলা প্রশাসক.');
        } elseif ($roleID == 7) {
            // Superadmin dashboard

            // Counter
            $data['total_case'] = EmAppeal::whereIn('appeal_status', ['CLOSED', 'ON_TRIAL_DM'])->where('district_id', user_district())->where('assigned_adc_id', $userID)->count();
            $data['running_case'] = EmAppeal::whereIn('appeal_status', ['ON_TRIAL_DM'])->where('district_id', user_district())->where('assigned_adc_id', $userID)->where('district_id', user_district())->count();
            $data['completed_case'] = EmAppeal::where('appeal_status', 'CLOSED')->where('district_id', user_district())->where('assigned_adc_id', $userID)->where('district_id', user_district())->count();
            $data['pending_case'] = EmAppeal::whereIn('appeal_status', ['SEND_TO_DM'])->where('assigned_adc_id', $userID)->where('district_id', user_district())->count();
            $data['trial_date_list'] = EmAppeal::where('next_date', date('Y-m-d', strtotime(now())))->where('assigned_adc_id', $userID)->where('district_id', user_district())->count();


            $data['draft_case'] = EmAppeal::where('appeal_status', 'DRAFT')->where('assigned_adc_id', $userID)->where('district_id', user_district())->count();
            $data['rejected_case'] = EmAppeal::where('appeal_status', 'REJECTED')->where('assigned_adc_id', $userID)->where('district_id', user_district())->count();
            $data['postpond_case'] = EmAppeal::where('appeal_status', 'POSTPONED')->where('assigned_adc_id', $userID)->where('district_id', user_district())->count();

            $data['total_office'] = DB::table('office')->where('is_gcc', 1)->whereNotIn('id', [1, 2, 7])->where('district_id', user_district())->count();
            $data['total_user'] = DB::table('users')->count();
            $data['total_court'] = DB::table('court')->whereNotIn('id', [1, 2])->count();
            $data['total_mouja'] = DB::table('mouja')->count();
            $data['total_ct'] = DB::table('case_type')->count();


            // return $this->sendResponse($data, 'ADC Data.');
            return $this->sendResponse($data, 'অতিরিক্ত জেলা প্রশাসক (এডিসি).');
        } elseif ($roleID == 14) {
            // Solicitor
            // Get case status by group
            // Counter
            $data['total_case'] = EmAppeal::whereNotIn('appeal_status', ['DRAFT'])->count();
            $data['running_case'] = EmAppeal::where('appeal_status', 'ON_TRIAL')->count();
            $data['completed_case'] = EmAppeal::where('appeal_status', 'CLOSED')->count();
            $data['rejected_case'] = EmAppeal::where('appeal_status', 'REJECTED')->count();
            $data['postpond_case'] = EmAppeal::where('appeal_status', 'POSTPONED')->count();
            $data['draft_case'] = EmAppeal::where('appeal_status', 'DRAFT')->count();

            return $this->sendResponse($data, 'সলিসিটর.');
        } elseif ($roleID == 20) {
            $appeal_no = [];
            $totalCase_bibadi = 0;
            $totalRunningCase_bibadi = 0;
            $totalCompleteCase_bibadi = 0;
            $pending_case_assigned_lawer_count = 0;

            $citizen_id = DB::table('em_citizens')
                ->where('citizen_NID', globalUserInfo()->citizen_nid)
                ->select('id')
                ->get();
            if (!empty($citizen_id)) {
                foreach ($citizen_id as $key => $value) {
                    // return $value;
                    $appeal_no = DB::table('em_appeal_citizens')
                        ->where('citizen_id', $value->id)
                        ->whereIN('citizen_type_id', [2, 4])
                        ->select('appeal_id')
                        ->get();
                }
            } else {
                $appeal_no = null;
            }
            // return $appeal_no;


            if (!empty($appeal_no)) {
                foreach ($appeal_no as $key => $value) {
                    if (!empty($value)) {
                        // return $value;
                        $all_case = EmAppeal::where('id', $value->appeal_id)->whereIn('appeal_status', ['ON_TRIAL', 'ON_TRIAL_DM', 'CLOSED'])->first();
                        if ($all_case) {
                            $totalCase_bibadi++;
                        }
                        $running_case = EmAppeal::where('id', $value->appeal_id)->whereIn('appeal_status', ['ON_TRIAL', 'ON_TRIAL_DM'])->first();
                        if ($running_case) {
                            $totalRunningCase_bibadi++;
                        }
                        $completed_case = EmAppeal::where('id', $value->appeal_id)->whereIn('appeal_status', ['CLOSED'])->first();
                        if ($completed_case) {
                            $totalCompleteCase_bibadi++;
                        }
                        $pending_case_assigned_lawer = EmAppeal::where('id', $value->appeal_id)->whereIn('appeal_status', ['SEND_TO_ASST_EM', 'SEND_TO_EM', 'SEND_TO_DM', 'SEND_TO_ASST_DM'])->first();
                        if ($pending_case_assigned_lawer) {
                            $pending_case_assigned_lawer_count++;
                        }
                    }
                }
            }

            $total_case_badi = EmAppeal::where('created_by', $userID)->whereIn('appeal_status', ['CLOSED', 'ON_TRIAL', 'ON_TRIAL_DM'])->count();
            $running_case_badi = EmAppeal::where('created_by', $userID)->whereIn('appeal_status', ['ON_TRIAL', 'ON_TRIAL_DM'])->count();
            $completed_case_badi = EmAppeal::where('created_by', $userID)->where('appeal_status', 'CLOSED')->count();
            $data['total_case'] = $totalCase_bibadi + $total_case_badi;
            $data['running_case'] = $totalRunningCase_bibadi + $running_case_badi;
            $data['completed_case'] = $totalCompleteCase_bibadi + $completed_case_badi;
            // Counter
            // $data['total_case'] = EmAppeal::where('created_by', $userID)->count();
            $lawer_created_case = EmAppeal::where('created_by', $userID)->whereIn('appeal_status', ['SEND_TO_ASST_EM', 'SEND_TO_EM', 'SEND_TO_DM', 'SEND_TO_ASST_DM'])->count();


            $data['pending_case'] = $pending_case_assigned_lawer_count + $lawer_created_case;

            $data['pending_review_case'] = EmAppeal::whereIn('appeal_status', ['SEND_TO_DM_REVIEW'])
                ->orWhere(function ($query) {
                    $query->where('review_applied_by', globalUserInfo()->id)
                        ->where('created_by', globalUserInfo()->id);
                })
                ->count();

            /*->where('review_applied_by', globalUserInfo()->id)->orWhere('created_by', globalUserInfo()->id)*/

            $data['running_review_case'] = EmAppeal::whereIn('appeal_status', ['ON_TRIAL_DM'])->where('is_applied_for_review', 1)->orWhere(function ($query) {
                $query->where('review_applied_by', globalUserInfo()->id)
                    ->where('created_by', globalUserInfo()->id);
            })
                ->count();

            // ->where('review_applied_by', globalUserInfo()->id)->orWhere('created_by', globalUserInfo()->id)->count();
            // $data['running_case'] = EmAppeal::where('created_by', $userID)->whereIn('appeal_status', ['ON_TRIAL','ON_TRIAL_DM'])->count();
            // $data['completed_case'] = EmAppeal::where('created_by', $userID)->where('appeal_status', 'CLOSED')->count();
            $data['draft_case'] = EmAppeal::where('created_by', $userID)->where('appeal_status', 'DRAFT')->count();
            $data['rejected_case'] = EmAppeal::where('created_by', $userID)->where('appeal_status', 'REJECTED')->count();
            $data['postpond_case'] = EmAppeal::where('created_by', $userID)->where('appeal_status', 'POSTPONED')->count();

            return $this->sendResponse($data, 'আইনজীবী.');
        } elseif ($roleID == 24) {
            // Superadmin dashboard
            // Counter
            $data['total_case'] = EmAppeal::whereNotIn('appeal_status', ['DRAFT'])->count();
            $data['running_case'] = EmAppeal::whereIn('appeal_status', ['ON_TRIAL', 'SEND_TO_GCO'])->count();
            $data['completed_case'] = EmAppeal::where('appeal_status', 'CLOSED')->count();
            $data['draft_case'] = EmAppeal::where('appeal_status', 'DRAFT')->count();
            $data['rejected_case'] = EmAppeal::where('appeal_status', 'REJECTED')->count();
            $data['postpond_case'] = EmAppeal::where('appeal_status', 'POSTPONED')->count();

            $data['total_office'] = DB::table('office')->where('is_gcc', 1)->whereNotIn('id', [1, 2, 7])->count();
            $data['total_user'] = DB::table('users')->count();
            $data['total_court'] = DB::table('court')->whereNotIn('id', [1, 2])->count();
            $data['total_mouja'] = DB::table('mouja')->count();
            $data['total_ct'] = DB::table('case_type')->count();

            return $this->sendResponse($data, 'অফিস সহকারী (এনবিআর).');
        } elseif ($roleID == 25) {
            // Superadmin dashboard

            // Counter
            $data['total_case'] = EmAppeal::whereNotIn('appeal_status', ['DRAFT'])->count();
            $data['running_case'] = EmAppeal::whereIn('appeal_status', ['ON_TRIAL', 'SEND_TO_GCO'])->count();
            $data['completed_case'] = EmAppeal::where('appeal_status', 'CLOSED')->count();
            $data['draft_case'] = EmAppeal::where('appeal_status', 'DRAFT')->count();
            $data['rejected_case'] = EmAppeal::where('appeal_status', 'REJECTED')->count();
            $data['postpond_case'] = EmAppeal::where('appeal_status', 'POSTPONED')->count();

            $data['total_office'] = DB::table('office')->where('is_gcc', 1)->whereNotIn('id', [1, 2, 7])->count();
            $data['total_user'] = DB::table('users')->count();
            $data['total_court'] = DB::table('court')->whereNotIn('id', [1, 2])->count();
            $data['total_mouja'] = DB::table('mouja')->count();
            $data['total_ct'] = DB::table('case_type')->count();



            $data['pending_case_list'] = EmAppeal::orderby('id', 'desc')->whereIn('appeal_status', ['SEND_TO_NBR_CM'])->count();
            $data['trial_date_list'] = EmAppeal::orderby('id', 'desc')->whereIn('appeal_status', ['SEND_TO_NBR_CM'])->where('next_date', date('Y-m-d', strtotime(now())))->count();
            $data['notifications'] = $data['pending_case_list'] + $data['trial_date_list'];
            return $this->sendResponse($data, 'চেয়ারম্যান(এনবিআর).');
        } elseif ($roleID == 27) {
            $data['total_case'] = EmAppeal::whereIn('appeal_status', ['ON_TRIAL', 'CLOSED'])->where('court_id', $court_id)->count();
            $data['pending_case'] = EmAppeal::whereIn('appeal_status', ['SEND_TO_EM'])->where('court_id', $court_id)->count();
            $data['running_case'] = EmAppeal::whereIn('appeal_status', ['ON_TRIAL'])->where('court_id', $court_id)->count();
            $data['completed_case'] = EmAppeal::where('appeal_status', 'CLOSED')->where('court_id', $court_id)->count();
            $data['trial_date_list'] = EmAppeal::orderby('id', 'desc')->where('next_date', date('Y-m-d', strtotime(now())))->where('court_id', $court_id)->count();

            $data['draft_case'] = EmAppeal::where('appeal_status', 'DRAFT')->where('court_id', $court_id)->count();
            $data['rejected_case'] = EmAppeal::where('appeal_status', 'REJECTED')->where('court_id', $court_id)->count();
            $data['postpond_case'] = EmAppeal::where('appeal_status', 'POSTPONED')->where('court_id', $court_id)->count();

            $data['pending_case_list'] = EmAppeal::orderby('id', 'desc')->whereIn('appeal_status', ['SEND_TO_EM'])->where('court_id', $court_id)->count();

            // $data['notifications'] = $data['pending_case_list'] + $data['trial_date_list'];

            // return $this->sendResponse($data, 'EM Data.');
            return $this->sendResponse($data, 'এক্সিকিউটিভ ম্যাজিস্ট্রেট.');
        } elseif ($roleID == 28) {
            $data['total_case'] = EmAppeal::whereIn('appeal_status', ['ON_TRIAL', 'CLOSED'])->where('court_id', $court_id)->count();
            $data['pending_case'] = EmAppeal::whereIn('appeal_status', ['SEND_TO_ASST_EM'])->where('court_id', $court_id)->count();
            $data['running_case'] = EmAppeal::whereIn('appeal_status', ['ON_TRIAL'])->where('court_id', $court_id)->count();
            $data['completed_case'] = EmAppeal::where('appeal_status', 'CLOSED')->where('court_id', $court_id)->count();
            $data['trial_date_list'] = EmAppeal::where('next_date', date('Y-m-d', strtotime(now())))->where('court_id', $court_id)->count();

            $data['draft_case'] = EmAppeal::where('appeal_status', 'DRAFT')->where('court_id', $court_id)->count();
            $data['rejected_case'] = EmAppeal::where('appeal_status', 'REJECTED')->where('court_id', $court_id)->count();
            $data['postpond_case'] = EmAppeal::where('appeal_status', 'POSTPONED')->where('court_id', $court_id)->count();

            $data['pending_case_list'] = EmAppeal::orderby('id', 'desc')->whereIn('appeal_status', ['SEND_TO_EM'])->where('court_id', globalUserInfo()->court_id)->count();
            // $data['notifications'] = $data['pending_case_list'] + $data['trial_date_list'];

            // return $this->sendResponse($data, 'Asst Em Data.');
            return $this->sendResponse($data, 'পেশকার (ইএম).');
        } elseif ($roleID == 32) {
            $moujaIDs = $this->get_mouja_by_ulo_office_id(Auth::user()->office_id);
            // ULAO office
            // Counter
            // Counter
            $$data['total_case'] = EmAppeal::whereNotIn('appeal_status', ['DRAFT'])->count();
            $data['running_case'] = EmAppeal::where('appeal_status', 'ON_TRIAL')->count();
            $data['completed_case'] = EmAppeal::where('appeal_status', 'CLOSED')->count();
            $data['rejected_case'] = EmAppeal::where('appeal_status', 'REJECTED')->count();
            $data['postpond_case'] = EmAppeal::where('appeal_status', 'POSTPONED')->count();
            $data['draft_case'] = EmAppeal::where('appeal_status', 'DRAFT')->count();

            return $this->sendResponse($data, 'সাব রেজিস্ট্রার.');
        } elseif ($roleID == 33) {
            // $moujaIDs = $this->get_mouja_by_ulo_office_id(Auth::user()->office_id);
            // ULAO office
            // Counter
            // Counter
            $data['total_case'] = EmAppeal::where('created_by', $userID)->count();
            $data['running_case'] = EmAppeal::where('created_by', $userID)->where('appeal_status', 'ON_TRIAL')->count();
            $data['completed_case'] = EmAppeal::where('created_by', $userID)->where('appeal_status', 'CLOSED')->count();
            $data['rejected_case'] = EmAppeal::where('created_by', $userID)->where('appeal_status', 'REJECTED')->count();
            $data['postpond_case'] = EmAppeal::where('created_by', $userID)->where('appeal_status', 'POSTPONED')->count();
            $data['draft_case'] = EmAppeal::where('created_by', $userID)->where('appeal_status', 'DRAFT')->count();
            return $this->sendResponse($data, 'ভারপ্রাপ্ত কর্মকর্তা(ওসি).');
        } elseif ($roleID == 34) {
            // Superadmin dashboard

            // Counter
            $data['total_case'] = EmAppeal::whereNotIn('appeal_status', ['DRAFT'])->count();
            $data['running_case'] = EmAppeal::whereIn('appeal_status', ['ON_TRIAL', 'SEND_TO_GCO'])->count();
            $data['completed_case'] = EmAppeal::where('appeal_status', 'CLOSED')->count();
            $data['draft_case'] = EmAppeal::where('appeal_status', 'DRAFT')->count();
            $data['rejected_case'] = EmAppeal::where('appeal_status', 'REJECTED')->count();
            $data['postpond_case'] = EmAppeal::where('appeal_status', 'POSTPONED')->count();

            $data['total_office'] = DB::table('office')->where('is_gcc', 1)->whereNotIn('id', [1, 2, 7])->count();
            $data['total_user'] = DB::table('users')->count();
            $data['total_court'] = DB::table('court')->whereNotIn('id', [1, 2])->count();
            $data['total_mouja'] = DB::table('mouja')->count();
            $data['total_ct'] = DB::table('case_type')->count();

            $data['pending_case_list'] = EmAppeal::orderby('id', 'desc')->whereIn('appeal_status', ['SEND_TO_DIV_COM'])->count();
            $data['trial_date_list'] = EmAppeal::orderby('id', 'desc')->where('next_date', date('Y-m-d', strtotime(now())))->where('updated_by', globalUserInfo()->id)->count();
            $data['notifications'] = $data['pending_case_list'] + $data['trial_date_list'];

            return $this->sendResponse($data, 'বিভাগীয় কমিশনার.');
        } elseif ($roleID == 36) {
            // dd('hit url here');
            $appeal_no = [];
            $totalCase_bibadi = 0;
            $totalRunningCase_bibadi = 0;
            $totalCompleteCase_bibadi = 0;

            $citizen_id = DB::table('em_citizens')
                ->where('citizen_nid', globalUserInfo()->citizen_nid)
                ->select('id')
                ->get();

            if (!empty($citizen_id)) {
                foreach ($citizen_id as $key => $value) {
                    // return $value;
                    $appeal_no = DB::table('em_appeal_citizens')
                        ->where('citizen_id', $value->id)
                        ->where('citizen_type_id', 1)
                        ->select('appeal_id')
                        ->get();
                }
            } else {
                $appeal_no = null;
            }

            //   dd($appeal_no);


            if (!empty($appeal_no)) {
                foreach ($appeal_no as $key => $value) {
                    if ($value != '') {
                        //   return $value;
                        $all_case = EmAppeal::where('id', $value->appeal_id)->whereIn('appeal_status', ['ON_TRIAL', 'ON_TRIAL_DM', 'CLOSED'])->first();
                        // dd('alll case',$all_case);
                        if ($all_case) {
                            $totalCase_bibadi++;
                        }
                        $running_case = EmAppeal::where('id', $value->appeal_id)->whereIn('appeal_status', ['ON_TRIAL', 'ON_TRIAL_DM'])->first();
                        if ($running_case) {
                            $totalRunningCase_bibadi++;
                        }
                        $completed_case = EmAppeal::where('id', $value->appeal_id)->whereIn('appeal_status', ['CLOSED'])->first();
                        if ($completed_case) {
                            $totalCompleteCase_bibadi++;
                        }
                    }
                }
            }
            // $total_case_badi = EmAppeal::where('created_by', $userID)->whereIn('appeal_status', ['CLOSED','ON_TRIAL','ON_TRIAL_DM'])->count();
            // $running_case_badi = EmAppeal::where('created_by', $userID)->whereIn('appeal_status', ['ON_TRIAL','ON_TRIAL_DM'])->count();
            // $completed_case_badi = EmAppeal::where('created_by', $userID)->where('appeal_status', 'CLOSED')->count();
            // $data['total_case'] = $totalCase_bibadi + $total_case_badi;


            // $data['running_case'] = $totalRunningCase_bibadi + $running_case_badi;
            // $data['completed_case'] = $totalCompleteCase_bibadi + $completed_case_badi;
            // Counter
            // $data['total_case'] = EmAppeal::where('created_by', $userID)->count();
            // $data['pending_case'] = EmAppeal::where('created_by', $userID)->whereIn('appeal_status', ['SEND_TO_ASST_EM', 'SEND_TO_EM', 'SEND_TO_DM', 'SEND_TO_ASST_DM'])->count();

            $data['pending_case'] = CitizenCaseCountRepository::total_pending_case_count_citizen()['total_count'];
            $data['running_case'] = CitizenCaseCountRepository::total_running_case_count_citizen()['total_count'];
            $data['total_case'] = CitizenCaseCountRepository::total_case_count_citizen()['total_count'];
            // $total_pending_case_count_citizen=CitizenCaseCountRepository::total_pending_case_count_citizen();
            $data['completed_case'] = CitizenCaseCountRepository::total_completed_case_count_citizen()['total_count'];


            // dd($userID,globalUserInfo());


            $data['pending_review_case'] = EmAppeal::whereIn('appeal_status', ['SEND_TO_DM_REVIEW'])
                ->where(function ($query) {
                    $query->where('review_applied_by', globalUserInfo()->id)
                        ->orWhere('created_by', globalUserInfo()->id);
                })
                ->count();


            /*->where('review_applied_by', globalUserInfo()->id)->orWhere('created_by', globalUserInfo()->id)*/

            $data['running_review_case'] = EmAppeal::whereIn('appeal_status', ['ON_TRIAL_DM'])->where('is_applied_for_review', 1)->where(function ($query) {
                $query->where('review_applied_by', globalUserInfo()->id)
                    ->orWhere('created_by', globalUserInfo()->id);
            })
                ->count();

            $data['draft_case'] = EmAppeal::where('created_by', $userID)->where('appeal_status', 'DRAFT')->count();
            $data['rejected_case'] = EmAppeal::where('created_by', $userID)->where('appeal_status', 'REJECTED')->count();
            $data['postpond_case'] = EmAppeal::where('created_by', $userID)->where('appeal_status', 'POSTPONED')->count();


            return $this->sendResponse($data, 'নাগরিক.');
        } elseif ($roleID == 37) {

            $data['total_case'] = EmAppeal::whereIn('appeal_status', ['CLOSED', 'ON_TRIAL_DM'])->where('district_id', $district_id)->count();
            $data['pending_case'] = EmAppeal::whereIn('appeal_status', ['SEND_TO_DM'])->where('district_id', $district_id)->count();
            $data['running_case'] = EmAppeal::whereIn('appeal_status', ['ON_TRIAL_DM'])->where('district_id', $district_id)->count();
            $data['completed_case'] = EmAppeal::where('appeal_status', 'CLOSED')->where('district_id', $district_id)->count();
            $data['trial_date_list'] = EmAppeal::where('next_date', date('Y-m-d', strtotime(now())))->where('district_id', $district_id)->count();

            $data['draft_case'] = EmAppeal::where('appeal_status', 'DRAFT')->where('district_id', $district_id)->count();
            $data['rejected_case'] = EmAppeal::where('appeal_status', 'REJECTED')->where('district_id', $district_id)->count();
            $data['postpond_case'] = EmAppeal::where('appeal_status', 'POSTPONED')->where('district_id', $district_id)->count();
            $data['total_office'] = DB::table('office')->where('is_gcc', 1)->whereNotIn('id', [1, 2, 7])->where('district_id', $district_id)->count();
            $data['total_user'] = DB::table('users')->count();
            $data['total_court'] = DB::table('court')->whereNotIn('id', [1, 2])->count();
            $data['total_mouja'] = DB::table('mouja')->count();
            $data['total_ct'] = DB::table('case_type')->count();

            // return $this->sendResponse($data, 'DM Data.');
            return $this->sendResponse($data, 'জেলা ম্যাজিস্ট্রেট.');
        } elseif ($roleID == 38) {
            // Superadmin dashboard
            //  echo 'Hello'; exit;

            // Counter
            $data['total_case'] = EmAppeal::whereIn('appeal_status', ['CLOSED', 'ON_TRIAL_DM'])->where('court_id', $court_id)->count();
            $data['running_case'] = EmAppeal::whereIn('appeal_status', ['ON_TRIAL_DM'])->where('court_id', $court_id)->count();
            $data['completed_case'] = EmAppeal::where('appeal_status', 'CLOSED')->where('court_id', $court_id)->count();
            $data['pending_case'] = EmAppeal::whereIn('appeal_status', ['SEND_TO_DM'])->where('court_id', $court_id)->count();
            $data['trial_date_list'] = EmAppeal::where('next_date', date('Y-m-d', strtotime(now())))->where('court_id', $court_id)->count();

            $data['draft_case'] = EmAppeal::where('appeal_status', 'DRAFT')->where('court_id', $court_id)->count();
            $data['rejected_case'] = EmAppeal::where('appeal_status', 'REJECTED')->where('court_id', $court_id)->count();
            $data['postpond_case'] = EmAppeal::where('appeal_status', 'POSTPONED')->where('court_id', $court_id)->count();

            // return $this->sendResponse($data, 'ADM Data.');
            return $this->sendResponse($data, 'অতিরিক্ত জেলা ম্যাজিস্ট্রেট');
        } elseif ($roleID == 39) {
            // Superadmin dashboard

            // Counter
            $data['total_case'] = EmAppeal::whereIn('appeal_status', ['CLOSED', 'ON_TRIAL_DM'])->where('court_id', $court_id)->count();
            $data['running_case'] = EmAppeal::whereIn('appeal_status', ['ON_TRIAL_DM'])->where('court_id', $court_id)->count();
            $data['completed_case'] = EmAppeal::where('appeal_status', 'CLOSED')->where('court_id', $court_id)->count();
            $data['pending_case'] = EmAppeal::whereIn('appeal_status', ['SEND_TO_ASST_DM'])->where('court_id', $court_id)->count();
            $data['trial_date_list'] = EmAppeal::where('next_date', date('Y-m-d', strtotime(now())))->where('court_id', $court_id)->count();

            $data['draft_case'] = EmAppeal::where('appeal_status', 'DRAFT')->where('court_id', $court_id)->count();
            $data['rejected_case'] = EmAppeal::where('appeal_status', 'REJECTED')->where('court_id', $court_id)->count();
            $data['postpond_case'] = EmAppeal::where('appeal_status', 'POSTPONED')->where('court_id', $court_id)->count();

            $data['total_office'] = DB::table('office')->where('is_gcc', 1)->whereNotIn('id', [1, 2, 7])->where('district_id', $district_id)->count();
            $data['total_user'] = DB::table('users')->count();
            $data['total_court'] = DB::table('court')->whereNotIn('id', [1, 2])->count();
            $data['total_mouja'] = DB::table('mouja')->count();
            $data['total_ct'] = DB::table('case_type')->count();

            return $this->sendResponse($data, 'পেশকার (এ ডি এম).');
        }
    }


    public function dashboardCauseList()
    {
        // return 'come';
        $all_appeal = EmAppeal::where('case_no', '!=', 'অসম্পূর্ণ মামলা')->whereIn('appeal_status', ['ON_TRIAL'])->get();
        $appeal_array = [];
        foreach ($all_appeal as $appeal_single) {
            $exists = DB::table('emc_manual_causelist')->where('case_no', '=', $appeal_single->case_no)->first();

            if (empty($exists)) {
                // dd($appeal_single->case_no);
                DB::table('emc_manual_causelist')->insert([
                    'case_no' => $appeal_single->case_no,
                    'appeal_id' => $appeal_single->id,
                    'court_id' => $appeal_single->court_id,
                    'division_id' => $appeal_single->division_id,
                    'district_id' => $appeal_single->district_id,
                    'upazila_id' => $appeal_single->upazila_id,
                    'next_date' => $appeal_single->next_date,
                    'type'     => 1
                ]);
            }
        }

        $roleID = Auth::user()->role_id;

        if ($roleID == 28 || $roleID == 27) {


            $data = array();

            $causelistdata = DB::table('emc_manual_causelist')
                ->leftJoin('em_appeals', 'em_appeals.id', '=', 'emc_manual_causelist.appeal_id')
                ->leftJoin('custom_causelist', 'custom_causelist.id', '=', 'emc_manual_causelist.causelist_id')
                ->select(
                    'emc_manual_causelist.appeal_id as id',
                    'emc_manual_causelist.case_no',
                    'em_appeals.case_date',
                    'emc_manual_causelist.next_date',
                    'em_appeals.office_id',
                    'em_appeals.office_name',
                    'em_appeals.district_id',
                    'em_appeals.district_name',
                    'em_appeals.division_id',
                    'em_appeals.division_name',
                    'em_appeals.law_section',
                    'em_appeals.peshkar_office_id',
                    'em_appeals.peshkar_name',
                    'em_appeals.peshkar_email',
                    'em_appeals.court_id',
                    'emc_manual_causelist.type',
                    'emc_manual_causelist.causelist_id',
                    'emc_manual_causelist.division_id',
                    'emc_manual_causelist.district_id',
                    'emc_manual_causelist.court_id',
                    'custom_causelist.applicantName',
                    'custom_causelist.defaulterName'
                );
            $causelistdata = $causelistdata->where('emc_manual_causelist.court_id', '=', globalUserInfo()->court_id)->get();

            if (!$causelistdata->isEmpty()) {
                foreach ($causelistdata as $key => $value) {
                    $data['appeal'][$key]['appealInfo'] = $value;
                    if ($value->type == 1) {
                        $citizenLists = DB::table('em_appeal_citizens')
                            ->select('citizen_id', 'citizen_type_id', 'appeal_id')
                            ->where('appeal_id', $value->id)->get();

                        foreach ($citizenLists as $citizenList) {

                            if ($citizenList->citizen_type_id == 1) {
                                $data['appeal'][$key]['applicantCitizen'] = DB::table('em_citizens')
                                    ->select('id as citizen_id', 'citizen_name')
                                    ->where('id', $citizenList->citizen_id)->get();
                            } else if ($citizenList->citizen_type_id == 2) {
                                $data['appeal'][$key]['defaulterCitizen'] = DB::table('em_citizens')
                                    ->select('id as citizen_id', 'citizen_name')
                                    ->where('id', $citizenList->citizen_id)->get();
                            }
                        }
                        $data['appeal'][$key]['notes'] = DB::table('em_notes_modified')
                            ->join('em_case_shortdecisions', 'em_notes_modified.case_short_decision_id', 'em_case_shortdecisions.id')
                            ->where('em_notes_modified.appeal_id', $value->id)
                            ->select('em_notes_modified.conduct_date as conduct_date', 'em_case_shortdecisions.case_short_decision as short_order_name')
                            ->orderBy('em_notes_modified.id', 'desc')
                            ->first();
                    } else {
                        $data['appeal'][$key]['defaulterCitizen'] = [
                            'citizen_id' => null,
                            'citizen_name' => $value->defaulterName,

                        ];
                        $data['appeal'][$key]['applicantCitizen'] =
                            [
                                'citizen_id' => null,
                                'citizen_name' => $value->applicantName,

                            ];
                        $custom_notes = DB::table('causelist_order')->where('causelist_id', $value->causelist_id)->orderby('id', 'desc')->first();
                        $data['appeal'][$key]['notes'] = [
                            "conduct_date" => null,
                            "short_order_name" => $custom_notes->short_order_name
                        ];
                    }
                }
            } else {
                $data['appeal'] = [];
            }

            if (empty($data)) {
                return $this->sendResponse($data, 'Data Not Found.');
            } else {
                return $this->sendResponse($data, 'Data Found Success.');
            }
        } elseif ($roleID == 36) {
            $data = array();

            foreach ($all_appeal as $key => $value) {
                $citizen_info=AppealRepository::getCauselistCitizen($value->id);
                $notes=PeshkarNoteRepository::get_last_order_list($value->id);
                if(isset($citizen_info) && !empty($citizen_info))
                {
                    $citizen_info=$citizen_info;
                }
                else
                {
                    $citizen_info=null;
                }
                if(isset($notes) && !empty($notes))
                {
                    $notes=$notes;
                }
                else
                {
                    $notes=null;
                }
             
                $data['appeal'][$key]['citizen_info'] = $citizen_info;
                $data['appeal'][$key]['notes'] =$notes; 
                if (empty($data)) {
                    return $this->sendResponse($data, 'Data Not Found.');
                } else {
                    return $this->sendResponse($data, 'Data Found Success.');
                }
            }
        } else {
            return $this->sendResponse(null, 'Data Not Found.');
        }
    }
    public function dashboardCauseListOld()
    {

        $data = [];
        $roleID = Auth::user()->role_id;
        $userID = globalUserInfo()->id;


        if (!empty($roleID)) {
            $data['authuserinfo'] = array(
                "id" => Auth::user()->id,
                'name' => Auth::user()->name,
                'username' => Auth::user()->username,
                "role_id" => 28
            );
        }
        if ($roleID == 27) {
            $appeal = EmAppeal::where('district_id', user_district())->whereIn('appeal_status', ['ON_TRIAL', 'ON_TRIAL_DM'])->limit(10)->get();
            // $data['appeal']  = $appeal;
            if ($appeal != null || $appeal != '') {
                foreach ($appeal as $key => $value) {
                    $citizen_info = AppealRepository::getCauselistCitizen($value->id);
                    $notes = PeshkarNoteRepository::get_last_order_list($value->id);
                    if (isset($citizen_info) && !empty($citizen_info)) {
                        $citizen_info = $citizen_info;
                    } else {
                        $citizen_info = null;
                    }
                    if (isset($notes) && !empty($notes)) {
                        $notes = $notes;
                    } else {
                        $notes = null;
                    }

                    $data['appeal'][$key]['citizen_info'] = $citizen_info;
                    $data['appeal'][$key]['notes'] = $notes;
                    // $data["notes"] = $value->appealNotes;
                }
            } else {

                $data['appeal'][$key]['citizen_info'] = '';
                $data['appeal'][$key]['notes'] = '';
            }

            return $this->sendResponse($data, ' অতিরিক্ত জেলা প্রশাসকের');
        } elseif ($roleID == 20) {
            $appeal_no = DB::table('em_appeals')
                ->join('em_appeal_citizens', 'em_appeals.id', '=', 'em_appeal_citizens.appeal_id')
                ->whereIn('em_appeal_citizens.citizen_type_id', [1, 2, 4, 7])
                ->whereIn('appeal_status', ['ON_TRIAL', 'ON_TRIAL_DM', 'CLOSED'])
                ->where('em_appeal_citizens.citizen_id', globalUserInfo()->citizen_id)
                ->select('em_appeals.id as appeal_id')->get();

            $cause_list_ids = [];
            if (!empty($appeal_no)) {
                foreach ($appeal_no as $value) {
                    array_push($cause_list_ids, $value->appeal_id);
                }
            }

            $appeal = EmAppeal::whereIn('id', $cause_list_ids)->limit(10)->get();

            if (!$appeal->isEmpty()) {
                foreach ($appeal as $key => $value) {
                    $citizen_info = AppealRepository::getCauselistCitizen($value->id);
                    $notes = PeshkarNoteRepository::get_last_order_list($value->id);
                    if (isset($citizen_info) && !empty($citizen_info)) {
                        $citizen_info = $citizen_info;
                    } else {
                        $citizen_info = null;
                    }
                    if (isset($notes) && !empty($notes)) {
                        $notes = $notes;
                    } else {
                        $notes = null;
                    }

                    $data['appeal'][$key]['citizen_info'] = $citizen_info;
                    $data['appeal'][$key]['notes'] = $notes;
                    // $data["notes"] = $value->appealNotes;
                }
            } else {

                // $data['appeal'][$key]['citizen_info'] = '';
                $data['appeal'] = [];
            }
            return $this->sendResponse($data, 'আইনজীবী');
        }
        if ($roleID == 27) {
            // Drildown Statistics
            $division_list = DB::table('division')
                ->select('division.id', 'division.division_name_bn', 'division.division_name_en')
                ->get();

            $divisiondata = array();
            $districtdata = array();

            $upazilatdata = array();

            $appeal = EmAppeal::where('court_id', globalUserInfo()->court_id)->whereIn('appeal_status', ['ON_TRIAL'])->get();
            if (!$appeal->isEmpty()) {
                // if ($appeal != null || $appeal != '') {
                foreach ($appeal as $key => $value) {
                    $citizen_info = AppealRepository::getCauselistCitizen($value->id);
                    $notes = PeshkarNoteRepository::get_last_order_list($value->id);
                    if (isset($citizen_info) && !empty($citizen_info)) {
                        $citizen_info = $citizen_info;
                    } else {
                        $citizen_info = null;
                    }
                    if (isset($notes) && !empty($notes)) {
                        $notes = $notes;
                    } else {
                        $notes = null;
                    }

                    $data['appeal'][$key]['citizen_info'] = $citizen_info;
                    $data['appeal'][$key]['notes'] = $notes;
                }
            } else {

                $data['appeal'] = [];
            }
            return $this->sendResponse($data, 'এক্সিকিউটিভ ম্যাজিস্ট্রেট');
        } elseif ($roleID == 28) {

            $division_list = DB::table('division')
                ->select('division.id', 'division.division_name_bn', 'division.division_name_en')
                ->get();

            $divisiondata = array();
            $districtdata = array();
            $upazilatdata = array();

            $appeal = EmAppeal::where('court_id', globalUserInfo()->court_id)->whereIn('appeal_status', ['ON_TRIAL'])->get();

            // || $appeal != null || $appeal != ''
            if (!$appeal->isEmpty()) {
                foreach ($appeal as $key => $value) {
                    $citizen_info = AppealRepository::getCauselistCitizen($value->id);
                    $notes = PeshkarNoteRepository::get_last_order_list($value->id);


                    if (isset($citizen_info) && !empty($citizen_info)) {
                        $citizen_info = $citizen_info;
                    } else {
                        $citizen_info = null;
                    }
                    if (isset($notes) && !empty($notes)) {
                        $notes = $notes;
                    } else {
                        $notes = null;
                    }

                    $data['appeal'][$key]['citizen_info'] = $citizen_info;
                    $data['appeal'][$key]['notes'] = $notes;
                }
            } else {

                $data['appeal'] = [];
            }

            return $this->sendResponse($data, ' এক্সিকিউটিভ ম্যাজিস্ট্রেট পেশকার');
        } elseif ($roleID == 36) {
            // Get case status by group
            $appeal_no = DB::table('em_appeals')
                ->join('em_appeal_citizens', 'em_appeals.id', '=', 'em_appeal_citizens.appeal_id')
                ->whereIn('em_appeal_citizens.citizen_type_id', [1, 2])
                ->whereIn('appeal_status', ['ON_TRIAL', 'ON_TRIAL_DM', 'CLOSED'])
                ->where('em_appeal_citizens.citizen_id', globalUserInfo()->citizen_id)
                ->select('em_appeals.id as appeal_id')->get();
            $cause_list_ids = [];
            if (!empty($appeal_no)) {
                foreach ($appeal_no as $value) {
                    array_push($cause_list_ids, $value->appeal_id);
                }
            }
            $appeal = EmAppeal::whereIn('id', $cause_list_ids)->get();

            // if ($appeal != null || $appeal != '') {
            if (!$appeal->isEmpty()) {
                foreach ($appeal as $key => $value) {
                    $citizen_info = AppealRepository::getCauselistCitizen($value->id);
                    $notes = PeshkarNoteRepository::get_last_order_list($value->id);
                    if (isset($citizen_info) && !empty($citizen_info)) {
                        $citizen_info = $citizen_info;
                    } else {
                        $citizen_info = null;
                    }
                    if (isset($notes) && !empty($notes)) {
                        $notes = $notes;
                    } else {
                        $notes = null;
                    }

                    $data['appeal'][$key]['citizen_info'] = $citizen_info;
                    $data['appeal'][$key]['notes'] = $notes;
                    // $data["notes"] = $value->appealNotes;
                }
            } else {

                $data['appeal'] = [];
            }

            return $this->sendResponse($data, ' নাগরিক ');
        } elseif ($roleID == 37) {
            $appeal = EmAppeal::where('district_id', user_district())->whereIn('appeal_status', ['ON_TRIAL', 'ON_TRIAL_DM'])->limit(10)->get();
            // $data['appeal']  = $appeal;
            // if ($appeal != null || $appeal != '') {
            if (!$appeal->isEmpty()) {
                foreach ($appeal as $key => $value) {
                    $citizen_info = AppealRepository::getCauselistCitizen($value->id);
                    $notes = PeshkarNoteRepository::get_last_order_list($value->id);
                    if (isset($citizen_info) && !empty($citizen_info)) {
                        $citizen_info = $citizen_info;
                    } else {
                        $citizen_info = null;
                    }
                    if (isset($notes) && !empty($notes)) {
                        $notes = $notes;
                    } else {
                        $notes = null;
                    }

                    $data['appeal'][$key]['citizen_info'] = $citizen_info;
                    $data['appeal'][$key]['notes'] = $notes;
                    // $data["notes"] = $value->appealNotes;
                }
            } else {

                // $data['appeal'][$key]['citizen_info'] = '';
                $data['appeal'] = [];
            }


            return $this->sendResponse($data, ' জেলা ম্যাজিস্ট্রেটের ');
        } elseif ($roleID == 38) {
            $appeal = EmAppeal::where('district_id', user_district())->whereIn('appeal_status', ['ON_TRIAL', 'ON_TRIAL_DM'])->limit(10)->get();
            // $data['appeal']  = $appeal;
            // if ($appeal != null || $appeal != '') {
            if (!$appeal->isEmpty()) {
                foreach ($appeal as $key => $value) {
                    $citizen_info = AppealRepository::getCauselistCitizen($value->id);
                    $notes = PeshkarNoteRepository::get_last_order_list($value->id);
                    if (isset($citizen_info) && !empty($citizen_info)) {
                        $citizen_info = $citizen_info;
                    } else {
                        $citizen_info = null;
                    }
                    if (isset($notes) && !empty($notes)) {
                        $notes = $notes;
                    } else {
                        $notes = null;
                    }

                    $data['appeal'][$key]['citizen_info'] = $citizen_info;
                    $data['appeal'][$key]['notes'] = $notes;
                    // $data["notes"] = $value->appealNotes;
                }
            } else {

                // $data['appeal'][$key]['citizen_info'] = '';
                $data['appeal'] = [];
            }
            return $this->sendResponse($data, ' অতিরিক্ত জেলা ম্যাজিস্ট্রেটের');
        } elseif ($roleID == 39) {

            $appeal = EmAppeal::where('district_id', user_district())->whereIn('appeal_status', ['ON_TRIAL', 'ON_TRIAL_DM'])->limit(10)->get();

            if (!$appeal->isEmpty()) {
                foreach ($appeal as $key => $value) {
                    $citizen_info = AppealRepository::getCauselistCitizen($value->id);
                    $notes = PeshkarNoteRepository::get_last_order_list($value->id);
                    if (isset($citizen_info) && !empty($citizen_info)) {
                        $citizen_info = $citizen_info;
                    } else {
                        $citizen_info = null;
                    }
                    if (isset($notes) && !empty($notes)) {
                        $notes = $notes;
                    } else {
                        $notes = null;
                    }

                    $data['appeal'][$key]['citizen_info'] = $citizen_info;
                    $data['appeal'][$key]['notes'] = $notes;
                    // $data["notes"] = $value->appealNotes;
                }
            } else {


                $data['appeal'] = [];
            }

            return $this->sendResponse($data, ' অতিরিক্ত জেলা ম্যাজিস্ট্রেটের পেশকারের ');
        }
    }
}