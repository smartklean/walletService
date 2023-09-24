<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function() {
    if(config('app.env') != "production"){
      return response()->json([
        'status' => true,
        'data' => [
          'key' => Illuminate\Support\Str::random(32),
        ],
        'message' => 'Welcome to CashEnvoy!'
      ], 200);
    }

    return response()->json([
      'status' => true,
      'message' => 'Welcome to CashEnvoy!'
    ], 200);
});

$router->get('/health', function() {
    return response()->json([
      'status' => true,
    ], 200);
});


$router->group([
  'prefix' => 'api',
], function() use ($router) {
  /* Version 1 */
  $router->group([
    'prefix' => 'v1'
  ], function() use ($router) {
    /* Wallet*/
    $router->group([
      'prefix' => 'wallet'
    ], function() use ($router) {
      $router->get('/', 'Apis\v1\WalletController@fetch');
      $router->get('/{walletId}', 'Apis\v1\WalletController@fetchSingle');
      $router->get('/user/{userId}', 'Apis\v1\WalletController@fetchUserWallet');
      $router->put('/{WalletId}', 'Apis\v1\WalletController@update');
      $router->post('/', 'Apis\v1\WalletController@store');
       /* Update  Wallet balance */
       $router->post('/balance', 'Apis\v1\WalletController@updateBalance');
    });
  });
  /* Version 1 */
});
