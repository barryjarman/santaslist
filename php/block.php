<?php

include 'library.php' ;

class Result {

	private $db;

	// Constructor - open DB connection
	function __construct() {
		$this->db = new mysqli('127.2.185.130', 'adminF2Jbm2B', 'nCi6Du5zhr4B' , 'santaslist');
		/* check connection */
		if (mysqli_connect_errno()) {
			printf("Connect failed: %s\n", mysqli_connect_error());
			exit();
		}
		$this->db->autocommit(FALSE);
	}

	// Destructor - close DB connection
	function __destruct() {
		$this->db->close();
	}

	// Main method to redeem count of pref updates per day
	function list_toys() {
		echo "<H4>Pref updates count</H4>" ;
		/* Create Table */
		echo "<table border=1>" ;
		
		/* fetch column names */
		echo "<th>Name</th><th>Count</th>" ;
		
		
		$query = "select toy from prefs group by toy ;" ;
		$result = $this->db->query($query) or die(mysql_error());
		/* fetch object array */
		while ($row = $result->fetch_row()) {
			$separate = strtok($row[0]," ") ;
			while ($separate !== false) {
				echo "<tr>" ;
#				printf ("<td>%s</td><td><a href=\"block.php?word=%s\">Block</a></td>", $row[0],$row[0]);
				printf ("<td>$separate</td><td><a href=\"block.php?word=$separate\">Block</a></td>");
				$separate = strtok(" ") ;
				echo "</tr>" ;
			}
		}
		echo "</table>" ;
		
		$result->close() ;
	}

	// Method to add blocked word to table
	function word_block($word_to_block) {
                    echo "<H4>Blocked Word: $word_to_block</H4>" ;
                    $stmt = $this->db->prepare("INSERT INTO banned_words (word) VALUES (?)");
                    $stmt->bind_param("s", $word_to_block);
                    $stmt->execute();
                    $stmt->close();
	}

}

$api = new Result;
if (isset($_GET["word"])) {
	$word = $_GET["word"] ;
	$api->word_block($word);
} else {
	$api->list_toys();
}

?>


