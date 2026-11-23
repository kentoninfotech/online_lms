<?php

namespace App\Http\Controllers;

use App\Models\CourseContentResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class CourseContentResourceController extends Controller
{
    /**
     * Download a resource file
     */
    public function download(CourseContentResource $resource): BinaryFileResponse
    {
        // Check if user is enrolled in the course
        $course = $resource->getCourse();
        $enrollment = auth()->user()->enrollments()
            ->where('course_id', $course->id)
            ->where('status', 'active')
            ->first();

        if (!$enrollment) {
            abort(403, 'You are not enrolled in this course or your enrollment is not approved.');
        }

        // Increment download count
        $resource->incrementDownloadCount();

        // Return the file
        $filePath = str_replace('storage/', '', $resource->file_path);
        return response()->download(storage_path('app/public/' . $filePath), basename($resource->file_path));
    }

    /**
     * Delete a resource
     */
    public function destroy(CourseContentResource $resource)
    {
        $this->authorize('delete', $resource->content);

        // Delete the file
        if ($resource->file_path) {
            $filePath = str_replace('storage/', '', $resource->file_path);
            if (Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }
        }

        $resourceId = $resource->id;
        $content = $resource->content;
        $course = $content->course;

        $resource->delete();

        return redirect()->route('admin.course-contents.edit', [$course, $content])
            ->with('success', 'Resource deleted successfully.');
    }
}
