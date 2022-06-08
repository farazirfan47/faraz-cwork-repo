<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Connection extends Model
{
    use HasFactory;

    protected $fillable = ["request_from", "request_to", "status"];

    public function request_from_user(){
        return $this->belongsTo("App\Models\User", "request_from");
    }

    public function request_to_user(){
        return $this->belongsTo("App\Models\User", "request_to");
    }

    public static function commonConnections($connection){
        $finalResp = [];
        $resp = self::select("id", "request_from", "request_to")
        ->where(function($sql) use ($connection){
            $sql->whereIn("request_from", [$connection["request_from"], $connection["request_to"]])
            ->orWhereIn("request_to", [$connection["request_from"], $connection["request_to"]]);
        })
        ->where("status", "ACCEPTED")
        ->get();
        $fromIds = $resp->pluck("request_from");
        $toIds = $resp->pluck("request_to");
        $mergedIds = $fromIds->merge($toIds);
        $commonIds = array_filter(array_count_values($mergedIds->toArray()), function($v) { return $v > 1; });
        foreach($commonIds as $k => $v){
            if($v == 2 && ($k != $connection["request_from"] && $k != $connection["request_to"])){
                $finalResp[] = User::select("id", "email", "name")->where("id", $k)->first();
            }
        }
        return $finalResp;
    }
}
