@extends('main.templates.main')
@section('title')
    Field: {{$targetField->name}}
@endsection

@section('styles')
    <link href="{{ asset('css/field.css') }}" rel="stylesheet" />
@endsection

@section('content')
    <div class="container-fluid px-4">
        <h4 class="mt-4">Fields</h4>
        @if($tableFields)
            <div class="row">
                @include('main.admin.field.partials._fields_table')
                @include('main.admin.field.partials._view_field')
            </div>
    </div>
    @else
        No fields found
    @endif
@endsection

