<?php

include 'library.php' ;

class RedeemAPI {


	private $db;

	public $jsonResponse = array() ;
	public $array_appender = array() ;


	// Constructor - open DB connection
	function __construct() {
                $this->db = new mysqli('127.2.185.130', 'adminF2Jbm2B', 'nCi6Du5zhr4B' , 'santaslist');
		$this->db->autocommit(FALSE);
	}

	// Destructor - close DB connection
	function __destruct() {
		$this->db->close();
	}

	// Main method to redeem a code
	function redeem() {
#	$shared="N6L8E29F" ;
#	$shared="" ;

		// Check for required parameters
		if ((isset($_POST["shared_code"])) || ( $shared != "" ) ) {
			$shared_code = $_POST["shared_code"] ;
#			$shared_code = $shared ;
			// Following SQL ensures that only unique child names are returned (last updated)
			$query = "select name, toy, age, status from ( select * from prefs order by update_time DESC) tmp WHERE shared_code = '${shared_code}' GROUP BY name ORDER BY name ;" ;
			$result = $this->db->query($query) or die(mysql_error());
			$shared_result = array() ;

			/* fetch object array */
			while ($row = $result->fetch_row()) {
				$jsonRow = array(
						"name" => $row[0],
						"toy" => $row[1],
						"age" => number_format($row[2]),
						"status" => $row[3],
				);
				array_push($shared_result, $jsonRow) ;
			}
			$this->array_appender =  array("name" => "shared_result" , "description" => "Share Code Results for ${shared_code}", "type" => "shared_code", "data" => $shared_result ) ;
			echo json_encode($this->array_appender, JSON_PRETTY_PRINT) ;
		}
	}

}

$api = new RedeemAPI;
$api->redeem();

?>
