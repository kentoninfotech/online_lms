<?php

namespace App\Http\Controllers;

use App\Models\CourseContentResource;
use App\Models\CourseEnrollee;
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
        $enrollment = CourseEnrollee::where('user_id', auth()->id())
            ->where('course_id', $course->id)
            ->where('status', 'active')
            ->first();

        if (!$enrollment) {
            abort(403, 'You are not enrolled in this course or your enrollment is not approved.');
        }

        // Increment download count
        $resource->incrementDownloadCount();

        // Get the file path and normalize it
        $filePath = $resource->file_path;
        
        // Remove 'storage/' prefix if it exists for the actual file path
        if (str_starts_with($filePath, 'storage/')) {
            $filePath = substr($filePath, 8);
        }

        // Check if file exists in the public disk
        if (!Storage::disk('public')->exists($filePath)) {
            // Try to find the file by checking if it was stored with an absolute path
            $filename = basename($filePath);
            $potentialPath = 'uploads/courses/' . $course->id . '/resources/' . $filename;
            
            if (Storage::disk('public')->exists($potentialPath)) {
                $filePath = $potentialPath;
            } else {
                abort(404, 'Resource file not found.');
            }
        }

        return response()->download(Storage::disk('public')->path($filePath), basename($resource->file_path));
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
