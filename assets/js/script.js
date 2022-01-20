jQuery(function ($) {

    $(document.body).off('click', '.upload_file_button');
    // Uploading files.
    var woo_amazons3_downloadable_file_frame;

    $(document.body).on('click', '.upload_file_button', function (e) {
        e.preventDefault();

        var $el = $(this);

        window.file_path_field = $el.closest('tr').find('td.file_url input');
        //window.file_name_field = $el.closest('tr').find('td.file_name input');


        // If the media frame already exists, reopen it.
        if (woo_amazons3_downloadable_file_frame) {
            woo_amazons3_downloadable_file_frame.open();
            return;
        }

        var downloadable_file_states = [
            // Main states.
            new wp.media.controller.Library({
                library: wp.media.query(),
                multiple: true,
                title: $el.data('choose'),
                priority: 30,
                filterable: 'uploaded'
            })
        ];

        // Create the media frame.
        woo_amazons3_downloadable_file_frame = wp.media.frames.downloadable_file = wp.media({
            // Set the title of the modal.
            frame: 'post',
            title: $el.data('choose'),
            library: {
                type: ''
            },
            button: {
                text: $el.data('update')
            },
            multiple: false,
            state: 'insert',
            states: downloadable_file_states
        });

        woo_amazons3_downloadable_file_frame.on('menu:render:default', function (view) {
            // Store our views in an object.
            var views = {};
            // Unset default menu items
            view.unset('library-separator');
            view.unset('gallery');
            view.unset('featured-image');
            view.unset('embed');

            // Initialize the views in our view object.
            view.set(views);
        });

        woo_amazons3_downloadable_file_frame.on('select', function () {
            var file_path = '';
            //var file_name = '';
            var selection = woo_amazons3_downloadable_file_frame.state().get('selection');

            selection.map(function (attachment) {
                attachment = attachment.toJSON();
                if (attachment.url) {
                    file_path = attachment.url;
                }
                //if (attachment.name) {
                    //file_name = attachment.name;
                //}
            });

            window.file_path_field.val(file_path);
            //window.file_name_field.val(file_name);
        });

        // When an image is selected, run a callback.
        woo_amazons3_downloadable_file_frame.on('insert', function () {
            var file_path = '';
            //var file_name = '';
            var selection = woo_amazons3_downloadable_file_frame.state().get('selection');

            selection.map(function (attachment) {
                attachment = attachment.toJSON();
                if (attachment.url) {
                    file_path = attachment.url;
                }
                //if (attachment.name) {
                  //  file_name = attachment.name;
                //}
            });

            window.file_path_field.val(file_path);
            //window.file_name_field.val(file_name);
        });

        woo_amazons3_downloadable_file_frame.on('close', function () {

            var selection = woo_amazons3_downloadable_file_frame.state().get('selection');

            if (typeof selection == 'undefined') {
                woo_amazons3_downloadable_file_frame.state().set('selection', {});
            }


        });



        // Finally, open the modal.
        woo_amazons3_downloadable_file_frame.open();
    });

});
