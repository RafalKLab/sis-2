@extends('main.templates.main')
@section('title')
    Create new user
@endsection
@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4">Create new user</h1>
        <div class="card mb-4">
            <div class="card-body">
                <form method="POST" action="{{ route('user.store') }}">
                    @csrf
                    <div class="form-group mb-2">
                        <label for="full_name">Full name</label>
                        <input type="text" class="form-control {{ $errors->has('full_name') ? 'is-invalid' : '' }}" id="full_name" name="full_name" placeholder="Enter full name" value="{{ old('full_name') }}">
                        @if ($errors->has('full_name'))
                            <div class="invalid-feedback">
                                {{ $errors->first('full_name') }}
                            </div>
                        @endif
                    </div>
                    <div class="form-group mb-2">
                        <label for="email">Email address</label>
                        <input type="email" class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}" id="email" name="email" aria-describedby="emailHelp" placeholder="Enter email" value="{{ old('email') }}">
                        @if ($errors->has('email'))
                            <div class="invalid-feedback">
                                {{ $errors->first('email') }}
                            </div>
                        @endif
                    </div>
                    <div class="form-group mb-2">
                        <label for="password">Password</label>
                        <input type="password" class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}" id="password" name="password" placeholder="Password">
                        @if ($errors->has('password'))
                            <div class="invalid-feedback">
                                {{ $errors->first('password') }}
                            </div>
                        @endif
                    </div>
                    <div class="form-group mb-2">
                        <label for="password_confirmation">Repeat password</label>
                        <input type="password" class="form-control {{ $errors->has('password_confirmation') ? 'is-invalid' : '' }}" id="password_confirmation" name="password_confirmation" placeholder="Repeat password">
                        @if ($errors->has('password_confirmation'))
                            <div class="invalid-feedback">
                                {{ $errors->first('password_confirmation') }}
                            </div>
                        @endif
                    </div>

                    <input type="hidden" name="is_admin" value="0">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="is_admin" name="is_admin" value="1" {{ old('is_admin') ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_admin">Is admin</label>
                    </div>

                    <button type="submit" class="btn btn-primary mt-4">Save</button>
                </form>
            </div>
        </div>
    </div>
@endsection

