<?php

namespace App\Http\Controllers;

use App\Models\Connection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReceivedRequestController extends Controller
{
    public function index(Request $request){
        $authUser = Auth::user();
        $request->validate([
            "limit" => "required",
            "offset" => "required"
        ]);
        $requestData = $request->all();
        $sendRequests = Connection::select("id", "request_from", "request_to")->where("request_to", $authUser->id)
            ->where("status", "REQUESTED")
            ->with(["request_from_user" => function($sql){
                $sql->select("id", "name", "email");
            }])
            ->offset($requestData["offset"])
            ->limit($requestData["limit"])
            ->get();
        $response = !$sendRequests->isEmpty() ? $sendRequests->toArray() : [];
        return response($response, 200);
    }
}
