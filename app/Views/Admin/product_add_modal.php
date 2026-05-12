<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content rounded">
            <div class="modal-header">
                <h5 class="modal-title"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
			<div id="uploadMsg" class="alert" style="display: none;"></div>
                <h5 class="mb-3">Image upload for: <span id="productName" class="text-primary"></span></h5>
                <form class="fileUpload" method="post" enctype="multipart/form-data">

                    <div class="row justify-content-center">
                        <div id="drop-area" class="drop-area text-center p-2 border rounded"
                            ondragover="event.preventDefault();" ondrop="handleDrop(event)"
                            style="width: 100%; max-width: 400px;">
                            <div class="drop-content">
                                <i class="bi bi-cloud-upload display-4 text-primary mb-2"></i>
                                <h6 class="mb-1">Drag & Drop images here</h6>
                                <p class="text-muted small">or</p>
                                <label class="btn btn-outline-primary btn-sm" for="fileElem">
                                    <i class="bi bi-upload me-1"></i> Select Images
                                </label>
                               <input type="file" id="fileElem" multiple accept="image/*" class="fileElem d-none"
                                    onchange="handleFiles(this.files)">
                                    
                            </div>
                             <span class="text-warning" style="font-size:14px;">Only images with width between 300–500 px and height between 400–600 px are allowed.</span>
                        </div>
                    </div>

                    <input type="hidden" id="productId" name="product_id">
                </form>

                <div class="mt-3" id="imagePreviewContainer">
                    <!-- <h6 class="text-center">Uploaded Images</h6> -->
                    <div id="imagePreview" class="d-flex flex-wrap justify-content-center gap-2">
                        <!-- Images will be inserted here -->
                    </div>
                </div>

            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                <!-- <button type="button" class="btn btn-primary btn-sm">Save changes</button> -->
            </div>
        </div>
    </div>
</div>