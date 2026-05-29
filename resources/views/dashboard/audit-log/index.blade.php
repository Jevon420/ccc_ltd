@extends('layouts.dashboard')

@section('title', 'Audit Log')
@section('page-title', 'Audit Log')
@section('page-subtitle', 'All user and system activity')

@section('content')

{{-- Search / Filter --}}
<div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-4 mb-5">
    <form method="GET" action="{{ route('dashboard.audit-log') }}" class="flex flex-wrap gap-3 items-center">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Search activity description..."
               class="flex-1 min-w-48 px-3.5 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-600">
        <button type="submit"
                class="bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg hover:bg-blue-800 transition-colors">
            Search
        </button>
        @if(request('search'))
        <a href="{{ route('dashboard.audit-log') }}" class="text-sm text-gray-500 hover:text-gray-700">Clear</a>
        @endif
    </form>
</div>

{{-- Logs Table --}}
<div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
        <h2 class="text-sm font-bold text-gray-900">Activity Log</h2>
        <span class="text-xs text-gray-400">{{ $logs->total() }} entries</span>
    </div>

    @if($logs->isEmpty())
    <div class="py-16 text-center">
        <div class="text-5xl mb-4">📋</div>
        <p class="text-sm font-semibold text-gray-700">No activity recorded yet</p>
        <p class="text-xs text-gray-400 mt-1">Activity will appear here as users interact with the system.</p>
    </div>
    @else
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                    <th class="px-6 py-3 text-left">User</th>
                    <th class="px-6 py-3 text-left">Action</th>
                    <th class="px-6 py-3 text-left">Subject</th>
                    <th class="px-6 py-3 text-left">Properties</th>
                    <th class="px-6 py-3 text-left">When</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($logs as $log)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-3">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 bg-blue-100 rounded-full flex items-center justify-center text-xs font-bold text-blue-700 flex-shrink-0">
                                {{ strtoupper(substr($log->causer?->name ?? 'S', 0, 1)) }}
                            </div>
                            <span class="text-xs font-medium text-gray-900 truncate max-w-28">
                                {{ $log->causer?->name ?? 'System' }}
                            </span>
                        </div>
                    </td>
                    <td class="px-6 py-3">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            {{ $log->event ?? 'action' }}
                        </span>
                    </td>
                    <td class="px-6 py-3 text-xs text-gray-600">
                        {{ class_basename($log->subject_type ?? '') }}
                        @if($log->subject_id) <span class="text-gray-400">#{{ $log->subject_id }}</span> @endif
                    </td>
                    <td class="px-6 py-3 text-xs text-gray-500">
                        <span class="truncate max-w-48 block" title="{{ $log->description }}">
                            {{ Str::limit($log->description, 60) }}
                        </span>
                    </td>
                    <td class="px-6 py-3 text-xs text-gray-400 whitespace-nowrap">
                        {{ $log->created_at->diffForHumans() }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="px-6 py-4 border-t border-gray-100">
        {{ $logs->links() }}
    </div>
    @endif
</div>

@endsection
