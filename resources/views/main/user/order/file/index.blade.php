@extends('main.templates.main')
@section('title')
    Order {{ $order->getKeyField() }} files
@endsection

@section('styles')
    <link href="{{ asset('css/file.css') }}" rel="stylesheet" />
@endsection

@section('content')
    <div class="container-fluid px-4">
        <h4 class="mt-4">Order <a class="link-order" href="{{ route('orders.view', ['id'=>$order->id]) }}"> {{ $order->getKeyField() }}  </a> files</h4>
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="col-md-1">Uploaded files</div>
                        <div class="col-md-1 d-flex justify-content-end">
                            <a title="Upload files" href="{{ route('order-files.upload', ['orderId' => $order->id]) }}" class="btn btn-outline-primary"><i class="fa-solid fa-file-arrow-up"></i></a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>name</th>
                                    <th>uploaded by</th>
                                    <th>uploaded at</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                @if(Auth::user()->hasPermissionTo('See uploaded files'))
                                    @foreach($order->files as $file)
                                        <tr>
                                            <td><i class="fa-regular fa-file"></i> {{$file->file_name}}</td>
                                            <td>{{$file->user->email}} (id: {{$file->user->id}})</td>
                                            <td>{{$file->created_at}}</td>
                                            <td>
                                                <!-- Hidden form for deletion -->
                                                <form id="{{ $file->id }}" action="{{ route('order-files.delete', ['fileId' => $file->id]) }}" method="POST" style="display: none;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <input type="hidden" id="formFileId" name="fileId" value=""> <!-- Hidden input to dynamically set file ID -->
                                                </form>

                                                <div class="btn-group" style="display: flex; width: 100%;">
                                                    <a href="#" title="View" class="btn btn-outline-info" onclick="openFilePreview({{ $file->id }});"><i class="fa-solid fa-magnifying-glass"></i></a>
                                                    @if(Auth::user()->hasPermissionTo('Delete uploaded files'))
                                                        <button type="submit" class="btn btn-outline-danger" onclick="submitDeleteForm({{ $file->id }});" title="Remove"><i class="fa-solid fa-trash"></i></button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    @foreach($order->files as $file)
                                        @if($file->user->id === Auth::user()->id)
                                            <tr>
                                                <td><i class="fa-regular fa-file"></i> {{$file->file_name}}</td>
                                                <td>{{$file->user->email}} (id: {{$file->user->id}})</td>
                                                <td>{{$file->created_at}}</td>
                                                <td>
                                                    <!-- Hidden form for deletion -->
                                                    <form id="{{ $file->id }}" action="{{ route('order-files.delete', ['fileId' => $file->id]) }}" method="POST" style="display: none;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <input type="hidden" id="formFileId" name="fileId" value=""> <!-- Hidden input to dynamically set file ID -->
                                                    </form>

                                                    <div class="btn-group" style="display: flex; width: 100%;">
                                                        <a href="#" title="View" class="btn btn-outline-info" onclick="openFilePreview({{ $file->id }});"><i class="fa-solid fa-magnifying-glass"></i></a>
                                                        @if(Auth::user()->hasPermissionTo('Delete uploaded files'))
                                                            <button type="submit" class="btn btn-outline-danger" onclick="submitDeleteForm({{ $file->id }});" title="Remove"><i class="fa-solid fa-trash"></i></button>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="filePreviewModal" tabindex="-1" aria-labelledby="filePreviewModalLabel" aria-hidden="true">
            <div class="modal-dialog" id="modalDialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="filePreviewModalLabel">File Preview</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="filePreviewContent">
                        <!-- Content will be loaded here via Ajax -->
                    </div>
                </div>
            </div>
        </div>
@endsection

@section('script')
            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
            <script>
                function openFilePreview(fileId) {
                    var urlTemplate = "{{ route('order-files.show', ['fileId' => ':fileId']) }}";
                    var filePreviewUrl = urlTemplate.replace(':fileId', fileId);

                    $.ajax({
                        url: filePreviewUrl,
                        type: 'GET',
                        success: function(response) {
                            var content = '';
                            var modalDialog = $('#modalDialog');

                            modalDialog.removeClass('large-modal medium-modal'); // Reset classes

                            if (response.success) {
                                if (response.fileType.includes('image')) {
                                    content = '<img src="' + response.src + '" class="img-fluid" />';
                                    modalDialog.addClass('medium-modal');  // 50% modal for images
                                } else if (response.fileType === 'application/pdf') {
                                    content = '<iframe src="' + response.src + '" style="width:100%; height: 100%; border:none;"></iframe>';
                                    modalDialog.addClass('large-modal');  // 90% modal for PDFs
                                }
                            } else {
                                content = '<p>Error: ' + (response.message || 'Unknown error') + '</p>';
                            }

                            $('#filePreviewContent').html(content);
                            $('#filePreviewModal').modal('show');
                        },
                        error: function(error) {
                            $('#filePreviewContent').html('<p>An error occurred while trying to fetch the file.</p>');
                            $('#filePreviewModal').modal('show');
                            console.error(error);
                        }
                    });
                }
            </script>
            <script>
                function submitDeleteForm(fileId) {
                    if (confirm('Are you sure you want to delete this file?')) {
                        var form = document.getElementById(fileId);
                        form.submit();
                    }
                }
            </script>
@endsection
