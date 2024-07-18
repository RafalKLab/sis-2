@extends('main.templates.main')
@section('title')
    Register new order
@endsection
@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Register new order</h1>
    <div class="card mb-4">
        <div class="card-body">
            <form method="POST" action="{{ route('orders.register-confirm') }}">
                @csrf
                <div class="form-group mb-2">
                    <label for="related_order">Select related order</label>
                    <select name="related_order" id="related_order" class="form-control {{ $errors->has('related_order') ? 'is-invalid' : '' }}">
                        <option selected value="0">-</option>
                        <!-- Add more options here -->
                        @foreach($relatedOrders as $id => $orderKey)
                            <option value="{{ $id }}">{{ $orderKey }}</option>
                        @endforeach
                    </select>
                    <small id="related_order_help" class="form-text text-muted">
                        If this order is related to a previous order, please select it from the list.
                    </small>
                    @if ($errors->has('related_order'))
                        <div class="invalid-feedback">
                            {{ $errors->first('related_order') }}
                        </div>
                    @endif
                </div>

                <button type="submit" class="btn btn-primary mt-4">Confirm</button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const newOrderCheckbox = document.getElementById('newOrderCheckbox');
            const relatedOrderCheckbox = document.getElementById('relatedOrderCheckbox');

            newOrderCheckbox.addEventListener('change', function() {
                relatedOrderCheckbox.checked = !this.checked;
            });

            relatedOrderCheckbox.addEventListener('change', function() {
                newOrderCheckbox.checked = !this.checked;
            });
        });
    </script>
@endsection
