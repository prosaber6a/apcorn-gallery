jQuery(document).ready(function($){
    var frame;
    $('.apcorn-add-gallery').on('click', function(e){
        e.preventDefault();

        var galleryInput = $('#apcorn_gallery_input');
        var galleryPreview = $('.apcorn-gallery-preview');

        if (frame) {
            frame.open();
            return;
        }

        frame = wp.media({
            title: 'Select or Upload Images',
            button: { text: 'Use Images' },
            multiple: true
        });

        frame.on('select', function(){
            var selection = frame.state().get('selection');
            var ids = [];
            galleryPreview.empty();

            selection.map(function(attachment){
                attachment = attachment.toJSON();
                ids.push(attachment.id);
                galleryPreview.append('<li data-id="'+attachment.id+'"><img src="'+attachment.sizes.thumbnail.url+'" /><span class="remove">×</span></li>');
            });

            galleryInput.val(ids.join(','));
        });

        frame.open();
    });

    $('body').on('click', '.apcorn-gallery-preview .remove', function(){
        $(this).parent().remove();

        var ids = [];
        $('.apcorn-gallery-preview li').each(function(){
            ids.push($(this).data('id'));
        });

        $('#apcorn_gallery_input').val(ids.join(','));
    });
});
