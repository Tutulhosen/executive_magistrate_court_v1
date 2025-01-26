@extends('layouts.landing')

@section('content')
    <!--begin::Row-->
    <div class="row">

        <div class="col-md-12">
            <!--begin::Card-->
            <div class="card card-custom gutter-b example example-compact">
                <div class="card-header">
                    <h3 class="card-title h2 font-weight-bolder">{{ $page_title }}</h3>
                </div>



                <!-- <div class="loadersmall"></div> -->
                {{--  @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif --}}
                @if (Session::has('withError'))
                    <div class="alert alert-danger text-center">
                        {{ Session::get('withError') }}
                    </div>
                @endif
                <!--begin::Form-->
                <form id="archiveCase" action="{{ route('appeal.old.dismiss.case.store') }}" class="form" method="POST"
                    enctype="multipart/form-data">
                    @csrf

                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="caseNo" class="control-label"><span style="color:#FF0000">*
                                            </span>মামলা নম্বর</label>
                                        <input name="caseNo" id="caseNo"
                                            class="form-control form-control-sm @error('caseNo') is-invalid @enderror"
                                            {{-- required --}} />
                                        @error('caseNo')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label><span class="text-danger">*</span>বাদী</label>
                                        <input type="text" name="applicantName" id="applicantName"
                                            class="form-control form-control-sm  @error('applicantName') is-invalid @enderror"
                                            placeholder="আবেদনকারী" autocomplete="off" {{-- required --}}>
                                        @error('applicantName')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label><span class="text-danger">*</span>বিবাদী</label>
                                        <input type="text" name="defaulterName" id="defaulterName"
                                            class="form-control form-control-sm  @error('defaulterName') is-invalid @enderror"
                                            placeholder="বিবাদী" autocomplete="off" {{-- required --}}>
                                        @error('defaulterName')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label><span class="text-danger">*</span> আবেদনের তারিখ</label>
                                        <input type="text" name="caseDate" id="case_date"
                                            class="form-control form-control-sm common_datepicker @error('caseDate') is-invalid @enderror"
                                            placeholder="দিন/মাস/তারিখ" autocomplete="off" {{-- required --}}>
                                        @error('caseDate')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group" id="victim">
                                        <label for="lawSection" class="control-label">অভিযোগের ধরণ <span
                                                class="text-danger">*</span><span id="link"></span></label>
                                        <select
                                            class="form-control crpc_select_law_section_adm_em @error('lawSection') is-invalid @enderror"
                                            id="kt" name="lawSection" data-placeholder="-- নির্বাচন করুন --">
                                            <option value=""> -- নির্বাচন করুন --</option>
                                            @foreach ($law_details as $value)
                                                <option law_section="{{ $value->crpc_id }}" value="{{ $value->id }}"
                                                    {{ old('lawSection') == $value->crpc_id ? 'selected' : '' }}>
                                                    {{ $value->crpc_name }} </option>
                                            @endforeach
                                        </select>
                                        @error('lawSection')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-5">

                                <div class="col-lg-4 mb-5">
                                    <div class="form-group">
                                        <label>বিভাগ নির্বাচন<span class="text-danger">*</span></label>

                                        <select class="form-control" aria-label=" example" name="div_section"
                                            id="div_section" {{-- required --}}>

                                            <option value="{{ $court_info->div_id }}" selected>{{ $court_info->div_name }}
                                            </option>

                                        </select>
                                        @error('div_section')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-lg-4 mb-5">
                                    <div class="form-group">
                                        <label>জেলা নির্বাচন করুন <span class="text-danger">*</span></label>

                                        <select class="form-control dis_section" aria-label="" name="dis_section"
                                            id="dis_section" {{-- required --}}>
                                            <option value="{{ $court_info->dis_id }}">{{ $court_info->dis_name }}</option>
                                        </select>
                                        @error('dis_section')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                </div>
                                <div class="col-lg-4 mb-5">
                                    <div class="form-group">
                                        <label>উপজেলা নির্বাচন করুন <span class="text-danger">*</span></label>
                                        <select class="form-control" aria-label=".form-select-lg example" name="upa_section"
                                            id="upazila_id" {{-- required --}}>
                                            <option value="">উপজেলা নির্বাচন করুন </option>

                                            @foreach ($upa_info as $key => $single_upa_info)
                                                <option value="{{ $single_upa_info->upa_id }}">
                                                    {{ $single_upa_info->upa_name }}</option>
                                            @endforeach

                                        </select>
                                        @error('upa_section')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                </div>


                            </div>

                            {{-- <div class="row mb-5">
                            
                      
    
                            <div class="col-lg-3 mb-5">
                                <div class="form-group">
                                    <label> প্রতিষ্ঠানের নাম <span class="text-danger">*</span></label>
                                    <input type="text" name="org_name" id="org_name" class="form-control" required>
                                </div>
                                
                            </div>

                            <div class="col-lg-3 mb-5">
                                <div class="form-group">
                                    <label> রাউটিং নং (ইংরেজিতে)<span class="text-danger">*</span></label>
                                    <input type="text" name="organization_id" id="organization_id"
                                        class="form-control form-control-sm only_english" required
                                        value="{{ old('organization_id') }}">
        
                                    @error('organization_id')
                                        <div class="alert alert-danger"> রাউটিং নং ইংরেজিতে দিন</div>
                                    @enderror
                                </div>
                                
                            </div>

                            <div class="col-lg-3 mb-5">
                                <div class="form-group">
                                    <label>প্রতিষ্ঠান প্রতিনিধির নাম (ইংরেজিতে)<span
                                        class="text-danger">*</span></label>
                                        <input type="text" name="organization_employee" {{ old('organization_employee') }} id="organization_employee" class="form-control" required autocomplete="off">
                                    
                                </div>
                                
                            </div>

                            <div class="col-lg-3 mb-5">
                                <div class="form-group">
                                    <label> পদবী <span class="text-danger">*</span></label>
                                    <input type="text" name="designation" id="designation"
                                        class="form-control form-control-sm" value="{{ old('designation') }}" required>
                                </div>
                                
                            </div>
                            
    
                        </div> --}}



                            {{-- <div class="row">
                    
                            <div class="col-md-6">
                                <div class="form-group">
                                   
                                    <label for="totalLoanAmount" class="control-label"><span style="color:#FF0000">*
                                        </span>দাবিকৃত অর্থের পরিমাণ</label>
                                    <input type="text" name="totalLoanAmount" id="totalLoanAmount"
                                        class="form-control form-control-sm input_bangla" required>
                                        
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="totalLoanAmountText" class="control-label"><span style="color:#FF0000">*
                                    </span>দাবিকৃত অর্থের পরিমাণ
                                        (কথায়)</label>
                                    <input readonly="readonly" type="text" name="totalLoanAmountText"
                                        id="totalLoanAmountText" class="form-control form-control-sm">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                           
                            <div class="col-md-6">
                                <div class="form-group">
                                   
                                    <label for="totalcollectAmount" class="control-label"><span style="color:#FF0000">*
                                        </span> আদায়কৃত অর্থের পরিমাণ</label>
                                    <input type="text" name="totalcollectAmount" id="totalcollectAmount"
                                        class="form-control form-control-sm input_bangla" required>
                                        
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="totalcollectAmountText" class="control-label"><span style="color:#FF0000">*
                                    </span>আদায়কৃত অর্থের পরিমাণ
                                        (কথায়)</label>
                                    <input readonly="readonly" type="text" name="totalcollectAmountText"
                                        id="totalcollectAmountText" class="form-control form-control-sm">
                                </div>
                            </div>
                        </div> --}}

                            <div class="row">
                              <div class="col-md-6">
                                <div class="form-group">
                                        <label for="caseJudgmentNote" class="control-label"><span style="color:#FF0000">*
                                        </span>মামালার রায়</label>
                                        <input  type="text" name="caseJudgmentNote"
                                            id="caseJudgmentNote" class="form-control form-control-sm  @error('caseJudgmentNote') is-invalid @enderror">
                                        @error('caseJudgmentNote')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror    
                                    </div>
                              </div>
                            </div>
                           
                            <div class="row">

                                <div class="col-md-6">
                                    <div class="form-group">

                                        <label><span class="text-danger">*</span> সর্বশেষ আদেশ এর তারিখ </label>
                                        <input type="text" name="lastorderDate" id="lastorderDate"
                                            class="form-control form-control-sm common_datepicker" placeholder="সাল/মাস/দিন"
                                            autocomplete="off" {{-- required --}}>
                                        @error('lastorderDate')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="" class="control-label @error('attached_file') is-invalid @enderror"><span style="color:#FF0000">*
                                            </span>আদেশ এর সংযুক্তি </label>
                                        <input type="file" name="attached_file" class="form-control">
                                        @error('attached_file')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>


                            <fieldset class=" mb-8">
                                <div
                                    class="rounded bg-success-100 d-flex align-items-center justify-content-between flex-wrap px-5 py-0">
                                    <div class="d-flex align-items-center mr-2 py-2">
                                        <h3 class="mb-0 mr-8">
                                            অন্যান্য সংযুক্তি <span class="text-danger">*</span></h3>
                                    </div>
                                    <!--end::Info-->
                                    <!--begin::Users-->
                                    <div class="symbol-group symbol-hover py-2">
                                        <div class="symbol symbol-30 symbol-light-primary" data-toggle="tooltip"
                                            data-placement="top" title="" role="button"
                                            data-original-title="Add New File">
                                            <div id="addFileRow">
                                                <span class="symbol-label font-weight-bold bg-success">
                                                    <i class="text-white fa flaticon2-plus font-size-sm"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <!--end::Users-->
                                </div>
                                <div class="mt-3 px-5">
                                    <table width="100%" class="border-0 px-5" id="fileDiv"
                                        style="border:1px solid #dcd8d8;">
                                        <tr></tr>
                                    </table>
                                    <input type="hidden" id="other_attachment_count" value="1"
                                        name="other_attachment_count[]">
                                </div>
                            </fieldset>

                            <div class="row buttonsDiv">
                                <div class="col-md-12 text-center">
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary" data-toggle="modal"
                                            data-target="">
                                            সংরক্ষণ
                                        </button>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>


                    <!--end::Card-body-->
                </form>
            </div>
        </div>

    </div>
@endsection

@section('styles')
@endsection
@section('scripts')
    <script src="{{ asset('js/number2banglaWord.js') }}"></script>
    @include('citizenAppealInitiate.appealCreate_Js')
@endsection
@section('scripts')
    @include('appealTrial.inc._script')
@endsection
