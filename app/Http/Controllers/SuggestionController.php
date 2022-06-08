<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuggestionController extends Controller
{
    /**
     * This function generates all other users I am not connected yet and sent/receive invitation 
     */
    public function index(Request $request){

        $responseData = [];
        $authUser = Auth::user();
        $request->validate([
            "limit" => "required",
            "offset" => "required"
        ]);
        $requestData = $request->all();
        // We are exluding current user to show in the suggestion list
        $suggestedUsers = User::select("id", "name", "email")
            ->where("id", "!=", $authUser->id)
            ->whereDoesntHave("requested_from")
            ->whereDoesntHave("requested_to")
            ->offset($requestData["offset"])
            ->limit($requestData["limit"])
            ->get();;            
        $responseData["list"] = !$suggestedUsers->isEmpty() ? $suggestedUsers->toArray() : [];
        return response($responseData, 200);
    }
}
