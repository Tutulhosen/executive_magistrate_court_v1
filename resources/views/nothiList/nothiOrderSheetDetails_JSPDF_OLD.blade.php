@extends('layouts.landing')
@section('content')
    <div class="card card-custom">
        <div class="card-header flex-wrap py-5">
            <div class="container">
                <div class="row">
                    <div class="col-10">
                        <h3 class="card-title h2 font-weight-bolder">{{ $page_title }}</h3>
                    </div>
                    {{-- <div class="col-2">
                    @if (Auth::user()->role_id == 2)
                    <a href="{{ route('messages_group') }}?c={{ $appeal->id }}" class="btn btn-primary float-right">বার্তা</a>
                        @endif
                </div> --}}
                    <a href="javascript:generatePDF()" class="button">Generate PDF</a>
                </div>
            </div>
        </div>

        <div class="card-body" id="fromHTMLtestdiv">
            <?php foreach($appealOrderLists as $key=>$row){?>
            <div class="contentForm" style="font-size: medium;">
                <?php if($key == 0){?>
                <div id="head">
                    <?php echo nl2br($row->order_header); ?>
                </div>
                <?php } ?>
                <?php }?>

                <div id="body" style="overflow: hidden;">
                    <table cellspacing="0" cellpadding="0" border="1" width="100%">
                        <thead>
                            <tr>
                                <td valign="middle" width="5%" align="center"> আদেশের ক্রমিক নং </td>
                                <td valign="middle" width="10%" align="center"> তারিখ</td>
                                <td valign="middle" width="75%" align="center"> আদেশ </td>
                                <td valign="middle" width="10%" align="center"> স্বাক্ষর</td>
                            </tr>
                        </thead>
                        <?php foreach($appealOrderLists as $key=>$row){?>
                        <tbody>
                            <?php echo $row->order_detail_table_body; ?>
                        </tbody>
                        <?php }?>
                    </table>
                </div>
                <h3 id="rayNamaHeading" style="text-align: center;"></h3>
                <div id="rayHeadAppealNama" class="ray-head"></div>
                <div id="rayBodyAppealNama" class="ray-body"></div>
            </div>

        </div>

    </div>
    {{-- <div id="appealNamaTemplate" style="display: none; ">
        @include('reports.appealNama')
    </div>
    <div id="appealRayTemplate" style="display: none; ">
        @include('reports.rayNama')
    </div>

    <div id="appealShortOrderTemplate" style="display: none; ">
        @include('ShortOrderTemplate.shortOrderTemplateView')
    </div> --}}
@endsection

@section('scripts')
    <script src="{{ asset('plugins/custom/jsPDF/dist/jspdf.debug.js') }}"></script>
    <script src="{{ asset('plugins/custom/jsPDF/libs/html2pdf.js') }}"></script>
    <script>
        function generatePDF() {
            var pdf = new jsPDF('p', 'pt', 'letter')

                // source can be HTML-formatted string, or a reference
                // to an actual DOM element from which the text will be scraped.
                ,
                source = $('#fromHTMLtestdiv')[0]

                // we support special element handlers. Register them with jQuery-style
                // ID selector for either ID or node name. ("#iAmID", "div", "span" etc.)
                // There is no support for any other type of selectors
                // (class, of compound) at this time.
                ,
                specialElementHandlers = {
                    // element with id of "bypass" - jQuery style selector
                    '#bypassme': function(element, renderer) {
                        // true = "handled elsewhere, bypass text extraction"
                        return true
                    }
                }

            margins = {
                top: 80,
                bottom: 60,
                left: 40,
                width: 522
            };

            // define custom font

            pdf.addFileToVFS("Nikosh.ttf", font);
            // ttf font file converted to base64
            // following is Consolas with only hex digit glyphs defined (0-9, A-F)

            // all coords and widths are in jsPDF instance's declared units
            // 'inches' in this case
            pdf.addFont("Nikosh-normal.ttf", "Nikosh", "normal");
            // pdf.setFont("Nikosh","Bold");
            pdf.setFontSize(12);
            pdf.setFont("Nikosh");

            // doc.addFileToVFS("MyFont.ttf", myFont);
            // doc.addFont("MyFont.ttf", "MyFont", "normal");
            // doc.setFont("MyFont");


            pdf.fromHTML(
                source // HTML string or DOM elem ref.
                , margins.left // x coord
                , margins.top // y coord
                , {
                    'width': margins.width // max width of content on PDF
                        ,
                    'elementHandlers': specialElementHandlers
                },
                function(dispose) {
                    // dispose: object with X, Y of the last line add to the PDF
                    //          this allow the insertion of new lines after html
                    pdf.save('Test.pdf');
                },
                margins
            )
        }
    </script>
@endsection

@section('jsComponent')
    <script src="{{ asset('js/appeal/appeal-ui-utils.js') }}"></script>
    <script src="{{ asset('js/appeal/appealPopulate.js') }}"></script>
    {{-- <script src="{{ asset('js/initiate/ .js') }}"></script> --}}
    <script src="{{ asset('js/reports/appealNama.js') }}"></script>
    <script src="{{ asset('js/reports/rayNama.js') }}"></script>
    <script src="{{ asset('js/shortOrderTemplate/shortOrderTemplate.js') }}"></script>
    <script src="{{ asset('js/englishToBangla/convertEngToBangla.js') }}"></script>
    <script>
        appealNama = module.exports = {
            getAppealOrderListsInfo: function(appealId) {
                return $.ajax({
                    headers: {
                        'X-CSRF-Token': appealPopulate.token
                    },
                    url: '/appeal/get/appealnama',
                    method: "post",
                    data: {
                        appealId: appealId
                    },
                    dataType: 'json'
                });
            },
            printAppealNama: function() {
                var appealNamaContent = '',
                    rayNamaContent = '',
                    rayHead = '',
                    rayBody = '';
                var appealId = $('#appealId').val();
                appealNama.getAppealOrderListsInfo(appealId).done(function(response, textStatus, jqXHR) {

                    if (response.appealOrderLists.length > 0) {

                        appealNamaContent = appealNama.getAppealNamaReport(response);

                        $('#head').empty();
                        $('#body').empty();

                        $('#head').append(appealNamaContent.header);
                        $('#body').append(appealNamaContent.body);


                        //-------------------------------------------------------------------//

                        var newwindow = window.open();
                        newdocument = newwindow.document;
                        newdocument.write($('#appealNamaTemplate').html());
                        newdocument.close();
                        setTimeout(function() {
                            newwindow.print();
                        }, 500);
                        return false;
                    } else {
                        $.alert('আদেশ প্রদান করা হয় নি', 'অবহিতকরণ বার্তা');
                    }

                })

            },
            getAppealNamaReport: function(appealInfo) {
                var header = '',
                    body = '',
                    th = '',
                    tableClose = '';
                header = appealNama.prepareAppealNamaHeader(appealInfo);
                th = appealInfo.appealOrderLists[0].order_detail_table_th;
                tableClose = appealInfo.appealOrderLists[0].order_detail_table_close;
                body = th + appealNama.prepareAppealNamaBody(appealInfo) + tableClose;

                return {
                    header: header,
                    body: body
                };
            },
            prepareAppealNamaHeader: function(appealInfo) {
                var length = appealInfo.appealOrderLists.length;
                var header = appealInfo.appealOrderLists[length - 1].order_header;

                return header;

            },
            prepareAppealNamaBody: function(appealInfo) {

                var body = "";
                $.each(appealInfo.appealOrderLists, function(index, orderList) {
                    body += orderList.order_detail_table_body;
                });

                return body;

            }
        };
    </script>
@endsection