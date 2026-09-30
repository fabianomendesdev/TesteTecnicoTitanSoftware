var $ = jQuery;

$(document).ready(function() {
    let className = 'show-toggle-nav';
    // let showToggleNav = JSON.parse(localStorage.getItem(className)) ?? true;
    // $('body').toggleClass(className, showToggleNav);

    $('#btn-toggle-nav').on('click', function(e) {
        e.preventDefault();
        $('body').toggleClass(className);

        let isVisible = $('body').hasClass(className);
        localStorage.setItem(className, isVisible);
    });
});