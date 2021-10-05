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
    private $business_id =  'business_id';
    private $consumer_id =  'consumer_id';
    private $first_name =  'first_name';
    private $last_name =  'last_name';
    private $phone_number =  'phone_number';
    private $code =  'code';
    private $is_live =  'is_live';
    private $keyword = 'keyword';
    private $is_blacklisted =  'is_blacklisted';
    private $updated_at = 'updated_at';
    private $consumerEntity;


    public function fetch(Request $request){

        $consumer = $this->fetchConsumers($request->is_live, $request->business_id );
        
        return $this->jsonResponse(__($this->foundMultipleMessage, ['attr' => $this->consumersAttribute]), __($this->successCode), 200, $consumer);
    
    }

    public function getConsumer(Request $request, $id){

        $consumer = $this->fetchConsumer( $request->business_id, $id, $request->is_live);
        
        return $this->jsonResponse(__($this->foundMultipleMessage, ['attr' => $this->consumersAttribute]), __($this->successCode), 200, $consumer);
    }

    public function search(Request $request){

        $rules = [
            $this->keyword =>$this->isRequiredString,
        ];

        $validator =  Validator::make($request->all(), $rules);

        if($validator->fails()){
        return $this->jsonValidationError($validator);
        }

        $keyword = $request->input('keyword');

        $table = $request->is_live ? 'business_consumers' : 'test_business_consumers';
        
            $result = DB::table($table)->where($this->first_name, "LIKE","%$keyword%")
                ->where($this->business_id, '=', $request->input('business_id'))
                 ->orWhere($this->last_name, "LIKE", "%$keyword%")
                 ->where($this->business_id, '=', $request->input('business_id'))
                ->orWhere($this->consumer_id, "LIKE", "%$keyword%")
                ->where($this->business_id, '=', $request->input('business_id'))
                ->orWhere($this->phone_number, "LIKE", "%$keyword%")
                ->where($this->business_id, '=', $request->input('business_id'))
                ->orWhere($this->code, "LIKE", "%$keyword%")
                ->where($this->business_id, '=', $request->input('business_id'))
                ->orWhereHas('consumer', function ($query) use ($keyword) {
                    $query->where($this->email, 'like', '%'.$keyword.'%');
                })->get();
    
        return $this->jsonResponse(__($this->foundMultipleMessage, ['attr' => $this->consumersAttribute]), __($this->successCode), 200, $result);
      
    }

    public function store(Request $request){
        $rules = [
            $this->business_id =>$this->isRequiredInteger,
            $this->email =>$this->isRequiredEmail,
            $this->last_name => $this->isNullableString,
            $this->first_name => $this->isNullableString,
            $this->phone_number=> $this->isNullableString,
            $this->is_live => $this->isRequiredBoolean,
        ];

        $validator =  Validator::make($request->all(), $rules);

        if($validator->fails()){
            return $this->jsonValidationError($validator);
        }

        $consumer = Consumer::firstOrCreate(['email' =>  request('email')]);

        if(!$this->findConsumer($request->business_id, $consumer->id, $request->is_live)){
            $code = str_shuffle(sprintf('%05d', mt_rand(1,99999)));

            $table = $request->is_live ? 'business_consumers' : 'test_business_consumers';

            DB::insert('insert into '.$table.' (business_id, consumer_id, first_name, last_name, code, phone_number, created_at, updated_at) values (?,?,?,?,?,?,?,?)', [$request->business_id, $consumer->id, $request->first_name, $request->last_name, $code, $request->phone_number,Carbon::now(),Carbon::now() ]);
        }

        $data = $this->fetchConsumer($request->business_id, $consumer->id, $request->is_live);

        return $this->jsonResponse(__($this->addedMessage, ['attr' => $this->consumerAttribute]), __($this->successCode), 200, $data);
    }

    public function update(Request $request, $consumerId, $businessId){
        $rules = [
            $this->last_name => $this->isNullableString,
            $this->first_name => $this->isNullableString,
            $this->phone_number=> $this->isNullableString,
            $this->is_live => $this->isRequiredBoolean,
            $this->is_blacklisted => $this->isBoolean,
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

        DB::table($table)->where([ [$this->business_id,'=',$businessId],[$this->consumer_id,'=',$consumerId]])->update([
            $this->last_name => $request->last_name ?  $request->last_name : $consumer->last_name,
            $this->first_name => $request->first_name ?  $request->first_name : $consumer->first_name,
            $this->phone_number => $request->phone_number ?  $request->phone_number : $consumer->phone_number,
            $this->is_blacklisted => $request->is_blacklisted ?  true : false,
            $this->updated_at => Carbon::now(),
        ]);

        $data = $this->fetchConsumer($businessId, $consumerId, $request->is_live);

        return $this->jsonResponse(__($this->updatedMessage, ['attr' => $this->consumerAttribute]), __($this->successCode), 200, $data);

    }


}
