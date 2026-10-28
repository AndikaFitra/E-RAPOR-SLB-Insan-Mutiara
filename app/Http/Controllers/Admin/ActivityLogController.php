<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search'  => 'nullable|string|max:100',
            'user_id' => 'nullable|integer',
            'status'  => 'nullable|string|max:50',
            'dari'    => 'nullable|date',
            'sampai'  => 'nullable|date|after_or_equal:dari',
        ]);

        $logs = ActivityLog::with('user')
            ->when($filters['search'] ?? null, fn ($q, $v) => $q->where('subjek', 'like', "%{$v}%"))
            ->when($filters['user_id'] ?? null, fn ($q, $v) => $q->where('user_id', $v))
            ->when($filters['status'] ?? null, fn ($q, $v) => $q->where('status', $v))
            ->when($filters['dari'] ?? null, fn ($q, $v) => $q->whereDate('created_at', '>=', $v))
            ->when($filters['sampai'] ?? null, fn ($q, $v) => $q->whereDate('created_at', '<=', $v))
            ->latest()
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        return view('admin.log.index', [
            'logs'       => $logs,
            'userList'   => User::orderBy('name')->get(['id', 'name']),
            'statusList' => ActivityLog::whereNotNull('status')->distinct()->orderBy('status')->pluck('status'),
        ]);
    }
}
