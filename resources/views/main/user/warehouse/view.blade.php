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
                        <div class="col-md-5"><i class="fa-solid fa-box-open"></i>Products in stock</div>
                        <div class="col-md-3 d-flex justify-content-end">
                        </div>
                    </div>
                    <div class="card-body">
                    </div>
                </div>
            </div>
            <div class="col-md-3">
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
                            <span class="text-primary"><i style="cursor: pointer" id="edit_warehouse" class="fa-solid fa-pen" onclick="showModal()"></i></span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card product-worth">
                                    <div class="amount-block">
                                        <span id="totalProducts" data-target="0">0</span>
                                    </div>
                                    <div class="label-block">
                                        Total products
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card product-worth">
                                    <div class="amount-block">
                                        €<span id="netWorth" data-target="0.00">0.00</span>
                                    </div>
                                    <div class="label-block">
                                        Net worth
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
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

        function showModal() {
            $('#updateWarehouseModal').modal('show');
        }

        document.addEventListener('DOMContentLoaded', function() {
            const totalProductsSpan = document.getElementById('totalProducts');
            const netWorthSpan = document.getElementById('netWorth');
            const totalProductsTarget = parseInt(totalProductsSpan.getAttribute('data-target'));
            const netWorthTarget = parseFloat(netWorthSpan.getAttribute('data-target'));

            animateValue(totalProductsSpan, 0, totalProductsTarget, 1500); // No decimals for total products
            animateValue(netWorthSpan, 0, netWorthTarget, 1500, 2); // Two decimals for net worth

            // Check if there are any validation errors and show the modal if there are
            @if($errors->any())
            $('#updateWarehouseModal').modal('show');
            @endif
        });
    </script>
@endsection
