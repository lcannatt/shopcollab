<?php
require_once 'dbo.php';

//Database class
//Function naming generally follows [Prefix][Activity] format
//Prefixes:
//set: update existing data rows
//del: removes data from a table
//put: adds new rows to a table

class Database {
	//the singleton instance
	private static $instance = null;

	//the database instance
	private $db;
	private $authStatus=0;
	//Constructor: Very simple, not a lot of user session data to keep track of
	private function __construct(){
		$this->db = DB_Wrapper::getDB();
	}
	public static function getDB() {
		//generic singleton pattern, if we don't have an instance,
		//make one
		if(self::$instance == null) {
			self::$instance = new Database();
		}
		//return singleton instance
		return self::$instance;
	}
	public function getAuthStatus(){
		return $this->authStatus;
	}
	//AUTHORIZATION OF DEVICES
	public function authenticateDevice($token){
		$this->delPruneDevices();
		$sql="SELECT * FROM devices WHERE SESS_ID=?";
		$results=$this->db->preparedQuerySingle($sql,'s',array($token));
		if($results===false){
			$this->authStatus=0;
			return false;
		}else{
			$this->setUpdateDevice($token);
			$this->authStatus=1;
			return true;
		}
	}
	private function delPruneDevices(){
		//clears out old devices that have expired.
		//no return value, should run before authenticating device
		$sql="DELETE FROM devices WHERE TIMESTAMPDIFF(MINUTE,LAST_ACTIVE,now())>1440;";
		$results=$this->db->preparedQuerySingle($sql);
	}
	private function setUpdateDevice($token){
		//refreshes last activity time for current token
		$sql="UPDATE devices SET LAST_ACTIVE = now() WHERE SESS_ID=?";
		$results=$this->db->preparedQuerySingle($sql,'s',array($token));
	}
	public function putAddDevice($token){
		//adds new authenticated device to table
		$sql="INSERT INTO devices VALUES (?,now());";
		$result=$this->db->preparedQuerySingle($sql,'s', array($token));
		return $this->authenticateDevice($token);
	}
	public function delLogoutDevice($token){
		//removes authenticated device from table
		$sql="DELETE from devices WHERE SESS_ID=?";
		$result=$this->db->preparedQuerySingle($sql,'s',array($token));
		return $this->authenticateDevice($token);
	}
	private function getVoteTotals($sessId){
		#Returns array of arrays containing all items on shopping list with associated metadata
		#Helper function : Output is array of [Category, vote count,item Name,earliest Vote date, id, one or zero if SESSID voted for this item]
		$sql="SELECT MAX(CATEGORY) as CATEGORY, Count(votes_active.ITEM_ID) as VOTES, MAX(NAME) as NAME, Min(VOTE_DATE)as VOTE_DATE, MAX(votes_active.ITEM_ID) as ITEM_ID, IF(SESSID='::1',1,0) as VOTED FROM votes_active left outer join item_master on votes_active.ITEM_ID=item_master.ITEM_ID GROUP BY votes_active.ITEM_ID ORDER BY CATEGORY ASC, VOTES DESC, NAME ASC; ";
		$result = $this->db->preparedQuery($sql,'s',array($sessId));
		return $result;
	}
	public function getUserVotes($sessId){
		$sql="SELECT item_master.ITEM_ID FROM votes_active LEFT JOIN item_master ON votes_active.ITEM_ID = item_master.ITEM_ID WHERE SESSID=?";
		$dbResult=$this->db->preparedQuery($sql,'s',array($sessId));
		$result=[];
		foreach($dbResult as $row){
			$result[]=$row['ITEM_ID'];
		}
		return $result;
	}
	public function getVoteInfo($sessId){
		#wrapper function for vote box retrieval and formatting
		#returns formatted shoppinglist array for rendering
		$db=Database::getDB();
		$tableName ='votes_active';
		$voteData = $this->getVoteTotals($sessId);
		$result=[];
		foreach($voteData as $row){
			$result[$row['CATEGORY']][]=[
				'VOTES'=>$row['VOTES'],
				'NAME' => $row['NAME'],
				'VOTE_DATE' => $row['VOTE_DATE'],
				'VOTED' => $row['VOTED'],
				'ITEM_ID' => $row['ITEM_ID']
			];
		}
		return $result;
	}
	public function getItemMasterList(){
		$sql="SELECT ITEM_ID,NAME FROM item_master;";
		return $this->db->preparedQuery($sql,'',array());
	}
	public function getShoppingListEmpty(){
		$sql="SELECT count(*) as votes FROM votes_active;";
		return $this->db->preparedQuerySingle($sql,'',array())['votes']==0;
	}
	public function setGoShopping(){
		//moves all the votes from the active list to the inactive list.
		$sql="INSERT INTO votes_inactive (ITEM_ID,SESSID,VOTE_DATE,PURCHASE_DATE) SELECT ITEM_ID,SESSID,VOTE_DATE,now() FROM votes_active";
		$result=$this->db->preparedQuerySingle($sql,'',array());
		$sql="DELETE FROM votes_active WHERE ITEM_ID IS NOT NULL;";
		return $this->db->preparedQuerySingle($sql,'',array());
	}
	public function setUndoShopping(){
		$sql="INSERT INTO votes_active (ITEM_ID,SESSID,VOTE_DATE) SELECT ITEM_ID,SESSID,VOTE_DATE FROM votes_inactive WHERE PURCHASE_DATE = (SELECT MAX(PURCHASE_DATE) FROM votes_inactive);";
		$result=$this->db->preparedQuery($sql,'',array());
		$sql="DELETE FROM votes_inactive where ITEM_ID is not null;";
		return $this->db->preparedQuerySingle($sql,'',array());
	}
	public function getPrintInfo($votes){
		$bindClause=implode(',',array_fill(0,count($votes),'?'));
		$bindString=str_repeat('i',count($votes));
		$sql=("SELECT NAME, CATEGORY FROM item_master WHERE ITEM_ID IN($bindClause) ORDER BY CATEGORY ASC, NAME ASC");
		$result=$this->db->preparedQuery($sql,$bindString,$votes);
		return $result;
	}
	public function delVotes($votes,$sessId){
		$bindClause=implode(',',array_fill(0,count($votes),'?'));
		$bindString=str_repeat('i',count($votes)).'s';
		$votes[]=$sessId;
		$sql="DELETE FROM votes_active WHERE (ITEM_ID IN($bindClause) AND SESSID=?);";
		$result=$this->db->preparedQuerySingle($sql,$bindString,$votes);
		return $result;
	}
	public function putInsertVotes($votes,$sessId){
	
		foreach($votes as $item){
			$sql="INSERT INTO votes_active (ITEM_ID,SESSID,VOTE_DATE) VALUES (?,?,NOW());";
			$result=$this->db->preparedQuery($sql,'is',array($item,$sessId));
			if(!$result){
				echo 'fuck';
			}

		}
	}
	public function getItemId($itemName){
		$sql="SELECT ITEM_ID FROM item_master WHERE NAME=?";
		$result=$this->db->preparedQuerySingle($sql,'s',array(strtoupper($itemName)));
		return $result?$result['ITEM_ID']:$result;
	}
	public function getCatExists($catName){
		// returns false if there are no available items in the category, or the first item with that category if it exists.
		$sql="SELECT count(*) as TOTAL FROM item_master WHERE CATEGORY=?";
		return $this->db->preparedQuerySingle($sql,'s',array(strtoupper($catName)))['TOTAL'];
	}
	public function putItemDefinition($name,$category){
		$sql="INSERT INTO item_master (NAME,CATEGORY) VALUES (?,?)";
		$result=$this->db->preparedQuery($sql,'ss',array($name,$category));
		if(!$result){
			return false;
		}
		$sql="SELECT MAX(ITEM_ID) AS ID FROM item_master";
		$result=$this->db->preparedQuerySingle($sql,'',array());
		return $result['ID'];
	}
	public function delItemDefinitions($items){
		$bindClause=implode(',',array_fill(0,count($items),'?'));
		$bindString=str_repeat('i',count($items));
		$sql="DELETE FROM votes_active WHERE (ITEM_ID in ($bindClause));";
		$this->db->preparedQuerySingle($sql,$bindString,$items);
		$sql="DELETE FROM votes_inactive WHERE (ITEM_ID in ($bindClause));";
		$this->db->preparedQuerySingle($sql,$bindString,$items);
		$sql="DELETE FROM item_master WHERE (ITEM_ID in ($bindClause));";
		$this->db->preparedQuerySingle($sql,$bindString,$items);

	}
	public function getAllItemsCatSort(){
		$sql="SELECT CATEGORY,NAME FROM item_master ORDER BY CATEGORY";
		$result=$this->db->preparedQuery($sql,'',Array());
		$output=[];
		foreach($result as $row){
			$output[$row['CATEGORY']][]=$row['NAME'];
		}
		return $output;
	}
	public function getShoppingPreview(){
		$sql="SELECT item_master.NAME as NAME,count(VOTE_DATE) as PRIO
		from votes_active
			left join item_master on votes_active.ITEM_ID=item_master.ITEM_ID
		group by NAME
		order by count(VOTE_DATE) desc
		LIMIT 6;";
		$result = $this->db->preparedQuery($sql,'',Array());
		$output=[];
		foreach($result as $row){
			$output[$row['NAME']]=$row['PRIO'];
		}
		return $output;
	}
}