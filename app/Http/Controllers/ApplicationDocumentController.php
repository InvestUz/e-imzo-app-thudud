<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\ApplicationDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ApplicationDocumentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request, Application $application)
    {
        $this->authorize('view', $application);

        $request->validate([
            'document'  => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
            'doc_type'  => 'required|in:application_letter,contract,other',
            'doc_notes' => 'nullable|string|max:500',
        ]);

        $file = $request->file('document');
        $path = $file->store('applications/' . $application->id, 'public');

        $application->documents()->create([
            'uploaded_by'   => Auth::id(),
            'type'          => $request->input('doc_type'),
            'original_name' => $file->getClientOriginalName(),
            'path'          => $path,
            'mime_type'     => $file->getMimeType(),
            'size'          => $file->getSize(),
            'notes'         => $request->input('doc_notes'),
        ]);

        return back()->with('success', 'Fayl muvaffaqiyatli yuklandi');
    }

    public function destroy(ApplicationDocument $document)
    {
        $this->authorize('view', $document->application);

        Storage::disk('public')->delete($document->path);
        $document->delete();

        return back()->with('success', 'Fayl o\'chirildi');
    }
}
