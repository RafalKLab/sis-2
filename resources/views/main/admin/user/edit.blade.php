@extends('main.templates.main')
@section('title')
    Edit user: {{ $user->id }}
@endsection
@section('content')
    <div class="container-fluid px-4">
        <h4 class="mt-4">Edit user: {{ $user->email }} (id:{{ $user->id }})</h4>
        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="POST" action="{{ route('user.update', $user->id) }}">
                            @csrf
                            @method('PUT')
                            <div class="form-group mb-2">
                                <label for="full_name">Full name</label>
                                <input type="text" class="form-control {{ $errors->has('full_name') ? 'is-invalid' : '' }}" id="full_name" name="full_name" placeholder="Enter full name" value="{{ old('full_name', $user->name) }}">
                                @if ($errors->has('full_name'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('full_name') }}
                                    </div>
                                @endif
                            </div>
                            <div class="form-group mb-2">
                                <label for="email">Email address</label>
                                <input type="email" class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}" id="email" name="email" aria-describedby="emailHelp" placeholder="Enter email" value="{{ old('email', $user->email) }}">
                                @if ($errors->has('email'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('email') }}
                                    </div>
                                @endif
                            </div>

                            <input type="hidden" name="is_admin" value="0">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="is_admin" name="is_admin" value="1" {{ old('is_admin', $user->hasRole('admin')) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_admin">Is admin</label>
                            </div>

                            <button type="submit" class="btn btn-primary mt-4">Save</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        User permissions
                        @if($user->hasRole('admin'))
                            <br><span class="text-decoration-underline">This user has admin role and all permissions</span>
                        @endif
                    </div>
                    <div class="card-body">
                        <table class="table text-center">
                            <thead>
                            <tr>
                                <th scope="col">Assigned permissions</th>
                                <th scope="col">Available permissions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($groupedPermissions as $group => $permissions)
                                <tr>
                                    <td style="background-color: #F7F7F7" colspan="4" class="text-center font-weight-bold"><i>{{ $group }}</i></td>
                                </tr>
                                @foreach($permissions as $permission)
                                    <tr>
                                        @if($user->hasPermissionTo($permission->name))
                                            <td>{{ $permission->name }} <a title="Remove permission" href="{{ route('user.remove-permission', ['userId' => $user->id, 'permission' => $permission->name]) }}" class="text-danger"><i class="fa-solid fa-minus"></i></a></td>
                                            <td></td>
                                        @else
                                            <td></td>
                                            <td>{{ $permission->name }} <a title="Give permission" href="{{ route('user.give-permission', ['userId' => $user->id, 'permission' => $permission->name]) }}" class="text-primary"><i class="fa-solid fa-plus"></i></a></td>
                                        @endif
                                    </tr>
                                @endforeach
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
