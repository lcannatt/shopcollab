<?php
require_once 'config.php';

class DB_Wrapper {
	//Singleton instance
	private static $instance=null;
	//db connection
	private $pdocon;

	//override pdo db object constructor to initiate singleton pattern
	private function __construct() {
		$this->pdocon = new PDO("mysql:host=".DB_SERVER.";dbname=".DB_NAME.";charset=utf8", DB_USER, DB_PASS);
		if(!$this->pdocon){
			echo 'badpdo';
		}
	}

	//singleton constructor
	public static function getDB() {
		//Check if singleton already initiated, make one if needed
		if(self::$instance==null){
			self::$instance = new DB_Wrapper();
		}
		//return singleton instance
		return self::$instance;
	}

	public function preparedQuerySingle($sql,$argtypes="",$arguments=array()){
		$result=$this->preparedQuery($sql,$argtypes,$arguments);
		if(!$result){
			return false;
		} else if($result===true){
			return false;
		}
		return $result[0];
	}

	public function preparedQuery($sql,$argtypes,$arguments,$rettype=MYSQLI_ASSOC){
		if($rettype == MYSQLI_ASSOC) {
			$rettype=PDO::FETCH_ASSOC;
		} else {
				$rettype=PDO::FETCH_NUM;
		}
		$stmt=$this->pdocon->prepare($sql);
		if(!$stmt){
			echo "bad statement preparation";
		}
		$bindNumber=empty($arguments)?0:sizeof($arguments);
		$arguments=array_values($arguments);//make sure there's no out of order array indices
		for($i=0; $i< $bindNumber;$i++){
			$type = $argtypes[$i]=="s"?PDO::PARAM_STR:($argtypes[$i]=="i"?PDO::PARAM_INT:PDO::PARAM_STR);
				if(!$stmt->bindValue($i+1, $arguments[$i], $type)) {
						echo 'cant bind';
				}
		}
		if(!$stmt->execute()) {
			// print_r($stmt->errorInfo());
			// print_r($arguments);
			// echo $sql;
			echo 'cant exec, time to debug';
		}
		if($stmt->columnCount()==0) {
				return true;
		}
		$vals = $stmt->fetchAll($rettype);
		return $vals;
	}
}