<div class="col-md-6">
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div class="col-md-5">
                Field: <b>{{$targetField->name}}</b>
                <a href="{{ route('admin-fields.edit', ['id' => $targetField->id]) }}" title="Edit" class="text-primary"><i class="fa-solid fa-pen"></i></a>
            </div>
            <div class="col-md-1 d-flex justify-content-end">
                <a href="{{ route('admin-fields.index') }}" title="Close" class="text-secondary"><i class="fa-solid fa-xmark"></i></a>
            </div>
        </div>
        <div class="card-body">
            <table class="table">
                <tr>
                    <th>Name</th>
                    <td>{{ $targetField->name }}</td>
                </tr>
                <tr>
                    <th>Type</th>
                    <td>{{ $targetField->type }}</td>
                </tr>
                <tr>
                    <th>Group</th>
                    <td>{{ $targetField->group }}</td>
                </tr>
                <tr>
                    <th>Field order in table</th>
                    <td>{{ $targetField->order }}</td>
                </tr>
                <tr>
                    <th>Color</th>
                    <td>
                        <div class="row">
                            <div class="col-md-4">
                                {{ $targetField->color }}
                            </div>
                            <div class="col-md-6">
                                <div id="field-color-block" style="background-color: {{ $targetField->color }};"></div>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>Created at</th>
                    <td>{{ $targetField->created_at }}</td>
                </tr>
                <tr>
                    <th>Updated at</th>
                    <td>{{ $targetField->created_at }}</td>
                </tr>
            </table>
        </div>
    </div>
</div>
