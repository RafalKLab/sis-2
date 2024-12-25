@extends('main.templates.main')
@section('title')
    Add new goal
@endsection
@section('content')
    <div class="container-fluid px-4">
        <h4 class="mt-4">
        @if(isset($goal))
            Edit goal
        @else
            Add new goal
        @endif
        </h4>

        <div class="card mb-4">
            <div class="card-body">
                <form method="POST" action="{{ isset($goal) ? route('goals.update', ['id'=>$goal->id]) : route('goals.store') }}">
                    @csrf
                    @if(isset($goal))
                        @method('PUT')
                    @endif
                    <div class="form-group mb-2">
                        <label for="name">Goal name</label>
                        <input type="text" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" id="name" name="name" placeholder="Enter goal name" value="{{ isset($goal) ? $goal->name : old('name') }}">
                        @if ($errors->has('name'))
                            <div class="invalid-feedback">
                                {{ $errors->first('name') }}
                            </div>
                        @endif
                    </div>

                    <div class="form-group mb-2">
                        <label for="amount">Amount</label>
                        <input type="text" class="form-control {{ $errors->has('amount') ? 'is-invalid' : '' }}" id="amount" name="amount" placeholder="Enter amount" value="{{ isset($goal) ? $goal->amount : old('amount') }}">
                        @if ($errors->has('amount'))
                            <div class="invalid-feedback">
                                {{ $errors->first('amount') }}
                            </div>
                        @endif
                    </div>

                    <div class="form-group mb-2">
                        <label for="date">Start date</label>
                        <input type="date" class="form-control {{ $errors->has('date') ? 'is-invalid' : '' }}" id="date" name="date" placeholder="Enter goal start date" value="{{ isset($goal) ? $goal->start_date : old('date') }}">
                        @if ($errors->has('date'))
                            <div class="invalid-feedback">
                                {{ $errors->first('date') }}
                            </div>
                        @endif
                    </div>

                    <div class="form-group mb-2">
                        <label for="visible">Visible</label>
                        <select class="form-control" name="is_visible" id="visible" required>
                            <option value="0" {{ (isset($goal) && $goal->is_visible == 0) ? 'selected' : '' }} {{ (old('is_visible') === "0") ? 'selected' : '' }}>NO</option>
                            <option value="1" {{ (isset($goal) && $goal->is_visible == 1) ? 'selected' : '' }} {{ (old('is_visible') === "1") ? 'selected' : '' }}>YES</option>
                        </select>
                        <small id="visible_help" class="form-text text-muted">Visible on main statistics page.</small>
                    </div>

                    <button type="submit" class="btn btn-primary mt-4">Save</button>
                </form>
            </div>
        </div>
    </div>
@endsection

