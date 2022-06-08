<?php

namespace App\Http\Controllers;

use App\Models\Connection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SentRequestController extends Controller
{
    public function index(Request $request){
        $authUser = Auth::user();
        $request->validate([
            "limit" => "required",
            "offset" => "required"
        ]);
        $requestData = $request->all();
        $sendRequests = Connection::select("id", "request_from", "request_to")->where("request_from", $authUser->id)
            ->where("status", "REQUESTED")
            ->with(["request_to_user" => function($sql){
                $sql->select("id", "name", "email");
            }])
            ->offset($requestData["offset"])
            ->limit($requestData["limit"])
            ->get();
        $response = !$sendRequests->isEmpty() ? $sendRequests->toArray() : [];
        return response($response, 200);
    }

    public function store(Request $request){
        $authUser = Auth::user();
        $request->validate([
            "requestTo" => "required"
        ]);
        $requestData = $request->all();
        if(Connection::create(["request_from" => $authUser->id, "request_to" => $requestData["requestTo"], "status" => "REQUESTED"])){
            return response(["success" => true], 200);
        }
        return response(["success" => false], 400);
    }

    public function destroy(Request $request){
        $request->validate([
            "connectionId" => "required"
        ]);
        $requestData = $request->all();
        if(Connection::where("id", $requestData["connectionId"])->delete()){
            return response(["success" => true], 200);
        }
        return response(["success" => false], 400);
    }
}
