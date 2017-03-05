<?php
if(isset($_GET)){
	$food=new food();
	$rawData=$food->getFood();
	$response=json_encode(array('food'=>$rawData));
	echo $response;
}
class Food {
	private $dbserver="mysql.dur.ac.uk";
	private $dbuser="dcs8s01";
	private $dbpw="b87yker";
	private $db="Xdcs8s01_FlavorTown";
	
	public function getFood(){
		$mysqli=new MySQLi($this->dbserver, $this->dbuser, $this->dbpw, $this->db);
		if ( $mysqli->connect_errno ) {
			return ("Connect failed: ".mysqli_connect_error());
		}
		$query="SELECT * FROM FoodItems ORDER BY Food_ID";
		$result=$mysqli->query($query);
		
		while ($row=$result->fetch_array(MYSQL_ASSOC)){
			$rows[]=$row;
		}
		
		
		$result->close;
		$mysqli->close;
		
		return $rows;
		
	}
}
?>
