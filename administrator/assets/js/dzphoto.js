if (typeof Dropzone !== 'undefined') {
    Dropzone.options.adminForm = {
        url: "index.php?option=com_dzphoto&view=upload&task=upload.upload&format=json",
        acceptedFiles: "image/jpeg,image/png,image/gif",
        addRemoveLinks: true,
        dictRemoveFile: 'Clear',
        init: function() {
            this.on('success', function(file, response) {
                console.log(response);
                file.previewElement.querySelector("[data-dz-name]").textContent = response.original.replace(/^.*[\\\/]/, ''); 
            });
            this.on('reset', function() {
                jQuery('#clearzone').hide();
            });
            this.on('addedfile', function(file) {
                jQuery('#clearzone').show();
            });
            this.on('error', function(file, errorMessage) {
                file.previewElement.querySelector("[data-dz-errormessage]").textContent = errorMessage.message;
            });
            jQuery('#clearzone').hide();
        }
    }
}
jQuery(document).ready(function() {
    /* -------- COMMON --------- */
    // Utility function to display alert message
    var displayAlert = function (message, alertClass) {
        // Prepare the alert message
        var $alert_tpl = jQuery('<div class="alert fade in"><a class="close" data-dismiss="alert" href="#">&times;</a></div>');
        if (typeof alertClass !== 'undefined')
            $alert_tpl.addClass(alertClass);
        $alert_tpl.append('<b>' + message + '</b>');
        
        // Display the alert
        jQuery('#alert-area').html($alert_tpl);
    },
    // Disable handler to prevent button from submitting form
    btn_disable_handler = function() {
        return false;
    };
    
    
    
    // Utilize part of the toolbar to display alert message
    jQuery('#toolbar').append('<div class="btn-wrapper pull-right"><div id="alert-area"></div></div>');
    
    /* ----- UPLOAD VIEW ------- */
    // Add the clear dropzone button functionality
    jQuery('#clearzone button').on('click', function(){
        Dropzone.forElement("#adminForm").removeAllFiles();
    });
    
    // Add functionality for create album through ajax
    var album_submit_handler = function() {
        // Loading animation
        jQuery('#alert-area').html('<img src="../media/system/images/modal/spinner.gif" />');
        
        var $form = jQuery('form#album-form');
        jQuery.ajax({
            type: 'POST',
            url: 'index.php?option=com_dzphoto&task=upload.newalbum&format=json',
            data: $form.serialize(),
            success: function(response) {
                $form.load('index.php?option=com_dzphoto&view=upload #album-form > *', null, function() {
                    displayAlert(response.message, 'alert-success');
                    
                    // Rebind event
                    jQuery('button#newalbum-submit', $form).on('click', album_submit_handler);
                    jQuery('select[name="albums"])', $form).on('change', albums_select_handler);
                });
            },
            error: function(jqXHR, textStatus, errorThrown) {
                displayAlert(errorThrown, 'alert-danger');
            }
        });
        
        return false;
    };
    jQuery('button#newalbum-submit').on('click', album_submit_handler);
    
    // Functionality for proceed button
    var $upload_accordion_heading = jQuery('a[href="#add-images"]'),
        disable_upload_accordion = function() {
            $upload_accordion_heading.addClass('muted').attr('href', null);
        },
        enable_upload_accordion = function() {
            $upload_accordion_heading.removeClass('muted').attr('href', '#add-images');
        }
        proceed_btn_handler = function() {        
        // Put selected album id into hidden input
        jQuery('input[name="album"]').val(jQuery('select[name="albums"]').val());
        
        // Reveal the upload accordion
        enable_upload_accordion();
        $upload_accordion_heading.trigger('click');
    }
    disable_upload_accordion();
    jQuery('button#proceed').on('click', proceed_btn_handler);
    jQuery('a[href="#new-album"]').on('click', disable_upload_accordion);
    
    /* -------- IMAGE VIEW ----------- */
    // Allow editing images' title and caption right on list view
    var td_focus_handler = function() {
        // Store current data
        jQuery(this).data('before', jQuery(this).text().trim());
    }, td_keypress_handler = function(event) {
        // Prevent newline on title or caption
        if (event.keyCode == 13) {
            jQuery(this).blur();
            return false;
        }
    }, td_blur_handler = function(event) {
        // Do not proceed if data hasn't been changed
        if (jQuery(this).data('before') == jQuery(this).text().trim())
            return jQuery(this);
        
        // Update current data
        jQuery(this).data('before', jQuery(this).text().trim());
        var id = jQuery(this).data('id'),
            field = jQuery(this).data('field'),
            value = jQuery(this).text().trim(),
            token = jQuery('input[type="hidden"][value="1"]').attr('name');
            
        // Prepare data
        data = new Object();
        data.jform = new Object();
        data.jform.id = id;
        data.jform[field] = value;
        data[token] = 1;
        
        // Loading animation
        jQuery('#alert-area').html('<img src="../media/system/images/modal/spinner.gif" />');
        
        // Send data
        jQuery.ajax({
            url: 'index.php?option=com_dzphoto&task=images.saveImageAjax',
            type: 'POST',
            data: data
        }).done(function(response) {
            displayAlert(response.message, 'alert-success');
        }).error(function(jqXHR, textStatus, errorThrown) {
            displayAlert(errorThrown, 'alert-danger');
        });
    };
    
    jQuery("#imageList td[contenteditable]")
        .on('focus', td_focus_handler)
        .on('keypress', td_keypress_handler)
        .on('blur', td_blur_handler);
    
    // Utilize bootstrap modal to preview images
    var img_modal_handler = function() {
        var $img = jQuery("<img src='" + jQuery(this).attr('href') + "' />"),
            $modal = jQuery("#item-modal");
        jQuery("div.modal-header", $modal).hide();
        jQuery("div.modal-body", $modal).html($img);
        jQuery("button.submit-btn", $modal).hide();
        $modal.modal('show');
        
        return false;
    };
    jQuery("a.img-modal").on('click', img_modal_handler);
    
    // Utilize bootstrap modal to edit tags for item
    var tags_modal_handler = function() {
        // Generate a select
        var $tags_container = jQuery('#hidden-area > div.tags-container'),
            $select = jQuery("select#jform_tags"),
            $modal = jQuery('#item-modal'),
            options = jQuery(this).data('item-tags'),
            a = this,
            $option, tag_id;
            
        // Prepare the form
        // -- prepare item id
        jQuery("input#jform_id").val(jQuery(this).data('item-id'));
        // -- clear current tag options
        $select.html('');
        // -- prepare the tag options
        if (options !== null) {
            for (var tag_id in options) {
                $option = jQuery('<option />');
                $option.text(options[tag_id]).val(tag_id).attr('selected', 'selected');
                $select.append($option);
            }
        }
        $select.trigger('liszt:updated');
        
        // Show the modal
        jQuery("div.modal-header", $modal).html('<h3>'+jQuery(this).data('item-title')+'</h3>').show();
        jQuery("div.modal-body", $modal).html($tags_container).css({"min-height": "150px"});
        
        // Bind submit button to send form through ajax
        jQuery("button.submit-btn", $modal).show().on('click', function() {
            // Loading animation
            jQuery('#alert-area').html('<img src="../media/system/images/modal/spinner.gif" />');
            
            // Submit data
            jQuery.ajax({
                type: 'POST',
                url: jQuery("form#tags_form").attr('action'),
                        data: jQuery("form#tags_form").serialize(),
                        success: function(response) {
                            // Update current row
                            var item_id = jQuery(a).data('item-id');
                            jQuery("tr#row-item-" + item_id).load('index.php?option=com_dzphoto&view=images #row-item-' + item_id + ' > * ', null, function() {
                                displayAlert(response.message, 'alert-success');
                                
                                // Rebind event for newly fetched element
                                jQuery('a.img-modal', this).on('click', img_modal_handler);
                                jQuery('a.tags-modal', this).on('click', tags_modal_handler);
                                jQuery('td[contenteditable]', this)
                                .on('focus', td_focus_handler)
                                .on('keypress', td_keypress_handler)
                                .on('blur', td_blur_handler);
                            });
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            displayAlert(errorThrown, 'alert-danger');
                        }
            });
            
            // Close the modal
            $modal.modal('hide');
        });
        $modal.modal('show').on('hidden', function() {
            // Move the tags container back
            jQuery("#hidden-area").append($tags_container);
        });
        
        return false;
    }
    jQuery("a.tags-modal").on('click', tags_modal_handler);
    
    /* ------- ALBUMS VIEW ------- */
    // Handler for album modal
    var album_modal_handler = function() {
        var $modal = jQuery("#item-modal"),
            title  = '<h3 class="pull-left">' + jQuery(this).data('album-title') + '</h3>',
            mode   = jQuery(this).data('album-mode'),
            src    = 'index.php?option=com_dzphoto&view=album&id=' + jQuery(this).data('album-id') + '&tmpl=component',
            $head_btns = jQuery('<div class="btn-group pull-right" data-toggle="buttons-radio">' +
            '<button type="button" class="btn btn-mode-view" data-href="'+src+'&mode=view"><span class="icon-eye" aria-hidden="true"></span>&nbsp;'+Joomla.JText._('COM_DZPHOTO_ALBUMS_VIEW_IMAGES')+'</button>' +
            '<button type="button" class="btn btn-mode-add" data-href="'+src+'&mode=add"><span class="icon-file-add" aria-hidden="true"></span>&nbsp;'+Joomla.JText._('COM_DZPHOTO_ALBUMS_ADD_IMAGES')+'</button>' +
                        '</div>');           
        jQuery("div.modal-header", $modal).html(title).append($head_btns).append('<div class="clearfix"></div>');
        jQuery("button.btn-mode-"+mode, $head_btns).addClass('active');
        jQuery("div.modal-body > iframe", $modal).attr('src', src + '&mode=' + mode);
        jQuery("button.btn", $head_btns).on('click', function() {
            jQuery("div.modal-body > iframe", $modal).attr('src', jQuery(this).data('href'));
        });
        $modal.modal('show');
    };
    jQuery("a.album-modal").on('click', album_modal_handler);
    
    /* ------- ALBUM VIEW ------ */

    // Image action button handler
    var btn_action_handler = function() {
        // Prevent this being clicked again
        jQuery(this).off('click', btn_action_handler);
        jQuery(this).on('click', btn_disable_handler);
        
        // Prepare data
        var data = new Object(), 
            $tokeninput = jQuery('input[type="hidden"][value="1"]'),
            button = this, url = null;
        data.albumid = jQuery(this).data('album-id');
        data.imageid = jQuery(this).data('image-id');
        data[$tokeninput.attr('name')] = 1;
            
        // Loading animation
        jQuery('#alert-area').html('<img src="../media/system/images/modal/spinner.gif" />');
        
        // Mute effect for the row
        jQuery(this).parents('tr').css('opacity', '0.7');
        
        // Submit data
        if (jQuery(this).hasClass('btn-remove-img'))
            url = 'index.php?option=com_dzphoto&task=album.removeImage&format=json';
        else if (jQuery(this).hasClass('btn-add-img'))
            url = 'index.php?option=com_dzphoto&task=album.addImage&format=json';
        
        jQuery.ajax({
            url: url,
            type: 'POST',
            data: data
        }).done(function(response) {
            displayAlert(response.message, 'alert-success');
            
            // Hide row animation
            jQuery(button).parents('tr').fadeOut(400, function() {
                // Then remove the row completely
                jQuery(this).remove(); 
            });
            
            // Reload the table when the removed row is the only left
            if (jQuery('table#imageList tbody tr').length == 1) {
                displayAlert('<img src="../media/system/images/modal/spinner.gif" />&nbsp;' + Joomla.JText._('COM_DZPHOTO_ALBUM_LOADING_IMAGES'), 'alert-success');
                
                jQuery('table#imageList').load(window.location.href + ' #imageList > *', function() {
                    displayAlert(Joomla.JText._('COM_DZPHOTO_ALBUM_LOADED_IMAGES'), 'alert-success');
                })
            }
        }).error(function(jqXHR, textStatus, errorThrown) {
            displayAlert(errorThrown, 'alert-danger');
            
            // Re-enable click event on error
            jQuery(button).off('click', btn_disable_handler);
            jQuery(button).on('click', btn_add_handler);
        });
        
        // Prevent submitting form
        return false;
    };
    jQuery("button.btn-remove-img, button.btn-add-img").on('click', btn_action_handler);
});