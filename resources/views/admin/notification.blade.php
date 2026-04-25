@extends('layouts.app')

@section('title', 'Notifications')

@section('content')

    <div class="container-fluid p-4">

        <h3 class="fw-bold mb-4">Notifications</h3>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        {{-- ADD NOTIFICATION --}}
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h5 class="mb-3">Send Notification</h5>

                <form method="POST" action="{{ route('admin.notification.store') }}">
                    @csrf

                    <div class="mb-3">
                        <label class="fw-semibold">Subject</label>
                        <input type="text" name="subject" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="fw-semibold">Notification Message</label>
                        <textarea name="message" rows="4" class="form-control" required></textarea>
                    </div>

                    <button type="submit" class="btn px-4" style="background:#D4AC1C;color:#fff;">
                        SUBMIT
                    </button>
                </form>
            </div>
        </div>

        {{-- TABLE --}}
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="mb-3">Notification History</h5>

               <div class="table-responsive" style="overflow-x:auto;">
                    <table id="notificationTable" class="table table-bordered align-middle" style="min-width:600px;">
                        <thead class="table-light">
                            <tr>
                                <th>Sr.No</th>
                                <th>Date</th>
                                <th>Subject</th>
                                <th>Message</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>

    </div>

    {{-- EDIT MODAL --}}
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Notification</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form id="editForm" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="modal-body">

                        <div class="mb-3">
                            <label class="fw-semibold">Subject</label>
                            <input type="text" name="subject" id="editSubject" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="fw-semibold">Message</label>
                            <textarea name="message" id="editMessage" rows="4" class="form-control" required></textarea>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn px-4" style="background:#D4AC1C;color:#fff;">Update</button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    {{-- DELETE CONFIRM MODAL --}}
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    Are you sure you want to delete this notification?
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <form id="deleteForm" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                    </form>
                </div>

            </div>
        </div>
    </div>

@endsection


@section('scripts')

    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

    <script>
        $(document).ready(function () {

            // ✅ Single DataTable initialization (removed duplicate)
           $('#notificationTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('admin.notification') }}",
                scrollX: true,

                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'created_at', name: 'created_at' },
                    { data: 'title', name: 'title' },
                    { data: 'message', name: 'message' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],

                dom: 'Bfrtip',

                buttons: [
                    { extend: 'copy', className: 'btn btn-warning btn-sm me-2' },
                    { extend: 'csv', className: 'btn btn-warning btn-sm me-2' },
                    { extend: 'excel', className: 'btn btn-warning btn-sm me-2' },
                    { extend: 'print', className: 'btn btn-warning btn-sm' }
                ],

                pageLength: 10,
                lengthMenu: [10, 25, 50, 100],
            });

            // ✅ OPEN EDIT MODAL
            $(document).on('click', '.editBtn', function () {

                $('#editSubject').val($(this).data('title'));
                $('#editMessage').val($(this).data('message'));

                // correct URL from backend
                $('#editForm').attr('action', $(this).data('update-url'));

                $('#editModal').modal('show');
            });

            $(document).on('click', '.deleteBtn', function () {

                $('#deleteForm').attr('action', $(this).data('delete-url'));

                $('#deleteModal').modal('show');
            });

        });
    </script>

@endsection