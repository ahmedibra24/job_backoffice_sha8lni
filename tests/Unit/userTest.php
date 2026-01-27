<?php
//! php artisan make:test userTest --unit

use App\Models\User;

test('create user with name and email',function(){
    //? unit test components
    //? 1- Arrange -> make the data ready for use
    $user = [
        'name'=>'ahmed',
        'email'=>'ahmed@email.com'
    ];

    //? 2- act ->  function to test 
    $result = User::create($user) ;

    //? 3- Assert -> expected output of the function
    expect($result->name)->toBe($user['name']);



});











