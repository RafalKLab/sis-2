@section('styles')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
@endsection

<div class="col-md-6">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div class="col-md-5">
                @if(isset($isEdit))
                    Edit item buyer {{$buyer->name}}
                @else
                    Add item buyer
                @endif
            </div>
            <div class="col-md-1 d-flex justify-content-end">
                <button class="btn-outline-primary btn" onclick="submitForm()">Save</button>
            </div>
        </div>
        <div class="card-body">
            @if(isset($isEdit))
                <form id="add-buyer-form" method="POST" action="{{ route('orders.update-item-buyer', ['orderId' => $orderId, 'itemId' => $itemId, 'buyerId' => $buyer->id]) }}">
                @method('PUT')
            @else
                <form id="add-buyer-form" method="POST" action="{{route('orders.store-item-buyer', ['orderId'=>$orderId, 'itemId'=>$itemId])}}">
            @endif
                    @csrf
                    <div class="form-group row mb-3">
                        <label for="buyer" class="col-sm-3 col-form-label">Pirkėjas</label>
                        <div class="col-sm-9">
                            <select class="form-control select-with-search" id="buyer" name="buyer">
                                @if(isset($isEdit))
                                    <option value="{{$buyer->name}}">{{$buyer->name}}</option>
                                    @foreach($availableBuyers as $availableBuyer)
                                        @if($availableBuyer !== $buyer->name)
                                            <option value="{{ $availableBuyer }}">{{ $availableBuyer }}</option>
                                        @endif
                                    @endforeach
                                @else
                                    @foreach($availableBuyers as $availableBuyer)
                                        <option value="{{ $availableBuyer }}">{{ $availableBuyer }}</option>
                                    @endforeach
                                @endif
                                <!-- Your options -->
                            </select>
                            @if ($errors->has('buyer'))
                                <span class="text-danger" role="alert">
                                     {{ $errors->first('buyer') }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group row mb-3">
                        <label for="quantity" class="col-sm-3 col-form-label">Kiekis</label>
                        <div class="col-sm-9">
                            <input name="quantity" type="number" class="form-control" id="quantity" value="{{ isset($isEdit) ? $buyer->quantity : '1' }}">
                            @if ($errors->has('quantity'))
                                <span class="text-danger" role="alert">
                                 {{ $errors->first('quantity') }}
                                </span>
                            @endif
                        </div>
                    </div>
                    <input type="hidden" name="itemId" value="{{$itemId}}">
                </form>
        </div>
    </div>
</div>

@section('script')
    <script>
        function submitForm() {
            // Ensure the ID here matches your form's ID
            document.getElementById('add-buyer-form').submit();
        }
    </script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.select-with-search').each(function() {
                $(this).select2({
                    placeholder: "",
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
