@extends('main.templates.main')
@section('title')
    Table admin view
@endsection

@section('styles')
    <link href="{{ asset('css/table.css') }}" rel="stylesheet" />
@endsection

@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4">Table admin view</h1>
        @if($tableData)
            <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="col-md-1">
                    <i class="fa-solid fa-table"></i>
                    {{ $tableData['name'] }}
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table" id="orders-table-admin">
                        <thead>
                        <tr>
                            @foreach($tableData['fields'] as $field)
                                <th style="background-color: {{$field['color']}};">{{$field['name']}}</th>
                            @endforeach
                            <th>files</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($tableData['orders']['data'] as $order)
                            <tr>
                                @foreach($tableData['fields'] as $field)
                                    @if(array_key_exists($field['name'], $order))
                                        <td>{{ $order[$field['name']] }}</td>
                                    @else
                                        <td></td>
                                    @endif
                                @endforeach
                                <td>files</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center mt-3">
                    {!! $tableData['orders']['links'] !!}
                </div>
                </div>
            </div>
        </div>
        @else
            Table does not exist
        @endif
    </div>
@endsection

