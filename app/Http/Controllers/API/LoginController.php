<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use App\Models\EmAppeal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Repositories\NDoptorRepository;
use Illuminate\Support\Facades\Validator;
use App\Repositories\NIDVerificationRepository;
use App\Repositories\CitizenNIDVerifyRepository;
use App\Repositories\CdapUserManagementRepository;
use App\Http\Controllers\API\BaseController as BaseController;

class LoginController extends BaseController
{
    public function test()
    {
        // Counter
        //$data['total_case'] = DB::table('case_register')->count();
        $data['Hello'] = 'Hello';
        // dd($data);
        // echo 'Hellollll'; exit;
        return $this->sendResponse($data, 'test successfully.');
    }

    /**
     * Login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {


        $validator = \Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->all(),
                'err_res' => '',
                'status' => 200,
                'data' => null,
            ]);
        }

        // echo 'Hello'; exit;
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();


            // role name //
            $roleName = DB::table('role')
                ->select('role_name')
                ->where('id', $user->role_id)
                ->first()->role_name;

            // Results
            $success['user_id'] = isset($user->id) ? $user->id : null;
            $success['name'] = isset($user->name) ? $user->name : null;
            $success['email'] = isset($user->email) ? $user->email : null;
            $success['profile_pic'] = isset($user->profile_pic) ? $user->profile_pic : null;
            $success['role_id'] = isset($user->role_id) ? $user->role_id : null;
            $success['court_id'] = isset($user->court_id) ? $user->court_id : null;
            $success['role_name'] = isset($roleName) ? $roleName : null;
            $success['office_id'] = isset($user->office_id) ? $user->office_id : null;

            // Office name //
            if (($user->role_id == 36 || $user->role_id == 20) && $user->office_id == null) {
                $success['office_name'] = null;
                $success['division_id'] = null;
                $success['district_id'] = null;
                $success['upazila_id'] = null;
                $success['is_verified_account'] = $user->is_verified_account;
                $success['citizen_id'] = $user->citizen_id;
            } else {

                $officeInfo = DB::table('office')
                    ->select('office_name_bn', 'division_id', 'district_id', 'upazila_id')
                    ->where('id', $user->office_id)
                    ->first();
                $success['office_name'] = $officeInfo->office_name_bn;
                $success['division_id'] = $officeInfo->division_id;
                $success['district_id'] = $officeInfo->district_id;
                $success['upazila_id'] = $officeInfo->upazila_id;
                $success['is_verified_account'] = $user->is_verified_account;
                $success['citizen_id'] = $user->citizen_id;
            }

            $success['token'] = $user->createToken('Login')->accessToken;

            return $this->sendResponse($success, 'User login successfully.');
        } elseif (Auth::attempt(['username' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            // return $user;
            // dd($user);   
            // role name //
            $roleName = DB::table('role')
                ->select('role_name')
                ->where('id', $user->role_id)
                ->first()->role_name;

            // Results
            $success['user_id'] = isset($user->id) ? $user->id : null;
            $success['name'] = isset($user->name) ? $user->name : null;
            $success['email'] = isset($user->email) ? $user->email : null;
            $success['profile_pic'] = isset($user->profile_pic) ? $user->profile_pic : null;
            $success['role_id'] = isset($user->role_id) ? $user->role_id : null;
            $success['court_id'] = isset($user->court_id) ? $user->court_id : null;
            $success['role_name'] = isset($roleName) ? $roleName : null;
            $success['office_id'] = isset($user->office_id) ? $user->office_id : null;

            // Office name //
            if ($user->role_id == 36 && $user->office_id == null) {
                $success['office_name'] = null;
                $success['division_id'] = null;
                $success['district_id'] = null;
                $success['upazila_id'] = null;
                $success['is_verified_account'] = $user->is_verified_account;
                $success['citizen_id'] = $user->citizen_id;
            } else {

                $officeInfo = DB::table('office')
                    ->select('office_name_bn', 'division_id', 'district_id', 'upazila_id')
                    ->where('id', $user->office_id)
                    ->first();
                $success['office_name'] = $officeInfo->office_name_bn;
                $success['division_id'] = $officeInfo->division_id;
                $success['district_id'] = $officeInfo->district_id;
                $success['upazila_id'] = $officeInfo->upazila_id;
                $success['citizen_id'] = $user->citizen_id;
            }

            $success['token'] = $user->createToken('Login')->accessToken;

            return $this->sendResponse($success, 'User login successfully.');
        } else {
            $username = DB::table('users')
                ->where('username', $request->email)
                ->first();

            if (!empty($username)) {
                if (!empty($username)) {
                    $is_current_office = NDoptorRepository::verifyCurrentDesk($username->username);
                }
                if (empty($username)) {
                    return response()->json([

                        'success' => false,
                        'message' => 'সঠিক ইমেইল, মোবাইল নং অথবা পাসওয়ার্ড প্রদান করুন ',
                        'err_res' => '',
                        'status' => 200,
                        'data' => null,

                    ]);
                } elseif ($is_current_office['status']) {
                    if ($is_current_office['role_id'] != 28) {
                        $url = url('/') . '/disable/doptor/user/' . $is_current_office['role_id'];
                        return redirect()->to($url);
                    } else {
                        $url = url('/') . '/disable/peshkar/' . $is_current_office['role_id'];
                        return redirect()->to($url);
                    }
                } else {
                    if (Auth::attempt(['username' => $username->username, 'password' => 'THIS_IS_N_DOPTOR_USER_wM-zu+93Fh+bvn%T78=j*G62nWH-C'])) {

                        $user = Auth::user();

                        $roleName = DB::table('role')
                            ->select('role_name')
                            ->where('id', $username->role_id)
                            ->first()->role_name;

                        // Results
                        $success['user_id'] = isset($user->id) ? $user->id : null;
                        $success['name'] = isset($user->name) ? $user->name : null;
                        $success['email'] = isset($user->email) ? $user->email : null;
                        $success['profile_pic'] = isset($user->profile_pic) ? $user->profile_pic : null;
                        $success['role_id'] = isset($user->role_id) ? $user->role_id : null;
                        $success['court_id'] = isset($user->court_id) ? $user->court_id : null;
                        $success['role_name'] = isset($roleName) ? $roleName : null;
                        $success['office_id'] = isset($user->office_id) ? $user->office_id : null;
                        $success['citizen_id'] = null;
                        // Office name //
                        if ($user->role_id == 36 && $user->office_id == null) {
                            $success['office_name'] = null;
                            $success['division_id'] = null;
                            $success['district_id'] = null;
                            $success['upazila_id'] = null;
                            $success['is_verified_account'] = $user->is_verified_account;
                        } else {
                            $officeInfo = DB::table('office')
                                ->select('office_name_bn', 'division_id', 'district_id', 'upazila_id')
                                ->where('id', $user->office_id)
                                ->first();
                            $success['office_name'] = $officeInfo->office_name_bn;
                            $success['division_id'] = $officeInfo->division_id;
                            $success['district_id'] = $officeInfo->district_id;
                            $success['upazila_id'] = $officeInfo->upazila_id;
                        }

                        $success['token'] = $user->createToken('Login')->accessToken;

                        return $this->sendResponse($success, 'User login successfully.');
                    } else {
                        return response()->json([

                            'success' => false,
                            'message' => 'সঠিক ইমেইল, মোবাইল নং অথবা পাসওয়ার্ড প্রদান করুন ',
                            'err_res' => '',
                            'status' => 200,
                            'data' => null,

                        ]);
                    }
                }
            }
            return response()->json([

                'success' => false,
                'message' => 'সঠিক ইমেইল, মোবাইল নং অথবা পাসওয়ার্ড প্রদান করুন ',
                'err_res' => '',
                'status' => 200,
                'data' => null,

            ]);
        }

        return $this->sendError('Unauthorised.', ['error' => 'User login failed.'], 401);
    }

    public function login_o(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required',
        ], [
            'email.required' => "মোবাইল/ইমেইল দিতে হবে",
            'password.required' => "পাসওয়ার্ড দিতে হবে",
        ]);


        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->all(),
                'err_res' => '',
                'status_code' => 401,
                'data' => null,
            ]);
        }
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {

            return response()->json([
                'status'  => true,
                'success' => 'Successfully logged in!',
            ]);
        } elseif (Auth::attempt(['username' => $request->email, 'password' => $request->password])) {
            return response()->json([
                'status'  => true,
                'success' => 'Successfully logged in!',
            ]);
        } else {
            return response()->json([
                'status'  => false,
                'nothi_msg' => 'সঠিক ইমেইল, মোবাইল নং অথবা পাসওয়ার্ড প্রদান করুন ',
            ]);
        }
    }

    public function cdap_user_login_verify(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->all(),
                'err_res' => '',
                'status' => 200,
                'data' => null,
            ]);
        }

        $token = CdapUserManagementRepository::create_token();
        if ($token['status'] == 'success') {
            $data_from_cdap = CdapUserManagementRepository::call_login_curl($token['token'], $request->password, $request->email);
            if ($data_from_cdap['status'] == 'success') {
                $user_exits_check_by_nid = DB::table('users')
                    ->where('citizen_nid', '=', $data_from_cdap['data']['nid'])
                    ->whereNotNull('citizen_nid')
                    ->where('is_cdap_user', '=', 0)
                    ->first();

                if (!empty($user_exits_check_by_nid)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'আপনার এন আই ডি দিয়ে ইতিমধ্যে নিবন্ধভুক্ত আপনি সাধারণ লগইন বাটন দিয়ে লগইন করুন',
                        'err_res' => '',
                        'status' => 200,
                        'data' => null,
                    ]);
                }

                $cdap_user_exits = DB::table('cdap_users')
                    ->where('mobile', '=', $data_from_cdap['data']['mobile'])
                    ->where('nid', '=', $data_from_cdap['data']['nid'])
                    ->first();

                if (empty($cdap_user_exits)) {
                    if ($data_from_cdap['data']['nid_verify'] == 0) {
                        return response()->json([
                            'success' => false,
                            'message' => 'দয়া করে CDAP এ গিয়ে আপনার NID verify করুন',
                            'err_res' => '',
                            'status' => 200,
                            'data' => null,
                        ]);
                    } else {
                        $userdata = CdapUserManagementRepository::create_cdap_user_with_login($data_from_cdap);

                        if (Auth::attempt(['username' => $userdata['username'], 'password' => 'THIS_IS_N_DOPTOR_USER_wM-zu+93Fh+bvn%T78=j*G62nWH-C'])) {
                            $user = Auth::user();
                            $roleName = DB::table('role')
                                ->select('role_name')
                                ->where('id', $user->role_id)
                                ->first()->role_name;

                            // Results
                            $success['user_id'] = isset($user->id) ? $user->id : null;
                            $success['name'] = isset($user->name) ? $user->name : null;
                            $success['email'] = isset($user->email) ? $user->email : null;
                            $success['profile_pic'] = isset($user->profile_pic) ? $user->profile_pic : null;
                            $success['role_id'] = isset($user->role_id) ? $user->role_id : null;
                            $success['court_id'] = isset($user->court_id) ? $user->court_id : null;
                            $success['role_name'] = isset($roleName) ? $roleName : null;
                            $success['office_id'] = isset($user->office_id) ? $user->office_id : null;

                            // Office name //
                            if ($user->role_id == 36 && $user->office_id == null) {
                                $success['office_name'] = null;
                                $success['division_id'] = null;
                                $success['district_id'] = null;
                                $success['upazila_id'] = null;
                            } else {
                                $officeInfo = DB::table('office')
                                    ->select('office_name_bn', 'division_id', 'district_id', 'upazila_id')
                                    ->where('id', $user->office_id)
                                    ->first();
                                $success['office_name'] = $officeInfo->office_name_bn;
                                $success['division_id'] = $officeInfo->division_id;
                                $success['district_id'] = $officeInfo->district_id;
                                $success['upazila_id'] = $officeInfo->upazila_id;
                            }

                            $success['token'] = $user->createToken('Login')->accessToken;

                            return $this->sendResponse($success, 'User login successfully.');
                        }
                    }
                } else {
                    $userdata = CdapUserManagementRepository::update_cdap_user_with_login($data_from_cdap);

                    if (Auth::attempt(['username' => $userdata['username'], 'password' => 'THIS_IS_N_DOPTOR_USER_wM-zu+93Fh+bvn%T78=j*G62nWH-C'])) {
                        $user = Auth::user();
                        $roleName = DB::table('role')
                            ->select('role_name')
                            ->where('id', $user->role_id)
                            ->first()->role_name;

                        // Results
                        $success['user_id'] = isset($user->id) ? $user->id : null;
                        $success['name'] = isset($user->name) ? $user->name : null;
                        $success['email'] = isset($user->email) ? $user->email : null;
                        $success['profile_pic'] = isset($user->profile_pic) ? $user->profile_pic : null;
                        $success['role_id'] = isset($user->role_id) ? $user->role_id : null;
                        $success['court_id'] = isset($user->court_id) ? $user->court_id : null;
                        $success['role_name'] = isset($roleName) ? $roleName : null;
                        $success['office_id'] = isset($user->office_id) ? $user->office_id : null;

                        // Office name //
                        if ($user->role_id == 36 && $user->office_id == null) {
                            $success['office_name'] = null;
                            $success['division_id'] = null;
                            $success['district_id'] = null;
                            $success['upazila_id'] = null;
                        } else {
                            $officeInfo = DB::table('office')
                                ->select('office_name_bn', 'division_id', 'district_id', 'upazila_id')
                                ->where('id', $user->office_id)
                                ->first();
                            $success['office_name'] = $officeInfo->office_name_bn;
                            $success['division_id'] = $officeInfo->division_id;
                            $success['district_id'] = $officeInfo->district_id;
                            $success['upazila_id'] = $officeInfo->upazila_id;
                        }

                        $success['token'] = $user->createToken('Login')->accessToken;

                        return $this->sendResponse($success, 'User login successfully.');
                    }
                }
            }

            return response()->json([
                'success' => false,
                'message' => 'আপনাকে খুজে পাওয়া যায়  নাই , আপনার প্রদান করা তথ্যগুলো ঠিক ভাবে প্রদান করুন ',
                'err_res' => '',
                'status' => 200,
                'data' => null,
            ]);
        }
    }


    public function registration(Request $request)
    {


        // $validation=$request->validate(
        //     [
        //         'input_name' => 'required',
        //         'email' => 'nullable|email|unique:users,email',
        //         'mobile_no' => 'required|unique:users,mobile_no|size:11|regex:/(01)[0-9]{9}/',
        //     ],
        //     [
        //         'input_name.required' => 'পুরো নাম লিখুন',
        //         'citizen_nid.unique' => 'আপনার জাতীয় পরিচয় পত্র দিয়ে ইতিমধ্যে নিবন্ধন করা হয়েছে',
        //         'email.unique' => 'আপনার ইমেইল দিয়ে দিয়ে ইতিমধ্যে নিবন্ধন করা হয়েছে',
        //         'mobile_no.required' => 'মোবাইল নং দিতে হবে',
        //         'mobile_no.size' => 'মোবাইল নং দিতে হবে ১১ সংখ্যার ইংরেজিতে',
        //         'mobile_no.unique' => 'আপনার মোবাইল নং দিয়ে ইতিমধ্যে নিবন্ধন করা হয়েছে',
        //     ],
        // );
        // $validator =Validator::make($request->all(),             [
        //     'input_name' => 'required',
        //     'email' => 'nullable|email|unique:users,email',
        //     'mobile_no' => 'required|unique:users,mobile_no|size:11|regex:/(01)[0-9]{9}/',
        // ],
        // [
        //     'input_name.required' => 'পুরো নাম লিখুন',
        //     'citizen_nid.unique' => 'আপনার জাতীয় পরিচয় পত্র দিয়ে ইতিমধ্যে নিবন্ধন করা হয়েছে',
        //     'email.unique' => 'আপনার ইমেইল দিয়ে দিয়ে ইতিমধ্যে নিবন্ধন করা হয়েছে',
        //     'mobile_no.required' => 'মোবাইল নং দিতে হবে',
        //     'mobile_no.size' => 'মোবাইল নং দিতে হবে ১১ সংখ্যার ইংরেজিতে',
        //     'mobile_no.unique' => 'আপনার মোবাইল নং দিয়ে ইতিমধ্যে নিবন্ধন করা হয়েছে',
        // ]);


        $validator = Validator::make(
            $request->all(),
            [
                'input_name' => 'required',
                'email' => 'nullable|email|unique:users,email',
                'mobile_no' => 'required|unique:users,mobile_no|size:11|regex:/(01)[0-9]{9}/',
                // 'password' => 'min:6|required_with:confirm_password|same:confirm_password',
                // 'confirm_password' => 'min:6',
            ],
            [
                'input_name.required' => 'পুরো নাম লিখুন',
                'citizen_nid.unique' => 'আপনার জাতীয় পরিচয় পত্র দিয়ে ইতিমধ্যে নিবন্ধন করা হয়েছে',
                'email.unique' => 'আপনার ইমেইল দিয়ে দিয়ে ইতিমধ্যে নিবন্ধন করা হয়েছে',
                'mobile_no.required' => 'মোবাইল নং দিতে হবে',
                'mobile_no.size' => 'মোবাইল নং দিতে হবে ১১ সংখ্যার ইংরেজিতে',
                'mobile_no.unique' => 'আপনার মোবাইল নং দিয়ে ইতিমধ্যে নিবন্ধন করা হয়েছে',
                // 'password.required_with' => 'উভয় ক্ষেত্রে সঠিক পাসওয়ার্ড লিখুন ৬ সংখ্যার বেশি হতে হবে',
                // 'password.min' => 'উভয় ক্ষেত্রে সঠিক পাসওয়ার্ড লিখুন ৬ সংখ্যার বেশি হতে হবে',
                // 'password.same' => 'উভয় ক্ষেত্রে একই পাসওয়ার্ড লিখুন',
                // 'confirm_password.min' => 'উভয় ক্ষেত্রে সঠিক পাসওয়ার্ড লিখুন, ৬ সংখ্যার বেশি হতে হবে',
            ]

        );

        if ($validator->fails()) {
            // return response()->json([
            //     "success"=>false,
            //     "message" => $validator->errors()->all(),
            //     "err_res"=>"",
            //     "status"=> 200,
            //     "data" =>null


            // ]);
            return  $this->sendError($validator->errors()->first(), ['error' => $validator->errors()->first()], 200);
        }


        $FourDigitRandomNumber = rand(1111, 9999);

        $result = DB::table('users')->insertGetId([
            'name' => $request->input_name,
            'username' => $request->mobile_no,
            'mobile_no' => $request->mobile_no,
            'email' => $request->email,
            'role_id' => 36,
            'otp' => $FourDigitRandomNumber,
            'password' => Hash::make('google_sso_login_password_14789_gcc_ourt_otp_based'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);


        if ($result) {

            $message = 'সিস্টেমে নিবন্ধন সম্পন্ন করার জন্য নিম্নোক্ত ওটিপি ব্যবহার করুন। ওটিপি: ' . $FourDigitRandomNumber . ' ধন্যবাদ।';
            $m = str_replace(' ', '%20', $message);
            $mobile = $request->mobile_no;
            $this->send_sms($mobile, $m);
            $user_id = ['user_id' => $result];
            return $this->sendResponse($user_id, 'সিস্টেমে নিবন্ধন সম্পন্ন করার জন্য ওটিপি ব্যবহার করুন');
        }
    }


    public function mobile_verify(Request $request)
    {
        $otp = $request->otp; // $request->otp_1 . $request->otp_2 . $request->otp_3 . $request->otp_4;

        $result = User::where('otp', $otp)
            ->where('id', $request->user_id)
            ->first();


        if (empty($result)) {

            return  $this->sendError('সঠিক ওটিপি প্রদান করুন', ['error' => 'সঠিক ওটিপি প্রদান করুন'], 200);
        } else {
            User::where('id', $request->user_id)->update(['password' => Hash::make('google_sso_login_password_14789_gcc_ourtm#P52s@ap$V')]);
            return $this->sendResponse('', 'সিস্টেমে নিবন্ধন সম্পন্ন হয়েছে');
        }
    }
    public function after_otp_password_set(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'password' => 'min:6|required_with:confirm_password|same:confirm_password',
                'confirm_password' => 'min:6',
            ],
            [
                'password.required_with' => 'উভয় ক্ষেত্রে সঠিক পাসওয়ার্ড লিখুন ৬ সংখ্যার বেশি হতে হবে',
                'password.min' => 'উভয় ক্ষেত্রে সঠিক পাসওয়ার্ড লিখুন ৬ সংখ্যার বেশি হতে হবে',
                'password.same' => 'উভয় ক্ষেত্রে একই পাসওয়ার্ড লিখুন',
                'confirm_password.min' => 'উভয় ক্ষেত্রে সঠিক পাসওয়ার্ড লিখুন, ৬ সংখ্যার বেশি হতে হবে',
            ],
        );
        if ($validator->fails()) {
            return  $this->sendError($validator->errors()->first(), ['error' => $validator->errors()->first()], 200);
            // return response()->json([
            //     "success"=>false,
            //     "message" => $validator->errors()->first(),
            //     "err_res"=>"",
            //     "status"=> 200,
            //     "data" =>null


            // ]);
        }


        User::where('id', $request->user_id)->update(['password' => Hash::make($request->password)]);

        return $this->sendResponse(null, 'পাসওয়ার্ড হালনাগাদ হয়েছে');
    }
    /**
     * cause list  api
     *
     * @return \Illuminate\Http\Response
     */

