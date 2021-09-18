<?php

namespace App\Http\Traits;

use Illuminate\Http\Request;
use App\Models\Consumer;
use Illuminate\Support\Facades\Http;
use DB;

trait HandlesConsumer{


    public function fetchConsumer(Request $request){
        $id = $request->input('business_id');
        $businessConsumer = DB::table('business_consumers')->where([
            'business_id' => $id,
        ])->get();

        if(!$businessConsumer){
            return null;
        }

        return $businessConsumer;
    }

    public function findConsumer($id){
        $consumer = Consumer::find($id);

        if(!$consumer){
        return false;
        }

        return $consumer;
    }

    

  
}
