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
jQuery(document).ready(function(){
    jQuery('#clearzone button').on('click', function(){
        Dropzone.forElement("#adminForm").removeAllFiles();
    });
    jQuery("a.img-modal").on('click', function() {
        var $img = jQuery("<img src='" + jQuery(this).attr('href') + "' />");
        jQuery("#item-modal > div.modal-body").html($img);
        jQuery("#item-modal button.submit-btn").hide();
        jQuery("#item-modal").modal('show');
        
        return false;
    });
    jQuery('#toolbar').append('<div class="btn-wrapper pull-right"><div id="alert-area"></div></div>');
    jQuery("#imageList td[contenteditable]").on('focus', function() {
        // Store current data
        jQuery(this).data('before', jQuery(this).text().trim());
    }).on('keypress', function(event) {
        if (event.keyCode == 13) {
            jQuery(this).blur();
            return false;
        }
    }).on('blur', function(event) {
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
        jQuery('.alert').alert('close');
        // Alert template
        $alert_tpl = jQuery('<div class="alert fade in"><a class="close" data-dismiss="alert" href="#">&times;</a></div>');
        // Send data
        jQuery.ajax({
            url: 'index.php?option=com_dzphoto&task=images.saveImageAjax',
            type: 'POST',
            data: data
        }).done(function(msg) {
            $success = $alert_tpl.clone().addClass('alert-success').append('<b>'+msg.message+'</b>');
            jQuery('#alert-area').html($success);
        }).fail(function(jqXHR, error) {
            $error = $alert_tpl.clone().addClass('alert-danger').append('<b>'+error.message+'</b>');
            jQuery('#alert-area').html($error);
        });
    });
});