@extends('main.templates.main')
@section('title')
    Register new order
@endsection

@section('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
@endsection

@section('content')
<div class="container-fluid px-4">
    <h4 class="mt-4">Register new order</h4>
    <div class="card mb-4">
        <div class="card-body">
            <form method="POST" action="{{ route('orders.register-confirm') }}">
                @csrf
                <div class="form-group mb-2">
                    <label for="company">Select first buyer</label>
                    <select name="company" id="company" class="form-control {{ $errors->has('company') ? 'is-invalid' : '' }}">
                        @foreach($companies as $id => $company)
                            <option value="{{ $id }}">{{ $company }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('company'))
                        <div class="invalid-feedback">
                            {{ $errors->first('company') }}
                        </div>
                    @endif
                </div>

                <div class="form-group mb-2">
                    <label for="related_order">Select related order</label>
                    <select class="form-control {{ $errors->has('related_order') ? 'is-invalid' : '' }} select2" id="related_order" name="related_order">
                        <option value="0" selected>-</option>
                        <!-- Options will be loaded via AJAX -->
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

                <div class="form-group mb-2">
                    <input name="copy_related_order" class="form-check-input" type="checkbox" id="copyRelatedOrder" >
                    <label class="form-check-label" for="copyRelatedOrder">
                        Copy related order data
                    </label>
                </div>

                <button type="submit" class="btn btn-primary mt-4">Confirm</button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                placeholder: "Select related order",
                allowClear: true,
                ajax: {
                    url: '{{ route('api.orders') }}', // Use the route name you defined
                    dataType: 'json',
                    delay: 250, // Wait 250ms after typing stops to send the request
                    data: function (params) {
                        return {
                            q: params.term, // Search term
                            page: params.page
                        };
                    },
                    processResults: function (data, params) {
                        // Parse the results into the format expected by Select2
                        params.page = params.page || 1;

                        return {
                            results: data.results,
                            pagination: {
                                more: data.pagination.more
                            }
                        };
                    },
                    cache: true
                },
                minimumInputLength: 1 // User must type at least 1 character to start the search
            });
        });
    </script>
@endsection
