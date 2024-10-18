<?php

namespace App\Http\Controllers;

use App\Models\ProccesStatus;
use Illuminate\Http\Request;

class ProccesStatusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($campaign_id)
    {
        $data['save'] = ProccesStatus::where(['campaign_id' => $campaign_id, 'type' => '1'])->latest()->first(['proccesd', 'failed']);
        $data['email'] = ProccesStatus::where(['campaign_id' => $campaign_id, 'type' => '2'])->latest()->first(['proccesd', 'total']);
        return response([
            'success' => true,
            'message' => 'Process status of campagin',
            'data'    => $data
        ]);
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(ProccesStatus $proccesStatus) {}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProccesStatus $proccesStatus)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProccesStatus $proccesStatus)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProccesStatus $proccesStatus)
    {
        //
    }
}
