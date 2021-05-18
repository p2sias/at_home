<?php

namespace App\Http\Controllers;

use App\Http\Resources\LogsResource;
use App\Models\Log;
use Illuminate\Http\Request;

class LogsController extends Controller
{
    public function index()
    {
        return response()->json(LogsResource::collection(Log::orderBy('id', 'DESC')->get()));
    }
}
