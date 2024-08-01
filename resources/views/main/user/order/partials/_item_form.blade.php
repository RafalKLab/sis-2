@section('styles')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
@endsection

<div class="col-md-6">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div class="col-md-5">
                Add item
            </div>
            <div class="col-md-1 d-flex justify-content-end">
                <button class="btn-outline-primary btn" onclick="submitForm()">Save</button>
            </div>
        </div>
        <div class="card-body">

            @if(isset($isEdit))
                <form id="edit-order-form" method="POST" action="{{ route('orders.update-item', ['orderId' => $orderData['id'], 'itemId' => $itemId]) }}">
                    @method('PUT')
            @else
                 <form id="edit-order-form" method="POST" action="{{ route('orders.store-item', ['id' => $orderData['id']]) }}">
            @endif
                @csrf
                @foreach($orderFormData as $data)
                    <div class="form-group row mb-3">
                        <label for="{{ $data['field_id'] }}" class="col-sm-3 col-form-label">{{ $data['field_name'] }}</label>
                        <div class="col-sm-9">
                            @switch($data['field_type'])
                                @case('id')
                                    <input disabled type="text" class="form-control" id="{{ $data['field_id'] }}" value="{{ $data['value'] }}">
                                    <small id="{{ $data['field_id'] }}" class="form-text text-muted">This is autogenerated field and can not be edited</small>
                                    @break
                                @case('purchase sum')
                                    <input disabled type="text" class="form-control" id="{{ $data['field_id'] }}" value="{{ $data['value'] }}">
                                    <small id="{{ $data['field_id'] }}" class="form-text text-muted">This field is automatically calculated and can not be edited</small>
                                    @break
                                @case('duty 7')
                                    <input disabled type="text" class="form-control" id="{{ $data['field_id'] }}" value="{{ $data['value'] }}">
                                    <small id="{{ $data['field_id'] }}" class="form-text text-muted">This field is automatically calculated and can not be edited</small>
                                    @break
                                @case('duty 15')
                                    <input disabled type="text" class="form-control" id="{{ $data['field_id'] }}" value="{{ $data['value'] }}">
                                    <small id="{{ $data['field_id'] }}" class="form-text text-muted">This field is automatically calculated and can not be edited</small>
                                    @break
                                @case('prime cost')
                                    <input disabled type="text" class="form-control" id="{{ $data['field_id'] }}" value="{{ $data['value'] }}">
                                    <small id="{{ $data['field_id'] }}" class="form-text text-muted">This field is automatically calculated and can not be edited</small>
                                    @break
                                @case('sales sum')
                                    <input disabled type="text" class="form-control" id="{{ $data['field_id'] }}" value="{{ $data['value'] }}">
                                    <small id="{{ $data['field_id'] }}" class="form-text text-muted">This field is automatically calculated and can not be edited</small>
                                    @break
                                @case('date')
                                    <input name="field_{{$data['field_id'] }}" type="date" class="form-control" id="{{ $data['field_id'] }}" value="{{ $data['value'] }}">
                                    @break
                                @case('load date')
                                    <input name="field_{{$data['field_id'] }}" type="date" class="form-control" id="{{ $data['field_id'] }}" value="{{ $data['value'] }}">
                                    @break
                                @case('delivery date')
                                    <input name="field_{{$data['field_id'] }}" type="date" class="form-control" id="{{ $data['field_id'] }}" value="{{ $data['value'] }}">
                                    @break
                                @case('file')
                                    <input disabled type="text" class="form-control" id="{{ $data['field_id'] }}" value="{{ $orderFormData['uploaded_files'] }}">
                                    @break
                                @case('select status')
                                    <select class="form-control" id="{{ $data['field_id'] }}" name="field_{{$data['field_id'] }}">
                                        @foreach($data['input_select'] as $option => $color)
                                            <option value="{{ $option }}" {{ $data['value'] == $option ? ' selected' : '' }}> {{ $option }}</option>
                                        @endforeach
                                    </select>
                                    @break
                                @case('select glue')
                                    <select class="form-control" id="{{ $data['field_id'] }}" name="field_{{$data['field_id'] }}">
                                        @foreach($data['input_select'] as $option)
                                            <option value="{{ $option }}" {{ $data['value'] == $option ? ' selected' : '' }}> {{ $option }}</option>
                                        @endforeach
                                    </select>
                                    @break
                                @case('select measurement')
                                    <select class="form-control" id="{{ $data['field_id'] }}" name="field_{{$data['field_id'] }}">
                                        @foreach($data['input_select'] as $option)
                                            <option value="{{ $option }}" {{ $data['value'] == $option ? ' selected' : '' }}> {{ $option }}</option>
                                        @endforeach
                                    </select>
                                    @break
                                @case('select certification')
                                    <select class="form-control" id="{{ $data['field_id'] }}" name="field_{{$data['field_id'] }}">
                                        @foreach($data['input_select'] as $option)
                                            <option value="{{ $option }}" {{ $data['value'] == $option ? ' selected' : '' }}> {{ $option }}</option>
                                        @endforeach
                                    </select>
                                    @break
                                @case('select country')
                                    <select class="form-control" id="{{ $data['field_id'] }}" name="field_{{$data['field_id'] }}">
                                        @foreach($data['input_select'] as $option)
                                            <option value="{{ $option }}" {{ $data['value'] == $option ? ' selected' : '' }}> {{ $option }}</option>
                                        @endforeach
                                    </select>
                                    @break
                                @case('select transport')
                                    <select class="form-control" id="{{ $data['field_id'] }}" name="field_{{$data['field_id'] }}">
                                        @foreach($data['input_select'] as $option)
                                            <option value="{{ $option }}" {{ $data['value'] == $option ? ' selected' : '' }}> {{ $option }}</option>
                                        @endforeach
                                    </select>
                                    @break
                                @case('dynamic select')
                                    <select class="form-control select-with-search" id="{{ $data['field_id'] }}" name="field_{{$data['field_id'] }}">
                                        <option>-</option>
                                        @foreach($data['input_select'] as $option)
                                            <option value="{{ $option }}" {{ $data['value'] == $option ? ' selected' : '' }}> {{ $option }}</option>
                                        @endforeach
                                    </select>
                                    @break
                                @default
                                    <input name="field_{{$data['field_id'] }}" type="text" class="form-control" id="{{ $data['field_id'] }}" value="{{ $data['value'] }}">
                            @endswitch
                        </div>
                    </div>
                @endforeach
            </form>
        </div>
    </div>
</div>

@section('script')
    <script>
        function submitForm() {
            // Ensure the ID here matches your form's ID
            document.getElementById('edit-order-form').submit();
        }
    </script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.select-with-search').each(function() {
                $(this).select2({
                    placeholder: "Select an option",
                    tags: true, // Allows the creation of new entries
                    createTag: function (params) {
                        var term = $.trim(params.term);
                        if (term === '') {
                            return null;
                        }
                        return {
                            id: term,
                            text: term,
                            newTag: true // Distinguishes between new and existing tags
                        };
                    }
                });
            });
        });
    </script>
@endsection
