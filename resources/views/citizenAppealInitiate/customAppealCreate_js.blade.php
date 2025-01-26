<script>
     jQuery(document).ready(function() {
        var mk = $('span.select2-selection__placeholder').text().trim();
        // console.log(mk);
        // document.getElementsByClassName("select2-selection__placeholder").value = "-- নির্বাচন করুন --";

        var load_url = "{{ asset('media/custom/preload.gif') }}";
        //===================Law section Details========//
        jQuery('select[name="lawSection"]').on('change', function() {
            var dataID = $('select[name=lawSection]').children("option:selected").attr('law_section');
            if (dataID && dataID != 0) {
                var link =
                    '<a href="#"  data-toggle="modal" data-target="#exampleModalScrollable">(ফৌজদারি ধারার বিবরণ)</a>';
                jQuery.ajax({
                    url: '{{ url('/') }}/generalCertificate/getdependentlawdetails/' +
                        dataID,
                    type: "GET",
                    dataType: "json",
                    success: function(data) {
                        console.log('foujdari',data);
                        $('#link').html(link);
                        jQuery('#lawdetails').html(nl2br(data.crpc_details));
                    }
                });
            }
        });
        //===================//Law section Details========//

        //===========District================//
        jQuery('select[name="division"]').on('change', function() {
            var dataID = jQuery(this).val();

            // var category_id = jQuery('#category_id option:selected').val();
            jQuery("#district_id").after('<div class="loadersmall"></div>');


            if (dataID) {
                jQuery.ajax({
                    url: '{{ url('/') }}/generalCertificate/case/dropdownlist/getdependentdistrict/' +
                        dataID,
                    type: "GET",
                    dataType: "json",
                    success: function(data) {
                        jQuery('select[name="district"]').html(
                            '<div class="loadersmall"></div>');


                        jQuery('select[name="district"]').html(
                            '<option value="">-- নির্বাচন করুন --</option>');
                        jQuery.each(data, function(key, value) {
                            jQuery('select[name="district"]').append(
                                '<option value="' + key + '">' + value +
                                '</option>');
                        });
                        jQuery('.loadersmall').remove();

                    }
                });
            } else {
                $('select[name="district"]').empty();
            }
        });

        //===========Upazila================//


        jQuery('select[name="district"]').on('change', function() {
            var dataID = jQuery(this).val();


            jQuery("#upazila_id").after('<div class="loadersmall"></div>');


            if (dataID) {
                jQuery.ajax({
                    url: '{{ url('/') }}/generalCertificate/case/dropdownlist/getdependentupazila/' +
                        dataID,
                    type: "GET",
                    dataType: "json",
                    success: function(data) {
                        jQuery('select[name="upazila"]').html(
                            '<div class="loadersmall"></div>');


                        jQuery('select[name="upazila"]').html(
                            '<option value="">-- নির্বাচন করুন --</option>');
                        jQuery.each(data, function(key, value) {
                            jQuery('select[name="upazila"]').append(
                                '<option value="' + key + '">' + value +
                                '</option>');
                        });
                        jQuery('.loadersmall').remove();

                    }
                });
            } else {
                $('select[name="upazila"]').empty();
            }
        });
    });
</script>