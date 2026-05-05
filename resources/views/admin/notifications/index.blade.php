@extends('layouts.master')

@section('content')
    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">

                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-body">

                        <div class="d-flex justify-content-between mb-3">
                            <h5 class="fw-semibold">
                                {{ __('admin.notifications') }}
                            </h5>

                            <form action="{{ route('admin.notifications.readAll') }}" method="POST" id="mark-all-form"
                                class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-primary">
                                    Mark All as Read
                                </button>
                            </form>
                        </div>

                        <div class="table-responsive">
                            <table class="dataTable dt-responsive table table-hover table-nowrap align-middle mb-0">

                                <thead class="table-light">
                                    <tr>
                                        <th>Message</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th width="120">Action</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @forelse($notifications as $notification)
                                        <tr class="{{ $notification->read_at ? '' : 'table-warning' }}">

                                            <td>
                                                {{ $notification->data['message'] ?? 'Notification' }}
                                            </td>

                                            <td>
                                                @if($notification->read_at)
                                                    <span class="badge bg-success">
                                                        Read
                                                    </span>
                                                @else
                                                    <span class="badge bg-danger">
                                                        New
                                                    </span>
                                                @endif
                                            </td>

                                            <td>
                                                {{ $notification->created_at->diffForHumans() }}
                                            </td>

                                            <td>
                                                @if(!$notification->read_at)
                                                    <form action="{{ route('admin.notifications.read', $notification->id) }}"
                                                        method="POST" class="mark-read-form d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-outline-primary">
                                                            Mark Read
                                                        </button>
                                                    </form>
                                                @else
                                                    —
                                                @endif
                                            </td>

                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted">
                                                No notifications found.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>

                            </table>
                        </div>

                        <div class="mt-3">
                            {{ $notifications->links() }}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            document.querySelectorAll('.mark-read-form').forEach(form => {

                form.addEventListener('submit', function (e) {
                    e.preventDefault();

                    const button = this.querySelector('button');
                    button.disabled = true;

                    fetch(this.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Failed');
                            }
                            return response.json();
                        })
                        .then(data => {

                            // Remove row OR visually mark as read
                            const row = this.closest('tr');
                            if (row) {
                                row.classList.add('table-light');
                            }

                            button.remove();
                            toastr.success(data.message);
                        })
                        .catch(() => {
                            button.disabled = false;
                            alert('Something went wrong');
                        });

                });

            });

        });

        document.addEventListener('DOMContentLoaded', function () {

            const markAllForm = document.getElementById('mark-all-form');

            if (markAllForm) {
                markAllForm.addEventListener('submit', function (e) {
                    e.preventDefault();

                    const button = this.querySelector('button');
                    button.disabled = true;

                    fetch(this.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                        .then(response => {
                            if (!response.ok) throw new Error('Failed');
                            return response.json();
                        })
                        .then(data => {

                            // Mark all rows visually as read
                            document.querySelectorAll('tr').forEach(row => {
                                row.classList.remove('table-warning');
                                row.classList.add('table-light');
                            });

                            // Remove all mark-read buttons
                            document.querySelectorAll('.mark-read-form button')
                                .forEach(btn => btn.remove());

                            // Optional: reset unread badge count in header
                            const badge = document.querySelector('.noti-icon .badge');
                            if (badge) badge.remove();

                            button.remove(); // remove mark-all button
                            toastr.success(data.message);
                        })
                        .catch(() => {
                            button.disabled = false;
                            alert('Something went wrong');
                        });
                });
            }

        });
    </script>
@endsection
