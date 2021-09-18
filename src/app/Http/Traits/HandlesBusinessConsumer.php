<?php

namespace App\Http\Traits;

use Illuminate\Http\Request;
use App\Models\BusinessConsumer;
use Illuminate\Support\Facades\Http;
use DB;

trait HandlesBusinessConsumer{

    public function fetchBusinessConsumer(Request $request){
        $id = $request->input('business_id');
        $businessConsumer = DB::table('business_consumers')->where([
            'business_id' => $id,
        ])->get();

        if(!$businessConsumer){
            return null;
        }

        return $businessConsumer;
    }

    public function findBusinessConsumer($id){

        $businessConsumer = BusinessConsumer::find($id);

        if(!$businessConsumer){
            return false;
        }

        return $businessConsumer;
    }

    public function toggleBlacklist(Request $request, $id){
        $businessId = $request->business_id;
        $mode = DB::table('business_consumers')->where([
        'business_id' => $businessId,
        'id' => $id,
        ])->first()->is_blacklisted;

        DB::table('business_consumers')->where([
        'id' => $id,
        'business_id' => $businessId
        ])->update([
        'is_blacklisted' => !$mode
        ]);

        return true;
    }

  
}