    public function cause_list(Request $request)
    {
        $dateFrom = date('Y-m-d', strtotime(str_replace('/', '-', now())));
        $dateTo = date('Y-m-d', strtotime(str_replace('/', '-', now())));
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
                'custom_causelist.defaulterName',
                'custom_causelist.applicantName'
            );

        // $test = $causelistdata->where('emc_manual_causelist.district_id', '=', $request->district)->get();
        // dd($request->district);
        // dd($test);

        if (!empty($request->division)) {
            $causelistdata = $causelistdata->where('emc_manual_causelist.division_id', '=', $request->division);
        }
        if (!empty($request->district)) {
            $causelistdata = $causelistdata->where('emc_manual_causelist.district_id', '=', $_GET['district']);
        }
        if (!empty($request->court)) {
            $causelistdata = $causelistdata->where('emc_manual_causelist.court_id', '=', $_GET['court']);
        }

        if (!empty($request->case_no)) {
            $causelistdata = $causelistdata->where('emc_manual_causelist.case_no', 'like', '%' . bn2en($_GET['case_no']) . '%')->orWhere('manual_case_no', '=', $_GET['case_no']);
        }

        if (!empty($request->date_start) && !empty($request->date_end)) {
            $dateFrom = date('Y-m-d', strtotime(str_replace('/', '-', $request->date_start)));
            $dateTo = date('Y-m-d', strtotime(str_replace('/', '-', $request->date_end)));
            $causelistdata = $causelistdata->whereBetween('emc_manual_causelist.next_date', [$dateFrom, $dateTo]);
        }

