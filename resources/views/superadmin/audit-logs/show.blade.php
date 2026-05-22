@extends('layouts.superadmin')

@section('title', 'Detail Audit Log')
@section('header', 'Detail Audit Log')

@section('content')
    <div class="mb-6">
        <a href="{{ route('superadmin.audit-logs') }}" class="inline-flex items-center text-gray-600 hover:text-gray-900">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18">
                </path>
            </svg>
            Kembali ke Audit Logs
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Basic Info -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Dasar</h3>
            <dl class="space-y-4">
                <div class="flex justify-between">
                    <dt class="text-sm font-medium text-gray-500">ID</dt>
                    <dd class="text-sm text-gray-900">#{{ $activityLog->id }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-sm font-medium text-gray-500">Waktu</dt>
                    <dd class="text-sm text-gray-900">{{ $activityLog->created_at->format('d M Y H:i:s') }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-sm font-medium text-gray-500">Tenant</dt>
                    <dd class="text-sm text-gray-900">{{ $activityLog->tenant->company_name ?? '-' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-sm font-medium text-gray-500">User</dt>
                    <dd class="text-sm text-gray-900">{{ $activityLog->user->name ?? '-' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-sm font-medium text-gray-500">Email</dt>
                    <dd class="text-sm text-gray-900">{{ $activityLog->user->email ?? '-' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-sm font-medium text-gray-500">Action</dt>
                    <dd>
                        @php
                            $actionColors = [
                                'created' => 'bg-green-100 text-green-800',
                                'updated' => 'bg-blue-100 text-blue-800',
                                'deleted' => 'bg-red-100 text-red-800',
                                'sent' => 'bg-indigo-100 text-indigo-800',
                                'approved' => 'bg-emerald-100 text-emerald-800',
                                'rejected' => 'bg-rose-100 text-rose-800',
                            ];
                            $color = $actionColors[$activityLog->action] ?? 'bg-gray-100 text-gray-800';
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $color }}">
                            {{ ucfirst($activityLog->action) }}
                        </span>
                    </dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-sm font-medium text-gray-500">Module</dt>
                    <dd class="text-sm text-gray-900">{{ ucfirst($activityLog->module) }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-sm font-medium text-gray-500">IP Address</dt>
                    <dd class="text-sm text-gray-900">{{ $activityLog->ip_address ?? '-' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-sm font-medium text-gray-500">User Agent</dt>
                    <dd class="text-sm text-gray-900 max-w-xs truncate">{{ $activityLog->user_agent ?? '-' }}</dd>
                </div>
            </dl>
        </div>

        <!-- Description -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Deskripsi</h3>
            <p class="text-gray-700">{{ $activityLog->description ?? 'Tidak ada deskripsi' }}</p>

            @if($activityLog->subject)
                <div class="mt-6 pt-6 border-t">
                    <h4 class="text-sm font-semibold text-gray-900 mb-2">Subject</h4>
                    <p class="text-sm text-gray-600">
                        {{ class_basename($activityLog->subject_type) }} #{{ $activityLog->subject_id }}
                    </p>
                </div>
            @endif
        </div>

        <!-- Old Values -->
        @if($activityLog->old_values)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Data Sebelumnya</h3>
                <pre
                    class="bg-gray-50 rounded-lg p-4 text-sm text-gray-700 overflow-x-auto">{{ json_encode($activityLog->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            </div>
        @endif

        <!-- New Values -->
        @if($activityLog->new_values)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Data Baru</h3>
                <pre
                    class="bg-gray-50 rounded-lg p-4 text-sm text-gray-700 overflow-x-auto">{{ json_encode($activityLog->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            </div>
        @endif
    </div>
@endsection