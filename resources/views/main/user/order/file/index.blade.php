@extends('main.templates.main')
@section('title')
    Order {{ $order->getKeyField() }} files
@endsection

@section('content')
    <div class="container-fluid px-4">
        <h4 class="mt-4">Order {{ $order->getKeyField() }} files</h4>
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
                                @foreach($order->files as $file)
                                    <tr>
                                        <td><i class="fa-regular fa-file"></i> {{$file->file_name}}</td>
                                        <td>{{$file->user->email}} (id: {{$file->user->id}})</td>
                                        <td>{{$file->created_at}}</td>
                                        <td>
                                            <div class="btn-group" style="display: flex; width: 100%;">
                                                <a href="" title="View" class="btn btn-outline-info"><i class="fa-solid fa-magnifying-glass"></i></a>
                                                <a href="" title="Remove" class="disabled btn btn-outline-danger"><i class="fa-solid fa-trash"></i></a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
@endsection

