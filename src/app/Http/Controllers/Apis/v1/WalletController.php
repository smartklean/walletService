<?php

namespace App\Http\Controllers\Apis\v1;

use Illuminate\Support\Facades\Validator;
use App\Models\Wallet;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Traits\HandlesJsonResponse;
use Illuminate\Support\Str;
use DB;
use Carbon\Carbon;


class WalletController extends Controller{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    use HandlesJsonResponse;

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
    private $consumerAttribute = 'wallet';
    private $consumersAttribute = 'wallets';
    private $isBoolean = 'boolean';
    private $isRequiredBoolean = 'required|boolean';
    private $isRequiredInteger = 'required|integer';
    private $isRequiredNumeric = 'required|numeric';
    private $isNullableInteger = 'nullable|integer';
    private $isNullableString = 'nullable|string|max:255';
    private $isRequiredEmail =  'required|email|max:255';
    private $isRequiredString =  'required|string|max:255';
    private $isUnique = 'unique:wallets';
    private $error = 'response.errors.request';
    

    public function fetch(Request $request){
        $wallet = Wallet::all();;
        return $this->jsonResponse(__($this->foundMultipleMessage, ['attr' => $this->consumersAttribute]), __($this->successCode), 200, $wallet);
    }

    public function fetchSingle(Request $request, $walletId){
        
        $wallet = Wallet::find($walletId);

        $response = !$wallet ? $this->jsonResponse(__($this->notFoundMessage, ['attr' => $this->consumerAttribute]), __($this->notFoundErrorCode), 404, [], __($this->notFoundError))
        : $this->jsonResponse(__($this->foundMessage, ['attr' => $this->consumerAttribute]), __($this->successCode), 200, $wallet);

        return $response;
    }

    public function fetchUserWallet(Request $request, $userId){
        $wallet = Wallet::where('user_id', $userId)->first();

        $response = !$wallet ? $this->jsonResponse(__($this->notFoundMessage, ['attr' => $this->consumerAttribute]), __($this->notFoundErrorCode), 404, [], __($this->notFoundError))
        : $this->jsonResponse(__($this->foundMessage, ['attr' => $this->consumerAttribute]), __($this->successCode), 200, $wallet);

        return $response;
    }
    
    public function store(Request $request){
        $rules = [
            'user_id' =>$this->isRequiredInteger,
            'email' =>$this->isRequiredEmail,
            'account_name' => $this->isRequiredString,
            'balance'=> $this->isNullableInteger ,
            'lien' => $this->isNullableInteger ,
        ];

        $validator =  Validator::make($request->all(), $rules);

        if($validator->fails()){
            return $this->jsonValidationError($validator);
        }

        $wallet_number = time().str_shuffle(time()).str_shuffle(sprintf('%05d', mt_rand(1,99999)));
        $input = $request->all();
        $input['wallet_number'] = $wallet_number;
        $wallet = Wallet::create($input);

        return $this->jsonResponse(__($this->addedMessage, ['attr' => $this->consumerAttribute]), __($this->successCode), 201, $wallet);
    }

    public function update(Request $request, $walletId){
        $rules = [
            'user_id' =>$this->isRequiredInteger,
            'email' =>$this->isRequiredEmail,
            'account_name' => $this->isRequiredString,
            'balance'=> $this->isNullableInteger ,
            'lien' => $this->isNullableInteger ,
        ];

        $validator =  Validator::make($request->all(), $rules);

        if($validator->fails()){
            return $this->jsonValidationError($validator);
        }

        $consumer = $this->findConsumer($businessId, $consumerId, $request->is_live);

        if(!$consumer){
            return $this->jsonResponse(__($this->notFoundMessage, ['attr' => $this->consumerAttribute]), __($this->notFoundErrorCode), 404, [], __($this->notFoundError));
        }

        $data = $this->fetchConsumer($businessId, $consumerId, $request->is_live);

        return $this->jsonResponse(__($this->updatedMessage, ['attr' => $this->consumerAttribute]), __($this->successCode), 200, $data);
    }

    public function updateBalance(Request $request){
        $rules = [
          'user_id' => $this->isRequiredInteger,
          'balance' => $this->isRequiredNumeric
        ];
        
        $validator =  Validator::make($request->all(), $rules);
    
        if($validator->fails()){
          return $this->jsonValidationError($validator);
        }
    
        $wallet = Wallet::where('user_id', $request->user_id)->first();
    
        if(!$wallet){
            $this->jsonResponse(__($this->notFoundMessage, ['attr' => $this->consumerAttribute]), __($this->notFoundErrorCode), 404, [], __($this->notFoundError));
        }

        if($request->balance < 0){
             return $this->jsonResponse(__("invalid amount detected please try again with a positive figure", ['attr' => $this->consumerAttribute]), __($this->notFoundErrorCode), 404, [], __($this->notFoundError));
        }
    
        Wallet::where([
          'user_id' => $request->user_id
        ])->update([
    
          'balance' => $request->balance,
        ]);
        
        $wallet = Wallet::where('user_id', $request->user_id)->first();
        
        return $this->jsonResponse(__($this->foundMessage, ['attr' => $this->consumerAttribute]), __($this->successCode), 200, $wallet);
    }

    
}
