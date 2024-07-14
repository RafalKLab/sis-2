@extends('main.templates.main')
@section('title')
    Blocked users
@endsection
@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4">Blocked users</h1>
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="col-md-1"><i class="fa-solid fa-user-lock"></i></div>
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
                                <th>
                                    <div class="btn-group" style="display: flex; justify-content: space-between; width: 100%;">
                                        <a href="{{ route('user-blocked.unblock', ['id'=>$user->id]) }}" title="Unblock" class="btn btn-outline-success"><i class="fa-solid fa-lock-open"></i></a>
                                        <a href="" title="Delete" class="disabled btn btn-outline-danger"><i class="fa-solid fa-trash-can"></i></a>
                                    </div>
                                </th>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
