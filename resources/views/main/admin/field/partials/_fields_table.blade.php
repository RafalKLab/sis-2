<div class="col-md-6">
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <!-- Hidden form -->
            <form id="changeTable" action="{{ route('admin-fields.change-table') }}" method="post" style="display: none;">
                @csrf
                <input type="hidden" name="table_context" id="table_context" value="">
            </form>

            <div class="col-md-3">
                <i class="fa-solid fa-table"></i>
                Table:
                <select id="table_context_select">
                    @foreach($availableTables as $table)
                        <option {{ $selectedTable === $table ? 'selected' : '' }} value="{{ $table }}">{{ $table }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-1 d-flex justify-content-end">
                <a href="{{ route('admin-fields.create') }}" class="btn btn-outline-primary" title="Add new field"><i class="fa-solid fa-plus"></i></a>
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
                                        <a href="{{ route('admin-fields.move-down', ['id'=>$field['id']]) }}" title="Move down" class="btn btn-outline-secondary" style="max-width: 100px;"><i class="fa-solid fa-arrow-down"></i></a>
                                    @elseif($index !== 0 && !$loop->last)
                                        <a href="{{ route('admin-fields.move-up', ['id'=>$field['id']]) }}" title="Move up" class="btn btn-outline-secondary" style="max-width: 50px;"><i class="fa-solid fa-arrow-up"></i></a>
                                        <a href="{{ route('admin-fields.move-down', ['id'=>$field['id']]) }}" title="Move down" class="btn btn-outline-secondary" style="max-width: 50px;"><i class="fa-solid fa-arrow-down"></i></a>
                                    @elseif($loop->last)
                                        <a href="{{ route('admin-fields.move-up', ['id'=>$field['id']]) }}" title="Move up" class="btn btn-outline-secondary" style="max-width: 100px;"><i class="fa-solid fa-arrow-up"></i></a>
                                    @endif
                                    <a href="{{ route('admin-fields.edit', ['id' => $field['id']]) }}" title="Edit" class="btn btn-outline-primary"><i class="fa-solid fa-pen"></i></a>
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

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Listen for change events on the select element
        document.getElementById('table_context_select').addEventListener('change', function () {
            // Set the value of the hidden input to the selected option's value
            document.getElementById('table_context').value = this.value;

            // Submit the hidden form
            document.getElementById('changeTable').submit();
        });
    });
</script>
