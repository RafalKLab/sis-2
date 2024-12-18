@extends('main.templates.main')
@section('title')
    Add new goal
@endsection
@section('content')
    <div class="container-fluid px-4">
        <h4 class="mt-4">Add new goal</h4>
        <div class="card mb-4">
            <div class="card-body">
                <form method="POST" action="{{ route('goals.store') }}">
                    @csrf
                    <div class="form-group mb-2">
                        <label for="name">Goal name</label>
                        <input type="text" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" id="name" name="name" placeholder="Enter goal name" value="{{ old('name') }}">
                        @if ($errors->has('name'))
                            <div class="invalid-feedback">
                                {{ $errors->first('name') }}
                            </div>
                        @endif
                    </div>
                    <div class="form-group mb-2">
                        <label for="date">Start date</label>
                        <input type="date" class="form-control {{ $errors->has('date') ? 'is-invalid' : '' }}" id="date" name="date" placeholder="Enter goal start date" value="{{ old('date') }}">
                        @if ($errors->has('date'))
                            <div class="invalid-feedback">
                                {{ $errors->first('date') }}
                            </div>
                        @endif
                    </div>

                    <div class="form-group mb-2">
                        <label for="amount">Amount</label>
                        <input type="text" class="form-control {{ $errors->has('amount') ? 'is-invalid' : '' }}" id="amount" name="amount" placeholder="Enter amount" value="{{ old('amount') }}">
                        @if ($errors->has('amount'))
                            <div class="invalid-feedback">
                                {{ $errors->first('amount') }}
                            </div>
                        @endif
                    </div>

                    <button type="submit" class="btn btn-primary mt-4">Save</button>
                </form>
            </div>
        </div>
    </div>
@endsection

