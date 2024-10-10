@extends('main.templates.main')
@section('title')
    Create new company
@endsection
@section('content')
    <div class="container-fluid px-4">
        <h4 class="mt-4">Create new company</h4>
        <div class="card mb-4">
            <div class="card-body">
                <form method="POST" action="">
                    @csrf
                    <div class="form-group mb-2">
                        <label for="name">Company name</label>
                        <input type="text" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" id="name" name="name" placeholder="Enter company name" value="{{ old('name') }}">
                        @if ($errors->has('name'))
                            <div class="invalid-feedback">
                                {{ $errors->first('name') }}
                            </div>
                        @endif
                    </div>

                    <button type="submit" class="btn btn-primary mt-4">Save</button>
                </form>
            </div>
        </div>
    </div>
@endsection

