<div class="col-md-8">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div class="col-md-5">
                Edit order
            </div>
            <div class="col-md-1 d-flex justify-content-end">
                <button class="btn-outline-primary btn" onclick="submitForm()">Save</button>
            </div>
        </div>
        <div class="card-body">
            <form id="edit-order-form" method="POST" action="{{ route('orders.update', ['id'=>$orderFormData['id']]) }}">
                @csrf
                @foreach($orderFormData['details'] as $data)
                    <div class="form-group row mb-3">
                        <label for="{{ $data['field_id'] }}" class="col-sm-2 col-form-label">{{ $data['field_name'] }}</label>
                        <div class="col-sm-10">
                            @if($data['field_type']==='id')
                                <input disabled type="text" class="form-control" id="{{ $data['field_id'] }}" value="{{ $data['value'] }}">
                                <small id="{{ $data['field_id'] }}" class="form-text text-muted">This is autogenerated field and can not be edited</small>
                            @elseif($data['field_type']==='date')
                                <input name="field_{{$data['field_id'] }}" type="date" class="form-control" id="{{ $data['field_id'] }}" value="{{ $data['value'] }}">
                            @elseif($data['field_type']==='file')
                                <input disabled type="text" class="form-control" id="{{ $data['field_id'] }}" value="{{ $orderFormData['uploaded_files'] }}">
                            @else
                                <input name="field_{{$data['field_id'] }}" type="text" class="form-control" id="{{ $data['field_id'] }}" value="{{ $data['value'] }}">
                            @endif
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
@endsection
