
@extends('layouts.default')

@section('content')

<style type="text/css">
   .tg {border-collapse:collapse;border-spacing:0;width: 100%;}
   .tg td{border-color:black;border-style:solid;border-width:1px;font-size:14px;overflow:hidden;padding:6px 5px;word-break:normal;}
   .tg th{border-color:black;border-style:solid;border-width:1px;font-size:14px;font-weight:normal;overflow:hidden;padding:6px 5px;word-break:normal;}
   .tg .tg-nluh{background-color:#dae8fc;border-color:#cbcefb;text-align:left;vertical-align:top}
   .tg .tg-19u4{background-color:#ecf4ff;border-color:#cbcefb;font-weight:bold;text-align:right;vertical-align:top}
</style>

<!--begin::Card-->
<div class="card card-custom">
   <div class="card-header flex-wrap py-5">
      {{-- <div class="card-title"> --}}
          <div class="container">
              <div class="row">
                  <div class="col-10"><h3 class="card-title h2 font-weight-bolder">{{ $page_title }}</h3></div>
                  {{-- <div class="col-8">fdsafsad</div> --}}
                  {{-- <div class="col-2"><a href="{{ route('messages_group') }}" class="btn btn-primary float-right">Message</a></div> --}}
                
              </div>
          </div>
         {{-- <h3 class="card-title h2 font-weight-bolder">{{ $page_title }}</h3>

         <table>
             <tr align="right">
                 <th>
                     <a  href="" class="btn btn-primary float-right">Message</a>

                 </th>
             </tr>
         </table> --}}
      {{-- </div> --}}
    
   </div>
   <div class="card-body">
      @if ($message = Session::get('success'))
      <div class="alert alert-success">
         {{ $message }}
      </div>
      @endif
      <div class="row">
        <div class="col-md-12" style="font-weight: bold">
            <h2>সাধারণ তথ্য</h2>
        </div>
      </div>
    <div class="row">
        <div class="col-md-6">
            <table class="table table-striped border">
                <thead>
                    
                </thead>
               <tbody>
                {{-- @dd($case_details) --}}
                <tr>
                    <th scope="row">মামলা নং</th>
                    <td >{{ en2bn($case_details->caseNo) ?? '-'}}</td>
                 </tr>
                <tr>
                    <th scope="row">মামলার ধারা</th>
                    <td >{{ $case_details->lawSectionDetails ?? '-'}}</td>
                 </tr>
                 <tr>
                    <th scope="row">আবেদনের তারিখ</th>
                    <td >{{ en2bn($case_details->caseDate) ?? '-'}}</td>
                 </tr>
                 <tr>
                    <th scope="row">আবেদনকারীর নাম</th>
                    <td >{{ $case_details->applicantName ?? '-'}}</td>
                 </tr>
                 <tr>
                    <th scope="row">বাদীর নাম</th>
                    <td >{{ $case_details->defaulterName ?? '-'}}</td>
                 </tr>
                  
                 
                  
                </tbody>
            </table>
            
          
            
        </div>

      <div class="col-md-6">
        <table class="table table-striped border">
            <thead>
                
            </thead>
           <tbody>

            <tr>
                <th scope="row">আদালত</th>
                <td >@php
                    if (isset($case_details->court_id)) {
                        echo DB::table('court')
                            ->where('id', $case_details->court_id)
                            ->first()->court_name;
                    }
                @endphp</td>
             </tr>
            
             <tr>
                <th scope="row">পরবর্তী তারিখ</th>
                <td >{{ en2bn($case_details->next_date) ?? '-'}}</td>
             </tr>
             @if (!empty($case_details->order_attached_file))
                <tr>
                    <th scope="row">আদেশ এর সংযুক্তি</th>
                    <td >
                        <a href="{{asset('/archive_attached_file/'. $case_details->order_attached_file)}}" target="_blank" class="btn btn-sm btn-success font-size-h5 float-left">
                            <i class="fa fas fa-file-pdf"></i>
                            <b>দেখুন</b>
                            
                        </a>
                    </td>
                </tr>

                <tr>
                    <th scope="row">সকল সংযুক্তি</th>
                    <td >
                        <a href="{{route('appeal.generate.pdf', encrypt($case_details->id))}}" target="_blank" class="btn btn-sm btn-success font-size-h5 float-left">
                            <i class="fa fas fa-file-pdf"></i>
                            <b>দেখুন</b>
                            
                        </a>
                    </td>
                </tr>
             @endif
               
               <tr><td>আদেশ সমূহ </td><td>সর্বশেষ আদেশ এর তারিখ</td></tr>
                
               @foreach($orderlist as $list)
               <tr><td>{{ $list->short_order_name}}</td><td>{{ $list->last_order_date}}</td></tr>
               @endforeach


             
              
            </tbody>
        </table>
           
      </div>
      
      
      {{-- <div class="col-md-12">
        <table class="table table-striped border">
           <thead>
               <th class="h3" scope="col" colspan="2">সংযুক্তি</th>
               
           </thead>
          <tbody>
             <tr>
                <td>
                   @forelse ($attachmentList as $key => $row)
                         <div class="form-group mb-2" id="deleteFile{{ $row->id }}">
                             <div class="input-group">
                                 <div class="input-group-prepend">
                                     <button class="btn bg-success-o-75" type="button">{{ en2bn(++$key) . ' - নম্বর :' }}</button>
                                 </div>
                                 
                                 <input readonly type="text" class="form-control" value="{{ $row->file_category ?? '' }}" />
                                 <div class="input-group-append">
                                     <a href="{{ asset($row->file_path . $row->file_headline) }}" target="_blank" class="btn btn-sm btn-success font-size-h5 float-left">
                                         <i class="fa fas fa-file-pdf"></i>
                                         <b>দেখুন</b>
                                         
                                      </a>
                                    
                                 </div>
                                 
                             </div>
                         </div>
                    @empty
                     <div class="pt-5">
                         <p class="text-center font-weight-normal font-size-lg">কোনো সংযুক্তি খুঁজে পাওয়া যায়নি</p>
                     </div>
                   @endforelse
                </td>
             </tr>
          </tbody>
       </table> 
     </div> --}}

    </div>
    <br>
   
   <br>


  
<!--end::Card-->

@endsection

{{-- Includable CSS Related Page --}}
@section('styles')
<link href="{{ asset('plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
<!--end::Page Vendors Styles-->
@endsection

{{-- Scripts Section Related Page--}}
@section('scripts')
<script src="{{ asset('plugins/custom/datatables/datatables.bundle.js') }}"></script>
<script src="{{ asset('js/pages/crud/datatables/advanced/multiple-controls.js') }}"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"
    integrity="sha512-GsLlZN/3F2ErC5ifS5QtgpiJtWd43JWSuIgh7mbzZ8zBps+dvLusV+eNQATqgA/HdeKFVgA5v3S/cIrLF7QnIg=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
    function generatePDF() {
        
        var element = document.getElementById('element-to-print');
    
        var opt = {
            margin: 1,
            filename: 'myfile.pdf',
            pagebreak: {
                avoid: ['tr', 'td']
            },
            image: {
                type: 'pdf',
                quality: 0.98
            },
            html2canvas: {
                scale: 2
            },
        };

        // New Promise-based usage:
        html2pdf().set(opt).from(element).save();
    }
</script>
<!--end::Page Scripts-->
@endsection



