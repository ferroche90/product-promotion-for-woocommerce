jQuery(document).ready(function($) {
    // Show date picker on checkbox checked
    $('input#_set_expiration').change(function() {
        if($(this).is(":checked")) {
            $('p._expiration_date_field').show();
        } else {
            $('p._expiration_date_field').hide();
        }
    }).change();

    // Make adding an expiration date mandatory if checkbox is checked
    $('form#post').on('submit', function(e) {
        var setExpirationChecked = $('#_set_expiration').is(':checked');
        var expirationDateEmpty = !$('#_expiration_date').val();

        if (setExpirationChecked && expirationDateEmpty) {
            alert('Please enter an expiration date for the promotion.');
            e.preventDefault();
        }
    });
});


