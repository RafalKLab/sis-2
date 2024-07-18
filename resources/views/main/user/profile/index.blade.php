@extends('main.templates.main')
@section('title')
    Profile
@endsection
@section('content')
    <div class="container-fluid px-4">
        <h4 class="mt-4">Profile Information</h4>
        <div class="card mb-4">
            <div class="card-header">
                Update your account's profile information.
            </div>
            <div class="card-body">
                <form method="post" action="{{ route('profile.update') }}">
                    @csrf
                    @method('patch')
                    <div class="form-group mb-2">
                        <label for="name">Full name</label>
                        <input type="text" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" id="name" name="name" placeholder="Enter full name" value="{{ Auth::User()->name  }}">
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

        <h4 class="mt-4">Update Password</h4>
        <div class="card mb-4">
            <div class="card-header">
                Ensure your account is using a long, random password to stay secure.
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('password.update') }}">
                    @csrf
                    @method('PUT')
                    <div class="form-group mb-2">
                        <label for="current_password">Password</label>
                        <input type="password" class="form-control {{ $errors->has('current_password') ? 'is-invalid' : '' }}" id="current_password" name="current_password" placeholder="Current password">
                        @if ($errors->has('current_password'))
                            <div class="invalid-feedback">
                                {{ $errors->first('current_password') }}
                            </div>
                        @endif
                    </div>

                    <div class="form-group mb-2">
                        <label for="update_password_password">New password</label>
                        <input type="password" class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}" id="update_password_password" name="password" placeholder="New password">
                        @if ($errors->has('password'))
                            <div class="invalid-feedback">
                                {{ $errors->first('password') }}
                            </div>
                        @endif
                    </div>

                    <div class="form-group mb-2">
                        <label for="update_password_password_confirmation">New password</label>
                        <input type="password" class="form-control {{ $errors->has('password_confirmation') ? 'is-invalid' : '' }}" id="update_password_password_confirmation" name="password_confirmation" placeholder="Repeat password">
                        @if ($errors->has('password_confirmation'))
                            <div class="invalid-feedback">
                                {{ $errors->first('password_confirmation') }}
                            </div>
                        @endif
                    </div>

                    <button type="submit" class="btn btn-primary mt-4">Save</button>
                </form>
            </div>
        </div>
    </div>
@endsection

