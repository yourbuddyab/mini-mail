<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessCsv;
use App\Models\Campaign;
use App\Models\CSVUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CSVUploadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'csv_file' => 'required|mimes:csv,txt',
            'campaign_name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid CSV file or campaign name',
                'errors' => $validator->errors()
            ], 400);
        }

        // Retrieve the file
        $path = Storage::put('uploads/csv_files', $request->file('csv_file'));

        // Create a new campaign
        $campaign = Campaign::create([
            'name' => $request->campaign_name,
            'user_id' => auth()->id(),
            'file_name' => $path
        ]);

        ProcessCsv::dispatch($campaign);

        return response()->json([
            'success' => true,
            'message' => 'CSV file processed successfully',
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(CSVUpload $cSVUpload)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CSVUpload $cSVUpload)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CSVUpload $cSVUpload)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CSVUpload $cSVUpload)
    {
        //
    }
}
