<div class="col-md-6">
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div class="col-md-5">
                Create new
            </div>
            <div class="col-md-1 d-flex justify-content-end">
                <a href="{{ route('admin-fields.index') }}" title="Close" class="text-secondary"><i class="fa-solid fa-xmark"></i></a>
            </div>
        </div>
        <div class="card-body">
            <div class="alert alert-warning" role="alert">
                Please note that adding a new field is a permanent action. Once you add this field, it will appear in the table and cannot be removed later. Ensure the field details are correct before proceeding.
            </div>
            <form action="{{ route('admin-fields.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="fieldName">Field name</label>
                    <input type="text" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" id="fieldName" name="name" value="">
                    @if ($errors->has('name'))
                        <div class="invalid-feedback">
                            {{ $errors->first('name') }}
                        </div>
                    @endif
                </div>
                <div class="form-group">
                    <label for="fieldType">Field type</label>
                    <select name="type" id="fieldType" class="form-control {{ $errors->has('type') ? 'is-invalid' : '' }}">
                        @foreach($fieldTypes as $type)
                            <option value="{{$type}}">{{$type}}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('type'))
                        <div class="invalid-feedback">
                            {{ $errors->first('type') }}
                        </div>
                    @endif
                </div>
                <div class="form-group">
                    <label for="fieldGroup">Field group</label>
                    <select name="group" id="fieldGroup" class="form-control {{ $errors->has('group') ? 'is-invalid' : '' }}">
                        @foreach($fieldGroups as $group)
                            <option value="{{$group}}">{{$group}}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('group'))
                        <div class="invalid-feedback">
                            {{ $errors->first('group') }}
                        </div>
                    @endif
                </div>
                <div class="form-group mt-1">
                    <div class="row">
                        <label for="fieldColor">Color</label>
                        <div class="row">
                            <div class="col-md-11">
                                <input type="color" class="form-control" id="fieldColor" name="color" value="#f2f2f2">
                            </div>
                            <div class="col-md-1">
                                <button title="Select default" class="text-secondary" style="border: none; background: none" type="button" onclick="selectDefaultColor()"><i class="fa-solid fa-rotate-left"></i></button>
                            </div>
                        </div>
                        <span id="colorCode">#f2f2f2</span>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary mt-3">Create</button>
            </form>
        </div>
    </div>
</div>

@section('script')
    <script>
        function updateColorCode(color) {
            document.getElementById('colorCode').textContent = color;
        }

        function selectDefaultColor() {
            const defaultColor = '#f2f2f2';
            document.getElementById('fieldColor').value = defaultColor;
            updateColorCode(defaultColor);
        }

        // Event listener to update color code when the color picker changes
        document.getElementById('fieldColor').addEventListener('change', function() {
            updateColorCode(this.value);
        });

        // Call updateColorCode on page load to ensure the color code is set
        window.onload = function() {
            const initialColor = document.getElementById('fieldColor').value;
            updateColorCode(initialColor);
        };
    </script>
@endsection
