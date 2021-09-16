<?php

namespace App\Http\Controllers\Apis\v1;

use Illuminate\Support\Facades\Validator;
use App\Models\Consumer;
use App\Models\BusinessConsumer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Consumer as ConsumerResource;
use App\Http\Resources\BusinessConsumer as BusinessConsumerResource;
use App\Http\Traits\HandlesConsumer;
use App\Http\Traits\HandlesBusinessConsumer;
use App\Http\Traits\HandlesJsonResponse;
use Illuminate\Support\Str;
use DB;


class ConsumerController extends Controller{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    use HandlesConsumer, HandlesBusinessConsumer, HandlesJsonResponse;

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
    private $isNullableBoolean = 'nullable|boolean';
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
    private $consumerEntity;


    public function fetch(Request $request){
        $consumer = $this->fetchConsumer($request);
        return (new ConsumerResource($consumer))
                ->additional([
                    'status' => true,
                    'code' => __($this->successCode),
                    'message' => __($this->foundMultipleMessage, ['attr' => $this->consumersAttribute]),
                ], 200);
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
            $result = BusinessConsumer::where($this->first_name, "LIKE","%$keyword%")
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
    
        return (new ConsumerResource($result))
        ->additional([
            'status' => true,
            'code' => __($this->successCode),
            'message' => __($this->foundMultipleMessage, ['attr' => $this->consumersAttribute]),
        ], 200);
       
    }

    public function store(Request $request){
        $rules = [
            $this->email =>$this->isRequiredEmail,
            $this->business_id => $this->isNullableInteger,
            $this->consumer_id => $this->isNullableInteger,
            $this->last_name => $this->isNullableString,
            $this->first_name => $this->isNullableString,
            $this->phone_number=> $this->isNullableString,
            $this->is_live => $this->isNullableBoolean,
            $this->is_blacklisted => $this->isNullableBoolean,
        ];

        $validator =  Validator::make($request->all(), $rules);

        if($validator->fails()){
        return $this->jsonValidationError($validator);
        }

        DB::transaction(function() use ($request){
            $consumer = Consumer::firstOrCreate(['email' =>  request('email')]);
            
            $businessConsumer = BusinessConsumer::create([
                $this->business_id=>$request->business_id,
                $this->consumer_id => $consumer->id,
                $this->first_name => $request->first_name,
                $this->last_name => $request->last_name,
                $this->code => substr(Str_shuffle("0123456789"), 0, 5),
                $this->phone_number => $request->phone_number,
            ]);

            $businessConsumer->email = $consumer->email;

            $this->consumerEntity = $businessConsumer;

        });

        if(!$this->consumerEntity){
        return $this->jsonResponse(__($this->notAddedMessage, ['attr' => $this->consumerAttribute]), __($this->errorCode), 400, [], __($this->error));
        }

        return (new ConsumerResource($this->consumerEntity))
            ->additional([
                'status' => true,
                'code' => __($this->successCode),
                'message' => __($this->addedMessage, ['attr' => $this->consumerAttribute]),
            ], 201);
    }

    public function update(Request $request, $id){
        $businessConsumer = $this->findBusinessConsumer($id);

        if(!$businessConsumer){
        return $this->jsonResponse(__($this->notFoundMessage, ['attr' => $this->consumerAttribute]), __($this->notFoundErrorCode), 404, [], __($this->notFoundError));
        }

        $rules = [
            $this->business_id => $this->isNullableInteger,
            $this->consumer_id => $this->isNullableInteger,
            $this->last_name => $this->isNullableString,
            $this->first_name => $this->isNullableString,
            $this->phone_number=> $this->isNullableString,
            $this->is_live => $this->isNullableBoolean,
            $this->is_blacklisted => $this->isNullableBoolean,
        ];

        $validator =  Validator::make($request->all(), $rules);

        if($validator->fails()){
        return $this->jsonValidationError($validator);
        }
        
        $businessConsumer->fill([
        $this->business_id => $request->business_id ?  $request->business_id : $businessConsumer->business_id,
        $this->consumer_id => $request->consumer_id ?  $request->consumer_id : $businessConsumer->consumer_id,
        $this->last_name => $request->last_name ?  $request->last_name : $businessConsumer->last_name,
        $this->first_name => $request->first_name ?  $request->first_name : $businessConsumer->first_name,
        $this->phone_number => $request->phone_number ?  $request->phone_number : $businessConsumer->phone_number,
        $this->is_live => $request->is_live ?  $request->is_live : $businessConsumer->is_live,
        $this->is_blacklisted => $request->is_blacklisted ?  $request->is_blacklisted : $businessConsumer->is_blacklisted,
        ])->save();
            
        return (new BusinessConsumerResource($businessConsumer))
            ->additional([
                'status' => true,
                'code' => __($this->successCode),
                'message' => __($this->updatedMessage, ['attr' => $this->consumerAttribute]),
            ], 200);
    }

    public function destroy($id){
        $businessConsumer = $this->findBusinessConsumer($id);

        if(!$businessConsumer){
        return $this->jsonResponse(__($this->notFoundMessage, ['attr' => $this->consumerAttribute]), __($this->notFoundErrorCode), 404, [], __($this->notFoundError));
        }

        $old = $businessConsumer;

        $businessConsumer->delete();

        return (new BusinessConsumerResource($old))
            ->additional([
                'status' => true,
                'code' => __($this->successCode),
                'message' => __($this->deletedMessage, ['attr' => $this->consumerAttribute]),
            ], 200);
    }

    public function blacklistToggle(Request $request, $id){

        $businessConsumer = $this->findBusinessConsumer($id);

        if(!$businessConsumer){
        return $this->jsonResponse(__($this->notFoundMessage, ['attr' => $this->consumerAttribute]), __($this->notFoundErrorCode), 404, [], __($this->notFoundError));
        }

       $this->toggleBlacklist($request, $id );
        $businessConsumer = BusinessConsumer::find($id);

        return (new BusinessConsumerResource($businessConsumer))
            ->additional([
                'status' => true,
                'code' => __($this->successCode),
                'message' => __($this->updatedMessage, ['attr' => $this->consumerAttribute]),
            ], 200);

    }

}
