<?php
namespace App\Core;
use App\Core\QueryBuilder;
use App\Core\Database;

 abstract class Model{

    public $db;
    protected static $table='';
    public $instanceQB;//kreira se novo polje u klasi Model odnosno u svim klasama koje naslede Model, tu se smesta novi objekat QB
   
    
    public static function getTable(){
        return static::$table;// return the value of table that is in class that has inherited class Model
    }
    
    public function __construct($qb){
        $this->instanceQB= $qb; // Kada se napravi novi objekat User, Pets,... onda se u novo polje koje smo kreirali $instanceQB kreira novi objekat QB => new User(new QueryBuilder())
    }

   public static function select($fields=[]){
        
        $instanceQB=new QueryBuilder();// prvi put se napravi objekat klase QueryBuilder u novo kreiranom polju User[$instanceQB]
        $instanceQB->select($fields);// poziva se funcija iz klase QueryBuilder koja vraca vrednost koju vraca sama funkcija
        
        return new static($instanceQB);// ovako se kreira novi objekat instance QueryBuilder u koju se smesta vrednost select()
      
       
   }

  
    public function where($field,$sign,$value=null){
       
        $this->instanceQB->where($field,$sign,$value);// nad objektom instance QueryBuilder se poziva funkcija u klasi QB
        
        return $this;
    }

    public function order($field,$order){
        
        $this->instanceQB->order($field,$order);
       
        return $this;// vraca se vrednost u objekat nad kojim je pozvana funkcija
    }

    public function limit($limit){
        
        $this->instanceQB->limit($limit);
       
        return $this;;
    }
    public function join($table,$field_far,$field,$cur_table){
        
        $this->instanceQB->join($table,$field_far,$field,$cur_table);
      
        return $this;
    }
    
    public function get(){

        $table=self::getTable();
        $this->instanceQB->from($table);
        $query=$this->instanceQB->buildSelect();
        
        
        $db= new Database();
        $db->prepare($query);
        return $db->resultSet();
        
    }

    public function save($data){
        
        return $this->instanceQB->insert(static::$table, $data);
    }

    public function update($data,$where){
        
        return $this->instanceQB->update(static::$table,$data,$where);
    }

    public static function find($id){

        $instanceQB=new QueryBuilder();
        $query=$instanceQB->findById(static::$table,$id);
        
        $db= new Database();
        $db->prepare($query);
        $db-> bind(':id',$id);
        return $db->single();

    }















}


