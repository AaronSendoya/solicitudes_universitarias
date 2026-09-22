<?php

namespace App\Http\Controllers;

use App\Models\Request as RequestModel;
use App\Models\RequestEvidence;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class StudentRequestController extends Controller
{
    /**
     * Display a listing of the student's requests.
     */
    public function index()
    {
        $requests = RequestModel::where('user_id', Auth::id())
            ->with(['category', 'assignedTo'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('student.requests.index', compact('requests'));
    }

    /**
     * Show the form for creating a new request.
     */
    public function create()
    {
        $categories = Category::where('is_active', true)->get();
        return view('student.requests.create', compact('categories'));
    }

    /**
     * Store a newly created request in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'title'       => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'evidences.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120' // Max 5MB per file
        ]);

        DB::transaction(function () use ($validated, $request) {
            $newRequest = RequestModel::create([
                'user_id'     => Auth::id(),
                'category_id' => $validated['category_id'],
                'title'       => $validated['title'],
                'description' => $validated['description'],
                'priority'    => 'Media', // Por defecto Media, el Gestor la cambiará
                'status'      => 'Pendiente'
            ]);

            if ($request->hasFile('evidences')) {
                foreach ($request->file('evidences') as $file) {
                    // Guarda en storage/app/public/evidences
                    $path = $file->store('evidences', 'public');
                    
                    RequestEvidence::create([
                        'request_id' => $newRequest->id,
                        'file_path'  => $path,
                    ]);
                }
            }
        });

        return redirect()->route('student.requests.index')
            ->with('success', 'Solicitud creada exitosamente.');
    }

    /**
     * Display the specified request.
     */
    public function show($id)
    {
        $requestModel = RequestModel::where('user_id', Auth::id())
            ->with(['category', 'assignedTo', 'evidences'])
            ->findOrFail($id);

        return view('student.requests.show', compact('requestModel'));
    }
}
