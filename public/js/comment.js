jQuery(document).ready(function() {
    var commentform = jQuery('.hidden-comment-form');

    jQuery('.comment-button').on('click', function(e) {
        e.preventDefault();
        commentform.show(); // You can instead use .toggle() here if you want to
    });
});