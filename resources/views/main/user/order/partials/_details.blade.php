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
                        <button class="text-secondary" style="border: none; background-color: inherit" type="button" data-bs-toggle="collapse" data-bs-target="#collapseItems" aria-expanded="false" aria-controls="collapseContent">
                            <i class="fa-solid fa-chevron-down"></i>
                        </button>
                    </div>
                </div>
                <div id="collapseItems" class="collapse" aria-labelledby="headingOne">
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
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
