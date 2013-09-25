$(document).ready(function () {
    $('.tweeter-import-btn').click(function (e) {
        e.preventDefault();
        
        var id = $(this).attr('id').substring(4);
        var title = $('#title-'+id).value();
        var imgUrl = $('#img-'+id).attr('src');
    })
});
