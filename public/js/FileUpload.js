/**
 *
 * Main App Component
 *
 */
"use strict";
/* global app, mediaSettings, Dropzone, trans, launchToast */

// Disable dropzone uploader auto loading globally as we will instantiate it manually
Dropzone.autoDiscover = false;

var FileUpload = {

    attachaments: [],
    myDropzone : null,
    isLoading:false,
    state: {},

    /**
     * Instantiates the media uploader plugin
     * @param selector
     * @param url
     */
    initDropZone:function (selector,url) {

        FileUpload.myDropzone = new Dropzone(selector, {
            paramName: "file", // The name that will be used to transfer the file
            previewTemplate: document.querySelector('#tpl').innerHTML,
            url: app.baseUrl + url,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            clickable:['.file-upload-button'],
            previewsContainer: ".dropzone-previews",
            maxFilesize: mediaSettings.max_file_upload_size, // MB
            addRemoveLinks: true,
            dictRemoveFile: "x",
            acceptedFiles: mediaSettings.allowed_file_extensions,
            init: function() {
                // FileUpload.attachaments
                FileUpload.attachaments.map((element)=>{
                    var mockFile = { name: element.attachmentID, upload:{attachmentID:element.attachmentID} , type:element.type, thumbnail: element.thumbnail};
                    this.emit("addedfile", mockFile);
                    this.emit("thumbnail", mockFile, element.thumbnail);
                    this.emit("complete", mockFile);
                    FileUpload.updatePreviewElement(mockFile, false, element);
                });
                var _this = this;
                $(".draft-clear-button").on("click", function() {
                    _this.removeAllFiles(true);
                });
            }
        });

        FileUpload.myDropzone.on("addedfile", file => {
            FileUpload.updatePreviewElement(file, true);
            FileUpload.isLoading = true;
        });

        FileUpload.myDropzone.on("success", (file, response) => {
            if(response.success){
                file.upload.attachmentID = response.attachmentID;
                FileUpload.attachaments.push({attachmentID: response.attachmentID, path: response.path, type:response.type, thumbnail:response.thumbnail});
                // If received file is a converted video
                switch (file.type) {
                case 'video/mp4':
                case 'video/avi':
                case 'video/quicktime':
                case 'video/x-m4v':
                case 'video/mpeg':
                case 'video/wmw':
                case 'video/x-matroska':
                case 'video/x-ms-asf':
                case 'video/x-ms-wmv':
                case 'video/x-ms-wmx':
                case 'video/x-ms-wvx':
                case 'video':
                    FileUpload.updatePreviewElement(file, false,response);
                    break;
                }
            }
            FileUpload.isLoading = false;
        });

        FileUpload.myDropzone.on("removedfile", function(file) {
            FileUpload.attachaments = FileUpload.attachaments.filter((attachment)=>{
                if(attachment.attachmentID !== file.upload.attachmentID){
                    return attachment;
                }
                else{
                    FileUpload.removeAttachment(attachment);
                }
            });
        });

        FileUpload.myDropzone.on("error", (file, errorMessage) => {
            if(typeof errorMessage.errors !== 'undefined'){
                // launchToast('danger',trans('Error'),errorMessage.errors.file)
                $.each(errorMessage.errors,function (field,error) {
                    launchToast('danger',trans('Error'),error);
                });
            }
            else{
                if(typeof errorMessage.message !== 'undefined'){
                    launchToast('danger',trans('Error'),errorMessage.message);
                }
                else{
                    launchToast('danger',trans('Error'),errorMessage);
                }
            }
            FileUpload.myDropzone.removeFile(file);
            FileUpload.isLoading = false;
        });
    },

    /**
     * Updates the preview template based on uploaded file
     * @param file
     * @param localFile
     * @param attachment
     */
    updatePreviewElement:function (file,localFile, attachment = false) {
        let filePreview = $(file.previewElement);
        filePreview.find('.dz-image').remove();
        switch (file.type) {
        case 'video/mp4':
        case 'video/avi':
        case 'video/quicktime':
        case 'video/x-m4v':
        case 'video/mpeg':
        case 'video/wmw':
        case 'video/x-matroska':
        case 'video/x-ms-asf':
        case 'video/x-ms-wmv':
        case 'video/x-ms-wmx':
        case 'video/x-ms-wvx':
        case 'video':
            filePreview.find('.video-preview-item').remove();
            filePreview.prepend(videoPreview());
            var videoPreviewEl = filePreview.find('video').get(0);
            if(localFile){
                FileUpload.setMediaSourceForPreviewByElementAndFile(videoPreviewEl, file);
            }
            else{
                FileUpload.setPreviewSource(videoPreviewEl, file, attachment);
            }
            break;
        case 'audio/mpeg':
        case 'audio/ogg':
        case 'audio':
            filePreview.prepend(audioPreview());
            filePreview.addClass("w-100");
            filePreview.find('audio').addClass("w-100");
            filePreview.find(".audio-preview-item").addClass("w-100");
            var audioPreviewEl = filePreview.find('audio').get(0);
            filePreview.addClass("w-100");
            if(localFile){
                FileUpload.setMediaSourceForPreviewByElementAndFile(audioPreviewEl, file);
            }
            else{
                FileUpload.setPreviewSource(audioPreviewEl, file, attachment);
            }
            break;
        default:
            filePreview.prepend(imagePreview());
            if(!localFile){
                let previewElement = filePreview.find('img').get(0);
                FileUpload.setPreviewSource(previewElement, file, attachment);
            }
            break;
        }
    },

    /**
     * Sets up the media src for the uploaded file type
     * @param element
     * @param file
     * @returns {boolean}
     */
    setMediaSourceForPreviewByElementAndFile: function (element, file) {
        if(typeof element === 'undefined'){ return false;}
        if (element.canPlayType(file.type) !== "no") {
            const fileURL = window.URL.createObjectURL(file);
            $(element).on('loadeddata', function () {
                window.URL.revokeObjectURL(fileURL);
            });
            $(element).attr('src', fileURL);
            $(element).attr('type',file.type);
        }
    },

    /**
     * Sets media source | Thumbnail
     * @param element
     * @param file
     * @param attachment
     */
    setPreviewSource: function (element, file, attachment) {
        $(element).attr('src', attachment.thumbnail);
    },

    /**
     * Removes an attached file
     * @param attachmentID
     */
    removeAttachment: function (attachmentID) {
        $.ajax({
            type: 'POST',
            data: {
                'attachmentId': attachmentID,
            },
            url: app.baseUrl+'/attachment/remove',
            success: function () {
                launchToast('success',trans('Success'), trans('Attachment removed.'));
            },
            error: function () {
                launchToast('danger',trans('Error'), trans('Failed to remove the attachment.'));
            }
        });
    },

};

/**
 * Video preview Component
 * @returns {string}
 */
function videoPreview() {
    return `<div class="video-preview-item shadow">
                <span data-dz-name></span>
                <span data-dz-size></span>
            <video class="video-preview" controls autoplay muted></video>
        </div>`;
}

/**
 * Image preview Component
 * @returns {string}
 */
function imagePreview() {
    return `<div class="dz-image shadow">
            <img data-dz-thumbnail/>
        </div>
        <div class="dz-details">
            <div class="dz-filename"><span data-dz-name></span></div>
            <div class="dz-size" data-dz-size></div>
        </div>`;
}

/**
 * Audio preview Component
 * @returns {string}
 */
function audioPreview() {
    return `<div class="audio-preview-item">
                    <span data-dz-name></span>
                    <span data-dz-size></span>
                <audio id="audio-preview" controls type="audio/mpeg" autoplay muted></audio>
        </div>`;
}
