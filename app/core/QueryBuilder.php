<?php
namespace App\Core;
use App\Core\Database;

class QueryBuilder{

    public static $db;
    private $query_parts=[
        "where"=>[],
        "order"=>[],
        "limit"=>null,
        "join"=>[],
        "fields"=>[],
        "table"=>null
    ];
    
    public function __construct(){
        if(!self::$db){
            self::$db=new Database();
        }
        return self::$db;
    }

    public function quote($string){

		$quote=strtolower($string);
		return $quote="'". $quote ."'";
	}

	public function strip($string) {
		
		$string=trim($string, '`');
		return $string;	
	}

    //SELECT ... FROM .... ...ORDER BY....WHERE  ...LIMIT... JOIN
    private static function _field($f){

        return "`". str_replace('.','`.`',$f)."`";
    }
    
    private static function _fieldR($f){

        return str_replace('`','',$f);
    }

    public function select($fields=[]){
        
        if(empty($fields)){
           return $this->query_parts["fields"]="*";
           
        } else{
            return $this->query_parts["fields"]=array_map(function ($f){return self::_field($f);},$fields);   
        }
    }

    public function from($table){
        
        $this->query_parts["table"]=$table;
        
        return $this; 

   }

    private function _order($field,$order){

        return [self::_field($field),$order];
    }
    public function order($field,$order){
        
       $this->query_parts["order"][]=$this->_order($field,$order);
       return $this;  
    }
    
    private function _where($type,$field,$sign,$value){

        if($value===null){
            $value=$sign;
            $sign= "=";
        }
        
        if($value[0]!="?" && $value[0]!=":" && !is_integer($value)){
            $field=$this>strip($field);
            $value=$this->quote($value);
        }
       return [$type,self::_field($field),$sign,$value];
    }

    public function where($field,$sign, $value=null){

         $this->query_parts["where"][]=$this->_where("",$field,$sign,$value);
         
         return $this; 
        
    }

    public function orWhere($field,$sign, $value=null){

         $this->_where($type="OR",$field,$sign,$value); 
         return $this; 
        
    }

    public function andWhere($field,$sign, $value=null){
        
         $this->_where($type="AND",$field,$sign,$value); 
         return $this; 
         
    }

    public function limit(int $limit){

         $this->query_parts["limit"]=$limit;
        
         return $this; 
        
    }
    
    private function _join($table,$field_far,$field="id",$cur_table=null,$type="INNER JOIN"){

        $cur_table= $cur_table === NULL ? $this->query_parts["table"] :$cur_table;
        $field= "{$cur_table}{$field}";
        $on="{$field}{$field_far}";
        $this->query_parts["join"][]=[$type,$table,$on];
    }

    public function join($table,$field_far,$field="id",$cur_table=NULL){

         $this->_join($table,$field_far,$field,$cur_table);
         return $this; 
        
    }

    public function insert($table, $data=[]){

        ksort($data);
        $fieldName= implode(', ', array_keys($data));
        $fieldValue= ':' . implode(', :', array_keys($data));
        $query= "INSERT INTO ". $table . "($fieldName) VALUES ($fieldValue)";
    
        
        self::$db->prepare($query);

        foreach($data as $key=>$value){
            self::$db->bind(":$key" , $value);
        }
        self::$db->execute();
    }
    public function update($table, $data=[], $where=''){

        ksort($data);
        $fieldDetail= NULL;
        foreach ($data as $key => $value){
            $fieldDetail .="$key = :$key,";
        }
        $fieldDetail = rtrim($fieldDetail,',');

        $query= "UPDATE $table SET $fieldDetail ".($where ? 'WHERE ' .$where : '');

        
        self::$db->prepare($query);

        foreach($data as $key=>$value){
            self::$db->bind(":$key" , $value);
        }
        self::$db->execute();
    }

    public function findById($table,$id){

        $query="SELECT * FROM {$table} WHERE id = :id";
        return $query;
    }

    public function buildSelect(){

        $fields=empty($this->query_parts["fields"]) ? "*":implode(", ",$this->query_parts["fields"]);
        
        
        $q="SELECT {$fields} FROM {$this->query_parts["table"]} ";
    
        if(!empty($this->query_parts["where"])){
            $q .=" WHERE ";
            foreach($this->query_parts["where"] as $where){
                if(count($where)>1){
                    $q.= "({$where[1]} {$where[2]} {$where[3]})";
                }
            }
        }
        
        if(!empty($this->query_parts["order"])){
            $this->query_parts["order"][0][0]=$this->strip($this->query_parts["order"][0][0]);
            $q .=" ORDER BY ";
                foreach($this->query_parts["order"] as $order){
                    if(count($order)>0){
                        $q.= "({$order[0]}) {$order[1]}";
                    }
                }
        }

        if(!empty($this->query_parts["join"])){
            $fields=$this->_fieldR($fields);
            $q="SELECT {$fields} FROM {$this->query_parts["table"]} ";
            foreach($this->query_parts["join"] as $join){
                if(count($join)>0){
                    $q.= "{$join[0]} {$join[1]} ON {$join[2]}";
                }
            }
        }
        
        if(!empty($this->query_parts["limit"])){
            $q .= " LIMIT {$this->query_parts["limit"]}";
        
        }
        //print_r($q);
        return $q;
    }

    



}