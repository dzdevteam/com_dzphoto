Dropzone.options.adminForm = {
    url: "index.php?option=com_dzphoto&view=upload&task=upload.upload&format=json",
    acceptedFiles: "image/jpeg,image/png,image/gif",
    addRemoveLinks: true,
    dictRemoveFile: 'Clear',
    init: function() {
        this.on('success', function(file, response){
            file.previewElement.querySelector("[data-dz-name]").textContent = response.original.replace(/^.*[\\\/]/, ''); 
        });
    }
}