var $ = jQuery;

$(document).ready(function() {
    $('#btn-toggle-nav').on('click', function(e) {
        e.preventDefault();
        $('.header-toogle').toggleClass('show');
        $('.header-right').toggleClass('show');
        $('.dashboard-aside').toggleClass('show');
    });
});