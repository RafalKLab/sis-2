@extends('main.templates.main')
@section('title')
    Companies
@endsection
@section('content')
    <div class="container-fluid px-4">
        <h4 class="mt-4">Companies</h4>
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="col-md-1"><i class="fa-solid fa-city"></i></div>
                <div class="col-md-1 d-flex justify-content-end">
                    <a href="{{ route('admin-companies.create') }}" class="btn btn-outline-primary"><i class="fa-solid fa-plus"></i></a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table" id="datatablesSimple">
                        <thead>
                        <tr>
                            <th>Id</th>
                            <th>Company name</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($companies as $company)
                            <tr>
                                <th>{{$company->id}}</th>
                                <th>{{$company->name}}</th>
                                <th>
                                    <div class="btn-group" style="display: flex; width: 100%;">
                                        <a href="{{ route('admin-companies.edit', ['id' => $company->id ]) }}" title="Edit" class="btn btn-outline-primary"><i class="fa-solid fa-pen"></i></a>
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
