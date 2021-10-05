<?php

namespace App\Http\Traits;

use App\Models\Consumer;
use DB;

trait HandlesConsumer{

    public function checkBusinessHasConsumer($businessId, $consumerId, $isLive) {
        return $this->findConsumer($businessId, $consumerId, $isLive);
    }

    public function fetchConsumer($businessId, $consumerId, $isLive){
        $consumer = Consumer::find($consumerId);

        if($consumer){
            $businessConsumerData = $this->findConsumer($businessId, $consumerId, $isLive);

            if($businessConsumerData){
                $consumer->business_id = $businessId;
                $consumer->first_name = $businessConsumerData->first_name;
                $consumer->last_name = $businessConsumerData->last_name;
                $consumer->phone_number = $businessConsumerData->phone_number;
                $consumer->code = $businessConsumerData->code;
                $consumer->is_blacklisted = $businessConsumerData->is_blacklisted;
                $consumer->created_at = $businessConsumerData->created_at;
                $consumer->updated_at = $businessConsumerData->updated_at;

                return $consumer;
            }
        }

        return null;
    }

    private function findConsumer($businessId, $consumerId, $isLive){

        if($isLive){
            $consumer = DB::table('business_consumers')->where([
                'business_id' => $businessId,
                'consumer_id' => $consumerId,
            ])->first();
    
            return $consumer ? $consumer : false;
        }else{
            $consumer = DB::table('test_business_consumers')->where([
                'business_id' => $businessId,
                'consumer_id' => $consumerId,
            ])->first();
    
             return $consumer ? $consumer : false;
        }
        
    }

    public function fetchConsumers($isLive, $businessId){
        $table = $isLive ? 'business_consumers' : 'test_business_consumers';
        $businessConsumer = DB::table($table)->where([
            'business_id' => $businessId,
        ])->get();

        if(!$businessConsumer){
            return null;
        }

        return $businessConsumer;
    }
    
}
