<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'title',
        'exam_code',
        'subject_id',
        'class_id',
        'exam_code',
        'time_limit',
        'total_items',
        'created_by',
        'created_at',
        'status',
        'duration',
        'passing_score',
        'opens_at',
        'closes_at',
    ];

    protected function casts(): array
    {
        return [
            'opens_at' => 'datetime',
            'closes_at' => 'datetime',
        ];
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function class()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function questions()
    {
        return $this->hasMany(Question::class, 'exam_id');
    }

    public function attempts()
    {
        return $this->hasMany(ExamAttempt::class, 'exam_id');
    }

    public function aiInsights()
    {
        return $this->hasMany(AiInsight::class);
    }

    public function scopeAvailableToStudent(Builder $query, User $student): Builder
    {
        $offering = new ClassSubject;
        $examTable = $query->getModel()->getTable();
        $offeringTable = $offering->getTable();
        $enrollmentTable = $offering->students()->getTable();

        return $query->whereExists(function ($subquery) use (
            $student,
            $examTable,
            $offeringTable,
            $enrollmentTable
        ) {
            $subquery->selectRaw('1')
                ->from($offeringTable.' as eligible_offerings')
                ->join(
                    $enrollmentTable.' as eligible_enrollments',
                    'eligible_enrollments.class_subject_id',
                    '=',
                    'eligible_offerings.id'
                )
                ->whereColumn('eligible_offerings.class_id', $examTable.'.class_id')
                ->whereColumn('eligible_offerings.subject_id', $examTable.'.subject_id')
                ->where('eligible_enrollments.student_id', $student->getKey());
        });
    }

    public function isAvailableToStudent(User $student): bool
    {
        return static::query()
            ->whereKey($this->getKey())
            ->availableToStudent($student)
            ->exists();
    }

    public function isWithinAvailabilityWindow(): bool
    {
        if ($this->status !== 'published') {
            return false;
        }

        if ($this->opens_at !== null && now()->lt($this->opens_at)) {
            return false;
        }

        if ($this->closes_at !== null && now()->gte($this->closes_at)) {
            return false;
        }

        return true;
    }

    public function availabilityMessage(): string
    {
        if ($this->status !== 'published') {
            return 'This exam is not published.';
        }

        if ($this->opens_at !== null && now()->lt($this->opens_at)) {
            return 'This exam opens at '.$this->opens_at->format('F j, Y \\a\\t g:i A').' (Philippine Time).';
        }

        if ($this->closes_at !== null && now()->gte($this->closes_at)) {
            return 'This exam deadline passed at '.$this->closes_at->format('F j, Y \\a\\t g:i A').' (Philippine Time).';
        }

        return 'This exam is open.';
    }

    public function effectiveDeadline($startedAt): Carbon
    {
        $durationDeadline = Carbon::parse($startedAt)
            ->addMinutes((int) $this->duration);

        if ($this->closes_at === null) {
            return $durationDeadline;
        }

        return $durationDeadline->lt($this->closes_at)
            ? $durationDeadline
            : $this->closes_at->copy();
    }

    public function closeIfExpired(): bool
    {
        if (
            $this->status === 'published' &&
            $this->closes_at !== null &&
            now()->gte($this->closes_at)
        ) {
            $this->update(['status' => 'closed']);

            return true;
        }

        return false;
    }

    public static function closeExpiredForTeacher(User $teacher): void
    {
        static::query()
            ->where('created_by', $teacher->getKey())
            ->where('status', 'published')
            ->whereNotNull('closes_at')
            ->where('closes_at', '<=', now())
            ->update(['status' => 'closed']);
    }
}
