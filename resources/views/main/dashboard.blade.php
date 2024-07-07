@extends('main.templates.main')
@section('title')
    Dashboard
@endsection

@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4">Dashboard</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item active">Latest updates</li>
        </ol>

        <div class="row mt-3">
            <div class="col-xl-12 col-md-12">
                <div class="card">
                    <div class="card-body"><b><i>2024-07-07</i> Release 1</b></div>
                    <div class="card-footer align-items-center justify-content-between">
                        <p>
                        <ul>
                            <li>Implemented user management feature for admin users</li>
                            <li>Implemented console command to create main admin user</li>
                        </ul>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-xl-12 col-md-12">
                <div class="card">
                    <div class="card-body"><b><i>2024-07-07</i> Project deployment</b></div>
                    <div class="card-footer align-items-center justify-content-between">
                        <p>
                            <ul>
                                <li>Created GitHub repository</li>
                                <li>Created .htaccess file for deploy</li>
                                <li>Project test version deployed on hostinger</li>
                            </ul>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-xl-12 col-md-12">
                <div class="card">
                    <div class="card-body"><b><i>2024-07-07</i> Project setup</b></div>
                    <div class="card-footer align-items-center justify-content-between">
                        <p>
                            <ul>
                                <li>Created init Laravel 11 project</li>
                                <li>Created two main roles: Admin, User</li>
                                <li>Created authorization for users</li>
                                <li>Created basic http auth for unprotected routes: login, register</li>
                                <li>Applied admin panel design</li>
                            </ul>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
