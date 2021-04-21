$(function () {
    $('.hidetoggler a').click(function (e) {
        e.preventDefault();
        $(this).parent().find('div').slideToggle();
    });
});