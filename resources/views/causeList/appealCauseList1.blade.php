@extends('layouts.landing')

@section('landing')
@if(Auth::user())
@section('content')
@else

@section('landing')
@endif
<style type="text/css">
    fieldset {
        border: 1px solid #ddd !important;
        margin: 0;
        xmin-width: 0;
        padding: 10px;
        position: relative;
        border-radius: 4px;
        background-color: #d5f7d5;
        padding-left: 10px !important;
    }

        fieldset .form-label {
            color: black;
        }

        legend {
            font-size: 14px;
            font-weight: bold;
            width: 45%;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 5px 5px 5px 10px;
            background-color: #5cb85c;
        }
    </style>
    <!--begin::Card-->
    <div class="container" style="margin-top: 50px">
        <div class="row">
            <div class="col-md-12">

                <div class="card card-custom">
                    <!-- <div class="card-header flex-wrap py-5">
                                  <div class="card-title">
                                  </div>
                                  
                               </div> -->
                    <div class="card-body overflow-auto">

                        <fieldset class="mb-6"
                            style="background-image: url({{ asset('images/causlist.png') }}); padding-top: 26px">
                            <!-- <legend >ফিল্টারিং ফিল্ড সমূহ</legend> -->
                            @include('causeList.inc.search')
                        </fieldset>
                        <table class="table mb-6 font-size-h5 caulist-table">
                            <thead class="thead-customStyle2 font-size-h6 text-center">
                                <tr>
                                    <h1 class="text-center mt-15" style="color: #371c7e; font-weight: 600;">এক্সিকিউটিভ
                                        ম্যাজিস্ট্রেট কোর্ট
                                    </h1>
                                    <h2 class="text-center" style="color: #371c7e; font-weight: 600">দৈনিক কার্যতালিকা</h2>
                                    @if ($division_name != null)
                                        <h5 style="color: #371c7e;" class="text-center">বিভাগঃ {{ $division_name }} </h5>
                                    @endif
                                    @if ($district_name != null)
                                        <h5 style="color: #371c7e;" class="text-center">জেলাঃ {{ $district_name }} </h5>
                                    @endif
                                    @if ($court_name != null)
                                        <h5 style="color: #371c7e;" class="text-center">আদালতঃ {{ $court_name }} </h5>
                                    @endif
                                    @if ($dateFrom == $dateTo)
                                        <h5 style="color: #371c7e;" class="text-center mb-6">তারিখঃ {{ en2bn($dateFrom) }}
                                            খ্রিঃ</h5>
                                    @else
                                        <h3 style="color: #371c7e;" class="text-center mb-6">তারিখঃ {{ en2bn($dateFrom) }}
                                            হতে {{ en2bn($dateTo) }} খ্রিঃ</h3>
                                    @endif
                                </tr>
                            </thead>
                            <thead class="thead-customStyle2 font-size-h6 text-center">
                                <tr>
                                    <th scope="col">ক্রমিক নং</th>
                                    <th scope="col">মামলা নম্বর</th>
                                    <th scope="col">পক্ষ</th>
                                    <th scope="col">পরবর্তী তারিখ</th>
                                    <th scope="col">সর্বশেষ আদেশ</th>
                                </tr>
                            </thead>
                            {{-- @@dd(causelistdata) --}}
                            <?php
                            // echo '<pre>';
                            // print_r($coselist);
                            
                            // echo '<pre>';
                            // print_r($coselist);
                            ?>
                            @if (!empty($causelistdata))
                                @foreach ($causelistdata as $key => $item)
                                    <?php
                                    
                                    if ($item->type == 1) {
                                        $data = DB::table('em_appeal_citizens')
                                            ->join('em_citizens', 'em_citizens.id', 'em_appeal_citizens.citizen_id')
                                            ->where('em_appeal_citizens.appeal_id', $item->appealid)
                                            ->whereIn('em_appeal_citizens.citizen_type_id', [1, 2])
                                            ->select('em_appeal_citizens.citizen_type_id', 'em_citizens.citizen_name', 'em_citizens.id')
                                            ->get();
                                    
                                        $datalist = [
                                            'applicant_name' => $data[1]->citizen_name,
                                            'defaulter_name' => $data[0]->citizen_name,
                                        ];
                                        $nodedata = DB::table('em_notes_modified')
                                            ->join('em_case_shortdecisions', 'em_notes_modified.case_short_decision_id', 'em_case_shortdecisions.id')
                                            ->where('em_notes_modified.appeal_id', $item->appealid)
                                            ->select('em_notes_modified.conduct_date as conduct_date', 'em_case_shortdecisions.case_short_decision as short_order_name', 'em_notes_modified.manual_short_decision as manual_decision_name')
                                            ->orderBy('em_notes_modified.id', 'desc')
                                            ->first();
                                    }
                                    
                                    if ($item->type == 0) {
                                        $custom_notes = DB::table('causelist_order')
                                            ->where('causelist_id', $item->causelist_id)
                                            ->orderby('id', 'desc')
                                            ->first();
                                    }
                                    
                                    ?>
                                    <tbody>
                                        <tr class="text-center">
                                            <td scope="row">{{ en2bn($key + 1) }}</td>
                                            <td>
                                                {{ en2bn($item->caseno) }}

                                            </td>
                                            @if ($item->type == 1)
                                                <td>
                                                    {{ isset($datalist['applicant_name']) ? $datalist['applicant_name'] : '-' }}
                                                    <br> <b>vs</b><br>
                                                    {{ isset($datalist['defaulter_name']) ? $datalist['defaulter_name'] : '-' }}
                                                </td>
                                            @else
                                                <td>
                                                    {{ isset($item->applicantName) ? $item->applicantName : '-' }}
                                                    <br> <b>vs</b><br>
                                                    {{ isset($item->defaulterName) ? $item->defaulterName : '-' }}
                                                </td>
                                            @endif
                                            @if ($item->type == 1)
                                                @if ($item->appeal_status == 'ON_TRIAL' || $item->appeal_status == 'ON_TRIAL_DM')
                                                    @if (date('Y-m-d', strtotime(now())) == $item->next_date)
                                                        <td class="blink_me text-danger">
                                                            <span>*</span>{{ en2bn($item->next_date) }}<span>*</span>
                                                        </td>
                                                    @else
                                                        <td>{{ en2bn($item->next_date) }}</td>
                                                    @endif
                                                @else
                                                    <td class="text-danger">
                                                        {{ appeal_status_bng($item->appeal_status) }}
                                                    </td>
                                                @endif
                                            @else
                                                {{-- @dd($custom_notes->appeal_status) --}}
                                                @if ($custom_notes !== null && $custom_notes->appeal_status == 'ON_TRIAL')
                                                    {{-- @dd($custom_notes) --}}
                                                    @if (date('Y-m-d', strtotime(now())) == $custom_notes->next_date)
                                                        <td class="blink_me text-danger">
                                                            <span>*</span>{{ en2bn($custom_notes->next_date) }}<span>*</span>
                                                        </td>
                                                    @else
                                                        <td>{{ en2bn($custom_notes->next_date) }}</td>
                                                    @endif
                                                @else
                                                    @if ($custom_notes !== null)
                                                        <td class="text-danger text-center fw-bolder"
                                                            style="font-size:20px;font-weight: lighter;">
                                                            @if ($custom_notes->appeal_status == 'CLOSED')
                                                                {{ '------' }}
                                                            @else
                                                                {{ appeal_status_bng($custom_notes->appeal_status) }}
                                                            @endif
                                                        </td>
                                                    @endif
                                                @endif
                                            @endif


                                            <td class="text-center">
                                                @if ($item->type == 1)
                                                    @if ($nodedata->manual_decision_name)
                                                        {{ isset($nodedata->manual_decision_name) ? $nodedata->manual_decision_name : '' }}
                                                    @else
                                                        {{ isset($nodedata->short_order_name) ? $nodedata->short_order_name : ' ' }}
                                                    @endif
                                                @else
                                                    {{ isset($custom_notes->short_order_name) ? $custom_notes->short_order_name : ' ' }}
                                                @endif

                                            </td>
                                            {{-- @include('dashboard.citizen._lastorder') --}}
                                        </tr>
                                    </tbody>
                                @endforeach
                            @endif
                        </table>

                        <div class="d-flex justify-content-center">
                            {{ $causelistdata->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <!--end::Card-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.1/jquery.min.js"
        integrity="sha512-aVKKRRi/Q/YV+4mjoKBsE4x3H+BkegoM/em46NNlCqNTmUYADjBbeNefNxYV7giUp0VxICtqdrbqU7iVaeZNXA=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script>
        var maximum_page = {
            {
                $total_page
            }
        };

        $('.paginate').on('click', function() {

            //alert($(this).data('paginate'));
            $('.paginate').each(function() {
                $(this).removeClass('btn-primary active');
                $(this).addClass('btn-outline-primary');
            });

            $(this).removeClass('btn-outline-primary');
            $(this).addClass('btn-primary active');

            var page_no = $(this).data('paginate');
            $('#landin_page_causelist_search_form_offset').val(page_no);
            $('#landin_page_causelist_search_form').submit();
        });

        $('.next').on('click', function() {
            var page_no_next = 0;
            $('.paginate').each(function(index, el) {

                if ($(this).hasClass('btn-primary')) {

                    page_no_next = $(this).data('paginate') + 1;

                    if (page_no_next <= maximum_page) {
                        $(this).removeClass('btn-primary active');
                        $(this).addClass('btn-outline-primary');
                    }

                    if (page_no_next <= maximum_page) {

                        $('#landin_page_causelist_search_form_offset').val(page_no_next);
                        $('#landin_page_causelist_search_form').submit();
                    }


                }
            });
            if (page_no_next <= maximum_page) {

                $('#paginate_id_' + page_no_next).removeClass('btn-outline-primary');
                $('#paginate_id_' + page_no_next).addClass('btn-primary active');
            }
        });
        $('.previous').on('click', function() {

            var page_no_previous = 0;

            $('.paginate').each(function(index, el) {


                if ($(this).hasClass('btn-primary')) {

                    page_no_previous = $(this).data('paginate') - 1;

                    if (page_no_previous >= 1) {
                        $(this).removeClass('btn-primary active');
                        $(this).addClass('btn-outline-primary');
                    }
                    if (page_no_previous >= 1) {
                        $('#landin_page_causelist_search_form_offset').val(page_no_previous);
                        $('#landin_page_causelist_search_form').submit();
                    }


                }
            });

            if (page_no_previous >= 1) {
                $('#paginate_id_' + page_no_previous).removeClass('btn-outline-primary');
                $('#paginate_id_' + page_no_previous).addClass('btn-primary active');
            }



        })
    </script>

@endsection

{{-- Includable CSS Related Page --}}
@section('styles')
    <!-- <link href="{{ asset('plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" /> -->
    <!--end::Page Vendors Styles-->
@endsection

{{-- Scripts Section Related Page --}}
@section('scripts')
    <!-- <script src="{{ asset('plugins/custom/datatables/datatables.bundle.js') }}"></script>
                   <script src="{{ asset('js/pages/crud/datatables/advanced/multiple-controls.js') }}"></script>
                 -->
    <!--end::Page Scripts-->
@endsection
