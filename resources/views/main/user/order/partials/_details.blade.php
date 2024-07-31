<div class="col-md-6">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div class="col-md-5">
                Order details
            </div>
            <div class="col-md-1 d-flex justify-content-end">
                <a href="{{ route('orders.edit', ['id'=>$orderData['id']]) }}" title="Edit" class="text-primary"><i class="fa-solid fa-pen"></i></a>
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
                @foreach($orderData['details'] as $data)

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
