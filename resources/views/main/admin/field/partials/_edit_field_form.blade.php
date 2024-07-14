<div class="col-md-6">
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div class="col-md-5">
                Edit field: <b>{{$targetField->name}}</b>
            </div>
            <div class="col-md-1 d-flex justify-content-end">
                <a href="{{ route('admin-fields.index') }}" title="Close" class="text-secondary"><i class="fa-solid fa-xmark"></i></a>
            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('admin-fields.update', ['id'=>$targetField->id]) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="fieldName">Field name</label>
                    <input type="text" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" id="fieldName" name="name" value="{{ $targetField->name }}">
                    @if ($errors->has('name'))
                        <div class="invalid-feedback">
                            {{ $errors->first('name') }}
                        </div>
                    @endif
                </div>
                <div class="form-group mt-1">
                    <div class="row">
                        <label for="fieldColor">Color</label>
                        <div class="row">
                            <div class="col-md-11">
                                <input type="color" class="form-control" id="fieldColor" name="color" value="{{ $targetField->color }}">
                            </div>
                            <div class="col-md-1">
                                <button title="Select default" class="text-secondary" style="border: none; background: none" type="button" onclick="selectDefaultColor()"><i class="fa-solid fa-rotate-left"></i></button>
                            </div>
                        </div>
                        <span id="colorCode">{{ $targetField->color }}</span>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary mt-3">Save</button>
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
