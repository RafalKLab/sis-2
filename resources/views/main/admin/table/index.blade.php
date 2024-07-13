@extends('main.templates.main')
@section('title')
    Table admin view
@endsection

@section('styles')
    <link href="{{ asset('css/table.css') }}" rel="stylesheet" />
@endsection

@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4">Table admin view</h1>
        @if($tableData)
            <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="col-md-1">
                    <i class="fa-solid fa-table"></i>
                    {{ $tableData['name'] }}
                </div>
                <div class="col-md-1 d-flex justify-content-end">
                    <a href="" title="Add field" class="disabled btn btn-outline-primary"><i class="fa-solid fa-plus"></i></a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table" id="datatablesSimple">
                        <thead>
                        <tr>
                            @foreach($tableData['fields'] as $field)
                                <th>{{$field['name']}}</th>
                            @endforeach
                            <th>files</th>
                            <th>actions</th>
                        </tr>
                        </thead>
                        <tbody>
{{--                        @foreach($users as $user)--}}
{{--                            <tr>--}}
{{--                                <th>{{$user->id}}</th>--}}
{{--                                <th>{{$user->name}}</th>--}}
{{--                                <th>{{$user->email}}</th>--}}
{{--                                <th>@if ($user->is_root) Root @endif {{$user->roles->first()->name}}</th>--}}
{{--                                <th>--}}
{{--                                    @if (!$user->is_root)--}}
{{--                                        <div class="btn-group" style="display: flex; width: 100%;">--}}
{{--                                            <a href="" title="View" class="disabled btn btn-outline-info"><i class="fa-solid fa-magnifying-glass"></i></a>--}}
{{--                                            <a href="{{ route('user.edit', ['id'=>$user->id]) }}" title="Edit" class="btn btn-outline-primary"><i class="fa-solid fa-pen"></i></a>--}}
{{--                                            <a href="" title="Assign fields" class="disabled btn btn-outline-primary"><i class="fa-solid fa-table-cells"></i></a>--}}
{{--                                            <a href="{{ route('user-blocked.block', ['id'=>$user->id]) }}" title="Block" class="btn btn-outline-danger"><i class="fa-solid fa-lock"></i></a>--}}
{{--                                        </div>--}}
{{--                                    @endif--}}
{{--                                </th>--}}
{{--                            </tr>--}}
{{--                        @endforeach--}}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @else
            Table does not exist
        @endif
    </div>
@endsection
