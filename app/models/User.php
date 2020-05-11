<?php

// USER class

namespace App\Models;
use App\Core\Model;

class User extends Model {

    public $id;
    public $first_name; 
    public $last_name;
    public $email;
    public $age;
    public $phone_number;
   
    protected static $table="user";


    





}
