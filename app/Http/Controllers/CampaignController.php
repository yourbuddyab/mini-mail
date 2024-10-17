<?php

namespace App\Http\Controllers;

use App\Events\EmailProgressUpdated;
use App\Events\EmailSaveUpdated;
use App\Jobs\ProcessCsv;
use App\Models\Campaign;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CampaignController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response([
            'success' => true,
            'message' => 'Campaign List',
            'data'    =>  Campaign::where('user_id', auth()->id())->with('emails')->withCount('emails')->get()
        ], 200);
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
            return response([
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
            'csv_file' => $path
        ]);

        ProcessCsv::dispatch($campaign);

        return response([
            'success' => true,
            'message' => 'CSV file uploaded successfully. The campaign is being processed in the background.',
            'data'    =>  $campaign
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Campaign $campaign)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Campaign $campaign)
    {
        return response([
            'success' => true,
            'message' => 'Campaign details',
            'data'    =>  $campaign
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Campaign $campaign)
    {
        $validator = Validator::make($request->all(), [
            'contant' => 'required',
            'name' => 'required',
            'scheduled_at' => 'sometimes'
        ]);

        if ($validator->fails()) {
            return response([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 400);
        }

        $campaign->update(['contant' => $request->contant, 'name' => $request->name, 'scheduled_at' => $request->scheduled_at]);

        return response([
            'success' => true,
            'message' => 'Campaign contant successfully stored.',
            'data'    =>  $campaign
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Campaign $campaign)
    {
        //
    }

    public function saveCount(Campaign $campaign)
    {
        $campaign->load('emails');
        broadcast(new EmailSaveUpdated($campaign->id, count($campaign->emails), 0))->toOthers();
    }

    public function sendCount(Campaign $campaign)
    {
        $campaign->load('emails');
        broadcast(new EmailProgressUpdated($campaign->id, count($campaign->emails), "hello"))->toOthers();
    }
}
