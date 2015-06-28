<?php
require_once 'config.php';
require_once 'Connector.php';

// Class For select query statment
class Selection  
{

	public $input; 		/// array containing fieldnames 
	public $where;		// Array containing where clause parameter
	public $table;		//Table name
	public $order;		// If an order by fieldname is available
	private $db;		// PDO Database connection parameter


	public function __construct($input, $table, $where = null, $order = null)
	{
		$this->input = $input;
		$this->where = $where;
		$this->table = $table;
		$this->order = $order; 
		$this->db = Connector::doConnect();
	}
	
	// array for forming table columns names
	public function fields()
	{
		if(is_array($this->input))
		 {
			 $para = '';
			 foreach($this->input as $key => $value)
			 {  
				$para .=  $value . ","; 						
			 }  
			return  $param = substr($para, 0,strlen($para)-1);              
		 }
		 else
		 {
			$param = $input;  
			return $param;   
		 } 

		return false;
	}
	
	// array for the where clause parameter
	public function where()
	{

		if(is_array($this->where))
		{
			//$value = array_values($where);
			$condition = '';
			foreach($this->where as  $key => $value)
			{                       
				$value = is_numeric($value) ? (int)$value : (string) $value; 
					 
				$condition = $key . " = ?";           
				//$prop = $value;                     
							
			}  
					 
		}
		return $condition;    
	}
	// Array that supplies the parameter values for the prepared statments
	public function To()
	{

		 if(is_array($this->where))
			 {
				  //$value = array_values($where);
				  $condition = '';
				 foreach($this->where as  $key => $value)
				 {      
					  
					  $value = is_numeric($value) ? (int)$value : (string) $value; 
					 
					  $condition = $key . " = ?";                     
					  $prop = $value;                     
							
				 }  
					 
			 }
		return $prop;    
	}



	// Executing function depending on the input parameters supplied
	public function selectAll()
	{
		 // $param = $input; 
		  $param = $this->fields();
		  $condition = $this->where();
		  $value = $this->To();
		 
		 if($this->where == null && $this->order == null)
		 {      
			  $query = 'SELECT '. {$param} . '  FROM  ' . {$this->table} .'  ';
			  
			  return $this->db->query($query);      
		 }
		 else if($this->where != null && $this->order == null)
		 { 
			  $query = 'SELECT '. $param . '  FROM  ' . $this->table . '  WHERE  ' . $condition . '   ';
			  $entry = $this->db->prepare($query); 
			  $entry->execute(array($value));
			  return $entry->fetchAll();  			 
		 }		
		 else if($this->where != null && $this->order != null)		 
		 {         

			  $query = 'SELECT  ' . {$param} . '  FROM  ' . {$this->table} . '  WHERE  ' . {$condition} . '  ORDER BY  ' . $this->order . '   ';
			  $entry =  $this->db->query($query);  
		      $entry->execute(array($value));
		      return $entry->fetchAll();    
		 } 
		 else if($this->input == null)
		 {
			$query = 'SELECT * fROM  ' .$this->table . ' ' ;
			return $this-db->query($query); 
		 }  
	  
	}
    
}


// Class that performs the Update SQL QUery
class Updater
 {
   public $table;    // Table name
   public $setter;  // Set field parameters
   public $parameter;  // Parameters field
   private $db;  // PDO Object holder
   
   
   public function __construct($table,$setter,$parameter)
   {
   
        $this->table = $table;
        $this->setter = $setter;
        $this->parameter = $parameter;
		$this->db = Connector::doConnect();
   
   }
   // Generates the SET clause parameters
   public function setSetter()
   {
        
      return $this->diff($this->setter);   
   }
   
   
   // Generates the where Clause Parameters
    public function where()
   {
         
        return $this->diff($this->parameter);    
   }
   
   // Generates placeholders from the inputs
    public function diff($param)
          
     {    
          $str = '';
          $arr = array();
          $len = '' ;
          $key = '';
          $values = '';
          if(is_array($param))
         {
            $len = sizeof($param);
            $key = array_keys($param);
            $values = array_values($param); 
            for($i= 0; $i < $len;  $i++)
            {
               
               $arr[$key[$i]] = " :" . $key[$i]; 		
            	
            } 
            
            foreach($arr as $key => $value)
            {      
                 $str .= "$key =". $value .",";
                
            }
            $str = substr($str, 0,strlen($str)-1); 
         }       
     
        return $str;    
     
     }
    
    // Invokes the Updater SQL query
	public function updater()
    {
       
      $query = 'UPDATE ' . $this->table . '  SET  ' . $this->setSetter() . '  WHERE  ' . $this->where() . '   ';
      echo $query;
      
      $result = $$this->db->prepare($query);
      
      return $result->execute($this->setter);
      
    
    }   
}

// Delete Query Class
class Delete
 {
   public $table;	// Table name
   public $setter;	// Set field parameters
   public $parameter;  //Parameters
   private $db;			// PDO Database Object
   
   // Constructor
   public function __construct($table,$parameter)
   {
   
        $this->table = $table;
        $this->parameter = $parameter;
		$this->db = Connector::doConnect();
   
   }   
   
   // Sets the where Clause
   public function where()
   {
         
         return $this->diff($this->parameter);    
   }
   
   // Builds Placeholders
   public function diff($param)          
   {    
          $str = '';
          $arr = array();
          $len = '' ;
          $key = '';
          $values = '';
          if(is_array($param))
          {
            $len = sizeof($param);
            $key = array_keys($param);
            $values = array_values($param); 
            for($i= 0; $i < $len;  $i++)
            {
               
               $arr[$key[$i]] = " :" . $key[$i]; 		
            	
            } 
            
            foreach($arr as $key => $value)
            {      
                 $str .= "$key =". $value .",";
                
            }
            $str = substr($str, 0,strlen($str)-1); 
         }       
     
        return $str;    
     
     }
    
	// Executes Delete SQL Query
    public function deleter()
    {
      
      $query = 'DELETE FROM ' . $this->table . '   WHERE  ' . $this->where() . '   ';
      echo $query;
      
      $result = $this->db->prepare($query);
      
      return $result->execute($this->parameter);
      
    
    }   
}


class Insert
{

  public $table;  //Table name
  public $values;	//Value
  private $db;			// PDO Database Object
   
   //Constructor
  public function __construct($table, $values)
  {
      $this->table = $table;
      $this->values =$values;  
	  $this->db = Connector::doConnect();
  
  }
  
  // Generates Placeholders
  public function placeholder()
  {
      $keys = array_keys($this->values);
      $placeholder = substr(str_repeat('?,',count($keys)),0,-1);
      return $placeholder;    
  }
  
  // Generates fieldname strings
  public function fields()
  {
      $keys = array_keys($this->values);
      $fields = ' '. implode(',',$keys) . " ";
      return $fields;        
  }
  
  // Generates values strings
  public function values()
  {
      $value = array_values($this->values);
      return $value;     
  } 
 
  //Executes SQL Query
  public function inserter()
  {
        global $db;
      $query = 'INSERT INTO ' . $this->table . '  ('  .$this->fields(). ' )  VALUES( '. $this->placeholder(). ')    ';
      echo $query;
      
      $result = $db->prepare($query);
      
      return $result->execute($this->values());
      
  }            

}



?>