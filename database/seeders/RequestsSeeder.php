<?php

namespace Database\Seeders;

use App\Models\Connection;
use App\Models\User;
use Illuminate\Database\Seeder;

class RequestsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 30 users have been created
        // Grab All users from DB
        $mainUser = User::where("email", "farazirfan47@gmail.com")->first();
        $allUsers = User::where("email", "!=", "farazirfan47@gmail.com")->get();
        // Creat Requests
        if(!$allUsers->isEmpty()){
            $chunkCount = 1;
            $secondChunk = [];          
            foreach($allUsers->chunk(10) as $userChunk){
                if($chunkCount == 1){
                    // Add Requests by me
                    foreach($userChunk as $singleUser){
                        Connection::create([
                            "request_from" => $mainUser["id"],
                            "request_to" => $singleUser["id"],
                            "status" => "REQUESTED",
                        ]);
                    }
                }else if($chunkCount == 2){
                    $secondChunk = $userChunk;
                    // Add Requests from others
                    foreach($userChunk as $singleUser){
                        Connection::create([
                            "request_from" => $singleUser["id"],
                            "request_to" => $mainUser["id"],
                            "status" => "REQUESTED",
                        ]);
                    }
                }else if($chunkCount == 3){
                    // Add connections in common
                    foreach($userChunk as $singleUser){
                        Connection::create([
                            "request_from" => $mainUser["id"],
                            "request_to" => $singleUser["id"],
                            "status" => "ACCEPTED",
                        ]);
                        // SECOND CHUNK USERS WILL ALSO GET A ACCEPTED STATUS
                        foreach($secondChunk as $secondChunkUser){
                            Connection::create([
                                "request_from" => $secondChunkUser["id"],
                                "request_to" => $singleUser["id"],
                                "status" => "ACCEPTED",
                            ]);
                        }
                    }
                    break;
                }
                $chunkCount++;
            }
        }
    }
}
