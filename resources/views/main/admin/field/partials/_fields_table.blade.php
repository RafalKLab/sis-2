<div class="col-md-6">
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div class="col-md-1">
                <i class="fa-solid fa-table"></i> Fields
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table" id="adminFieldsTable">
                    <thead>
                    <tr>
                        <th>name</th>
                        <th>order</th>
                        <th>actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($tableFields as $index => $field)
                        <tr>
                            <td>{{ $field['name'] }}</td>
                            <td>{{ $field['order'] }}</td>
                            <td>
                                <div class="btn-group" style="display: flex; justify-content: space-between; width: 100%;">
                                    <a href="{{ route('admin-fields.show', ['id' => $field['id']]) }}" title="View" class="btn btn-outline-info"><i class="fa-solid fa-magnifying-glass"></i></a>
                                    @if($index == 0)
                                        <a href="" title="Move down" class="btn btn-outline-secondary" style="max-width: 100px;"><i class="fa-solid fa-arrow-down"></i></a>
                                    @elseif($index !== 0 && !$loop->last)
                                        <a href="" title="Move up" class="btn btn-outline-secondary" style="max-width: 50px;"><i class="fa-solid fa-arrow-up"></i></a>
                                        <a href="" title="Move down" class="btn btn-outline-secondary" style="max-width: 50px;"><i class="fa-solid fa-arrow-down"></i></a>
                                    @elseif($loop->last)
                                        <a href="" title="Move up" class="btn btn-outline-secondary" style="max-width: 100px;"><i class="fa-solid fa-arrow-up"></i></a>
                                    @endif
                                    <a href="" title="Edit" class="btn btn-outline-primary"><i class="fa-solid fa-pen"></i></a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
