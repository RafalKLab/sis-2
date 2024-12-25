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
                        <label for="company">Select first buyer</label>
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
                            <th scope="row">Pirkėjas / Tarpininkas</th>
                            <td>{{ $orderData['company']['name'] }}</td>
                            <td></td>
                            <td></td>
                            <td>
                                <i title="Edit first buyer" onclick="showCompanyModal()" class="fa-solid fa-city text-primary" style="cursor: pointer"></i>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Užsakymo nr.</th>
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
                            <th scope="row">Grandis</th>
                            <td colspan="3">
                                <span class="related_order_links">
                                    @foreach($orderData['related_order_children_links']['children'] as $child)
                                        <a href="{{ route('orders.view', ['id' => $child['order_id']]) }}">{{ $child['order_key'] }}</a>
                                        <br>
                                    @endforeach
                                </span>
                            </td>
                            <td>
                                <a target="_blank" href="{{ route('orders.tree', ['id'=> $order['order_id']]) }}">
                                    <i style="transform: rotate(-90deg);" class="fa-solid fa-network-wired text-primary"></i>
                                </a>

                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <div class="row">
                        <div class="col-md-12">
                            @if(count($orderData['comments']))
                                <div class="card">
                                    <div class="card-header">
                                        Komentarai
                                    </div>
                                    <div class="card-body">
                                        @foreach($orderData['comments'] as $comment)
                                            <div class="row">
                                                <div class="col-md-7">
                                                    {{ $comment['content'] }}
                                                </div>
                                                <div class="col-md-5 d-flex justify-content-end align-items-center">
                                                    <i style="color:#AAAAAA; font-size: 12px;">{{ $comment['author'] }} {{ $comment['created_at'] }}</i>
                                                    @can('Delete order comments')
                                                        <form action="{{ route('orders.delete-comment', ['id' => $comment['id']]) }}" method="POST">
                                                            @method('DELETE')
                                                            @csrf
                                                            <button title="Delete comment" class="btn text-danger" type="submit" onclick="return confirm('Are you sure you want to delete this comment?')"><i class="fa-solid fa-trash"></i></button>
                                                        </form>
                                                    @endcan
                                                </div>
                                                <hr>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-12 mt-3">
                            <form method="POST" action="{{ route('orders.store-comment', ['id'=>$order['order_id']]) }}">
                                @csrf
                                <div class="row">
                                    <div class="col-md-10">
                                        <input type="text" class="form-control" name="content" aria-describedby="" placeholder="Komentaras">
                                    </div>
                                    <div class="col-md-2">
                                        <button type="submit" class="btn btn-primary" style="width: 100%">Save</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header d-flex justify-content-between align-items-center expandable-header" data-bs-toggle="collapse" data-bs-target="#collapseItems">
                    <div class="col-md-5">
                        Prekės ir logistika
                    </div>
                    <div class="col-md-1 d-flex justify-content-end">
                        <button class="text-secondary" style="border: none; background-color: inherit" aria-expanded="true" aria-controls="collapseContent">
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

                                    @case('load date from warehouse')
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

                                    @case('delivery date to buyer')
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
                                                        @can('See item buyer')
                                                            @foreach($item['buyers'] as $buyer)
                                                                <tr>
                                                                    <th scope="row">Pirkėjas {{ $buyer['name'] }} </th>
                                                                    <td colspan="2">
                                                                        <b>Kiekis:</b> {{ $buyer['quantity'] }} <br>
                                                                        <b>Pasik. šalis:</b> {{ $buyer['last_country'] }}<br>
                                                                        <b>Pasik. data:</b> {{ $buyer['load_date'] }}<br>
                                                                        <b>Išsik. šalis:</b> {{ $buyer['dep_country'] }}<br>
                                                                        <b>Pristatymo data:</b> {{ $buyer['delivery_date'] }}<br>
                                                                        <b>Pristatymo adresas:</b> {{ $buyer['address'] }}<br>
                                                                        <b>Vežėjas:</b> {{ $buyer['carrier'] }}<br>
                                                                        <b>Trans. nr.:</b> {{ $buyer['trans_number'] }}<br>
                                                                    </td>
                                                                    <td class="text-end">
                                                                        <a href="{{ route('orders.edit-item-buyer', ['orderId' => $orderData['id'], 'itemId' => $item['settings']['item_id'], 'buyerId' => $buyer['id']]) }}" title="Edit buyer"><i class="fa-solid fa-user-pen"></i></a>
                                                                        @can('Remove item buyer')
                                                                            <a class="text-danger" style="margin-left: 10px;" href="{{ route('orders.remove-item-buyer', ['orderId' => $orderData['id'], 'itemId' => $item['settings']['item_id'], 'buyerId' => $buyer['id']]) }}" title="Remove buyer"><i class="fa-solid fa-user-xmark"></i></a>
                                                                        @endcan
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        @endcan

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

                                                                @case('load date from warehouse')
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

                                                                @case('delivery date to buyer')
                                                                        <tr>
                                                                            <th scope="row">{{ $data['field_name'] }}</th>
                                                                            <td>
                                                                                @if($data['value'])
                                                                                    <div class="order-field-status-purple">
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
                                                                                <div class="order-field-status-orange">
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
                <div class="card-header d-flex justify-content-between align-items-center expandable-header" data-bs-toggle="collapse" data-bs-target="#collapseAccounting">
                    <div class="col-md-5">
                        Apskaita
                    </div>
                    <div class="col-md-1 d-flex justify-content-end">
                        <button class="text-secondary" style="border: none; background-color: inherit" aria-expanded="false" aria-controls="collapseContent">
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
                <div class="card-header d-flex justify-content-between align-items-center expandable-header" data-bs-toggle="collapse" data-bs-target="#collapseInvoice">
                    <div class="col-md-8">
                        Sąskaitos faktūros
                        <span id="invoice-alert-block" class="invoice-alert"></span>
                    </div>
                    <div class="col-md-1 d-flex justify-content-end">
                        <button class="text-secondary" style="border: none; background-color: inherit" aria-expanded="false" aria-controls="collapseContent">
                            <i class="fa-solid fa-chevron-down"></i>
                        </button>
                    </div>
                </div>
                <div id="collapseInvoice" class="collapse" aria-labelledby="headingOne">
                    <div class="card-body">
                        <table class="table">
                            <tbody>
                            @if(array_key_exists('SĄSKAITOS FAKTŪROS', $orderData['details']))
                                @can('See item buyer')
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
                                                    @elseif($data['invoice']['status'] === 'partial_payment')
                                                        Dalinis apmokėjimas
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
{{--                                    Customer transportation invoice--}}
                                    <tr>
                                        <th scope="row">SF. {{ $buyer }} Trans</th>
                                        <td>@if($data['invoice_transport']['number']) <b>Nr: </b> @endif {{ $data['invoice_transport']['number'] }}</td>
                                        <td><b>Suma:</b> {{ $data['invoice_transport']['sum'] }}</td>
                                        @if ($data['invoice_transport']['pay_until_date'])
                                            <td>
                                                <div class="{{ $data['invoice_transport']['display_class']}}">
                                                    @if($data['invoice_transport']['status'] === 'paid')
                                                        Apmokėta
                                                    @elseif($data['invoice_transport']['status'] === 'partial_payment')
                                                        Dalinis apmokėjimas
                                                    @else
                                                        Apmokėti iki {{ $data['invoice_transport']['pay_until_date'] }}
                                                    @endif
                                                </div>
                                            </td>
                                        @else
                                            <td></td>
                                        @endif
                                        <td><a href="{{ route('orders.edit-customer-invoice', ['orderId'=>$orderData['id'], 'customer'=>$buyer . ' Trans']) }}" title="Edit {{ $buyer }} invoice" class="text-primary"><i class="fa-solid fa-pen"></i></a></td>
                                    </tr>

                                @endforeach
                                @endcan
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
                                                        @elseif($data['additional_data']['status'] === 'partial_payment')
                                                            Dalinis apmokėjimas
                                                        @else
                                                            Apmokėti iki {{ $data['additional_data']['pay_until_date'] }}
                                                        @endif
                                                    </div>
                                                </td>
                                            @else
                                                <td></td>
                                            @endif
                                            <td>
                                                <a href="{{ route('orders.edit-field', ['orderId'=>$orderData['id'], 'fieldId'=>$data['field_id']]) }}" title="Edit {{ $data['field_name'] }}" class="text-primary"><i class="fa-solid fa-pen"></i></a>
                                                @can('Delete invoice')
                                                    @if($data['value'])
                                                        <a href="{{ route('orders.delete-invoice', ['orderId'=>$orderData['id'], 'fieldId'=>$data['field_id']]) }}" title="Delete" class="text-danger" onclick="return confirmAction();"><i class="fa-solid fa-eraser"></i></a>
                                                    @endif
                                                @endcan
                                            </td>
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
        return confirm('Are you sure you want to remove ?');
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
