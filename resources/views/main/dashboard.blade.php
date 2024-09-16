@extends('main.templates.main')
@section('title')
    Dashboard
@endsection

@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4">Dashboard</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item active"></li>
        </ol>

        <form class="form-control" method="post" action="{{ route('feedback.create') }}">
            @csrf
            <div class="form-group">
                <label for="feedback">Feedback</label>
                <textarea id="feedback" name="feedback" class="form-control"></textarea>
            </div>
            <button type="submit" class="btn btn-primary mt-2">Submit</button>
        </form>

        <div class="row mt-3">
            @foreach($feedback as $entry)
                <div class="col-xl-12 col-md-12 mb-3">
                    <div class="card">
                        <div class="card-body"><b>{{ $entry->user }}</b> <i>{{ $entry->created_at->format('Y-m-d') }}</i></div>
                        <div class="card-footer align-items-center justify-content-between">
                            <p>
                                {{ $entry->message }}
                            </p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
