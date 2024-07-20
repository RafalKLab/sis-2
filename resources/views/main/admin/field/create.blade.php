@extends('main.templates.main')
@section('title')
    Create new field
@endsection

@section('content')
    <div class="container-fluid px-4">
        <h4 class="mt-4">Fields</h4>
        @if($tableFields)
            <div class="row">
                @include('main.admin.field.partials._fields_table')
                @include('main.admin.field.partials._create_field_form')
            </div>
        @endif
    </div>

@endsection

