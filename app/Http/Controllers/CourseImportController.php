<?php

namespace App\Http\Controllers;

use App\Services\CourseCSVImportService;
use Illuminate\Http\Request;

class CourseImportController extends Controller
{
    protected $importService;

    public function __construct(CourseCSVImportService $importService)
    {
        $this->importService = $importService;
    }

    /**
     * Show import form
     */
    public function create()
    {
        $this->authorize('isAdmin');

        $categories = \App\Models\CourseCategory::where('is_active', true)->get();

        return view('admin.courses.import', compact('categories'));
    }

    /**
     * Process CSV import
     */
    public function store(Request $request)
    {
        $this->authorize('isAdmin');

        $validated = $request->validate([
            'file' => 'required|file|mimes:csv,txt,xlsx,xls',
            'category_id' => 'required|exists:course_categories,id',
            'csv_format' => 'required|in:standard,dates_venues'
        ]);

        try {
            // Get the uploaded file directly
            $uploadedFile = $request->file('file');
            $fileExt = strtolower($uploadedFile->getClientOriginalExtension());
            
            // Get the absolute path of the temporarily uploaded file in PHP's temp directory
            $filePath = $uploadedFile->getRealPath();
            
            $result = $this->importService->import(
                $filePath,
                $validated['category_id'],
                $fileExt,
                $validated['csv_format']
            );

            if ($result['imported'] > 0) {
                $msg = "Successfully imported {$result['imported']} course(s)";
                if ($result['errors_count'] > 0) {
                    $msg .= " with {$result['errors_count']} error(s).";
                }
                return redirect()->route('admin.courses.import')
                    ->with('success', $msg);
            }
            
            // No courses imported - show detailed errors
            $errorMessages = [];
            if (!empty($result['errors']) && is_array($result['errors'])) {
                $errorMessages = $result['errors'];
            } else {
                $errorMessages = ['No courses imported. Check file format.'];
            }
            
            return redirect()->route('admin.courses.import')
                ->withErrors($errorMessages);

        } catch (\Exception $e) {
            \Log::error('Course import error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('admin.courses.import')
                ->withErrors(['file' => 'Import failed: ' . $e->getMessage()]);
        }
    }
}
