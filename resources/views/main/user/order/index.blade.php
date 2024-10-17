@extends('main.templates.main')
@section('title')
    Orders
@endsection

@section('styles')
    <link href="{{ asset('css/table.css') }}" rel="stylesheet" />
@endsection

@section('content')
    <div class="container-fluid px-4">
        @if(config('app.debug'))
            <h6>Load time: {{ $execution_time }}</h6>
        @endif
        <h4 class="mt-4">Orders view</h4>
        @if($tableData)
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="col-md-1">
                        <i class="fa-solid fa-table"></i>
                        {{ $tableData['name'] }}
                    </div>
                    <div class="col-md-3 d-flex justify-content-end">
                        <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0" method="GET" action="{{ route('orders.index') }}">
                            <div class="input-group">
                                <input
                                    class="form-control"
                                    type="text"
                                    placeholder="Search for orders"
                                    aria-label="Search for orders"
                                    aria-describedby="btnNavbarSearch"
                                    name="search"
                                    value="{{ $search }}"
                                />
                                <button class="btn btn-primary" id="btnNavbarSearch" type="submit"><i class="fas fa-search"></i></button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    <div class="scrollbar-mirror" id="top-scrollbar"></div>
                    <div class="table-responsive">
                        <table class="table" id="orders-table-admin">
                            <thead>
                            <tr>
                                @foreach($tableData['fields'] as $index => $field)
                                    @if($index == 1)
                                        <th>Užregistravo</th>
                                    @endif
                                    @if($index == 3)
                                            <th>Pirkėjas 1</th>
                                    @endif
                                    @can('See order products')
                                        @if($index == 3)
                                            <th>Prekių sąrašas</th>
                                        @endif
                                    @endcan

                                    <th style="background-color: {{$field['color']}};">{{$field['name']}}</th>
                                @endforeach
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($tableData['orders']['data'] as $order)
                                <tr class>
                                    @foreach($tableData['fields'] as $index => $field)
                                        @if($index == 1)
                                            <td>{{ $order['user'] }}</td>
                                        @endif
                                            @if($index == 3)
                                                <td>{{ $order['company'] }}</td>
                                            @endif
                                        @can('See order products')
                                            @if($index == 3)
                                                <td>
                                                    <table class="items-table">
                                                        <thead>
                                                        <tr>
                                                            @foreach($order['items']['fields'] as $index => $itemField)
                                                                <th>{{ $itemField['name'] }}</th>
                                                            @endforeach
                                                                @can('See item buyer')
                                                                    <th style="width: 100%">Pirkėjai</th>
                                                                @endcan

                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        @foreach($order['items']['data'] as $itemData)
                                                            <tr>
                                                                @foreach($itemData['details'] as $index => $itemDatum)
                                                                    <td>{!! $itemDatum !!}</td>
                                                                @endforeach
                                                                    @can('See item buyer')
                                                                        <td>
                                                                            {{ $itemData['buyers'] }}
                                                                        </td>
                                                                    @endcan
                                                            </tr>
                                                        @endforeach
                                                        </tbody>
                                                    </table>
                                                </td>
                                            @endif
                                        @endcan

                                        @if(array_key_exists($field['name'], $order))
                                            @switch($field['type'])
                                                @case('id')
                                                    <td>
                                                        <a class="order-view-link" href="{{ route('orders.view', ['id'=>$order['id']]) }}">
                                                            {{ $order[$field['name']] }}
                                                        </a>
                                                    </td>
                                                    @break
                                                @case('file')
                                                    <td><a href="{{ route('order-files.index', ['orderId' => $order['id']]) }}" class="order-view-link" title="View files"><i class="fa-regular fa-file"></i> {{$order['uploaded_files']}}</a></td>
                                                    @break
                                                @case('select status')
                                                    <td>
                                                        @if($order[$field['name']])
                                                            <div class="order-field-status-{{$order['config'][$field['name']]['status_color_class']}}">
                                                                {{ $order[$field['name']] }}
                                                            </div>
                                                        @endif
                                                    </td>
                                                    @break
                                                @case('invoice')
                                                    <td>
                                                        @if($order[$field['name']])
                                                            <div class="{{$order['config'][$field['name']]['status_color_class']}}">
                                                                {{ $order[$field['name']] }}
                                                                @if($order['config'][$field['name']]['status_color_class'] === 'order-field-status-red invoice-after-deadline')
                                                                    <i title="Mokėjimo terminas yra pasibaigęs" class="fa-solid fa-triangle-exclamation"></i>
                                                                @elseif($order['config'][$field['name']]['status_color_class'] === 'order-field-status-yellow')
                                                                    <i title="Iki mokėjimo termino pabaigos liko mažiau nei 3 dienos" class="fa-solid fa-triangle-exclamation"></i>
                                                                @elseif($order['config'][$field['name']]['status_color_class'] === 'order-field-status-green')
                                                                    <i title="Apmokėta" class="fa-solid fa-circle-check"></i>
                                                                @endif
                                                            </div>
                                                        @endif
                                                    </td>
                                                    @break
                                                @case('profit')
                                                        <td style="font-size: 17px;"><b>{{ $order[$field['name']] }}</b></td>
                                                    @break
                                                @case('load date')
                                                    <td>
                                                        @if($order[$field['name']])
                                                            <div class="order-field-status-yellow">
                                                                {{ $order[$field['name']] }}
                                                            </div>
                                                        @endif
                                                    </td>
                                                    @break
                                                @case('delivery date')
                                                    <td>
                                                        @if($order[$field['name']])
                                                            <div class="order-field-status-green">
                                                                {{ $order[$field['name']] }}
                                                            </div>
                                                        @endif
                                                    </td>
                                                    @break
                                                @default
                                                    <td>{{ $order[$field['name']] }}</td>
                                            @endswitch
                                        @else
                                            <td></td>
                                        @endif
                                    @endforeach
                                    <td>
                                        <div class="btn-group" style="display: flex; width: 100%;">
                                            <a href="{{ route('orders.view', ['id'=>$order['id']]) }}" title="View" class="btn btn-outline-primary">View</a>
                                        </div>
                                    </td>
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
    @endsection

@section('script')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var table = document.getElementById('orders-table-admin');
            table.addEventListener('click', function (e) {
                var target = e.target;
                while (target && target.nodeName !== 'TR') {
                    target = target.parentElement;
                }
                if (target) {
                    target.classList.toggle('marked-row');
                }
            });


            var tableContainer = document.querySelector('.table-responsive');
            var topScrollbar = document.getElementById('top-scrollbar');

            // Create a dummy div inside the top scrollbar container that matches the width of the table
            var scrollContent = document.createElement('div');
            scrollContent.style.width = `${tableContainer.scrollWidth}px`;
            scrollContent.style.height = '1px'; // Invisible but necessary for scrolling
            topScrollbar.appendChild(scrollContent);

            // Function to sync scroll positions
            topScrollbar.onscroll = function() {
                tableContainer.scrollLeft = this.scrollLeft;
            };
            tableContainer.onscroll = function() {
                topScrollbar.scrollLeft = this.scrollLeft;
            };
        });
    </script>
@endsection

