<?php

namespace App\Http\Controllers;

use App\Models\Connection;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConnectionCountController extends Controller
{
    public function index(){
        $authUser  = Auth::user();
        // SUGGESTION COUNTS
        $suggestionsCount = User::selectRaw("COUNT(*) as count")
            ->where("id", "!=", $authUser->id)
            ->whereDoesntHave("requested_from")
            ->whereDoesntHave("requested_to")
            ->first();
        // SENT REQUESTS COUNT
        $sentRequestsCount = Connection::selectRaw("Count(*) as count")->where("request_from", $authUser->id)->where("status", "REQUESTED")->first();
        // RECEIVED REQUESTS COUNT
        $receivedRequestsCount = Connection::selectRaw("Count(*) as count")->where("request_to", $authUser->id)->where("status", "REQUESTED")->first();
        // CONNECTION COUNT
        $connectionsCount = Connection::selectRaw("Count(*) as count")->where(function($sql) use($authUser){
                $sql->where("request_from", $authUser->id)
                ->orWhere("request_to", $authUser->id);
            })
            ->where("status", "ACCEPTED")
            ->first();
        return response([
            "suggestionsCount" => $suggestionsCount->count,
            "sentRequestsCount" => $sentRequestsCount->count,
            "receivedRequestsCount" => $receivedRequestsCount->count,
            "connectionsCount" => $connectionsCount->count
        ], 200);
    }
}
