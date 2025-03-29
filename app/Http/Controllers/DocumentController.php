<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Document;

use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Storage;



class DocumentController extends Controller
{
    /**
     * Create a new controller instance
     * @return void
     */

    public function __construct(){
        $this->middleware('auth');
    }

    /*
     *  Display listing of a documents
     *  @return \Illuminate\View\View
     * */
    public function index(Request $request)
    {
        $query = Auth::user()->documents();

        //filter by document type if requested

        if($request->has('type') && $request->type != 'all'){
            $query->where('document_type', $request->type);
        }

        // Search functionality
        if($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {

                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");

            });
    }

        //sort documents
        $sort = $request->sort ?? 'newest';
        switch ($sort) {
            case 'oldest':
                $query->oldest();
                break;
            case 'name-asc':
                $query->orderBy('title', 'asc');
                break;
            case 'name-desc':
                $query->orderBy('title', 'desc');
                break;
            default:
                $query->latest();
                break;

        }

        $documents = $query->paginate(10);

        // get unique document types for filter dropdown

        $documentTypes = Document::where('user_id', Auth::id())
            ->select('document_type')
            ->distinct()
            ->pluck('document_type'); //


        return view('documents.index', compact('documents', 'documentTypes'));






    }

    /**
     * Show the form for creating a new document
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('documents.create');
    }

    /**
     * Store a newly created resource in storage.
     * @param \Illuminate\Http\Request request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'document_type' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'document_file'=> 'required|file|image|mimes:jpeg,png,jpg|max:10240',
        ]);

        $file = $request->file('document_file');
        $fileName = time() . '_' . $file->getClientOriginalName();

        //make sure storage directory exists
        Storage::disk('public')->makeDirectory('documents/'.Auth::id());

        //Store the file
        $filePath = $file->storeAs('documents/'.Auth::id().'/', $fileName, 'public');

        Document::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'document_type' => $request->document_type,
            'description' => $request->description,
            'file_path'=>$filePath,
            'file_name'=>$fileName,
            'file_type'=>$file->getClientOriginalExtension(),
            'file_size'=>$file->getSize(),
            'share_with_doctor'=>$request->has('share_with_doctor'),
            'share_with_specialist'=>$request->has('share_with_specialist'),
            'share_with_family'=>$request->has('share_with_family'),
        ]);

        return redirect()->route('documents.index')
                        ->with('success','Document created successfully.');
    }

    /**
     * Display the specified document
     * @param \App\Models\Document
     * @return \Illuminate\View\View
     */
    public function show(Document $document)
    {
        //check if the authenticated user owns this document
        if($document->user_id !== Auth::id()){
            abort(403, 'Unauthorized action.');
        }

        return view('documents.show', compact('document'));
    }

    /**
     * Show the form for editing the specified resource.
     * @param \App\Models\Document $document
     * @return \Illuminate\View\View
     *
     */
    public function edit(Document $document)
    {
        //check if the authenticated user owns this document
        if($document->user_id !== Auth::id()){
            abort(403, 'Unauthorized action.');
        }

        return view('documents.edit', compact('document'));

    }

    /**
     * Update the specified document in storage.
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Document $document
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Document $document)
    {
        if ($document->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        $request->validate([
            'title' => 'required|string|max:255',
            'document_type' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $document->update([
            'title' => $request->title,
            'document_type' => $request->document_type,
            'description' => $request->description,
            'share_with_doctor' => $request->has('share_with_doctor'),
            'share_with_specialist' => $request->has('share_with_specialist'),
            'share_with_family' => $request->has('share_with_family')
        ]);

        return redirect()->route('documents.index')
            ->with('success','Document updated successfully.');
    }

    /**
     * Download the specified resource from storage.
     * @param \App\Models\Document $document
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     *
     */
    public function download(Document $document)
    {
        if($document->user_id !== Auth::id()){
            abort(403, 'Unauthorized action.');
        }

        return Storage::disk('public')->download(
            $document->file_path,
            $document->file_name
        );
    }
}
