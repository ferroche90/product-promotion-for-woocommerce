jQuery(document).ready(function($) {
    // Show datetime picker on checkbox checked
    $('input#_set_expiration').change(function() {
        if ($(this).is(":checked")) {
            $('input#_expiration_datetime').prop('disabled', false);
            $('p._expiration_datetime_field').show();
        } else {
            $('input#_expiration_datetime').prop('disabled', true);
            var today = new Date();
            var date = today.getFullYear()+'-'+(today.getMonth()+1)+'-'+today.getDate();
            var time = '23:59';
            var dateTime = date+'T'+time;
            $('input#_expiration_datetime').val(dateTime);
            $('p._expiration_datetime_field').hide();
        }
    }).change(); // Trigger change event to set initial state

    // Make adding an expiration date mandatory if checkbox is checked
    $('form#post').on('submit', function(e) {
        var setExpirationChecked = $('.expiration-checkbox').is(':checked');
        var expirationDatetimeEmpty = !$('.expiration-date').val();

        if (setExpirationChecked && expirationDatetimeEmpty) {
            alert('Please enter an expiration date and time for the promotion.');
            e.preventDefault();
        }
    });
});