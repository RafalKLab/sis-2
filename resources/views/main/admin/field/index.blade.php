@extends('main.templates.main')
@section('title')
    Fields settings
@endsection

@section('content')
    <div class="container-fluid px-4">
        <h4 class="mt-4">Fields</h4>
        @if($tableFields)
            <div class="row">
                @include('main.admin.field.partials._fields_table')
            </div>
    </div>
    @else
        No fields found
    @endif
@endsection