        if (is_null($request->division) && is_null($request->district) && is_null($request->court) && is_null($request->case_no) && is_null($request->date_start) && is_null($request->date_end)) {
            $causelistdata = $causelistdata->where('emc_manual_causelist.next_date', $dateFrom);
        }



        $causelistdata = $causelistdata->orderBy('emc_manual_causelist.id', 'desc')->get();

        // foreach ($causelistdata as $key => $item) {
        //     if ($item->type == 0) {
        //         $data['custom_notes'] = DB::table('causelist_order')->where('causelist_id', $item->causelist_id)->orderby('id', 'desc')->get();
        //     }
        // }

        // dd($causelistdata);
        if (!$causelistdata->isEmpty()) {
            foreach ($causelistdata as $key => $value) {
                // return $value;
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
                    $data['appeal'][$key]['defaulterCitizen'] = [[
                        'citizen_id' => null,
                        'citizen_name' => $value->applicantName,

                    ],];
                    $data['appeal'][$key]['applicantCitizen'] =
                        [[
                            'citizen_id' => null,
                            'citizen_name' => $value->defaulterName,

                        ],];

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
    }


    public function cause_listOld(Request $request)
    {


        $data = [];
        $appeal = EmAppeal::whereIn('appeal_status', ['ON_TRIAL', 'ON_TRIAL_DM']);
        if (!empty($request->date_start) && !empty($request->date_end)) {
            $dateFrom = date('Y-m-d', strtotime(str_replace('/', '-', $request->date_start)));
            $dateTo = date('Y-m-d', strtotime(str_replace('/', '-', $request->date_end)));
            $appeal->whereBetween('next_date', [$dateFrom, $dateTo]);
        }


        if (!empty($request->division)) {
            $appeal->where('division_id', '=', $request->division);
        }

        if (!empty($request->district)) {
            $appeal->where('district_id', '=', $request->district);
        }

        if (!empty($request->case_no)) {
            $case_no = bn2en($request->case_no);

            $appeal->where('case_no', '=', $case_no);
        }
        if (!empty($request->court)) {

            $appeal->where('court_id', '=', $request->court);
        }


        $appeal = $appeal->select('id', 'case_no', 'case_date', 'next_date', 'office_id', 'office_name', 'district_id', 'district_name', 'division_id', 'division_name', 'law_section', 'peshkar_office_id', 'peshkar_name', 'peshkar_email', 'court_id', 'created_at')->get();


        if ($appeal != null || !empty($appeal)) {
            foreach ($appeal as $key => $value) {
                $data['appeal'][$key]['appealInfo'] = $value;

                //applicant and defaulter info
                $citizenLists = DB::table('em_appeal_citizens')
                    ->select('citizen_id', 'citizen_type_id', 'appeal_id')
                    ->where('appeal_id', $value->id)
                    ->get();

                foreach ($citizenLists as $citizenList) {
                    if ($citizenList->citizen_type_id == 1) {
                        $data['appeal'][$key]['applicantCitizen'] = DB::table('em_citizens')
                            ->select('id as citizen_id', 'citizen_name')
                            ->where('id', $citizenList->citizen_id)
                            ->get();
                    } elseif ($citizenList->citizen_type_id == 2) {
                        $data['appeal'][$key]['defaulterCitizen'] = DB::table('em_citizens')
                            ->select('id as citizen_id', 'citizen_name')
                            ->where('id', $citizenList->citizen_id)
                            ->get();
                    }
                }
                // return $citizenLists;

                // case note
                // $data['appeal'][$key]['notes'] = DB::table('em_notes')
                //     ->where('appeal_id', $value->id)
                //     ->leftjoin('em_case_shortdecisions', 'em_notes.case_short_decision_id', '=', 'em_case_shortdecisions.id')
                //     ->select('em_notes.appeal_id', 'em_case_shortdecisions.case_short_decision')
                //     ->orderBy('em_notes.id', 'desc')
                //     ->first();
                $data['appeal'][$key]['notes'] = DB::table('em_notes_modified')
                    ->join('em_case_shortdecisions', 'em_notes_modified.case_short_decision_id', 'em_case_shortdecisions.id')
                    ->where('em_notes_modified.appeal_id', $value->id)
                    ->select('em_notes_modified.conduct_date as conduct_date', 'em_case_shortdecisions.case_short_decision as short_order_name')
                    ->orderBy('em_notes_modified.id', 'desc')
                    ->first();
            }
        }

        if (empty($data)) {
            return $this->sendResponse(null, 'Data Not Found.');
        } else {
            return $this->sendResponse($data, 'Data Found Success.');
        }
    }

    public function test_login_fun($test_email, $test_password)
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => DOPTOR_ENDPOINT() . '/api/user/verify',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => ['username' => $test_email, 'password' => $test_password],
            CURLOPT_HTTPHEADER => ['api-version: 1'],
        ]);

        $response = curl_exec($curl);

        curl_close($curl);

        $response2 = json_decode($response, true);
        $response = json_decode($response);

        if ($response->status == 'success') {
            $username = DB::table('users')
                ->where('username', $response->data->user->username)
                ->first();

            if (empty($username)) {
                if (empty($response2['data']['office_info'])) {
                    return 0;
                }
                $ref_origin_unit_org_id = $response2['data']['organogram_info'][array_key_first($response2['data']['organogram_info'])]['ref_origin_unit_org_id'];

                $office_info = $response->data->office_info[0];

                if ($ref_origin_unit_org_id == 533) {
                    NDoptorRepository::Divisional_Commissioner_create($response, $office_info, $ref_origin_unit_org_id);
                }

                if ($ref_origin_unit_org_id == 51) {
                    NDoptorRepository::DC_create($response, $office_info, $ref_origin_unit_org_id);
                }
            } else {
                return 1;
            }
        }
    }

    public function update_password(Request $request)
    {

        $userid = Auth::guard('api')->user()->id;

        $validator = Validator::make($request->all(), [
            'current_password' => ['required'],
            'new_password' => ['required', 'min:6'],
            'new_confirm_password' => ['same:new_password'],
        ]);
        if ($validator->fails()) {
            $arr = $this->sendError($validator->errors()->first(), ['error' => $validator->errors()->first()], 200);
        } else {

            try {
                if (Hash::check(request('current_password'), Auth::user()->password) == false) {
                    $arr = $this->sendError('Check your old password.', ['error' => 'Your old password not match.']);
                } elseif (Hash::check(request('new_password'), Auth::user()->password) == true) {
                    $arr = $this->sendError('Make sure your current password does not match your new password.', ['error' => 'Please enter a new password which is not similar then current password.']);
                } else {
                    User::where('id', $userid)->update(['password' => Hash::make($request->new_password)]);
                    return $this->sendResponse(null, 'Password updated successfully.');
                }
            } catch (\Exception $ex) {
                if (isset($ex->errorInfo[2])) {
                    $msg = $ex->errorInfo[2];
                } else {
                    $msg = $ex->getMessage();
                }
                $arr = $this->sendError($msg, ['error' => $msg], 200);
            }
        }
        return $arr;
    }
    public function forget_password(Request $request)
    {


        $validator = Validator::make(
            $request->all(),
            [
                'mobile_number' => 'required|size:11|regex:/(01)[0-9]{9}/',
            ],
            [
                'mobile_number.required' => 'মোবাইল নং লিখুন',
                'mobile_number.size' => 'মোবাইল নং দিতে হবে ১১ সংখ্যার ইংরেজিতে',
            ],
        );
        if ($validator->fails()) {

            return  $this->sendError($validator->errors()->first(), ['error' => $validator->errors()->first()], 200);
            // return  $this->sendError($validator->errors()->first(), ['error' => $validator->errors()->first()]);
        }

        $user = DB::table('users')
            ->where('mobile_no', '=', $request->mobile_number)
            ->first();
        // return $user->mobile_no;


        if (!empty($user)) {
            $otp = rand(1111, 9999);

            $update_otp = DB::table('users')
                ->where('id', '=', $user->id)
                ->update([
                    'otp' => $otp,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            if ($update_otp) {
                $message = 'সিস্টেমে পাসওয়ার্ড রিসেট সম্পন্ন করার জন্য নিম্নোক্ত ওটিপি ব্যবহার করুন। ওটিপি: ' . $otp . 'ধন্যবাদ।';
                $m = str_replace(' ', '%20', $message);
                $mobile = $user->mobile_no;
                $this->send_sms($mobile, $m);
                $user_id = ['user_id' => $user->id];
                return $this->sendResponse($user_id, 'সিস্টেমে পাসওয়ার্ড রিসেট সম্পন্ন করার জন্য নিম্নোক্ত ওটিপি ব্যবহার করুন। ওটিপি:');
            }
        } else {
            return  $this->sendError('আপনার তথ্য পাওয়া যায়নি', ['error' => 'আপনার তথ্য পাওয়া যায়নি'], 200);
        }
    }

    public function mobile_first_password_match(Request $request)
    {

        $validator = Validator::make(
            $request->all(),
            [
                'password' => 'min:6|required_with:confirm_password|same:confirm_password',
                'confirm_password' => 'min:6',
            ],
            [
                'password.required_with' => 'উভয় ক্ষেত্রে সঠিক পাসওয়ার্ড লিখুন ৬ সংখ্যার বেশি হতে হবে',
                'password.min' => 'উভয় ক্ষেত্রে সঠিক পাসওয়ার্ড লিখুন ৬ সংখ্যার বেশি হতে হবে',
                'password.same' => 'উভয় ক্ষেত্রে একই পাসওয়ার্ড লিখুন',
                'confirm_password.min' => 'উভয় ক্ষেত্রে সঠিক পাসওয়ার্ড লিখুন, ৬ সংখ্যার বেশি হতে হবে',
            ],
        );

        if ($validator->fails()) {

            return  $this->sendError($validator->errors()->first(), ['error' => $validator->errors()->first()], 200);
        }


        $result = User::where('id', $request->user_id)
            ->first();

        if (empty($result)) {
            return  $this->sendError('আপনার তথ্য পাওয়া যায়নি', ['error' => 'আপনার তথ্য পাওয়া যায়নি'], 200);
        } else {

            User::where('id', $request->user_id)->update(['password' => Hash::make($request->password)]);

            return $this->sendResponse(null, 'পাসওয়ার্ড হালনাগাদ হয়েছে');
        }
    }
    public function update_passworddsgsfgdf(Request $request)
    {

        $userid = Auth::guard('api')->user()->id;

        $validator = Validator::make($request->all(), [
            'current_password' => ['required'],
            'new_password' => ['required', 'min:6'],
            'new_confirm_password' => ['same:new_password'],
        ]);
        if ($validator->fails()) {
            $arr = $this->sendError($validator->errors()->first(), ['error' => $validator->errors()->first()]);
        } else {
            try {
                if (Hash::check(request('current_password'), Auth::user()->password) == false) {
                    $arr = $this->sendError('Check your old password.', ['error' => 'Your old password not match.']);
                } elseif (Hash::check(request('new_password'), Auth::user()->password) == true) {
                    $arr = $this->sendError('Make sure your current password does not match your new password.', ['error' => 'Please enter a new password which is not similar then current password.']);
                } else {
                    User::where('id', $userid)->update(['password' => Hash::make($request->new_password)]);
                    return $this->sendResponse(null, 'Password updated successfully.');
                }
            } catch (\Exception $ex) {
                if (isset($ex->errorInfo[2])) {
                    $msg = $ex->errorInfo[2];
                } else {
                    $msg = $ex->getMessage();
                }
                $arr = $this->sendError($msg, ['error' => $msg]);
            }
        }
        return $arr;
    }



    public function new_nid_verify_mobile_reg_first(Request $request, NIDVerificationRepository $nidVerificationRepository)
    {
        $fields_message = [
            'nid_number' => 'জাতীয় পরিচয় পত্র দিতে হবে',
            'dob_number' => 'জাতীয় পরিচয় পত্র অনুযায়ী জন্ম তারিখ দিতে হবে',
        ];
        $message = '';
        foreach ($fields_message as $key => $value) {
            if (empty($request->$key)) {
                $message .= $value . ' ,';
            }
        }
        if ($message != '') {
            // return response()->json([
            //     'success' => 'error',
            //     'message' => $message,
            // ]);
            return response()->json([
                'success' => false,
                'message' => $message,
                "err_res" => $message,
                "status" => 200,
                "data" => null
            ]);
        }

        $dob_in_db = str_replace('/', '-', $request->dob_number);
        if (pull_from_api_not_local_dummi()) {


            return $nidVerificationRepository->new_nid_verify_mobile_reg_first_api_call($request);
        } else {
            $Nid_information = DB::table('dummy_nids')
                ->where('national_id', '=', $request->nid_number)
                ->where('dob', '=', $dob_in_db)
                ->first();
            $get_additional_info_citizen = CitizenNIDVerifyRepository::getAdditionalInfoFromCitizen($request);
            // dd($get_additional_info_citizen);
            if (!empty($Nid_information)) {
                $data = array(
                    'name_bn' => $Nid_information->name_bn,
                    'father' => $Nid_information->father,
                    'mother' => $Nid_information->mother,
                    'national_id' => $Nid_information->national_id,
                    'gender' => $Nid_information->gender,
                    'present_address' => $Nid_information->present_address,
                    'permanent_address' => $Nid_information->permanent_address,
                    'dob' => $request->dob_number,
                    'email' => $get_additional_info_citizen['email'],
                    'designation' => $get_additional_info_citizen['designation'],
                    'organization' => $get_additional_info_citizen['organization'],
                    'organization_id' => $get_additional_info_citizen['organization_id'],
                );
                return $this->sendResponse($data, 'এন আই ডি তে সফলভাবে তথ্য পাওয়া গিয়েছে');
            } else {
                // return response()->json([
                //     'success' => 'error',
                //     'message' => 'কোন তথ্য খুজে পাওয়া যায় নাই',
                // ]);
                return response()->json([
                    'success' => false,
                    'message' => 'কোন তথ্য খুজে পাওয়া যায় নাই',
                    "err_res" => 'কোন তথ্য খুজে পাওয়া যায় নাই',
                    "status" => 200,
                    "data" => null
                ]);
            }
        }
    }
    public function verify_account_mobile_reg_first(Request $request)
    {
        $fields_message = [
            'citizen_nid' => 'জাতীয় পরিচয় পত্র দিতে হবে',
            'name' => 'জাতীয় পরিচয় পত্র অনুযায়ী বাংলাতে নাম দিতে হবে',
            'father' => 'জাতীয় পরিচয় পত্র অনুযায়ী বাংলাতে পিতার নাম দিতে হবে',
            'mother' => 'জাতীয় পরিচয় পত্র অনুযায়ী বাংলাতে মাতার নাম দিতে হবে',
            'dob' => 'জাতীয় পরিচয় পত্র অনুযায়ী জন্ম তারিখ দিতে হবে',
            'citizen_gender' => 'লিঙ্গ দিতে হবে',
            'permanentAddress' => 'জাতীয় পরিচয় পত্র অনুযায়ী স্থায়ী ঠিকানা দিতে হবে',
            'presentAddress' => 'জাতীয় পরিচয় পত্র অনুযায়ী বর্তমান ঠিকানা দিতে হবে',
        ];
        $message = '';
        foreach ($fields_message as $key => $value) {
            if (empty($request->$key)) {
                $message .= $value . ' ,';
            }
        }

        $exits_user_by_nid = DB::table('users')->where('citizen_nid', $request->citizen_nid)->first();
        if (!empty($exits_user_by_nid)) {
            $message .=  'জাতীয় পরিচয় পত্র ' . $request->citizen_nid . ' দিয়ে ইতিমধ্যে ' . $exits_user_by_nid->mobile_no . ' এর সাথে নিবন্ধিত করা হয়েছে';
        }

        if ($message != '') {
            return response()->json([
                'success' => false,
                'message' => $message,
                "err_res" => $message,
                "status" => 200,
                "data" => null
            ]);
        }

        CitizenNIDVerifyRepository::verify_citizen_by_nid($request);

        // return response()->json([
        //     'success' => 'success',
        //     'message' => 'সফলভাবে আপনার প্রোফাইল সত্যায়িত হয়েছে',
        // ]);
        return $this->sendResponse(null, 'সফলভাবে আপনার প্রোফাইল সত্যায়িত হয়েছে');
    }

    public function send_sms_old($to, $message)
    {


        $mobile = $to;
        // dd($mobile);exit;
        $token = $this->get_token();
        $curl = curl_init();
        $m = curl_escape($curl, $message);
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://si.mysoftheaven.com/api/v1/sms?to=' . $mobile . '&message=' . $m,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            // CURLOPT_HTTPHEADER => array(
            //     $token
            // ),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Bearer' . $token
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }

    public function get_token()
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://si.mysoftheaven.com/api/v1/login',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array('email' => 'a2i@gmail.com', 'password' => 'mhl!a2i@2041', 'api_secret' => '2qwertyudfcvgbhn'),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }

    public function send_sms($to, $message)
    {
        $curl = curl_init();
        $new_message = curl_escape($curl, $message);
        $newto = '88' . $to;
        $url = 'http://103.69.149.50/api/v2/SendSMS?SenderId=8809617612638&Is_Unicode=true&ClientId=ec63aede-1c7e-4a5a-a1ad-36b72ab30817&ApiKey=AeHZPUEZXIILtxg0VEaGjsK%2BuPNlzhCDW0VuFRmcchs%3D&Message=' . $new_message . '&MobileNumbers=' . $newto;
        // dd($url);
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }

    public function userdelete(Request $request)
    {

        $citizen_id =  DB::table('users')
            ->where('id', $request->user_id)
            ->first();

        if (!empty($citizen_id)) {
            DB::table('em_appeal_citizens')
                ->where('citizen_id', $citizen_id->citizen_id)
                ->delete();
            DB::table('em_citizens')
                ->where('id', $citizen_id->citizen_id)
                ->delete();
            DB::table('em_appeals')
                ->where('id', $request->user_id)
                ->delete();
            DB::table('users')
                ->where('id', $request->user_id)
                ->delete();
            return $this->sendResponse(null, 'successfully delete');
        } else {
            return $this->sendError(null, 'your information not found');
        }
    }
}
