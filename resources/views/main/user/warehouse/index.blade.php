@extends('main.templates.main')
@section('title')
    Warehouses
@endsection
@section('styles')
    <link href="{{ asset('css/warehouse.css') }}" rel="stylesheet" />
@endsection
@section('content')
    <div class="container-fluid px-4">
        <h4 class="mt-4">Warehouses</h4>
        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="col-md-1"><i class="fa-solid fa-warehouse"></i></div>
                        <div class="col-md-3 d-flex justify-content-end">
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table">
                            <thead>
                            <tr>
                                <th scope="col">Id</th>
                                <th scope="col">Name</th>
                                <th scope="col">Address</th>
                                <th scope="col">Products in stock</th>
                                <th scope="col">Status</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($warehouses as $warehouse)
                                <tr data-url="{{ route('warehouses.view', ['name'=>$warehouse->name])}}">
                                    <th scope="row">{{ $warehouse->id }}</th>
                                    <td>{{ $warehouse->name }}</td>
                                    <td>{{ $warehouse->address }}</td>
                                    <td>{{ $productsInStock[$warehouse->id] }}</td>
                                    <td>
                                        @if($warehouse->is_active)
                                            <a class="btn btn-outline-success status-button" href="#">Active</a>
                                        @else
                                            <a class="btn btn-outline-danger status-button" href="#">Disabled</a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="col-md-6">Add new warehouse</div>
                        <div class="col-md-6 d-flex justify-content-end">
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="post" action="{{ route('warehouses.create') }}">
                            @csrf
                            <div class="form-group row mb-3">
                                <label for="name" class="col-md-2 col-form-label">Name</label>
                                <div class="col-md-10">
                                    <input name="name" type="text" class="form-control @error('name') is-invalid @enderror" id="name" placeholder="Warehouse name" value="{{ old('name') }}">
                                    @if ($errors->has('name'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('name') }}
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group row mb-3">
                                <label for="address" class="col-md-2 col-form-label">Address</label>
                                <div class="col-md-10">
                                    <input name="address" type="text" class="form-control @error('address') is-invalid @enderror" id="address" placeholder="Warehouse address" value="{{ old('address') }}">
                                    @if ($errors->has('address'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('address') }}
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group row mb-3">
                                <label for="is_active" class="col-md-2 col-form-label">Status</label>
                                <div class="col-md-10">
                                    <select name="is_active" id="is_active" class="form-control @error('is_active') is-invalid @enderror">
                                        <option value="1" {{ old('is_active') == '1' ? 'selected' : '' }}>Active</option>
                                        <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Disabled</option>
                                    </select>
                                    @if ($errors->has('is_active'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('is_active') }}
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-md-10 offset-md-2">
                                    <button type="submit" class="btn btn-primary">Create</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get all table rows within the tbody
            var rows = document.querySelectorAll("table.table tbody tr");

            // Add a click event listener for each row
            rows.forEach(function(row) {
                row.addEventListener('click', function() {
                    var url = this.getAttribute('data-url'); // Get the row's data-url attribute
                    window.location.href = url; // Redirect to the URL
                });
            });
        });
    </script>
@endsection

