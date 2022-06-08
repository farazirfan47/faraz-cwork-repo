<?php

namespace App\Http\Controllers;

use App\Models\Connection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConnectionController extends Controller
{
    public function index(Request $request){
        $resp = [];
        $authUser = Auth::user();
        $request->validate([
            "limit" => "required",
            "offset" => "required"
        ]);
        $requestData = $request->all();
        $connections = Connection::select("id", "request_from", "request_to")->where(function($sql) use($authUser){
            $sql->where("request_from", $authUser->id)
            ->orWhere("request_to", $authUser->id);
        })
        ->where("status", "ACCEPTED")
        ->with(["request_to_user" => function($sql){
            $sql->select("id", "name", "email");
        }])
        ->with(["request_from_user" => function($sql){
            $sql->select("id", "name", "email");
        }])
        ->offset($requestData["offset"])
        ->limit($requestData["limit"])
        ->get();
        $response = !$connections->isEmpty() ? $connections->toArray() : [];
        // PRPEARE RESPONSE
        foreach($response as $connection){
            $resp[] = [
                "connectionId" => $connection["id"],
                "user" => $connection["request_to_user"]["id"] != $authUser["id"] ? $connection["request_to_user"] : $connection["request_from_user"],
                "commonConnections" => Connection::commonConnections($connection)
            ];
        }
        return response($resp, 200);
    }

    public function update(Request $request){
        $request->validate([
            "connectionId" => "required"
        ]);
        $requestData = $request->all();
        if(Connection::where("id", $requestData["connectionId"])->update(["status" => "ACCEPTED"])){
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
