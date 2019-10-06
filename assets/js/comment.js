$(document).ready(function() {
    // Store link with class `Supprimer` to a variable
    var commentform = jQuery('.hidden-comment-form');

    // Show it when the link with class `Ajout` is clicked
    jQuery('.comment-button').on('click', function(e) {
        e.preventDefault();
        commentform.toggle(); // You can instead use .toggle() here if you want to
    });
});