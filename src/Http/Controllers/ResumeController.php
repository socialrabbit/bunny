<?php

namespace Bunny\Http\Controllers;

use Bunny\Models\Resume;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ResumeController extends Controller
{
    /**
     * Get the active resume data.
     */
    public function show()
    {
        $resume = Resume::active()->latest()->first();

        if (!$resume) {
            return response()->json(['error' => 'No active resume found'], 404);
        }

        return response()->json([
            'title' => $resume->title,
            'subtitle' => $resume->subtitle,
            'description' => $resume->description,
            'highlights' => $resume->highlights,
            'fileType' => $resume->file_type,
            'fileSize' => $resume->file_size,
            'downloadUrl' => $resume->download_url,
            'alternateFormats' => $resume->alternate_formats,
            'lastUpdated' => $resume->updated_at,
            'downloads' => $resume->downloads,
        ]);
    }

    /**
     * Upload a new resume.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'required|string',
            'highlights' => 'nullable|array',
            'file' => 'required|file|mimes:pdf,doc,docx|max:10240', // 10MB max
            'alternate_formats' => 'nullable|array',
            'alternate_formats.*' => 'file|mimes:pdf,doc,docx|max:10240',
        ]);

        // Store the main resume file
        $filePath = $request->file('file')->store('resumes', 'public');
        $fileType = $request->file('file')->getClientOriginalExtension();

        // Handle alternate formats
        $alternateFormats = [];
        if ($request->hasFile('alternate_formats')) {
            foreach ($request->file('alternate_formats') as $format) {
                $formatPath = $format->store('resumes', 'public');
                $alternateFormats[] = [
                    'type' => $format->getClientOriginalExtension(),
                    'url' => Storage::url($formatPath),
                ];
            }
        }

        // Deactivate previous resumes
        Resume::where('is_active', true)->update(['is_active' => false]);

        // Create new resume
        $resume = Resume::create([
            'title' => $request->title,
            'subtitle' => $request->subtitle,
            'description' => $request->description,
            'highlights' => $request->highlights,
            'file_path' => $filePath,
            'file_type' => $fileType,
            'alternate_formats' => $alternateFormats,
            'is_active' => true,
            'downloads' => 0,
        ]);

        return response()->json([
            'message' => 'Resume uploaded successfully',
            'resume' => $resume,
        ], 201);
    }

    /**
     * Download the resume and track the download.
     */
    public function download(Resume $resume)
    {
        if (!Storage::exists($resume->file_path)) {
            return response()->json(['error' => 'Resume file not found'], 404);
        }

        $resume->trackDownload();

        return Storage::download(
            $resume->file_path,
            Str::slug($resume->title) . '.' . $resume->file_type
        );
    }

    /**
     * Delete a resume.
     */
    public function destroy(Resume $resume)
    {
        // Delete the main file
        if (Storage::exists($resume->file_path)) {
            Storage::delete($resume->file_path);
        }

        // Delete alternate format files
        foreach ($resume->alternate_formats as $format) {
            if (Storage::exists($format['url'])) {
                Storage::delete($format['url']);
            }
        }

        $resume->delete();

        return response()->json(['message' => 'Resume deleted successfully']);
    }
} 