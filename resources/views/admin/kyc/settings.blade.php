@extends('admin.layouts.app')
@section('panel')
    <div class="container-fluid">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="text-muted mb-1">@lang('KYC Management')</h5>
                        <h2>@lang('Document Types')</h2>
                    </div>
                    <button type="button" class="btn btn-primary addBtn">
                        <i class="la la-plus"></i> @lang('Add New Document Type')
                    </button>
                </div>
            </div>
        </div>

        <!-- Document Types Table -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>@lang('ID')</th>
                                        <th>@lang('Document Type')</th>
                                        <th class="d-none d-md-table-cell">@lang('Status')</th>
                                        <th class="text-center">@lang('Action')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $i = 1; @endphp
                                    @forelse($kyc as $data)
                                        <tr>
                                            <td>
                                                <strong>{{ __($i++) }}</strong>
                                            </td>
                                            <td>
                                                <strong>{{ __($data->type) }}</strong>
                                            </td>
                                            <td class="d-none d-md-table-cell">
                                                @if($data->status == 0)
                                                    <span class="badge bg-secondary">
                                                        <i class="la la-times"></i> @lang('Disabled')
                                                    </span>
                                                @else
                                                    <span class="badge bg-success">
                                                        <i class="la la-check"></i> @lang('Enabled')
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-sm btn-info editBtn"
                                                        data-id='{{ $data->id }}'
                                                        data-name='{{ $data->type }}'
                                                        data-status='{{ $data->status }}'>
                                                    <i class="la la-edit"></i> @lang('Edit')
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td class="text-center text-muted py-4" colspan="4">{{ __($empty_message ?? 'No document types found') }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Modal -->
    <div id="addModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Add New KYC Document Type')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">@lang('Document Type Name')</label>
                            <input type="text" name="type" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">@lang('Status')</label>
                            <select name="status" class="form-select" required>
                                <option value="1">@lang('Enable')</option>
                                <option value="0">@lang('Disable')</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('Close')</button>
                        <button type="submit" class="btn btn-primary">@lang('Save')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Edit Document Type')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.users.kyc.editsettings') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id" required>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">@lang('Document Type Name')</label>
                            <input type="text" name="type" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">@lang('Status')</label>
                            <select name="status" class="form-select" required>
                                <option value="1">@lang('Enable')</option>
                                <option value="0">@lang('Disable')</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('Close')</button>
                        <button type="submit" class="btn btn-primary">@lang('Update')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('script')
<script>
    (function () {
        "use strict";

        // Add Modal
        document.querySelector('.addBtn').addEventListener('click', function() {
            const modal = new bootstrap.Modal(document.getElementById('addModal'));
            modal.show();
        });

        // Edit Modal
        document.querySelectorAll('.editBtn').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const name = this.getAttribute('data-name');
                const status = this.getAttribute('data-status');

                document.querySelector('#editModal input[name=id]').value = id;
                document.querySelector('#editModal input[name=type]').value = name;
                document.querySelector('#editModal select[name=status]').value = status;

                document.getElementById('editModal').classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            });
        });

        // Close functions
        window.closeAddModal = function() {
            document.getElementById('addModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        };

        window.closeEditModal = function() {
            document.getElementById('editModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        };

        // Close on outside click
        document.getElementById('addModal').addEventListener('click', function(e) {
            if (e.target === this) closeAddModal();
        });

        document.getElementById('editModal').addEventListener('click', function(e) {
            if (e.target === this) closeEditModal();
        });
    })();
</script>
@endpush
