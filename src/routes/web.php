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
    /* Consumer*/
    $router->group([
      'prefix' => 'consumer'
    ], function() use ($router) {
      $router->get('/', 'Apis\v1\ConsumerController@fetch');
      $router->get('/search', 'Apis\v1\ConsumerController@search');
      $router->put('/{id}', 'Apis\v1\ConsumerController@update');
      $router->put('/{id}/blacklist', 'Apis\v1\ConsumerController@blacklistToggle');
      $router->post('/', 'Apis\v1\ConsumerController@store');
      $router->delete('/{id}', 'Apis\v1\ConsumerController@destroy');
      
       /* Type */
       $router->group([
        'prefix' => 'type'
      ], function() use ($router) {
        
      });

      


    });
  });
  /* Version 1 */
});
