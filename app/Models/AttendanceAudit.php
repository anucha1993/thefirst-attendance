<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceAudit extends Model
{
    use HasFactory;

    protected $fillable = [
        'trip_id',
        'attendance_record_id',
        'user_id',
        'action',
        'reason',
        'old_data',
        'new_data',
    ];

    protected $casts = [
        'old_data' => 'json',
        'new_data' => 'json',
    ];

    // ============ Relationships ============

    /**
     * Trip being audited
     */
    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }

    /**
     * Attendance record being audited
     */
    public function attendanceRecord()
    {
        return $this->belongsTo(AttendanceRecord::class);
    }

    /**
     * User who performed the action
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ============ Helper Methods ============

    /**
     * Get action label in Thai
     */
    public function getActionLabel()
    {
        $labels = [
            'created' => 'สแกนลงทะเบียน',
            'deleted' => 'ลบการสแกน',
            'cancelled' => 'ยกเลิกการสแกน',
            'manually_added' => 'เพิ่มด้วยตนเอง',
            'manually_removed' => 'ลบด้วยตนเอง',
        ];

        return $labels[$this->action] ?? $this->action;
    }
}
