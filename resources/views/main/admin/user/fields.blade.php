@extends('main.templates.main')
@section('title')
    Assign fields {{ $user->email }}
@endsection
@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4">Assign fields</h1>
        <div class="row mt-3">
            <div class="col-md-12">
                <div class="card mb-2">
                    <div class="card-body">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div class="col-md-5">Assign fields for user {{ $user->email }}</div>
                            <div class="col-md-5 d-flex justify-content-end">
                                <form id="rolesForm" method="POST" action="{{ route('user.save-fields') }}">
                                    @csrf
                                    <input type="hidden" name="assignedIds" id="assignedIds">
                                    <input type="hidden" name="userId" value="{{$user->id}}">
                                    <button class="btn btn-primary" type="submit">Save</button>
                                </form>
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="card mb-4">
                                    <div class="card-header bg-primary text-white">
                                        Assigned fields
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table" id="">
                                                <thead>
                                                <tr>
                                                    <th>Name</th>
                                                </tr>
                                                </thead>
                                                <tbody id="assignedFields">
                                                @foreach($assignedFields as $id => $field)
                                                    <tr>
                                                        <td>
                                                            <button title="Unassign field" class="btn btn-outline-secondary move-btn w-100" data-id="{{ $id }}" data-direction="right">{{$field}} <i class="fa fa-arrow-right" aria-hidden="true"></i></button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card mb-4">
                                    <div class="card-header bg-secondary text-white">
                                        Available fields
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table" id="">
                                                <thead>
                                                <tr>
                                                    <th>Name</th>
                                                </tr>
                                                </thead>
                                                <tbody id="notAssignedFields">
                                                @foreach($notAssignedFields as $id => $field)
                                                    <tr>
                                                        <td>
                                                            <button title="Assign field" class="btn btn-outline-primary move-btn w-100" data-id="{{ $id }}" data-direction="left"><i class="fa fa-arrow-left" aria-hidden="true"></i> {{$field}} </button>
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
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const assignedContainer = document.getElementById('assignedFields');
            const availableContainer = document.getElementById('notAssignedFields');
            const assignedIdsInput = document.getElementById('assignedIds');

            // Function to move fields between columns
            function moveField(button) {
                const fieldId = button.getAttribute('data-id');
                const direction = button.getAttribute('data-direction');
                const fieldText = button.textContent.trim();

                if (direction === 'right') {
                    // Move from assigned to available
                    availableContainer.insertAdjacentHTML('beforeend', getFieldHtml(fieldId, fieldText, 'left'));
                    button.parentElement.parentElement.remove();
                } else {
                    // Move from available to assigned
                    assignedContainer.insertAdjacentHTML('beforeend', getFieldHtml(fieldId, fieldText, 'right'));
                    button.parentElement.parentElement.remove();
                }

                updateAssignedIds();
            }

            // Helper function to create field HTML
            function getFieldHtml(id, text, direction) {
                const arrowLeft = '<i class="fa fa-arrow-left" aria-hidden="true"></i> ';
                const arrowRight = ' <i class="fa fa-arrow-right" aria-hidden="true"></i>';
                const btnClass = direction === 'left' ? 'btn-outline-primary' : 'btn-outline-secondary';

                // Determine the order of the arrow and text based on the direction
                const buttonText = direction === 'left' ? (arrowLeft + text) : (text + arrowRight);

                return `
        <tr>
            <td>
                <button class="btn ${btnClass} move-btn w-100" data-id="${id}" data-direction="${direction}">${buttonText}</button>
            </td>
        </tr>
    `;
            }

            // Function to update the hidden input with assigned field IDs
            function updateAssignedIds() {
                const assignedButtons = assignedContainer.getElementsByClassName('move-btn');
                const ids = Array.from(assignedButtons).map(button => button.getAttribute('data-id'));
                assignedIdsInput.value = ids.join(',');
            }

            // Event delegation to handle button clicks
            document.addEventListener('click', function (e) {
                if (e.target.classList.contains('move-btn')) {
                    moveField(e.target);
                }
            });

            // Initial update of assigned field IDs
            updateAssignedIds();
        });
    </script>
@endsection
