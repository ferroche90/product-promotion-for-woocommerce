jQuery(document).ready(function($) {
    // Show datetime picker on checkbox checked
    $('input#_set_expiration').change(function() {
        if ($(this).is(":checked")) {
            $('p._expiration_datetime_field').show();
        } else {
            $('p._expiration_datetime_field').hide();
        }
    }).change();

    // Make adding an expiration date mandatory if checkbox is checked
    $('form#post').on('submit', function(e) {
        var setExpirationChecked = $('#_set_expiration').is(':checked');
        var expirationDatetimeEmpty = !$('#_expiration_datetime').val();

        if (setExpirationChecked && expirationDatetimeEmpty) {
            alert('Please enter an expiration date and time for the promotion.');
            e.preventDefault();
        }
    });
});
