@extends('main.templates.main')
@section('title')
    Upload files
@endsection

@section('styles')
    <link href="{{ asset('css/file.css') }}" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.2/min/dropzone.min.css" rel="stylesheet">
@endsection

@section('content')
    <div class="container-fluid px-4">
        <h4 class="mt-4">Order <a class="link-order" href="{{ route('orders.view', ['id'=>$order->id]) }}"> {{ $order->getKeyField() }}  </a></h4>
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="col-md-3">
                            Upload files
                        </div>
                        <div class="col-md-1 d-flex justify-content-end">
                            <a title="Close" href="{{ route('order-files.index', ['orderId' => $order->id]) }}" class="text-secondary"><i class="fa-solid fa-xmark"></i></a>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('order-files.store') }}" class="dropzone" id="my-awesome-dropzone">
                            @csrf
                            <input type="hidden" name="order_id" value="{{ $order->id }}"/>
                            <div class="fallback">
                                <input name="files[]" type="file" multiple />
                            </div>
                        </form>

                        <button type="button" id="uploadButton" class="btn btn-primary mt-3">Upload Files</button>
                    </div>
                </div>
            </div>
        </div>
@endsection

@section('script')
            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.2/min/dropzone.min.js"></script>
            <script type="text/javascript">
                // If you have other Dropzone instances, you might want to disable autoDiscover
                Dropzone.autoDiscover = false;

                // Instantiate the Dropzone
                $(document).ready(function() {
                    let myDropzone = new Dropzone("#my-awesome-dropzone", {
                        paramName: "files", // The name that will be used to transfer the file
                        addRemoveLinks: true, // Show remove button
                        dictDefaultMessage: "Drop files here to upload (or click)", // Set default message
                        maxFilesize: 5, // Set maximum file size in MB
                        parallelUploads: 5, // Number of files process in parallel
                        uploadMultiple: true, // Allow multiple file uploads
                        acceptedFiles: "image/*,application/pdf,.psd", // File type specific
                        autoProcessQueue: false, // Disable auto-processing to prevent AJAX submission
                        dictRemoveFile: '<i class="fa-solid fa-xmark"></i>',
                    });

                    myDropzone.on("error", function(file, response) {
                        // This will handle string responses
                        let message = response;

                        // If response is an object with a `message` property, use that message
                        if (typeof response === 'object' && response.message) {
                            message = response.message;
                        }

                        // Display the message
                        $(file.previewElement).find('.dz-error-message').text(message);

                        // Get the remove button for the file
                        var removeButton = $(file.previewElement).find('.dz-remove');

                        // Add click event listener to remove button
                        removeButton.on("click", function(e) {
                            e.preventDefault();
                            e.stopPropagation(); // Stop the event from propagating to other elements
                            myDropzone.removeFile(file);
                        });
                    });

                    // Listen to the button click
                    $("#uploadButton").on("click", function() {
                        myDropzone.processQueue(); // Process all queued files
                    });

                });
            </script>
@endsection

