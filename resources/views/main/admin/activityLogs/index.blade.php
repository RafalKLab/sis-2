@extends('main.templates.main')
@section('title')
    Activity logs
@endsection
@section('content')
    <div class="container-fluid px-4">
        <h4 class="mt-4">Activity logs</h4>
        <h6>{{ $dateRange }}</h6>
        <div class="row">
            <div class="col-md-12">
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="row">
                            <form action="{{ route('logs.index') }}" class="d-flex" method="GET">
                                @csrf
                                <div class="p-1">
                                    <label for="from_date" class="">From:</label>
                                </div>
                                <div class="p-1">
                                    <input type="date" id="from_date" name="from_date" class="form-control" placeholder="From date">
                                </div>
                                <div class="p-1">
                                    <label for="to_date" class="">To:</label>
                                </div>
                                <div class="p-1">
                                    <input type="date" id="to_date" name="to_date" class="form-control" placeholder="To date">
                                </div>
                                <div class="p-1">
                                    <button type="submit" class="btn btn-primary">Apply filters</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        Info logs
                    </div>
                    <div class="card-body">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table" id="infoLogsTable">
                                    <thead>
                                    <tr>
                                        <th>Author</th>
                                        <th>Description</th>
                                        <th>Date</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($infoLogs as $log)
                                        <tr>
                                            <th>{{$log->user}}</th>
                                            <th>{{$log->description}}</th>
                                            <th>{{$log->created_at}}</th>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header bg-warning text-white">
                        Warning logs
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table" id="warningLogsTable">
                                <thead>
                                <tr>
                                    <th>Author</th>
                                    <th>Description</th>
                                    <th>Date</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($warningLogs as $log)
                                    <tr>
                                        <th>{{$log->user}}</th>
                                        <th>{{$log->description}}</th>
                                        <th>{{$log->created_at}}</th>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header bg-danger text-white">
                        Danger logs
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table" id="dangerLogsTable">
                                <thead>
                                <tr>
                                    <th>Author</th>
                                    <th>Description</th>
                                    <th>Date</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($dangerLogs as $log)
                                    <tr>
                                        <th>{{$log->user}}</th>
                                        <th>{{$log->description}}</th>
                                        <th>{{$log->created_at}}</th>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
