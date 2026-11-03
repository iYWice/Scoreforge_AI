<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $logs = ActivityLog::with('user')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->input('search');

                $query->where(function ($q) use ($search) {
                    $q->where('description', 'like', "%{$search}%")
                        ->orWhere('action', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($user) use ($search) {
                            $user->where('fname', 'like', "%{$search}%")
                                ->orWhere('lname', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        });
                });
            })
            ->when($request->filled('action'), function ($query) use ($request) {
                $query->where('action', $request->input('action'));
            })
            ->when($request->filled('date_from'), function ($query) use ($request) {
                $query->whereDate('created_at', '>=', $request->input('date_from'));
            })
            ->when($request->filled('date_to'), function ($query) use ($request) {
                $query->whereDate('created_at', '<=', $request->input('date_to'));
            })
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        $actions = ActivityLog::distinct()
            ->orderBy('action')
            ->pluck('action');

        return view('admin.activity-logs.index', compact(
            'logs',
            'actions'
        ));
    }
    public function export(Request $request)
    {
        return response()->streamDownload(function () use ($request): void {
            $stream = fopen('php://output', 'w');

            fputcsv($stream, [
                'Date and Time',
                'User',
                'Email',
                'Action',
                'Description',
                'IP Address',
            ]);

            ActivityLog::with('user')
                ->when($request->filled('search'), function ($query) use ($request): void {
                    $search = $request->input('search');

                    $query->where(function ($q) use ($search): void {
                        $q->where('description', 'like', "%{$search}%")
                            ->orWhere('action', 'like', "%{$search}%")
                            ->orWhereHas('user', function ($user) use ($search): void {
                                $user->where('fname', 'like', "%{$search}%")
                                    ->orWhere('lname', 'like', "%{$search}%")
                                    ->orWhere('email', 'like', "%{$search}%");
                            });
                    });
                })
                ->when($request->filled('action'), function ($query) use ($request): void {
                    $query->where('action', $request->input('action'));
                })
                ->when($request->filled('date_from'), function ($query) use ($request): void {
                    $query->whereDate('created_at', '>=', $request->input('date_from'));
                })
                ->when($request->filled('date_to'), function ($query) use ($request): void {
                    $query->whereDate('created_at', '<=', $request->input('date_to'));
                })
                ->orderBy('id')
                ->chunk(500, function ($logs) use ($stream): void {
                    foreach ($logs as $log) {
                        fputcsv($stream, [
                            $log->created_at,
                            trim(($log->user?->fname ?? '') . ' ' . ($log->user?->lname ?? '')),
                            $log->user?->email,
                            $log->action,
                            $log->description,
                            $log->ip_address,
                        ]);
                    }
                });

            fclose($stream);
        }, 'activity-logs-' . now()->format('Ymd-His') . '.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
