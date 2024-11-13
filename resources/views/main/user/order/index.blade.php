@extends('main.templates.main')
@section('title')
    Orders
@endsection

@section('styles')
    <link href="{{ asset('css/table.css') }}" rel="stylesheet" />
@endsection

@section('content')
    <div class="container-fluid px-4 main-orders">
        @if(config('app.debug'))
{{--            <h6>Load time: {{ $execution_time }}</h6>--}}
        @endif

        @if($tableData)
                <div class="scroll-wrapper">
                    <div class="d-flex justify-content-between align-items-center w-100">
                        <h4 class="mb-0">Orders view</h4>
                        <div class="pagination-nav">
                            {!! $tableData['orders']['links'] !!}
                        </div>
                        <div>
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

                    <div class="table-responsive inverted-scroll">
                        <table class="table" id="orders-table-admin-sticky">
                            <thead>
                            <tr>
                                @foreach($tableData['fields'] as $index => $field)
                                    @if($index == 1)
                                        <th>Komentarai</th>
                                    @endif
                                    @if($index == 3)
                                        <th>Užregistravo</th>
                                        {{--                                            <th>Pirkėjas / Tarpininkas</th>--}}
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
                        </table>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table" id="orders-table-admin">
                        <tbody>
                        @foreach($tableData['orders']['data'] as $order)
                            <tr>
                                @foreach($tableData['fields'] as $index => $field)
                                    @if($index == 1)
                                        <td class="order-related">{{ $order['comment'] }}</td>
                                    @endif
                                    @if($index == 3)
                                        <td class="order-related">{{ $order['user'] }}</td>
                                        {{--                                                <td>{{ $order['company'] }}</td>--}}
                                    @endif
                                    @can('See order products')
                                        @if($index == 3)
                                            <td class="order-related">
                                                <table class="items-table">
                                                    <thead>
                                                    <tr>
                                                        @can('See item buyer')
                                                            {{--                                                                <th style="width: 100%">Pirkėjai</th>--}}
                                                        @endcan
                                                        @foreach($order['items']['fields'] as $itemIndex => $itemField)
                                                            <th>{{ $itemField['name'] }}</th>
                                                            @if($itemIndex === 0)
                                                                <th>Pirkėjas / Tarpininkas</th>
                                                                @can('See item buyer')
                                                                    <th style="width: 100%">Pirkėjai</th>
                                                                @endcan
                                                            @endif
                                                        @endforeach
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @foreach($order['items']['data'] as $itemData)
                                                        <tr>
                                                            @foreach($itemData['details'] as $itemIndex => $itemDatum)
                                                                <td>{!! $itemDatum !!}</td>
                                                                @if($itemIndex === 0)
                                                                    <td>{{ $order['company'] }}</td>
                                                                    @can('See item buyer')
                                                                        <td>
                                                                            {{ $itemData['buyers'] }}
                                                                        </td>
                                                                    @endcan
                                                                @endif
                                                            @endforeach
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
                                                <td class="order-related">
                                                    <a class="order-view-link" href="{{ route('orders.view', ['id'=>$order['id']]) }}">
                                                        {{ $order[$field['name']] }}
                                                    </a>
                                                </td>
                                                @break
                                            @case('file')
                                                <td class="order-related"><a href="{{ route('order-files.index', ['orderId' => $order['id']]) }}" class="order-view-link" title="View files"><i class="fa-regular fa-file"></i> {{$order['uploaded_files']}}</a></td>
                                                @break
                                            @case('select status')
                                                <td class="order-related">
                                                    @if($order[$field['name']])
                                                        <div class="order-field-status-{{$order['config'][$field['name']]['status_color_class']}}">
                                                            {{ $order[$field['name']] }}
                                                        </div>
                                                    @endif
                                                </td>
                                                @break
                                            @case('invoice')
                                                <td class="order-related">
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
                                                <td style="font-size: 17px;" class="order-related"><b>{{ $order[$field['name']] }}</b></td>
                                                @break
                                            @case('load date')
                                                <td class="order-related">
                                                    @if($order[$field['name']])
                                                        <div class="order-field-status-yellow">
                                                            {{ $order[$field['name']] }}
                                                        </div>
                                                    @endif
                                                </td>
                                                @break
                                            @case('delivery date')
                                                <td class="order-related">
                                                    @if($order[$field['name']])
                                                        <div class="order-field-status-green">
                                                            {{ $order[$field['name']] }}
                                                        </div>
                                                    @endif
                                                </td>
                                                @break
                                            @default
                                                <td class="order-related">{{ $order[$field['name']] }}</td>
                                        @endswitch
                                    @else
                                        <td class="order-related"></td>
                                    @endif
                                @endforeach
                                <td class="order-related">
                                    <div class="btn-group" style="display: flex; width: 100%;">
                                        <a href="{{ route('orders.view', ['id'=>$order['id']]) }}" title="View" class="btn btn-outline-primary">View</a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
{{--            <div class="card mb-4">--}}
{{--                <div class="card-header d-flex justify-content-between align-items-center">--}}
{{--                    <div class="col-md-1">--}}
{{--                        <i class="fa-solid fa-table"></i>--}}
{{--                        {{ $tableData['name'] }}--}}
{{--                    </div>--}}

{{--                </div>--}}

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


            var headerTable = document.getElementById('orders-table-admin-sticky');
            var contentTable = document.getElementById('orders-table-admin');

            var headerTableWrapper = document.querySelector('.table-responsive.inverted-scroll'); // Wrapper of the header table
            var contentTableWrapper = document.querySelector('.table-responsive:not(.inverted-scroll)'); // Wrapper of the content table

            // Sync the horizontal scroll positions
            function syncScroll(){
                // When the header scrolls, the content should follow
                headerTableWrapper.onscroll = function() {
                    contentTableWrapper.scrollLeft = this.scrollLeft;
                };

                // When the content scrolls, the header should follow
                contentTableWrapper.onscroll = function() {
                    headerTableWrapper.scrollLeft = this.scrollLeft;
                };
            }

            syncScroll(); // Initialize the syncScroll function

            // Function to apply the widths of content cells to header cells
            var syncColumnWidthsAndCountDifferences = function() {
                // Select all 'th' elements within the header table (assuming all th need to be adjusted)
                var headerCells = headerTable.querySelectorAll('th');
                // Select only the first 'td' elements with the 'order-related' class within the first row of the content table
                var contentCells = contentTable.querySelectorAll('tbody tr:first-child td.order-related');
                var mismatchCount = 0;

                if (headerCells.length !== contentCells.length) {
                    console.error('The number of header cells and "order-related" content cells do not match.');
                    return;
                }

                for (var i = 0; i < contentCells.length; i++) {
                    var contentCellWidth = contentCells[i].getBoundingClientRect().width;

                    // Apply the width of content cells to header cells
                    headerCells[i].style.minWidth = contentCellWidth + 'px';
                }

                // Count discrepancies after setting widths
                for (var i = 0; i < headerCells.length; i++) {
                    var headerCellWidth = headerCells[i].getBoundingClientRect().width;
                    if (headerCellWidth !== contentCellWidth) {
                        mismatchCount++;
                    }
                }

                if (mismatchCount > 0) {
                    console.log(mismatchCount + ' header cells have different widths from their corresponding "order-related" content cells.');
                } else {
                    console.log('All header and "order-related" content cell widths match.');
                }
            };

            // Ensure the widths are set after the tables have fully rendered
            window.onload = syncColumnWidthsAndCountDifferences;

            // Re-sync widths when the window is resized
            window.addEventListener('resize', syncColumnWidthsAndCountDifferences);
        });
    </script>
@endsection

