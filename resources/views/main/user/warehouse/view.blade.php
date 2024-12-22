@extends('main.templates.main')
@section('title')
    Warehouse: {{ $warehouse->name }}
@endsection
@section('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/odometer.js/0.4.8/themes/odometer-theme-default.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/odometer.js/0.4.8/odometer.min.js"></script>
    <link href="{{ asset('css/warehouse.css') }}" rel="stylesheet" />
@endsection
@section('content')
    <div class="container-fluid px-4">
        <h4 class="mt-4"></h4>
        <div class="row">
            <div class="col-md-9">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="col-md-5"><i class="fa-solid fa-box-open"></i>Products in stock
                        </div>
                        <div class="col-md-3 d-flex justify-content-end">
                        </div>
                    </div>
                    @if($items['items_exceeding_deadline'])
                        <div style="padding: 5px;">
                            <div class="alert alert-danger">{{ $items['items_exceeding_deadline'] }} prekės viršija preliminarią datą</div>
                        </div>
                    @endif
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table" id="datatablesSimple">
                                <thead>
                                <tr>
                                    <th scope="col">Užsakymas</th>
                                    <th scope="col">Pavadinimas</th>
                                    <th scope="col">Išmatavimai</th>
                                    <th scope="col">Klijai</th>
                                    <th scope="col">Kokybė</th>
                                    <th scope="col">Kiekis</th>
                                    <th scope="col">Vieneto kaina</th>
                                    <th scope="col">Vieneto savikaina</th>
                                    <th scope="col">Bendra vertė</th>
                                    <th scope="col">Komentarai</th>
                                    <th scope="col">Preliminari data</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($items['items'] as $item)
                                    <tr>
                                        <td>
                                            <a class="custom-link" href="{{ route('orders.view', ['id'=>$item['order']['id']]) }}">
                                                {{ $item['order']['key'] }}
                                            </a>
                                        </td>
                                        <td>{{ $item['name'] }}</td>
                                        <td>{{ $item['measurement'] }}</td>
                                        <td>{{ $item['glue'] }}</td>
                                        <td>{{ $item['quality'] }}</td>
                                        <td>{{ $item['amount'] }} {{ $item['measurement_unit'] }}</td>
                                        <td>{{ $item['price'] }}</td>
                                        <td>{{ $item['prime_cost'] }}</td>
                                        <td>{{ $item['total_price'] }}</td>
                                        <td>
                                            <div class="d-flex justify-content-between">
                                                <div>
                                                    <p>Preke turi buti parduota.
                                                    <small class="text-secondary">rafcioks@gmail.com <br> 2024-12-22</small>
                                                    </p>
                                                </div>
                                                <div><a href="" class="text-primary"><i class="fa-regular fa-window-restore"></i></a></div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-between">
                                                @if($item['exceeded_deadline'])
                                                    <div class="text-danger"><i title="Viršyta preliminari data" class="fa-solid fa-triangle-exclamation text-danger"></i>&nbsp;<b>{{ $item['tentative_date'] }}</b></div>
                                                @else
                                                    <div> {{ $item['tentative_date'] }}</div>
                                                @endif
                                                @can('Update tentative date')
                                                    <div>
                                                        <a href="" onclick="setItemIdForDateForm({{$item['item_id']}}, `{{ $item['tentative_date'] }}`); showModal('#dateModal'); event.preventDefault();" class="{{ $item['exceeded_deadline'] ? 'text-danger' : 'text-primary' }}"><i class="fa-regular fa-calendar"></i></a></div>
                                                    </div>
                                               @endcan
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div class="col-md-6">
                                    <i class="fa-solid fa-warehouse"></i>
                                    {{ $warehouse->name }} <span class="text-danger">
                                        @if(!$warehouse->is_active)
                                            Disabled
                                        @endif
                            </span>
                                </div>
                                <div class="col-md-3 d-flex justify-content-end">
                                    <span class="text-primary"><i style="cursor: pointer" id="edit_warehouse" class="fa-solid fa-pen" onclick="showModal('#updateWarehouseModal')"></i></span>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">

                                    @foreach($warehouseItemsValueByName as $item => $itemData)
                                        <div class="col-md-12">
                                            <div class="card product-worth">
                                                <div class="amount-block">
                                                    {{ $item }}
                                                    <span id="totalProducts" class="totalProducts" data-target="{{ $itemData['amount'] }}">0</span>
                                                    {{ $itemData['unit'] }}
                                                    <span id="netWorth" class="netWorth" data-target="{{ $itemData['total_price'] }}">0.00</span> €
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="card mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div class="col-md-6">
                                    <i title="Showing last 30 entries" class="fa-solid fa-arrow-right-arrow-left"></i>
                                    Overview
                                </div>
                            </div>
                            <div class="card-body">
                                <row>
                                    <div class="col-md-12">
                                        <div class="">
                                            <table class="table">
                                                <tbody>
                                                @foreach($warehouseStockOverview as $itemStock)
                                                    <tr>
                                                        <td>{{ $itemStock['item_name'] }} (id:{{ $itemStock['warehouse_item_id'] }})</td>
                                                        <td>
                                                            {{ $itemStock['quantity'] }}
                                                            </br>
                                                            @if($itemStock['type'] === 'incoming')
                                                                <i class="fa-solid fa-arrow-left text-success "></i>
                                                            @else
                                                                <i class="fa-solid fa-arrow-right text-danger"></i>
                                                            @endif
                                                        </td>
                                                        <td><a class="custom-link" href="{{ route('orders.view', ['id'=>$itemStock['order_id']]) }}">{{ $itemStock['order_key'] }}</a></td>
                                                        <td>{{ $itemStock['date'] }}</td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                            Showing 30 newest entries
                                        </div>
                                    </div>
                                </row>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="updateWarehouseModal" tabindex="-1" role="dialog" aria-labelledby="updateWarehouseModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateWarehouseModalLabel">Update Warehouse</h5>
                </div>
                <div class="modal-body">
                    <form action="" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group row mb-3">
                            <label for="name" class="col-md-2 col-form-label">Name</label>
                            <div class="col-md-10">
                                <input name="name" type="text" class="form-control @error('name') is-invalid @enderror" id="name" placeholder="Warehouse name" value="{{ old('name', $warehouse->name) }}">
                                @if ($errors->has('name'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('name') }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="address" class="col-md-2 col-form-label">Address</label>
                            <div class="col-md-10">
                                <input name="address" type="text" class="form-control @error('address') is-invalid @enderror" id="address" placeholder="Warehouse address" value="{{ old('address', $warehouse->address) }}">
                                @if ($errors->has('address'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('address') }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="is_active" class="col-md-2 col-form-label">Status</label>
                            <div class="col-md-10">
                                <select name="is_active" id="is_active" class="form-control @error('is_active') is-invalid @enderror">
                                    <option value="1" {{ old('is_active', $warehouse->is_active) == '1' ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ old('is_active', $warehouse->is_active) == '0' ? 'selected' : '' }}>Disabled</option>
                                </select>
                                @if ($errors->has('is_active'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('is_active') }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Your form fields go here --}}

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Date Modal -->
    <div class="modal fade" id="dateModal" tabindex="-1" aria-labelledby="dateModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="dateModalLabel">Preliminari data</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="dateForm" method="POST" action="{{ route('warehouses.update-date') }}">
                        @csrf
                        <input type="hidden" id="itemIdField" name="item_id" value="">
                        <input type="hidden" name="warehouse_name" value="{{ $warehouse->name }}">

                        <div class="mb-3">
                            <input type="date" class="form-control" id="dateField" name="date" required>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('script')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        function animateValue(obj, start, end, duration, decimals = 0) {
            let startTimestamp = null;
            const step = (timestamp) => {
                if (!startTimestamp) startTimestamp = timestamp;
                const progress = Math.min((timestamp - startTimestamp) / duration, 1);
                obj.innerHTML = (progress * (end - start) + start).toFixed(decimals);
                if (progress < 1) {
                    window.requestAnimationFrame(step);
                }
            };
            window.requestAnimationFrame(step);
        }

        function showModal(modalId) {
            $(modalId).modal('show');
        }

        function setItemIdForDateForm(itemId, date) {
            const itemIdField = document.getElementById('itemIdField');
            const dateField = document.getElementById('dateField');

            if (itemIdField) {
                itemIdField.value = itemId;
            }

            if (dateField) {
                dateField.value = date;
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const totalProductsSpans = document.querySelectorAll('.totalProducts');
            const netWorthSpans = document.querySelectorAll('.netWorth');

            totalProductsSpans.forEach(span => {
                const totalProductsTarget = parseFloat(span.getAttribute('data-target'));
                animateValue(span, 0, totalProductsTarget, 1500, 2);
            });

            netWorthSpans.forEach(span => {
                const netWorthTarget = parseFloat(span.getAttribute('data-target'));
                animateValue(span, 0, netWorthTarget, 1500, 2);
            });

            // Check if there are any validation errors and show the modal if there are
            @if($errors->any())
            $('#updateWarehouseModal').modal('show');
            @endif
        });
    </script>
@endsection

