@extends('main.templates.main')
@section('title')
    Users
@endsection
@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4">Users</h1>
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="col-md-1"><i class="fas fa-table me-1"></i></div>
                <div class="col-md-1 d-flex justify-content-end">
                    <a href="{{ route('user.create') }}" class="btn btn-outline-primary"><i class="fa-solid fa-user-plus"></i></a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table" id="datatablesSimple">
                        <thead>
                        <tr>
                            <th>id</th>
                            <th>full name</th>
                            <th>email</th>
                            <th>role</th>
                            <th>actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($users as $user)
                            <tr>
                                <th>{{$user->id}}</th>
                                <th>{{$user->name}}</th>
                                <th>{{$user->email}}</th>
                                <th>{{$user->roles->first()->name}}</th>
                                <th><a href="{{ route('user.edit', ['id'=>$user->id]) }}" title="Edit" class="btn btn-outline-primary"><i class="fa-solid fa-pen"></i></a></th>
{{--                                <th><a href="{{route('admin.user.assign-fields', ['id' => $user->id])}}">Assign table fields</a></th>--}}
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
