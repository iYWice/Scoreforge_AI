<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;

/**
 * Represents the ActivityLogController application component.
 */
class ActivityLogController extends Controller
{
    /**
     * Handles index.
     */
    public function index(Request $request)
    {
        $logs = ActivityLog::with('user')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->input('search');

                $query->where(function ($q) use ($search) {
                    $q->where('description', 'like', "%{$search}%")
                        ->orWhere('action', 'like', "%{$search}%")
                        ->orWhere('id', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($user) use ($search) {
                            $user->where('fname', 'like', "%{$search}%")
                                ->orWhere('lname', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%")
                                ->orWhere('student_id', 'like', "%{$search}%")
                                ->orWhere('teacher_id', 'like', "%{$search}%");
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

    /**
     * Handles export.
     */
    public function export(Request $request)
    {
        app(ActivityLogger::class)->log(
            'activity_logs.exported',
            'Administrator exported activity logs.'
        );

        return response()->streamDownload(function () use ($request): void {
            $stream = fopen('php://output', 'w');

            fputcsv($stream, [
                'Log ID',
                'Date and Time',
                'User',
                'Account ID',
                'Email',
                'Action',
                'Description',
                'Properties',
            ]);

            ActivityLog::with('user')
                ->when($request->filled('search'), function ($query) use ($request): void {
                    $search = $request->input('search');

                    $query->where(function ($q) use ($search): void {
                        $q->where('description', 'like', "%{$search}%")
                            ->orWhere('action', 'like', "%{$search}%")
                            ->orWhere('id', 'like', "%{$search}%")
                            ->orWhereHas('user', function ($user) use ($search): void {
                                $user->where('fname', 'like', "%{$search}%")
                                    ->orWhere('lname', 'like', "%{$search}%")
                                    ->orWhere('email', 'like', "%{$search}%")
                                    ->orWhere('student_id', 'like', "%{$search}%")
                                    ->orWhere('teacher_id', 'like', "%{$search}%");
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
                        $accountId = $log->user?->role === 'teacher'
                            ? ($log->user?->teacher_id ?? '')
                            : ($log->user?->student_id ?? '');

                        fputcsv($stream, [
                            $log->id,
                            $log->created_at,
                            trim(($log->user?->fname ?? '').' '.($log->user?->lname ?? '')),
                            $accountId,
                            $log->user?->email,
                            $log->action,
                            $log->description,
                            $log->properties ? json_encode($log->properties) : '',
                        ]);
                    }
                });

            fclose($stream);
        }, 'activity-logs-'.now()->format('Ymd-His').'.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
