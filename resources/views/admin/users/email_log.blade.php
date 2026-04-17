@extends('admin.layouts.app')
@section('panel')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                <tr>
                                    <th>@lang('User')</th>
                                    <th>@lang('Sent')</th>
                                    <th class="d-none d-md-table-cell">@lang('Mail Sender')</th>
                                    <th>@lang('Subject')</th>
                                    <th class="text-center">@lang('Action')</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @forelse($logs as $log)
                                        <tr>
                                            <td>
                                                <div class="font-weight-bold">{{ $log->user->fullname }}</div>
                                                <small class="text-muted">
                                                    <a href="{{ route('admin.users.detail', $log->user_id) }}"><span>@</span>{{ $log->user->username }}</a>
                                                </small>
                                            </td>
                                            <td>
                                                <small>{{ showDateTime($log->created_at) }}</small>
                                                <br>
                                                <small class="text-muted">{{ $log->created_at->diffForHumans() }}</small>
                                            </td>
                                            <td class="d-none d-md-table-cell">
                                                <span class="badge bg-info">{{ __($log->mail_sender) }}</span>
                                            </td>
                                            <td>
                                                <small>{{ __($log->subject) }}</small>
                                                <div class="d-md-none">
                                                    <small class="text-muted d-block mt-1">{{ __($log->mail_sender) }}</small>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('admin.users.email.details',$log->id) }}" class="btn btn-sm btn-primary" target="_blank">View</a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td class="text-muted text-center py-4" colspan="100%">{{ __($emptyMessage) }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer py-4">
                        {{ paginateLinks($logs) }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

