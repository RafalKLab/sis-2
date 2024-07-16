@extends('main.templates.main')
@section('title')
    Order: {{ $orderData['key'] }}
@endsection

@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4">Order: {{ $orderData['key'] }}</h1>
    <div class="row mb-3">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    Order details
                </div>
                <div class="card-body">
                    <table class="table">
                        <tbody>
                        @foreach($orderData['details'] as $data)
                            <tr>
                                <th scope="row">{{ $data['field_name'] }}:</th>
                                <td>{{ $data['value'] }}</td>
                            </tr>
                        @endforeach
                        <!-- Row 1 -->
                        <tr>
                            <th scope="row">Užregistruotas:</th>
                            <td>{{ $orderData['created_at'] }}</td>
                        </tr>
                        <!-- Row 2 -->
                        <tr>
                            <th scope="row">Atnaujintas:</th>
                            <td>{{ $orderData['updated_at'] }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-8">

        </div>
    </div>
@endsection

