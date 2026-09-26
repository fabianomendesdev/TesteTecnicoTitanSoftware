var $ = jQuery;

$(document).ready(function() {
    $('#btn-toggle-nav').on('click', function(e) {
        e.preventDefault();
        $('body').toggleClass('show-toggle-nav');
    });
});