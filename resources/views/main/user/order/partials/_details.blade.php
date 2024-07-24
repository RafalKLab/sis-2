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
                @foreach($orderData['details'] as $data)
                    @if($data['field_type'] === 'file')
                        <tr>
                            <th scope="row">{{ $data['field_name'] }}</th>
                            <td><i class="fa-regular fa-file"></i> {{$orderData['uploaded_files']}}</td>
                            <td></td>
                            <td></td>
                            <td><a href="{{ route('order-files.index', ['orderId'=>$orderData['id']]) }}" title="Edit files" class="text-primary"><i class="fa-solid fa-file"></i></a></td>
                        </tr>
                    @else
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
                    @endif
                @endforeach
                <!-- Row 1 -->
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
</div>
