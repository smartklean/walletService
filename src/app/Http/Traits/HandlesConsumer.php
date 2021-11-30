<?php

namespace App\Http\Traits;

use App\Models\Consumer;
use DB;

trait HandlesConsumer{

    public function checkBusinessHasConsumer($businessId, $consumerId, $isLive) {
        return $this->findConsumer($businessId, $consumerId, $isLive);
    }

    private function findConsumer($businessId, $consumerId, $isLive){
        $table = $isLive ? 'business_consumers' : 'test_business_consumers';

        $consumer = DB::table($table)->where([
            'business_id' => $businessId,
            'consumer_id' => $consumerId,
        ])->first();

        return $consumer ? $consumer : false;
    }

    public function findConsumerByQuery($businessId, $param, $isLive){
      $consumerIds = [];

      $records = Consumer::where('email', 'LIKE', "%{$param}%")->get();

      foreach($records as $record){
        array_push($consumerIds, $record->id);
      }

      $table = $isLive ? 'business_consumers' : 'test_business_consumers';

      $records = DB::table($table)->where('first_name', 'LIKE', "%{$param}%")
              ->orWhere('last_name', 'LIKE', "%{$param}%")
              ->orWhere('phone_number', 'LIKE', "%{$param}%")
              ->orWhere('code', 'LIKE', "%{$param}%")
              ->get();

      foreach ($records as $record) {
        array_push($consumerIds, $record->id);
      }

      $consumerIds = array_unique($consumerIds);

      $consumers = [];

      foreach($consumerIds as $consumerId){
        $consumer = $this->fetchConsumer($businessId, $consumerId, $isLive);

        if($consumer){
          array_push($consumers, $consumer);
        }
      }

      return $consumers;
    }

    public function searchConsumer($businessId, $param, $isLive){
      return !$param ? $this->fetchConsumers($businessId, $isLive) : $this->findConsumerByQuery($businessId, $param, $isLive);
    }

    public function fetchConsumerByEmail($businessId, $email, $isLive){
        $consumer = Consumer::where('email', $email)->first();

        if($consumer){
            $businessConsumerData = $this->findConsumer($businessId, $consumer->id, $isLive);

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

    public function fetchConsumers($businessId, $isLive){
      $table = $isLive ? 'business_consumers' : 'test_business_consumers';

      $businessConsumerData = DB::table($table)->where([
          'business_id' => $businessId,
      ])->get();

      $consumers = [];

      if(!$businessConsumerData->isEmpty()){
        foreach($businessConsumerData as $data){
          $consumer = $this->fetchConsumer($businessId, $data->consumer_id, $isLive);

          array_push($consumers, $consumer);
        }
      }

      return $consumers;
    }
}
