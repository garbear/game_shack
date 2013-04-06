(function($) {
    $(document).ready(function() {
        $('#Email').bind("change paste keydown keyup", function(e) {
            if ((e.keyCode || e.which) == 13) {
                $(document).ready(function() {
                    updatePasswd();
                    submit();
                });
            }
            else {
                $(document).ready(function() {
                    updatePasswd();
                });
            }
            //e.preventDefault();
        });
    });
})(jQuery);

function updatePasswd() {
    if ($('#Email').val()) {
        $('#Passwd').val(md5($('#Email').val()).substring(0, 16));
    }
    else {
        $('#Passwd').val('');
    }
};

function submit() {
    // Guard against double-submitting
    if (typeof window.submitting === 'undefined') {
        window.submitting = true;
        $('#loginform').submit();
    }
}
