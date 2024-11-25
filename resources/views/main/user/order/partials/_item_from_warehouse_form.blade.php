@section('styles')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
@endsection

<div class="col-md-6">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div class="col-md-5">
                Add item from warehouse
            </div>
            <div class="col-md-1 d-flex justify-content-end">
                <button class="btn-outline-primary btn" onclick="submitForm()">Save</button>
            </div>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('orders.add-item-from-warehouse', ['id'=>$orderData['id']]) }}" id="item-from-warehouse-form">
                @csrf
                <!-- Warehouse Select -->
                <div class="form-group row mb-3">
                    <label for="warehouse" class="col-sm-2 col-form-label">Sandėlis</label>
                    <div class="col-sm-10">
                        <select required class="form-control {{ $errors->has('warehouse') ? 'is-invalid' : '' }}" id="warehouse" name="warehouse" onchange="updateItems()">
                            <option value="">Select Warehouse</option>
                            @foreach($warehouseItems as $warehouse => $items)
                                <option value="{{ $warehouse }}">{{ $warehouse }}</option>
                            @endforeach
                        </select>
                        @if ($errors->has('warehouse'))
                            <div class="invalid-feedback">
                                {{ $errors->first('warehouse') }}
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Items Select -->
                <div class="form-group row mb-3">
                    <label for="item" class="col-sm-2 col-form-label">Prekė</label>
                    <div class="col-sm-10">
                        <select required class="form-control {{ $errors->has('item') ? 'is-invalid' : '' }}" id="item" name="item">
                            <!-- Options will be populated by JavaScript -->
                        </select>
                        @if ($errors->has('item'))
                            <div class="invalid-feedback">
                                {{ $errors->first('item') }}
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Amount Input -->
                <div class="form-group row mb-3">
                    <label for="amount" class="col-sm-2 col-form-label">Kiekis</label>
                    <div class="col-sm-10">
                        <input required class="form-control {{ $errors->has('amount') ? 'is-invalid' : '' }}" type="number" name="amount" id="amount" value="{{ old('amount') }}">
                        @if ($errors->has('amount'))
                            <div class="invalid-feedback">
                                {{ $errors->first('amount') }}
                            </div>
                        @endif
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>

@section('script')
    <script>
        const warehouseItems = @json($warehouseItems);
        function updateItems() {
            const warehouseSelect = document.getElementById('warehouse');
            const itemSelect = document.getElementById('item');
            const selectedWarehouse = warehouseSelect.value;

            // Clear current items
            itemSelect.innerHTML = '';

            // Make sure a warehouse is selected
            if(selectedWarehouse) {
                // Get items for selected warehouse and populate item select
                const items = warehouseItems[selectedWarehouse]['items'];
                items.forEach((item) => {
                    const itemText = `${item.order.key} ${item.name} - sandėlyje: ${item.amount}${item.measurement_unit} - klijai: ${item.glue} - išmatavimai: ${item.measurement} - kokybė: ${item.quality} - savikaina: ${item.prime_cost} - pardavėjas: ${item.seller}`;
                    const option = new Option(itemText, item.item_id); // assumes items have 'name' and 'id' properties
                    itemSelect.add(option);
                });
            } else {
                // No warehouse selected, so add a placeholder
                const defaultOption = new Option('Select Item', '');
                itemSelect.add(defaultOption);
            }
        }
    </script>

    <script>
        function submitForm() {
            // Ensure the ID here matches your form's ID
            document.getElementById('item-from-warehouse-form').submit();
        }
    </script>
@endsection
