// jQuery( document ).ready() block.
jQuery(document).ready(function () {

    // Event date
    jQuery("#evntmgmt_date").datepicker({
        minDate: new Date(),
        dateFormat: 'dd-mm-yy',
        showOn: "both",
        buttonImage: evntmgmt_ajax_object.datepicker_img,
        buttonImageOnly: true,
        changeMonth: true,
        changeYear: true
    });

    // Start date
    jQuery("#evntmgmt_start_date").datepicker({
        minDate: new Date(),
        dateFormat: 'dd-mm-yy',
        showOn: "both",
        buttonImage: evntmgmt_ajax_object.datepicker_img,
        buttonImageOnly: true,
        changeMonth: true,
        changeYear: true,
        onSelect: function () {
            var date = jQuery(this).datepicker('getDate');
            if (date) {
                date.setDate(date.getDate() + 1);
            }
            var checkout = jQuery('#evntmgmt_end_date');
            checkout.datepicker('option', 'minDate', date);
            jQuery('#evntmgmt_end_date').val('');
        }
    });

    // End date
    jQuery("#evntmgmt_end_date").datepicker({
        minDate: new Date(),
        dateFormat: 'dd-mm-yy',
        showOn: "both",
        buttonImage: evntmgmt_ajax_object.datepicker_img,
        buttonImageOnly: true,
        changeMonth: true,
        changeYear: true
    });

    // Filters events listing
    jQuery("#evntmgmt_filters").click(function () {
        var cat = jQuery('#evntmgmt_type').val();
        var start_dt = jQuery('#evntmgmt_start_date').val();
        var end_dt = jQuery('#evntmgmt_end_date').val();

        if (end_dt != '' && start_dt == '') {
            alert('Please select start date first');
            return false;
        }

        if (start_dt != '' && end_dt == '') {
            alert('Please select end date');
            return false;
        }

        if (cat != '' || start_dt != '' || end_dt != '') {
            
            jQuery.ajax({
                type: 'POST',
                url: evntmgmt_ajax_object.ajaxurl,
                data: {
                    action: 'get_events_listing_by_filters',
                    security: evntmgmt_ajax_object.nonce,
                    cat: cat,
                    start_dt: start_dt,
                    end_dt: end_dt
                },
                beforeSend: function () {
                    jQuery('.evntmgmt_loader').show();
                    jQuery('#evntmgmt_filters').hide();
                },
                success: function (response) {
                    jQuery('.evntmgmt_loader').hide();
                    jQuery('#evntmgmt_filters').show();
                    var eventsDataObj = JSON.parse(response);
                    var output = '';
                    if (eventsDataObj.no_events_found.length) {
                        output += '<div class="evntmgmt_not_found">';
                        output += eventsDataObj.no_events_found;
                        output += '</div>';
                    } else {
                        jQuery(eventsDataObj.events_dtls).each(function (index, element) {
                            output += '<div class="evntmgmt_img">';
                            output += '<img src="' + element.img + '" />';
                            output += '</div>';

                            output += '<div class="evntmgmt_title"><h2>' + element.title + '</h2></div>';
                            output += '<div class="evntmgmt_date"><strong>Event Date:</strong> ' + element.event_date + '</div>';
                            output += '<div class="evntmgmt_venue"><strong>Venue:</strong> ' + element.venue + '</div>';
                            output += '<div class="evntmgmt_location"><strong>Location:</strong> ' + element.location + '</div>';
                            output += '<div class="evntmgmt_desc">' + element.desc + '</div>';
                            output += '<hr/>';
                        });
                    }
                    jQuery('.evntmgmt_details').html(output);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    jQuery('.evntmgmt_loader').hide();
                    jQuery('#evntmgmt_filters').show();
                    console.log('Oops! something went wrong');
                }
            });

        }
    });
});