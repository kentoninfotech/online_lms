<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Lesson;
use App\Models\Attendance;
use App\Models\Instructor;
use Illuminate\Support\Facades\Response;
use Barryvdh\DomPDF\Facade\Pdf;

class StudentController extends Controller
{
    /**
     * Display the specified student along with their lessons and attendance records.
     */
    public function show(Request $request, Student $student)
    {
        // $student->load([]);
        // Instructors dropdown (for lesson filtering)
        $instructors = Instructor::pluck('name', 'id');

        // Lessons with filters
        $lessonsQuery = Lesson::with(['instructor.user', 'occurrences'])
            ->where('student_id', $student->id);

        if ($request->filled('subject')) {
            $lessonsQuery->where('subject', 'like', '%' . $request->subject . '%');
        }
        if ($request->filled('instructor')) {
            $lessonsQuery->where('instructor_id', $request->instructor);
        }

        $lessons = $lessonsQuery->paginate(10, ['*'], 'lessons_page');

        // Attendance with filters
        $attendancesQuery = Attendance::with('occurrence.lesson')
            ->where('attendable_type', Student::class)
            ->where('attendable_id', $student->id);

        if ($request->filled('status')) {
            $attendancesQuery->where('status', $request->status);
        }
        if ($request->filled('from')) {
            $attendancesQuery->whereDate('created_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $attendancesQuery->whereDate('created_at', '<=', $request->to);
        }

        $attendances = $attendancesQuery->orderByDesc('created_at')
            ->paginate(10, ['*'], 'attendance_page');

        return view('dashboard.student', compact('student', 'lessons', 'attendances', 'instructors'));
    }

    /**
     * Export lessons or records
     */
    public function exportLessons(Student $student, $format)
    {
        $lessons = Lesson::with(['instructor.user', 'occurrences'])
            ->where('student_id', $student->id)
            ->get();

        if ($format === 'csv') {
            $csvData = "Subject,Instructor,Start Time,Duration\n";
            foreach ($lessons as $lesson) {
                foreach ($lesson->occurrences as $occ) {
                    $csvData .= "{$lesson->subject},{$lesson->instructor->name}," .
                                $occ->scheduled_start->format('d M Y h:i A') . "," .
                                $occ->duration_minutes . " mins\n";
                }
            }
            return Response::make(rtrim($csvData, "\n"), 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=student-{$student->id}-lessons.csv",
            ]);
        }

        if ($format === 'pdf') {
            $pdf = Pdf::loadView('dashboard.exports.lessons', compact('student', 'lessons'));
            return $pdf->download("student-{$student->id}-lessons.pdf");
        }

        abort(404);
    }

    /**
     * Export lessons or records
     */
    public function exportAttendance(Student $student, $format)
    {
        $attendances = Attendance::with('occurrence.lesson')
            ->where('attendable_type', Student::class)
            ->where('attendable_id', $student->id)
            ->orderByDesc('created_at')
            ->get();

        if ($format === 'csv') {
            $csvData = "Lesson,Status,Join,Leave,Duration\n";
            foreach ($attendances as $a) {
                $csvData .= "{$a->occurrence->lesson->subject}," .
                            ucfirst($a->status) . "," .
                            ($a->join_time?->format('h:i A') ?? '-') . "," .
                            ($a->leave_time?->format('h:i A') ?? '-') . "," .
                            ($a->duration_minutes ?? '-') . " mins\n";
            }
            return Response::make(rtrim($csvData, "\n"), 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=student-{$student->id}-attendance.csv",
            ]);
        }

        if ($format === 'pdf') {
            $pdf = Pdf::loadView('dashboard.exports.attendance', compact('student', 'attendances'));
            return $pdf->download("student-{$student->id}-attendance.pdf");
        }

        abort(404);
    }

}
