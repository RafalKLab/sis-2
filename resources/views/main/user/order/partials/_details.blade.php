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
                        <div class="card mt-3">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div class="col-md-5">
                                    Prekių sąrašas
                                </div>
                                <div class="col-md-1 d-flex justify-content-end">
                                    @if(Auth::user()->hasPermissionTo('Add order products'))
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
                                        <tr data-bs-toggle="collapse" data-bs-target="#collapseItem_{{$item['settings']['collapse_id']}}" aria-expanded="false" aria-controls="collapseItem_{{$item['settings']['collapse_id']}}">
                                            <td>
                                                {{ $name }}
                                                <i class="fa-solid fa-chevron-down"></i>
                                            </td>
                                            <td>
                                                {{$item['settings']['purchase_sum_field_name']}}: {{$item['settings']['purchase_sum']}}
                                            </td>
                                            <td>
                                                {{$item['settings']['sales_sum_field_name']}}: {{$item['settings']['sales_sum']}}
                                            </td>
                                            <td class="text-end">
                                                @if(Auth::user()->hasPermissionTo('Edit order products'))
                                                    <a href="{{ route('orders.edit-item', ['orderId' => $orderData['id'], 'itemId' => $item['settings']['item_id']]) }}" title="Edit"><i class="fa-solid fa-pen"></i></a>
                                                @endif
                                                @if(Auth::user()->hasPermissionTo('Remove order products'))
                                                    <a style="font-size: 20px; margin-left: 10px;" href="{{ route('orders.remove-item', ['orderId' =>$orderData['id'], 'itemId' => $item['settings']['item_id']]) }}" onclick="return confirmAction()" class="text-danger" title="Remove"><i class="fa-solid fa-xmark"></i></a>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr id="collapseItem_{{$item['settings']['collapse_id']}}" class="collapse">
                                            <td colspan="4">
                                                <table class="table">
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
                                                                </tr>
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
                    <div class="col-md-5">
                        Sąskaitos faktūros
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
</script>
