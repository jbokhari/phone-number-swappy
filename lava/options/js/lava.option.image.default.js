// good resource --- http://stackoverflow.com/questions/13847714/wordpress-3-5-custom-media-upload-for-your-theme-options
jQuery(document).ready(function($) {
    if (typeof wp.media == "undefined"){
        return;
    }
    var _custom_media = true,
        _orig_send_attachment = wp.media.editor.send.attachment;

    $('.media-upload-clear').click( function(e) {
        var $this = $(this);
        var id = $this.data("image-id");
        var container = $this.parent();
        $( ".image-source", container ).val("");
        $( ".image-id", container ).val("");
        $( ".image-width", container ).val("");
        $( ".image-height", container ).val("");
        $( ".image-preview", container ).fadeOut( function() {
            $(this).attr("src", "");
        });
    });
    $('.media-upload').click(function(e) {
        var send_attachment_bkp = wp.media.editor.send.attachment;
        var button = $(this);
        var container = button.parent();
        var id = button.data("image-id");
        // var id = button.data('image-id');
        console.log(id);
        if ( !this.mediawindow ){
            this.mediawindow = wp.media({
                title: "Insert an Image",
                button: {
                    text: "Insert"
                },
                multiple: false // TODO : allow multiple uploads in repeater fields.
            });
       }
        var uploader = this.mediawindow;
        uploader.on("select", function(){
            var attachment = uploader.state().get('selection').first().toJSON();
            console.log(attachment);
            $( ".image-source", container ).val(attachment.url);
            $( ".image-preview", container ).attr("src",attachment.url).hide().fadeIn();
            $( ".image-id", container ).val(attachment.id);
            $( ".image-width", container ).val(attachment.width);
            $( ".image-height", container ).val(attachment.height);
        });
        uploader.open();
        return false;
    });

});