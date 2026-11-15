<style>
    /*
     * FilePond does not always hide its idle drop label after the editor rebuilds the file
     * list programmatically (`pond.files = ...`). When an item is present, hide the label so it
     * cannot overlap the image preview. Removing the image brings the label back automatically.
     */
    .fi-fo-file-upload.blog-media-upload .filepond--root:has(.filepond--item) .filepond--drop-label {
        display: none !important;
    }
</style>
