<?php

use Laravel\Lumen\Testing\DatabaseTransactions;

class ConsumerTest extends TestCase
{

    use DatabaseTransactions;

    private $url = "api/v1/business" ;
    /**
     * /Consumer[POST]
     */
    // public function testShouldCreateConsumer(){

    //     $parameters = [
    //         'email' => 'craig@david.co.uk',
    //         'first_name'=>"Craig", 
    //         'last_name' => 'David',
    //         'code'=>'005',
    //         'phone_number'=>"0123456789",
    //         'business_id'=>5,
    //         'is_live' =>true,
    //         'is_blacklisted' =>false,
    //     ];

    //     $this->post($this->url."consumer/", $parameters, []);
    //     $this->seeStatusCode(201);
    //     $this->seeJsonStructure(
    //         ['data' =>
    //             [
    //                 'first_name', 
    //                 'last_name',
    //                 'code',
    //                 'phone_number',
    //                 'business_id',
    //                 'consumer_id', 
    //                 'is_live',
    //                 'is_blacklisted',
    //                 'created_at',
    //                 'updated_at',
    //             ]
    //         ]    
    //     );
        
    // }

    /**
     * /products [GET]
     */

    // public function testShouldReturnAllConsumers(){

    //     $this->get($this->url ."consumer/", []);
    //     $this->seeStatusCode(200);
    //     $this->seeJsonStructure([
    //         'data' => ['*' =>
    //             [
    //                 'business_id',
    //                 'consumer_id', 
    //                 'first_name',
    //                 'last_name',
    //                 'phone_number',
    //                 'code',
    //                 'is_live',
    //                 'is_blacklisted',
    //                 'created_at',
    //                 'updated_at',
    //             ]
    //         ],
            
    //     ]);
        
    // }

    
    
    /**
     * /products/id [PUT]
     */
    // public function testShouldUpdateConsumer(){

    //     $parameters = [
    //         'first_name'=>"Muhammed", 
    //         'last_name' => 'Salah',
    //         'code'=>'0027',
    //         'phone_number'=>"01234890866",
    //         'business_id'=>5,
    //         'is_live' =>true,
    //         'is_blacklisted' =>false,
    //     ];

    //     $this->put($this->url."consumer/1", $parameters, []);
    //     $this->seeStatusCode(200);
    //     $this->seeJsonStructure(
    //         ['data' =>
    //             [
    //                 'business_id',
    //                 'consumer_id', 
    //                 'first_name',
    //                 'last_name',
    //                 'phone_number',
    //                 'code',
    //                 'is_live',
    //                 'is_blacklisted',
    //                 'created_at',
    //                 'updated_at',
    //             ]
    //         ]    
    //     );
    // }

    /**
     * /consumer/id [DELETE]
     */
    // public function testShouldDeleteAccount(){
        
    //     $this->delete($this->url."account/5/delete", [], []);
    //     $this->seeStatusCode(410);
    //     $this->seeJsonStructure([
    //             'code',
    //             'message'
    //     ]);
    // }

}