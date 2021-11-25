<?php

namespace App\Http\Controllers\Apis\v1;

use Illuminate\Support\Facades\Validator;
use App\Models\Consumer;
use App\Models\BusinessConsumer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Traits\HandlesConsumer;
use App\Http\Traits\HandlesJsonResponse;
use Illuminate\Support\Str;
use DB;
use Carbon\Carbon;


class ConsumerController extends Controller{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    use HandlesConsumer, HandlesJsonResponse;

    private $foundMessage = 'response.messages.found';
    private $foundMultipleMessage = 'response.messages.found_multiple';
    private $notFoundError = 'response.errors.not_found';
    private $notFoundMessage = 'response.messages.not_found';
    private $addedMessage = 'response.messages.added';
    private $notAddedMessage = 'response.messages.not_added';
    private $updatedMessage = 'response.messages.updated';
    private $deletedMessage = 'response.messages.deleted';
    private $successCode = 'response.codes.success';
    private $errorCode = 'response.codes.error';
    private $notFoundErrorCode = 'response.codes.not_found_error';
    private $consumerAttribute = 'consumer';
    private $consumersAttribute = 'consumers';
    private $isBoolean = 'boolean';
    private $isRequiredBoolean = 'required|boolean';
    private $isRequiredInteger = 'required|integer';
    private $isNullableInteger = 'nullable|integer';
    private $isNullableString = 'nullable|string|max:255';
    private $isRequiredEmail =  'required|email|max:255';
    private $isRequiredString =  'required|string|max:255';
    private $isUnique = 'unique:consumers';
    private $error = 'response.errors.request';
    private $email = 'email';
    private $businessId =  'business_id';
    private $consumerId =  'consumer_id';
    private $firstName =  'first_name';
    private $lastName =  'last_name';
    private $phoneNumber =  'phone_number';
    private $code =  'code';
    private $isLive =  'is_live';
    private $keyword = 'keyword';
    private $isBlacklisted =  'is_blacklisted';
    private $updatedAt = 'updated_at';

    public function fetch(Request $request, $businessId){
        $isLive = $request->is_live ? $request->is_live : false;

        $consumer = $this->fetchConsumers($businessId, $isLive);

        return $this->jsonResponse(__($this->foundMultipleMessage, ['attr' => $this->consumersAttribute]), __($this->successCode), 200, $consumer);
    }

    public function fetchSingle(Request $request, $consumerId, $businessId){
        $isLive = $request->is_live ? $request->is_live : false;

        $consumer = $this->fetchConsumer($businessId, $consumerId, $isLive);

        $response = !$consumer ? $this->jsonResponse(__($this->notFoundMessage, ['attr' => $this->consumerAttribute]), __($this->notFoundErrorCode), 404, [], __($this->notFoundError))
        : $this->jsonResponse(__($this->foundMessage, ['attr' => $this->consumerAttribute]), __($this->successCode), 200, $consumer);

        return $response;
    }

    public function search(Request $request, $businessId){
       $param = $request->param;

       $isLive = $request->is_live ? $request->is_live : false;

       $consumers = $this->searchConsumer($businessId, $param, $isLive);

       return $this->jsonResponse(__($this->foundMultipleMessage, ['attr' => $this->consumersAttribute]), __($this->successCode), 200, $consumers);
    }

    public function store(Request $request){
        $rules = [
            $this->businessId =>$this->isRequiredInteger,
            $this->email =>$this->isRequiredEmail,
            $this->firstName => $this->isNullableString,
            $this->lastName => $this->isNullableString,
            $this->phoneNumber=> $this->isNullableString,
            $this->isLive => $this->isRequiredBoolean,
        ];

        $validator =  Validator::make($request->all(), $rules);

        if($validator->fails()){
            return $this->jsonValidationError($validator);
        }

        $consumer = Consumer::firstOrCreate(['email' =>  request('email')]);

        $table = $request->is_live ? 'business_consumers' : 'test_business_consumers';

        if(!$this->findConsumer($request->business_id, $consumer->id, $request->is_live)){
            $code = str_shuffle(sprintf('%05d', mt_rand(1,99999)));

            DB::insert('insert into '.$table.' (business_id, consumer_id, first_name, last_name, code, phone_number, created_at, updated_at) values (?,?,?,?,?,?,?,?)', [$request->business_id, $consumer->id, $request->first_name, $request->last_name, $code, $request->phone_number,Carbon::now(),Carbon::now()]);
        }else{
          DB::table($table)->where([
            $this->businessId => $request->business_id,
            $this->consumerId => $consumer->id,
          ])->update([
              $this->firstName => $request->first_name ?  $request->first_name : $consumer->first_name,
              $this->lastName => $request->last_name ?  $request->last_name : $consumer->last_name,
              $this->phoneNumber => $request->phone_number ?  $request->phone_number : $consumer->phone_number,
              $this->updatedAt => Carbon::now(),
          ]);
        }

        $data = $this->fetchConsumer($request->business_id, $consumer->id, $request->is_live);

        return $this->jsonResponse(__($this->addedMessage, ['attr' => $this->consumerAttribute]), __($this->successCode), 201, $data);
    }

    public function update(Request $request, $consumerId, $businessId){
        $rules = [
            $this->firstName => $this->isNullableString,
            $this->lastName => $this->isNullableString,
            $this->phoneNumber=> $this->isNullableString,
            $this->isLive => $this->isRequiredBoolean,
            $this->isBlacklisted => $this->isBoolean,
        ];

        $validator =  Validator::make($request->all(), $rules);

        if($validator->fails()){
            return $this->jsonValidationError($validator);
        }

        $consumer = $this->findConsumer($businessId, $consumerId, $request->is_live);

        if(!$consumer){
            return $this->jsonResponse(__($this->notFoundMessage, ['attr' => $this->consumerAttribute]), __($this->notFoundErrorCode), 404, [], __($this->notFoundError));
        }

        $table = $request->is_live ? 'business_consumers' : 'test_business_consumers';

        DB::table($table)->where([
          $this->businessId => $businessId,
          $this->consumerId => $consumerId,
        ])->update([
            $this->firstName => $request->first_name ?  $request->first_name : $consumer->first_name,
            $this->lastName => $request->last_name ?  $request->last_name : $consumer->last_name,
            $this->phoneNumber => $request->phone_number ?  $request->phone_number : $consumer->phone_number,
            $this->isBlacklisted => $request->is_blacklisted ?  true : false,
            $this->updatedAt => Carbon::now(),
        ]);

        $data = $this->fetchConsumer($businessId, $consumerId, $request->is_live);

        return $this->jsonResponse(__($this->updatedMessage, ['attr' => $this->consumerAttribute]), __($this->successCode), 200, $data);
    }
}
