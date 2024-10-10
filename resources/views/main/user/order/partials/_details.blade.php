<!-- Modal -->
<div class="modal fade" id="companyModal" tabindex="-1" aria-labelledby="companyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('orders.update-company', ['id' => $orderData['id']]) }}">
                    @csrf
                    <div class="form-group mb-2">
                        <label for="company">Select company context</label>
                        <select name="company" id="company" class="form-control {{ $errors->has('company') ? 'is-invalid' : '' }}">
                            @foreach($orderData['available_companies'] as $id => $company)
                                <option @if($orderData['company']['id'] === $id) selected @endif value="{{ $id }}">{{ $company }}</option>
                            @endforeach
                        </select>
                        @if ($errors->has('company'))
                            <div class="invalid-feedback">
                                {{ $errors->first('company') }}
                            </div>
                        @endif
                    </div>

                    <button type="submit" class="btn btn-primary mt-4">Save</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="col-md-6">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div class="col-md-5">
                Užsakymo detalės
            </div>
            <div class="col-md-1 d-flex justify-content-end">
                <a href="{{ route('orders.edit', ['id'=>$orderData['id']]) }}" title="Edit" class="text-primary"><i class="fa-solid fa-pen"></i></a>
            </div>
        </div>
        <div class="card-body">

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="col-md-5">
                        Bendra informacija
                    </div>
                </div>
                <div class="card-body">
                    <table class="table">
                        <tbody>
                        <tr>
                            <th scope="row">Užregistravo:</th>
                            <td>{{ $orderData['user'] }}</td>
                            <td>{{ $orderData['created_at'] }}</td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <th scope="row">Įmonės kontekstas:</th>
                            <td>{{ $orderData['company']['name'] }}</td>
                            <td></td>
                            <td></td>
                            <td>
                                <i title="Edit company context" onclick="showCompanyModal()" class="fa-solid fa-city text-primary" style="cursor: pointer"></i>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Tėvų hierarchija</th>
                            <td colspan="4">
                                <span class="related_order_links">
                                @foreach($orderData['related_order_parent_links'] as $order)
                                        @if(!$loop->last)
                                            <a href="{{ route('orders.view', ['id' => $order['order_id']]) }}">{{ $order['order_key'] }}</a>
                                            &nbsp;<i class="fa-solid fa-angles-right"></i> &nbsp;
                                        @else
                                            <b><a href="{{ route('orders.view', ['id' => $order['order_id']]) }}">{{ $order['order_key'] }}</a></b>
                                        @endif
                                @endforeach
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Vaikų hierarchija</th>
                            <td colspan="4">
                                <span class="related_order_links">
                                    @foreach($orderData['related_order_children_links']['children'] as $child)
                                        <a href="{{ route('orders.view', ['id' => $child['order_id']]) }}">{{ $child['order_key'] }}</a>
                                        <br>
                                    @endforeach
                                </span>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="col-md-5">
                        Prekės ir logistika
                    </div>
                    <div class="col-md-1 d-flex justify-content-end">
                        <button class="text-secondary" style="border: none; background-color: inherit" type="button" data-bs-toggle="collapse" data-bs-target="#collapseItems" aria-expanded="true" aria-controls="collapseContent">
                            <i class="fa-solid fa-chevron-down"></i>
                        </button>
                    </div>
                </div>
                <div id="collapseItems" class="collapse show" aria-labelledby="headingOne">
                    <div class="card-body">
                        <table class="table">
                            <tbody>
                            @foreach($orderData['details']['PREKĖS IR LOGISTIKA'] as $data)
                                @switch($data['field_type'])
                                    @case('file')
                                        <tr>
                                            <th scope="row">{{ $data['field_name'] }}</th>
                                            <td><i class="fa-regular fa-file"></i> {{$orderData['uploaded_files']}}</td>
                                            <td></td>
                                            <td></td>
                                            <td><a href="{{ route('order-files.index', ['orderId'=>$orderData['id']]) }}" title="Edit files" class="text-primary"><i class="fa-solid fa-file"></i></a></td>
                                        </tr>
                                        @break

                                    @case('select status')
                                        <tr>
                                            <th scope="row">{{ $data['field_name'] }}</th>
                                            <td>
                                                @if($data['value'])
                                                    <div class="order-field-status-{{$data['input_select'][$data['value']]}}">
                                                        {{ $data['value'] }}
                                                    </div>
                                                @endif
                                            </td>
                                            @if($data['updated_by'])
                                                <td><i>Atnaujino:</i> {{ $data['updated_by'] }}</td>
                                                <td>{{ $data['updated_at'] }}</td>
                                            @else
                                                <td></td>
                                                <td></td>
                                            @endif
                                            <td><a href="{{ route('orders.edit-field', ['orderId'=>$orderData['id'], 'fieldId'=>$data['field_id']]) }}" title="Edit {{ $data['field_name'] }}" class="text-primary"><i class="fa-solid fa-pen"></i></a></td>
                                        </tr>
                                        @break
                                    @case('load date')
                                        <tr>
                                            <th scope="row">{{ $data['field_name'] }}</th>
                                            <td>
                                                @if($data['value'])
                                                    <div class="order-field-status-yellow">
                                                        {{ $data['value'] }}
                                                    </div>
                                                @endif
                                            </td>
                                            @if($data['updated_by'])
                                                <td><i>Atnaujino:</i> {{ $data['updated_by'] }}</td>
                                                <td>{{ $data['updated_at'] }}</td>
                                            @else
                                                <td></td>
                                                <td></td>
                                            @endif
                                            <td><a href="{{ route('orders.edit-field', ['orderId'=>$orderData['id'], 'fieldId'=>$data['field_id']]) }}" title="Edit {{ $data['field_name'] }}" class="text-primary"><i class="fa-solid fa-pen"></i></a></td>
                                        </tr>
                                        @break

                                    @case('delivery date')
                                        <tr>
                                            <th scope="row">{{ $data['field_name'] }}</th>
                                            <td>
                                                @if($data['value'])
                                                    <div class="order-field-status-green">
                                                        {{ $data['value'] }}
                                                    </div>
                                                @endif
                                            </td>
                                            @if($data['updated_by'])
                                                <td><i>Atnaujino:</i> {{ $data['updated_by'] }}</td>
                                                <td>{{ $data['updated_at'] }}</td>
                                            @else
                                                <td></td>
                                                <td></td>
                                            @endif
                                            <td><a href="{{ route('orders.edit-field', ['orderId'=>$orderData['id'], 'fieldId'=>$data['field_id']]) }}" title="Edit {{ $data['field_name'] }}" class="text-primary"><i class="fa-solid fa-pen"></i></a></td>
                                        </tr>
                                        @break

                                    @default
                                        <tr>
                                            <th scope="row">{{ $data['field_name'] }}</th>
                                            <td>{{ $data['value'] }} </td>
                                            @if($data['updated_by'])
                                                <td><i>Atnaujino:</i> {{ $data['updated_by'] }}</td>
                                                <td>{{ $data['updated_at'] }}</td>
                                            @else
                                                <td></td>
                                                <td></td>
                                            @endif
                                            <td><a href="{{ route('orders.edit-field', ['orderId'=>$orderData['id'], 'fieldId'=>$data['field_id']]) }}" title="Edit {{ $data['field_name'] }}" class="text-primary"><i class="fa-solid fa-pen"></i></a></td>
                                        </tr>
                                @endswitch
                            @endforeach
                            </tbody>
                        </table>
                        @if(Auth::user()->hasPermissionTo('See order products'))
                            <div class="card mt-3">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <div class="col-md-5">
                                        Prekių sąrašas
                                    </div>
                                    <div class="col-md-1 d-flex justify-content-end">
                                        @if(Auth::user()->hasPermissionTo('Add order products'))
                                            <a href="{{ route('orders.add-item-from-warehouse', ['id' => $orderData['id']]) }}" class="text-primary" title="Select product from warehouse"><i class="fa-solid fa-warehouse"></i></a> &nbsp;&nbsp;&nbsp;
                                            <a href="{{ route('orders.add-item', ['id' => $orderData['id']]) }}" class="text-primary" title="Add new product"><i class="fa-solid fa-plus"></i></a>
                                        @endif
                                    </div>
                                </div>
                                <div class="card-body">
                                    <table class="table">
                                        <thead>
                                        <tr>
                                            <th scope="col"></th>
                                            <th scope="col"></th>
                                            <th scope="col"></th>
                                            <th scope="col"></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @if (empty($orderData['items']))
                                            Prekių sąrašas tuščias
                                        @endif
                                        @foreach($orderData['items'] as $name => $item)
                                            <tr class="order-item-row" data-bs-toggle="collapse" data-bs-target="#collapseItem_{{$item['settings']['collapse_id']}}" aria-expanded="false" aria-controls="collapseItem_{{$item['settings']['collapse_id']}}">
                                                <td>
                                                    {{ $name }}
                                                    <i class="fa-solid fa-chevron-down"></i>
                                                    @if($item['settings']['is_locked'])
                                                        <span title="This item is used in other orders and cannot be edited." class="locked-item-icon"><i class="fa-solid fa-lock"></i></span>
                                                    @endif

                                                </td>
                                                <td>
                                                    {{$item['settings']['purchase_sum_field_name']}}: {{$item['settings']['purchase_sum']}}
                                                </td>
                                                <td>
                                                    {{$item['settings']['sales_sum_field_name']}}: {{$item['settings']['sales_sum']}}
                                                </td>
                                                <td class="text-end">
                                                    @if($item['settings']['is_locked'] && Auth::user()->hasPermissionTo('Unlock item'))
                                                        <a href="{{ route('orders.unlock-item', ['orderId'=>$orderData['id'], 'itemId'=>$item['settings']['item_id']]) }}" class="text-primary" title="Unlock item" onclick="return confirm('Are you sure you want to unlock this item?')"><i class="fa-solid fa-lock-open text-warning"></i></a>
                                                    @endif
                                                    <a style="margin-left: 10px;" title="Add buyer" href="{{route('orders.add-item-buyer', ['orderId'=>$orderData['id'], 'itemId'=>$item['settings']['item_id']])}}"><i class="fa-solid fa-user-plus"></i></a>
                                                    @if(Auth::user()->hasPermissionTo('Edit order products'))
                                                        <a style="margin-left: 10px;" href="{{ route('orders.edit-item', ['orderId' => $orderData['id'], 'itemId' => $item['settings']['item_id']]) }}" title="Edit"><i class="fa-solid fa-pen"></i></a>
                                                    @endif
                                                    @if(Auth::user()->hasPermissionTo('Remove order products'))
                                                        <a style="font-size: 20px; margin-left: 10px;" href="{{ route('orders.remove-item', ['orderId' =>$orderData['id'], 'itemId' => $item['settings']['item_id']]) }}" onclick="return confirmAction()" class="text-danger" title="Remove"><i class="fa-solid fa-xmark"></i></a>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr id="collapseItem_{{$item['settings']['collapse_id']}}" class="collapse">
                                                <td colspan="4">
                                                    <table class="table">
                                                        @foreach($item['buyers'] as $buyer)
                                                            <tr>
                                                                <th scope="row">Pirkėjas {{ $buyer['name'] }} kiekis:</th>
                                                                <td>{{ $buyer['quantity'] }}</td>
                                                                <td></td>
                                                                <td class="text-end">
                                                                    <a href="{{ route('orders.edit-item-buyer', ['orderId' => $orderData['id'], 'itemId' => $item['settings']['item_id'], 'buyerId' => $buyer['id']]) }}" title="Edit buyer"><i class="fa-solid fa-user-pen"></i></a>
                                                                    @can('Remove item buyer')
                                                                        <a class="text-danger" style="margin-left: 10px;" href="{{ route('orders.remove-item-buyer', ['orderId' => $orderData['id'], 'itemId' => $item['settings']['item_id'], 'buyerId' => $buyer['id']]) }}" title="Remove buyer"><i class="fa-solid fa-user-xmark"></i></a>
                                                                    @endcan
                                                                </td>
                                                            </tr>
                                                        @endforeach

                                                        @foreach($item['details'] as $data)
                                                            @switch($data['field_type'])
                                                                @case('load date')
                                                                    <tr>
                                                                        <th scope="row">{{ $data['field_name'] }}</th>
                                                                        <td>
                                                                            @if($data['value'])
                                                                                <div class="order-field-status-yellow">
                                                                                    {{ $data['value'] }}
                                                                                </div>
                                                                            @endif
                                                                        </td>
                                                                        @if($data['updated_by'])
                                                                            <td><i>Atnaujino:</i> {{ $data['updated_by'] }}</td>
                                                                            <td>{{ $data['updated_at'] }}</td>
                                                                        @else
                                                                            <td></td>
                                                                            <td></td>
                                                                        @endif
                                                                    </tr>
                                                                    @break

                                                                @case('delivery date')
                                                                    <tr>
                                                                        <th scope="row">{{ $data['field_name'] }}</th>
                                                                        <td>
                                                                            @if($data['value'])
                                                                                <div class="order-field-status-green">
                                                                                    {{ $data['value'] }}
                                                                                </div>
                                                                            @endif
                                                                        </td>
                                                                        @if($data['updated_by'])
                                                                            <td><i>Atnaujino:</i> {{ $data['updated_by'] }}</td>
                                                                            <td>{{ $data['updated_at'] }}</td>
                                                                        @else
                                                                            <td></td>
                                                                            <td></td>
                                                                        @endif
                                                                    </tr>
                                                                    @break

                                                                    @case('select warehouse')
                                                                        <tr>
                                                                            <th scope="row">{{ $data['field_name'] }}</th>
                                                                            <td>
                                                                                @if($data['value'] && array_key_exists($data['value'],$data['input_select']))
                                                                                    {{ $data['input_select'][$data['value']] }}
                                                                                @endif
                                                                            </td>
                                                                            @if($data['updated_by'])
                                                                                <td><i>Atnaujino:</i> {{ $data['updated_by'] }}</td>
                                                                                <td>{{ $data['updated_at'] }}</td>
                                                                            @else
                                                                                <td></td>
                                                                                <td></td>
                                                                            @endif
                                                                        </tr>
                                                                        @break

                                                                @default
                                                                    @if($data['is_from_warehouse'])
                                                                        @if(!in_array($data['field_id'], $excludedFieldsForDetails['from_warehouse']))
                                                                                <tr>
                                                                                    <th scope="row">{{ $data['field_name'] }}</th>
                                                                                    <td>{{ $data['value'] }} </td>
                                                                                    @if($data['updated_by'])
                                                                                        <td><i>Atnaujino:</i> {{ $data['updated_by'] }}</td>
                                                                                        <td>{{ $data['updated_at'] }}</td>
                                                                                    @else
                                                                                        <td></td>
                                                                                        <td></td>
                                                                                    @endif
                                                                                </tr>
                                                                        @endif
                                                                    @else
                                                                        @if(!in_array($data['field_id'], $excludedFieldsForDetails['not_from_warehouse']))
                                                                                <tr>
                                                                                    <th scope="row">{{ $data['field_name'] }}</th>
                                                                                    <td>{{ $data['value'] }} </td>
                                                                                    @if($data['updated_by'])
                                                                                        <td><i>Atnaujino:</i> {{ $data['updated_by'] }}</td>
                                                                                        <td>{{ $data['updated_at'] }}</td>
                                                                                    @else
                                                                                        <td></td>
                                                                                        <td></td>
                                                                                    @endif
                                                                                </tr>
                                                                        @endif
                                                                    @endif
                                                            @endswitch
                                                        @endforeach
                                                    </table>
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

            </div>

            <div class="card mt-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="col-md-5">
                        Apskaita
                    </div>
                    <div class="col-md-1 d-flex justify-content-end">
                        <button class="text-secondary" style="border: none; background-color: inherit" type="button" data-bs-toggle="collapse" data-bs-target="#collapseAccounting" aria-expanded="false" aria-controls="collapseContent">
                            <i class="fa-solid fa-chevron-down"></i>
                        </button>
                    </div>
                </div>
                <div id="collapseAccounting" class="collapse" aria-labelledby="headingOne">
                    <div class="card-body">
                        <table class="table">
                            <tbody>
                            @if(array_key_exists('APSKAITA', $orderData['details']))
                                @foreach($orderData['details']['APSKAITA'] as $data)

                                    @switch($data['field_type'])
                                        @case('file')
                                            <tr>
                                                <th scope="row">{{ $data['field_name'] }}</th>
                                                <td><i class="fa-regular fa-file"></i> {{$orderData['uploaded_files']}}</td>
                                                <td></td>
                                                <td></td>
                                                <td><a href="{{ route('order-files.index', ['orderId'=>$orderData['id']]) }}" title="Edit files" class="text-primary"><i class="fa-solid fa-file"></i></a></td>
                                            </tr>
                                            @break

                                        @case('select status')
                                            <tr>
                                                <th scope="row">{{ $data['field_name'] }}</th>
                                                <td>
                                                    @if($data['value'])
                                                        <div class="order-field-status-{{$data['input_select'][$data['value']]}}">
                                                            {{ $data['value'] }}
                                                        </div>
                                                    @endif
                                                </td>
                                                @if($data['updated_by'])
                                                    <td><i>Atnaujino:</i> {{ $data['updated_by'] }}</td>
                                                    <td>{{ $data['updated_at'] }}</td>
                                                @else
                                                    <td></td>
                                                    <td></td>
                                                @endif
                                                <td><a href="{{ route('orders.edit-field', ['orderId'=>$orderData['id'], 'fieldId'=>$data['field_id']]) }}" title="Edit {{ $data['field_name'] }}" class="text-primary"><i class="fa-solid fa-pen"></i></a></td>
                                            </tr>
                                            @break
                                        @case('load date')
                                            <tr>
                                                <th scope="row">{{ $data['field_name'] }}</th>
                                                <td>
                                                    @if($data['value'])
                                                        <div class="order-field-status-yellow">
                                                            {{ $data['value'] }}
                                                        </div>
                                                    @endif
                                                </td>
                                                @if($data['updated_by'])
                                                    <td><i>Atnaujino:</i> {{ $data['updated_by'] }}</td>
                                                    <td>{{ $data['updated_at'] }}</td>
                                                @else
                                                    <td></td>
                                                    <td></td>
                                                @endif
                                                <td><a href="{{ route('orders.edit-field', ['orderId'=>$orderData['id'], 'fieldId'=>$data['field_id']]) }}" title="Edit {{ $data['field_name'] }}" class="text-primary"><i class="fa-solid fa-pen"></i></a></td>
                                            </tr>
                                            @break

                                        @case('delivery date')
                                            <tr>
                                                <th scope="row">{{ $data['field_name'] }}</th>
                                                <td>
                                                    @if($data['value'])
                                                        <div class="order-field-status-green">
                                                            {{ $data['value'] }}
                                                        </div>
                                                    @endif
                                                </td>
                                                @if($data['updated_by'])
                                                    <td><i>Atnaujino:</i> {{ $data['updated_by'] }}</td>
                                                    <td>{{ $data['updated_at'] }}</td>
                                                @else
                                                    <td></td>
                                                    <td></td>
                                                @endif
                                                <td><a href="{{ route('orders.edit-field', ['orderId'=>$orderData['id'], 'fieldId'=>$data['field_id']]) }}" title="Edit {{ $data['field_name'] }}" class="text-primary"><i class="fa-solid fa-pen"></i></a></td>
                                            </tr>
                                            @break
                                        @case('duty 7')
                                            <tr>
                                                <th scope="row">{{ $data['field_name'] }}</th>
                                                <td>{{ $data['value'] }} </td>
                                                @if($data['updated_by'])
                                                    <td><i>Atnaujino:</i> {{ $data['updated_by'] }}</td>
                                                    <td>{{ $data['updated_at'] }}</td>
                                                @else
                                                    <td>{{ $data['additional_data']['settings']['disabled-auto-calculation'] ?? false ? 'Išjungta' : '' }}</td>
                                                    <td></td>
                                                @endif
                                                <td><a href="{{ route('orders.edit-field', ['orderId'=>$orderData['id'], 'fieldId'=>$data['field_id']]) }}" title="Edit {{ $data['field_name'] }}" class="text-primary"><i class="fa-solid fa-pen"></i></a></td>
                                            </tr>
                                            @break
                                        @case('duty 15')
                                            <tr>
                                                <th scope="row">{{ $data['field_name'] }}</th>
                                                <td>{{ $data['value'] }} </td>
                                                @if($data['updated_by'])
                                                    <td><i>Atnaujino:</i> {{ $data['updated_by'] }}</td>
                                                    <td>{{ $data['updated_at'] }}</td>
                                                @else
                                                    <td>{{ $data['additional_data']['settings']['disabled-auto-calculation'] ?? false ? 'Išjungta' : '' }}</td>
                                                    <td></td>
                                                @endif
                                                <td><a href="{{ route('orders.edit-field', ['orderId'=>$orderData['id'], 'fieldId'=>$data['field_id']]) }}" title="Edit {{ $data['field_name'] }}" class="text-primary"><i class="fa-solid fa-pen"></i></a></td>
                                            </tr>
                                            @break
                                        @default

                                            <tr>
                                                <th scope="row">{{ $data['field_name'] }}</th>
                                                <td>{{ $data['value'] }} </td>
                                                @if($data['updated_by'])
                                                    <td><i>Atnaujino:</i> {{ $data['updated_by'] }}</td>
                                                    <td>{{ $data['updated_at'] }}</td>
                                                @else
                                                    <td></td>
                                                    <td></td>
                                                @endif
                                                <td><a href="{{ route('orders.edit-field', ['orderId'=>$orderData['id'], 'fieldId'=>$data['field_id']]) }}" title="Edit {{ $data['field_name'] }}" class="text-primary"><i class="fa-solid fa-pen"></i></a></td>
                                            </tr>
                                    @endswitch
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="col-md-8">
                        Sąskaitos faktūros
                        <span id="invoice-alert-block" class="invoice-alert"></span>
                    </div>
                    <div class="col-md-1 d-flex justify-content-end">
                        <button class="text-secondary" style="border: none; background-color: inherit" type="button" data-bs-toggle="collapse" data-bs-target="#collapseInvoice" aria-expanded="false" aria-controls="collapseContent">
                            <i class="fa-solid fa-chevron-down"></i>
                        </button>
                    </div>
                </div>
                <div id="collapseInvoice" class="collapse" aria-labelledby="headingOne">
                    <div class="card-body">
                        <table class="table">
                            <tbody>
                            @if(array_key_exists('SĄSKAITOS FAKTŪROS', $orderData['details']))
                                @foreach($orderData['details']['PIRKĖJŲ SĄSKAITOS'] as $buyer => $data)
                                    <tr>
                                        <th scope="row">SF. {{ $buyer }}</th>
                                        <td>@if($data['invoice']['number']) <b>Nr: </b> @endif {{ $data['invoice']['number'] }}</td>
                                        <td><b>Suma:</b> {{ $data['invoice']['sum'] }}</td>
                                        @if ($data['invoice']['pay_until_date'])
                                            <td>
                                                <div class="{{ $data['invoice']['display_class']}}">
                                                    @if($data['invoice']['status'] === 'paid')
                                                        Apmokėta
                                                    @else
                                                        Apmokėti iki {{ $data['invoice']['pay_until_date'] }}
                                                    @endif
                                                </div>
                                            </td>
                                        @else
                                            <td></td>
                                        @endif
                                        <td><a href="{{ route('orders.edit-customer-invoice', ['orderId'=>$orderData['id'], 'customer'=>$buyer]) }}" title="Edit {{ $buyer }} invoice" class="text-primary"><i class="fa-solid fa-pen"></i></a></td>
                                    </tr>
                                @endforeach

                                @foreach($orderData['details']['SĄSKAITOS FAKTŪROS'] as $data)
                                @switch($data['field_type'])
                                    @case('file')
                                        <tr>
                                            <th scope="row">{{ $data['field_name'] }}</th>
                                            <td><i class="fa-regular fa-file"></i> {{$orderData['uploaded_files']}}</td>
                                            <td></td>
                                            <td></td>
                                            <td><a href="{{ route('order-files.index', ['orderId'=>$orderData['id']]) }}" title="Edit files" class="text-primary"><i class="fa-solid fa-file"></i></a></td>
                                        </tr>
                                        @break

                                    @case('select status')
                                        <tr>
                                            <th scope="row">{{ $data['field_name'] }}</th>
                                            <td>
                                                @if($data['value'])
                                                    <div class="order-field-status-{{$data['input_select'][$data['value']]}}">
                                                        {{ $data['value'] }}
                                                    </div>
                                                @endif
                                            </td>
                                            @if($data['updated_by'])
                                                <td><i>Atnaujino:</i> {{ $data['updated_by'] }}</td>
                                                <td>{{ $data['updated_at'] }}</td>
                                            @else
                                                <td></td>
                                                <td></td>
                                            @endif
                                            <td><a href="{{ route('orders.edit-field', ['orderId'=>$orderData['id'], 'fieldId'=>$data['field_id']]) }}" title="Edit {{ $data['field_name'] }}" class="text-primary"><i class="fa-solid fa-pen"></i></a></td>
                                        </tr>
                                        @break
                                    @case('load date')
                                        <tr>
                                            <th scope="row">{{ $data['field_name'] }}</th>
                                            <td>
                                                @if($data['value'])
                                                    <div class="order-field-status-yellow">
                                                        {{ $data['value'] }}
                                                    </div>
                                                @endif
                                            </td>
                                            @if($data['updated_by'])
                                                <td><i>Atnaujino:</i> {{ $data['updated_by'] }}</td>
                                                <td>{{ $data['updated_at'] }}</td>
                                            @else
                                                <td></td>
                                                <td></td>
                                            @endif
                                            <td><a href="{{ route('orders.edit-field', ['orderId'=>$orderData['id'], 'fieldId'=>$data['field_id']]) }}" title="Edit {{ $data['field_name'] }}" class="text-primary"><i class="fa-solid fa-pen"></i></a></td>
                                        </tr>
                                        @break

                                    @case('delivery date')
                                        <tr>
                                            <th scope="row">{{ $data['field_name'] }}</th>
                                            <td>
                                                @if($data['value'])
                                                    <div class="order-field-status-green">
                                                        {{ $data['value'] }}
                                                    </div>
                                                @endif
                                            </td>
                                            @if($data['updated_by'])
                                                <td><i>Atnaujino:</i> {{ $data['updated_by'] }}</td>
                                                <td>{{ $data['updated_at'] }}</td>
                                            @else
                                                <td></td>
                                                <td></td>
                                            @endif
                                            <td><a href="{{ route('orders.edit-field', ['orderId'=>$orderData['id'], 'fieldId'=>$data['field_id']]) }}" title="Edit {{ $data['field_name'] }}" class="text-primary"><i class="fa-solid fa-pen"></i></a></td>
                                        </tr>
                                        @break
                                    @case('invoice')
                                        <tr>
                                            <th scope="row">{{ $data['field_name'] }}</th>
                                            <td>@if($data['value']) <b>Nr: </b> @endif{{ $data['value'] }} </td>
                                            <td>
                                                <b>Suma:</b> {{ $data['additional_data']['sum'] }}
                                                @if($data['additional_data']['auto_calculated_sum']['exists'] && $data['additional_data']['auto_calculated_sum']['amount'] !== $data['additional_data']['sum'])
                                                    <span class="text-warning"><i title="Suma nėra lygi susieto lauko sumai" class="fa-solid fa-triangle-exclamation"></i></span>
                                                @endif
                                            </td>
                                            @if ($data['additional_data']['pay_until_date'])
                                                <td>
                                                    <div class="{{ $data['additional_data']['display_class']}}">
                                                        @if($data['additional_data']['status'] === 'paid')
                                                            Apmokėta
                                                        @else
                                                            Apmokėti iki {{ $data['additional_data']['pay_until_date'] }}
                                                        @endif
                                                    </div>
                                                </td>
                                            @else
                                                <td></td>
                                            @endif
                                            <td><a href="{{ route('orders.edit-field', ['orderId'=>$orderData['id'], 'fieldId'=>$data['field_id']]) }}" title="Edit {{ $data['field_name'] }}" class="text-primary"><i class="fa-solid fa-pen"></i></a></td>
                                        </tr>
                                        @break
                                    @default
                                        <tr>
                                            <th scope="row">{{ $data['field_name'] }}</th>
                                            <td>{{ $data['value'] }} </td>
                                            @if($data['updated_by'])
                                                <td><i>Atnaujino:</i> {{ $data['updated_by'] }}</td>
                                                <td>{{ $data['updated_at'] }}</td>
                                            @else
                                                <td></td>
                                                <td></td>
                                            @endif
                                            <td><a href="{{ route('orders.edit-field', ['orderId'=>$orderData['id'], 'fieldId'=>$data['field_id']]) }}" title="Edit {{ $data['field_name'] }}" class="text-primary"><i class="fa-solid fa-pen"></i></a></td>
                                        </tr>
                                @endswitch
                            @endforeach
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function confirmAction() {
        return confirm('Are you sure you want to remove item?');
    }

    function showCompanyModal() {
        var myModal = new bootstrap.Modal(document.getElementById('companyModal'), {});
        myModal.show();
    }
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Count the divs with the class 'invoice-after-deadline'
        var invoiceAfterDeadlineCount = document.querySelectorAll('.invoice-after-deadline').length;

        // Select the span with the id 'invoice-alert-block'
        var invoiceAlertBlock = document.getElementById('invoice-alert-block');

        // Only update the HTML content of the span if the count is greater than 0
        if (invoiceAfterDeadlineCount > 0 && invoiceAlertBlock) {
            invoiceAlertBlock.innerHTML = 'Pasibaigęs mokėjimo terminas: ' + invoiceAfterDeadlineCount + ' <i class="fa-solid fa-triangle-exclamation"></i>';
        }
    });
</script>
