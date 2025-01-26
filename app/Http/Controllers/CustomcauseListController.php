<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCausePostRequest;
use App\Repositories\ArchiveRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CustomcauseListController extends Controller
{

    public function show_causelist()
    {

        $data['page_title'] = 'কজ লিস্ট';
        $user = globalUserInfo();
        $user_role = $user->role_id;
        $user_court = $user->court_id;

        $court_info = ArchiveRepository::get_court_info($user_court);

        //dd($court_info);

        $results = DB::table('custom_causelist')->orderby('id', 'desc');
        // if ($user_role == 28 || $user_role == 27) {
        //     $results = $results->where('court_id', $user_court);
        //     if (!empty($_GET['date_start']) && !empty($_GET['date_end'])) {
        //         $dateFrom = date('Y-m-d', strtotime(str_replace('/', '-', $_GET['date_start'])));
        //         $dateTo = date('Y-m-d', strtotime(str_replace('/', '-', $_GET['date_end'])));
        //         $results = $results->whereBetween('appeal_date', [$dateFrom, $dateTo]);
        //     }
        //     if (!empty($_GET['case_no'])) {
        //         $results = $results->where('case_no', '=', $_GET['case_no']);
        //     }
        // }

        // if ($user_role == 6) {
        //     $results = $results->where('div_id', $court_info->div_id)->where('dis_id', $court_info->dis_id);
        //     if (!empty($_GET['date_start']) && !empty($_GET['date_end'])) {
        //         $dateFrom = date('Y-m-d', strtotime(str_replace('/', '-', $_GET['date_start'])));
        //         $dateTo = date('Y-m-d', strtotime(str_replace('/', '-', $_GET['date_end'])));
        //         $results = $results->whereBetween('appeal_date', [$dateFrom, $dateTo]);
        //     }
        //     if (!empty($_GET['case_no'])) {
        //         $results = $results->where('case_no', '=', $_GET['case_no']);
        //     }
        // }

        // if ($user_role == 2) {

        //     if (!empty($_GET['date_start']) && !empty($_GET['date_end'])) {
        //         $dateFrom = date('Y-m-d', strtotime(str_replace('/', '-', $_GET['date_start'])));
        //         $dateTo = date('Y-m-d', strtotime(str_replace('/', '-', $_GET['date_end'])));
        //         $results = $results->whereBetween('appeal_date', [$dateFrom, $dateTo]);
        //     }
        //     if (!empty($_GET['case_no'])) {
        //         $results = $results->where('case_no', '=', $_GET['case_no']);
        //     }
        // }

        $output = $results->paginate(10);

        $data['results'] = $output;
        return view('customCauselist.archive')->with($data);
    }
    public function show_old_dsimiss_case_entry_form()
    {

        $user = globalUserInfo();
        $court_id = $user->court_id;
        $court_info = ArchiveRepository::get_court_info($court_id);

        if ($court_info) {
            $upa_info = ArchiveRepository::get_court_upa_info($court_info->div_id, $court_info->dis_id);

            $data['upa_info'] = $upa_info;
        }

        $data['court_info'] = $court_info;


        $data['page_title'] = 'মামলা সংক্রান্ত তথ্য এন্ট্রি';
        $data['division'] = DB::table('division')->get();
        $laws = DB::table('crpc_sections')
            ->select('crpc_sections.*')
            ->where('status', 1)
            ->get();
        $law_details = DB::table('crpc_section_details')
            ->select('crpc_section_details.*')
            ->get();
        $data['lawSections'] = $laws;
        $data['law_details'] = $law_details;

        $data['courselection'] = DB::table('court')
            ->select('*')
            ->where('division_id', $court_info->div_id)
            ->where('district_id', $court_info->dis_id)
            ->get();

        return view('customCauselist.archiving_form')->with($data);
    }


    public function case_store(StoreCausePostRequest  $request)
    {

        $data['page_title'] = 'মামলা এন্ট্রি';

        // dd($request->all());
  
        $case_id = DB::table('custom_causelist')->insertGetId([
            'causeTitle' => $request->causeTitle,
            'applicantName' => $request->applicantName,
            'caseNo' => $request->caseNo,
            'applicantMobile' => $request->applicantMobile,
            'defaulterName' => $request->defaulterName,
            'lawSection' => $request->lawSection,
            'lawSectionDetails' => $request->lawSectionNo,
            'court_id' => $request->court_id, //Auth::user()->court_id,
            'caseDate' => $request->caseDate,
            'dis_section' => $request->dis_section,
            'div_section' => $request->div_section,
            'upa_section' => $request->upa_section,
            'next_date' => $request->next_date,
            'lastorderDate' => $request->lastorderDate,
        ]);

        DB::table('causelist_order')->insertGetId([
            'causelist_id' =>  $case_id,
            'short_order_name' => $request->causeTitle,
            'last_order_date' => $request->lastorderDate,
            'next_date' => $request->next_date
        ]);

        DB::table('emc_manual_causelist')->insertGetId([
            'causelist_id' =>  $case_id,
            'case_no' => $request->caseNo,
            'court_id' => Auth::user()->court_id,
            'division_id' => $request->div_section,
            'district_id' => $request->dis_section,
            'upazila_id' => $request->upa_section,
            'next_date' => $request->lastorderDate,
        ]);



        // $case_id= ArchiveRepository::store_dismiss_case($request);
        // if ($case_id) {
        //     $allached_file= ArchiveRepository::storeAttachment('ARCHIVE_FILE', $case_id, $causeListId = date('Y-m-d'), $request->file_type);
        // }
        return redirect()->route('appeal.causelist.case.list')->with('success', 'সফলভাবে এন্ট্রি হয়েছে');
    }
    public function causelist_details($id)
    {
        $id = decrypt($id);
        $result = DB::table('custom_causelist')->where('id', $id)->first();

        $data['orderlist'] = DB::table('causelist_order')->where('causelist_id', $id)->get();

        // if ($case_details->id) {
        //     $attachmentList=ArchiveRepository::old_dismiss_case_attach_file($case_details->id);
        // }
        // $data['attachmentList']=$attachmentList;
        $data['page_title'] = 'কজ লিস্ট';
        $data['case_details'] = $result;
        // dd($data);
        return view('customCauselist.archive_details')->with($data);
    }



    public function causelist_edit($id)
    {

        $result = DB::table('custom_causelist')->where('id', $id)->first();
        $lastorder = DB::table('causelist_order')->where('causelist_id', $id)->orderBy('id', 'desc')->first();
        $page_title = 'কজ লিস্ট';
        $classEditdata = $result;
        // dd($lastorder);
        return view('customCauselist.edit', with(compact('classEditdata', 'lastorder', 'page_title')));
    }

    public function causelist_update(request $request)
    {

        $data = array(
            'next_date' => $request->next_date,
            'lastorderDate' => $request->lastorderDate
        );

        DB::table('causelist_order')->insertGetId([
            'causelist_id' => $request->id,
            'short_order_name' => $request->causeTitle,
            'last_order_date' => $request->lastorderDate,
            'appeal_status' => $request->appeal_status,
            'next_date' => $request->next_date
        ]);

        $customcauselist = [
            'causelist_id' =>  $request->id,
            'next_date' => $request->next_date
        ];
        DB::table('emc_manual_causelist')->where('causelist_id', $request->id)->update($customcauselist);
        // dd($data);
        DB::table('custom_causelist')->where('id', $request->id)->update($data);
        return redirect()->route('appeal.causelist.case.list')->with('success', 'সফলভাবে এন্ট্রি হয়েছে');
    }
}
