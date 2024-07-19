<div class="col-md-4">
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
                            <td>{{ $data['value'] }} </td>
                            <td><a href="{{ route('order-files.index', ['orderId'=>$orderData['id']]) }}" title="Edit files" class="text-primary"><i class="fa-solid fa-file"></i></a></td>
                        </tr>
                    @else
                        <tr>
                            <th scope="row">{{ $data['field_name'] }}</th>
                            <td>{{ $data['value'] }} </td>
                            <td><a href="{{ route('orders.edit-field', ['orderId'=>$orderData['id'], 'fieldId'=>$data['field_id']]) }}" title="Edit {{ $data['field_name'] }}" class="text-primary"><i class="fa-solid fa-pen"></i></a></td>
                        </tr>
                    @endif
                @endforeach
                <!-- Row 1 -->
                <tr>
                    <th scope="row">Užregistruotas</th>
                    <td>{{ $orderData['created_at'] }}</td>
                    <td></td>
                </tr>
                <!-- Row 2 -->
                <tr>
                    <th scope="row">Atnaujintas</th>
                    <td>{{ $orderData['updated_at'] }}</td>
                    <td></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
