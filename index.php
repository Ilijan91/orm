<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require "vendor/autoload.php";

use App\Models\User;
use App\Models\Pets;
use App\Core\QueryBuilder;

$q1=new User(new QueryBuilder());

// QUERY FOR JOIN =>working

// print_r(
//     $q1::
//         select(["user.first_name","pets.name"])
        
//         ->join('pets', 'pets.user_id', '=', 'user.id')
//         ->get()
//     );


//QUERY FOR SELECT FROM WHERE ORDER BY LIMIT for two OBJECTS=> working
/*
$q1=new User(new QueryBuilder());
$q2= new Pets(new QueryBuilder());
print_r(
    $q1::
        select(["first_name"])
        
        ->where('id', '>',9)
        ->order('id','ASC')
        ->limit(3)
        ->get()
    );
    
print_r(
        $q2::
            select(["name"])
          
            ->order('id','DESC')
            ->limit(2)
            ->get()
        );
*/
// QUERY for SAVE record in database => working

// $q1->save([
//     'first_name' => 'aaaaaaaaa',
//     'last_name' => 'Dogg',
//     'age' => 55,
//     'email'=>'snoop@hotmail.com',
//     'phone_number'=>65654
//   ]);


// QUERY for UPDATE record in database => working
/*
$q1->update([
    'first_name' => 'BBBB',
    'last_name' => 'Doggy',
    'age' => 48,
    'email'=>'snoop@hotmail.com',
    'phone_number'=>2222222
],'id=12');
*/

// QUERY to find record by given ID

// print_r($q1 = User::find(12));
