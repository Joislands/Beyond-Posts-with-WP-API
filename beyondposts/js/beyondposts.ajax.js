/**
 * AJAX for button, actions, etc
 */

(function($){
    $('.get-related-posts').on( 'click', function(event) {
        event.preventDefault();

        // Remove "Get Related Posts" button
        $('a.get-related-posts').remove();
        // Display loader
        $('.ajax-loader').show();
        
        // Get REST URL and post ID from WordPress
        var json_url = postdata.json_url;
        var post_id = postdata.post_id;

        // The AJAX
        $.ajax({
            dataType: 'json',
            url: json_url
        })

        .done(function(response){

            // Display "Related Posts:" header
            $('#related-posts').append('<h1 class="related-header">You may be interested:</h1>');
            // Loop through each of the related posts
            $.each(response, function(index, object) {

                if (object.id == post_id) {
                    return;
                }
                // Assume there is no featured image
                var feat_img = '';

                // If there is a featured image, populate feat_img with the required HTML
                if ( object.featured_image !== 0 ) {
                    feat_img =      '<figure class="related-featured">' +
                                    '<img src="' + object.featured_image_src + '" alt="">' +
                                    '</figure>';
                }

                // Set up HTML to be added
                var related_loop =  '<aside class="related-post clear">' +
                                    '<a href="' + object.link + '">' +
                                    '<h1 class="related-post-title">' + object.title.rendered + '</h1>' +
                                    '<div class="related-author">by <em>' + object.author_name + '</em></div>' +
                                    '<div class="related-excerpt">' +
                                    feat_img +
                                    object.excerpt.rendered +
                                    '</div>' +
                                    '</a>' +
                                    '</aside><!-- .related-post -->';

                // Hide loader
                $('.ajax-loader').remove();
                // Append HTML to existing content
                $('#related-posts').append(related_loop);
            });
        })

        .fail(function(){
            // Hide loader
            $('.ajax-loader').remove();
            // If something goes wrong, say so
            $('#related-posts').append('<div>Please try again, something is wrong</div>');
            console.log('Error');
        })

        .always(function(){

        });

    });
})(jQuery);
