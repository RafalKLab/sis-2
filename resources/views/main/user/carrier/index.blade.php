@extends('main.templates.main')
@section('title')
    Carriers
@endsection
@section('styles')
    <link href="{{ asset('css/customers.css') }}" rel="stylesheet" />
@endsection
@section('content')
    <div class="container-fluid px-4">
        <h4 class="mt-4">Carriers</h4>
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="col-md-1"><i class="fa-solid fa-truck-moving"></i></div>
                <div class="col-md-1 d-flex justify-content-end"></div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table" id="datatablesSimple">
                        <thead>
                        <tr>
                            <th>Carrier</th>
                            <th style="min-width: 500px;">Orders</th>
                            <th>Notes</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($carriers as $carrier => $data)
                            <tr>
                                <td>{{$carrier}}</td>
                                <td style="min-width: 500px;">
                                    @foreach($data['orders'] as $orderKey => $orderId)
                                        @if(is_array($orderId))
                                            <a class="custom-link" href="{{ route('orders.view', ['id'=>$orderId[0]]) }}">{{ $orderKey }}</a>
                                        @else
                                            <a class="custom-link" href="{{ route('orders.view', ['id'=>$orderId]) }}">{{ $orderKey }}</a>
                                        @endif
                                    @endforeach
                                </td>
                                <td>
                                    <div class="note-block-actions text-end">
                                        <a title="add note" class="text-primary openModalButton" href="#" onclick="openNoteModal('{{ $carrier }}')"><i class="fa-solid fa-plus"></i></a>
                                    </div>
                                    <div class="row" id="notes-container-{{ $carrier }}">
                                        @foreach($data['notes'] as $note)
                                            <div class="note-{{ $note['note_id'] }} col-md-10">
                                                <div class="note-block">
                                                    {{ $note['message'] }}
                                                    <div class="note-block-author">
                                                        on {{ $note['created_at'] }} by {{ $note['author']  }}
                                                    </div>
                                                </div>
                                            </div>
                                            @can('Delete carrier notes')
                                                <div class="note-{{ $note['note_id'] }} col-md-2">
                                                    <div class="note-block-actions text-end">
                                                        <a title="remove" class="text-danger" href="#" onclick="confirmRemoveNote('{{ $note['note_id'] }}')"><i class="fa-solid fa-trash"></i></a>
                                                    </div>
                                                </div>
                                            @endcan
                                        @endforeach
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

    <!-- Modal -->
    <div class="modal fade" id="newNoteModal" tabindex="-1" aria-labelledby="newNoteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="newNoteModalLabel">Note</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Form starts here -->
                    <form id="newNoteForm">
                        <input type="hidden" id="target" name="target" value="">
                        <input type="hidden" id="identifier" name="identifier" value="carrier">
                        <!-- Add your input fields here -->
                        <div class="mb-3">
                            <label for="noteText" class="form-label">Note for <span id="note-target"></span></label>
                            <textarea name="message" class="form-control" id="noteText" rows="3" required></textarea>
                        </div>
                        <!-- ... other fields ... -->
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save Note</button>
                        </div>
                    </form>
                    <!-- Form ends here -->
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        // add new note
        function openNoteModal(customer) {
            // Set the customer ID in the hidden input
            document.getElementById('target').value = customer;
            // Set the customer name/id in the label
            document.getElementById('note-target').textContent = customer;

            // Get the modal instance and show it
            var noteModal = new bootstrap.Modal(document.getElementById('newNoteModal'));
            noteModal.show();
        }

        document.addEventListener('DOMContentLoaded', function () {
            var noteForm = document.getElementById('newNoteForm');
            noteForm.addEventListener('submit', function (event) {
                event.preventDefault();

                // FormData will grab all the fields from the form
                var formData = new FormData(noteForm);

                // Perform the AJAX request
                fetch('{{ route("notes.store") }}', { // Use the route that handles note storage
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}', // Include CSRF token header
                        'Accept': 'application/json', // Expect JSON response
                    },
                    body: formData // Send the form data
                })
                    .then(response => response.json())
                    .then(data => {
                        // Handle the response data
                        if (data.success) {
                            $('#newNoteModal').modal('hide');

                            // Assume data.note contains the necessary note information
                            // Target the div where the notes should be displayed
                            // This div must have a unique identifier, like 'notes-container-{customer}'
                            var notesContainer = document.getElementById('notes-container-' + data.customer);

                            // Create the new note elements (adjust to match your HTML structure)
                            var deleteButtonHtml = data.canDelete ? `
            <div class="note-${data.note.note_id} col-md-2">
                <div class="note-block-actions text-end">
                    <a title="remove" class="text-danger" href="#" onclick="confirmRemoveNote(${data.note.note_id})"><i class="fa-solid fa-trash"></i></a>
                </div>
            </div>
        ` : ''; // Only add delete button if user has permission

                            var newNoteHtml = `
            <div class="note-${data.note.note_id} col-md-10">
                <div class="note-block">
                    ${data.note.message}
                    <div class="note-block-author">
                        on ${data.note.created_at} by ${data.note.author}
                    </div>
                </div>
            </div>
            ${deleteButtonHtml}
        `;

                            // Append the new note to the container
                            notesContainer.insertAdjacentHTML('afterbegin', newNoteHtml);
                        }
                    })
                    .catch(error => {
                        // Handle any errors
                        console.error('Error:', error);
                    });
            });
        });
    </script>
    <script>
        // Remove note
        function confirmRemoveNote(noteId) {
            if (confirm('Are you sure you want to remove this note?')) {
                fetch('{{ route("notes.destroy") }}', {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ note_id: noteId }) // send the note ID to the server
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Remove the note elements with class 'note-{noteId}'
                            var noteElements = document.querySelectorAll('.note-' + noteId);
                            noteElements.forEach(function(element) {
                                element.remove();
                            });
                        } else {
                            console.error('Failed to delete the note:', data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
            }
        }
    </script>
@endsection
