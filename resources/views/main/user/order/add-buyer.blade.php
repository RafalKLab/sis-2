@extends('main.templates.main')
@section('title')
    Add item buyer for order: {{ $orderData['key'] }}
@endsection

@section('content')
    <div class="container-fluid px-4">
        <h4 class="mt-4">Add item buyer for order: {{ $orderData['key'] }}</h4>
        <div class="row mb-3">
            @include('main.user.order.partials._details')
            @include('main.user.order.partials._buyer_form')
        </div>
@endsection

