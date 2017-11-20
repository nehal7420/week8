<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL);
define('DATABASE', 'nnu2');
define('USERNAME', 'nnu2');
define('PASSWORD', 'p8cptWlff');
define('CONNECTION', 'sql1.njit.edu');
  class dbConn{
    protected static $db;
    private function __construct() {
        try {
      
            self::$db = new PDO( 'mysql:host=' . CONNECTION .';dbname=' . DATABASE, USERNAME, PASSWORD );
            self::$db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
        }
        catch (PDOException $e) {
            echo "Connection Error: " . $e->getMessage();
        } 
    }
    public static function getConnection() {
        if (!self::$db) {
            
            new dbConn();
        }
        return self::$db;
    }
  }
  class collection {
    static public function create() {
      $model = new static::$modelName;
      return $model;
    }
    static public function findAll() {
        $db = dbConn::getConnection();
        $tableName = get_called_class();
        $sql = 'SELECT * FROM ' . $tableName;
        $statement = $db->prepare($sql);
        $statement->execute();
        $class = static::$modelName;
        $statement->setFetchMode(PDO::FETCH_CLASS, $class);
        $recordsSet =  $statement->fetchAll();
        return $recordsSet;
    }
    static public function findOne($id) {
        $db = dbConn::getConnection();
        $tableName = get_called_class();
        $sql = 'SELECT * FROM ' . $tableName . ' WHERE id =' . $id;
        $statement = $db->prepare($sql);
        $statement->execute();
        $class = static::$modelName;
        $statement->setFetchMode(PDO::FETCH_CLASS, $class);
        $recordsSet =  $statement->fetchAll();
        return $recordsSet;
    }
    
    static public function buildHtml($records,$obj){
    
    $html="<table border='1'><tr>";
    foreach($obj as $key => $value){
    $html.="<th>$key</th>";
    }
    $html.="</tr>";
    foreach($records as $row){
    $rowHtml="<tr>";
    foreach($obj as $key => $value){
    $rowHtml.="<td>";
    $rowHtml.=$row->$key;
    $rowHtml.="</td>";
    }
    $rowHtml.="</tr>";
    $html.=$rowHtml;
    }
    $html.="</table>";
    return $html;
    }
    
  }
  class accounts extends collection {
    protected static $modelName = 'account';
  }
  class todos extends collection {
    protected static $modelName = 'todo';
  }
  class model {
    protected $tableName;
    public function save()
    {
        if ($this->id = '') {
            $sql = $this->insert();
        } else {
            $sql = $this->update();
        }
    echo 'I just saved record: ' . $this->id;
    }
    public function insert() {
        $tableName = $this->tableName;
        $array = get_object_vars($this);
        $columns=array();$values=array();
         foreach($array as $key => $value){
          if($key!="tableName"){
           array_push($columns,$key);
           array_push($values,$value);
          }
        }
        $columnString=implode(",",$columns);
        $valueString=implode("','",$values);
        $sql="INSERT INTO $tableName (".$columnString.") VALUES ('".$valueString."')";
        $db = dbConn::getConnection();
        $statement = $db->prepare($sql);
        $statement->execute();
        echo $sql;
        echo "<br>Inserted Successfully";
        return $sql;
    }
    public function update() {
        $tableName = $this->tableName;
        $array = get_object_vars($this);
        $update=array();
         foreach($array as $key => $value){
        if($key!="tableName"&&$key!="id"){ 
          if($value!=""){
          array_push($update," $key='$value'");
          }
        }
    }
        $columnString=implode(",",$update);
        $sql="update $tableName set " .$columnString. " where id=$this->id";
        $db = dbConn::getConnection();
        $statement = $db->prepare($sql);
        $statement->execute();
        echo $sql;
        echo "<br>I just updated record" . $this->id;
        return $sql;
  }
  public function delete() {
        $tableName = $this->tableName;
        $sql="delete from $tableName where id='$this->id'";
        $db = dbConn::getConnection();
        $statement = $db->prepare($sql);
        $statement->execute();
        echo $sql;
        echo '<br>I just deleted record' . $this->id;
        return $sql;
        
    }
  }
  class account extends model {
    public $id;
    public $email;
    public $fname;
    public $lname;
    public $phone;
    public $birthday;
    public $gender;
    public $password;
    
    public function __construct(){ 
     $this->tableName = 'accounts';
    }
  }
  class todo extends model {
    public $id;
    public $owneremail;
    public $ownerid;
    public $createddate;
    public $duedate;
    public $message;
    public $isdone;
    
    public function __construct(){
     $this->tableName = 'todos';
	  }
  }
 $record = todos::findOne(1);
 $result=todos::buildHtml($record,get_object_vars(todos::create()));
 print_r($result);
 $record = accounts::findOne(4);
 $result=accounts::buildHtml($record,get_object_vars(accounts::create()));
 print_r($result);
 $record = new account();
 $record->email="nnu2@njit.edu";
 $record->fname="n";
 $record->lname="uk";
 $record->phone="20188925";
 $record->birthday="19-01-1995";
 $record->gender="male";
 $record->password="123";
 $record->insert();
 $record = todos::create();
 $records = accounts::findAll();
 $result=accounts::buildHtml($records,get_object_vars(accounts::create()));
 print_r($result);
 $record = new todo();
 $record->owneremail="nehal@njit.edu";
 $record->ownerid="1";
 $record->createddate="2017-10-23";
 $record->duedate="2018-10-1";
 $record->message="Hello";
 $record->isdone="0";
//$record->insert();
 $record = new todo();
 $record->id='2';
 $record->owneremail="nuk@njit.edu";
 $record->ownerid="1";
 $record->createddate="2018-10-20";
 $record->duedate="2018-10-25";
 $record->message="yo";
 $record->isdone="1";
 $record->update();
 $records = todos::findAll();
 $result=todos::buildHtml($records,get_object_vars(todos::create()));
 print_r($result);
 $record = new todo();
 $record->id='30';
 $record->delete();
 $records = todos::findAll();
 $result=todos::buildHtml($records,get_object_vars(todos::create()));
 print_r($result);
?>