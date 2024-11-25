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
                            <div class="feedback">
                                <i><small>Galimas kiekis iki: <b>{{ $availableItemQuantity }}</b></small></i>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row mb-3">
                        <label for="address" class="col-sm-3 col-form-label">Pristatymo adresas</label>
                        <div class="col-sm-9">
                            <input name="address" type="text" class="form-control" id="address" value="{{ isset($isEdit) ? $buyer->address : '' }}">
                            @if ($errors->has('address'))
                                <span class="text-danger" role="alert">
                                 {{ $errors->first('address') }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group row mb-3">
                        <label for="last_country" class="col-sm-3 col-form-label">Pakrovimo iš sandėlio šalis</label>
                        <div class="col-sm-9">
                            <select class="form-control" id="last_country" name="last_country">
                                @foreach($countryMap as $country)
                                    <option value="{{ $country }}"
                                        {{ isset($isEdit) && $buyer->last_country === $country ? 'selected' : '' }}
                                    > {{ $country }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('last_country'))
                                <span class="text-danger" role="alert">
                                 {{ $errors->first('last_country') }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group row mb-3">
                        <label for="dep_country" class="col-sm-3 col-form-label">Iškrovimo šalis pas pirkeją</label>
                        <div class="col-sm-9">
                            <select class="form-control" id="dep_country" name="dep_country">
                                @foreach($countryMap as $country)
                                    <option value="{{ $country }}"
                                        {{ isset($isEdit) && $buyer->dep_country === $country ? 'selected' : '' }}
                                    >{{ $country }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('dep_country'))
                                <span class="text-danger" role="alert">
                                 {{ $errors->first('dep_country') }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group row mb-3">
                        <label for="carrier" class="col-sm-3 col-form-label">Vežėjas</label>
                        <div class="col-sm-9">
                            <select class="form-control select-with-search" id="carrier" name="carrier">
                                <option>-</option>
                                @foreach($carriers as $carrier)
                                    <option value="{{ $carrier }}"
                                        {{ isset($isEdit) && $buyer->carrier === $carrier ? 'selected' : '' }}
                                    > {{ $carrier }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('carrier'))
                                <span class="text-danger" role="alert">
                                 {{ $errors->first('carrier') }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group row mb-3">
                        <label for="trans_number" class="col-sm-3 col-form-label">Trans. nr.</label>
                        <div class="col-sm-9">
                            <input name="trans_number" type="text" class="form-control" id="trans_number" value="{{ isset($isEdit) ? $buyer->trans_number : '' }}">
                            @if ($errors->has('trans_number'))
                                <span class="text-danger" role="alert">
                                 {{ $errors->first('trans_number') }}
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
