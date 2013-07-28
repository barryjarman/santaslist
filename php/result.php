<?php

include 'library.php' ;

class Result {

	private $db;

	// Constructor - open DB connection
	function __construct() {
		$this->db = new mysqli('localhost', 'adminF2Jbm2B', 'nCi6Du5zhr4B' , 'santaslist');
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





	// Main method to redeem a prefs
	function prefs() {
		$hquery = "SHOW COLUMNS FROM prefs" ;
		$headings = $this->db->query($hquery) or die(mysql_error());

		/* Create Table */
		echo "<table border=1>" ;
		
		/* fetch column names */
		
		while ($row = $headings->fetch_row()) {
			printf ("<th>%s</th>", $row[0]);
		}
		
		
		$query = "SELECT * FROM prefs" ;
		$result = $this->db->query($query) or die(mysql_error());
		/* fetch object array */
		while ($row = $result->fetch_row()) {
			echo "<tr>" ;
			printf ("<td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td>", $row[0], $row[1], $row[2], $row[3], $row[4], $row[5], $row[6], $row[7], $row[8]);
			echo "</tr>" ;
		}
		echo "</table>" ;
		
		$result->close() ;
	}





	// Main method to redeem a uses
	function tracking() {
		$hquery = "SHOW COLUMNS FROM tracking" ;
		$headings = $this->db->query($hquery) or die(mysql_error());

		/* Create Table */
		echo "<table border=1>" ;
		
		/* fetch column names */
		
		while ($trow = $headings->fetch_row()) {
			printf ("<th>%s</th>", $trow[0]);
		}
		
		
		$query = "SELECT * FROM tracking" ;
		$result = $this->db->query($query) or die(mysql_error());
		/* fetch object array */
		while ($trow = $result->fetch_row()) {
			echo "<tr>" ;
			printf ("<td>%s</td><td>%s</td><td>%s</td><td>%s</td>", $trow[0], $trow[1], $trow[2], $trow[3]);
			echo "</tr>" ;
		}
		echo "</table>" ;
		
		$result->close() ;
	}



	# Method to display phone data
	function phone() {
		$hquery = "SHOW COLUMNS FROM phone" ;
		$headings = $this->db->query($hquery) or die(mysql_error());

		/* Create Table */
		echo "<table border=1>" ;
		
		/* fetch column names */
		
		while ($trow = $headings->fetch_row()) {
			printf ("<th>%s</th>", $trow[0]);
		}
		
		
		$query = "SELECT * FROM phone" ;
		$result = $this->db->query($query) or die(mysql_error());
		/* fetch object array */
		while ($trow = $result->fetch_row()) {
			echo "<tr>" ;
			printf ("<td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td>", $trow[0], $trow[1], $trow[2], $trow[3], $trow[4], $trow[5], $trow[6]);
			echo "</tr>" ;
		}
		echo "</table>" ;
		
		$result->close() ;
	}

	// Method to return top 10 toys
	function top10toys() {
		/* Create Table */
		echo "<table border=1>" ;
		
		/* fetch column names */
		echo "<th>Toy</th><th>Count</th>" ;
		
		
		$query = "select toy,count(*) as cnt from prefs group by toy order by cnt DESC LIMIT 10 ;" ;
		$result = $this->db->query($query) or die(mysql_error());
		/* fetch object array */
		while ($row = $result->fetch_row()) {
			echo "<tr>" ;
			printf ("<td>%s</td><td>%s</td>", $row[0], $row[1]);
			echo "</tr>" ;
		}
		echo "</table>" ;
		
		$result->close() ;
	}

	// Method to return top 10 toys in 2013
	function top10toys_by_year($year) {
		/* Create Table */
		echo "<table border=1>" ;
		
		/* fetch column names */
		echo "<th>Toy</th><th>Count</th>" ;
		
		
		$query = "select toy,count(*) as cnt from prefs where YEAR (update_time) = $year group by toy order by cnt DESC LIMIT 10 ;" ;
		$result = $this->db->query($query) or die(mysql_error());
		/* fetch object array */
		while ($row = $result->fetch_row()) {
			echo "<tr>" ;
			printf ("<td>%s</td><td>%s</td>", $row[0], $row[1]);
			echo "</tr>" ;
		}
		echo "</table>" ;
		
		$result->close() ;
	}

	// Method to return percentage of good and bad list
	function good_and_bad_percentage() {
		/* Create Table */
		echo "<table border=1>" ;
		
		/* fetch column names */
		echo "<th>List</th><th>Count</th><th>Percentage</th>" ;
		
		
		$query = "select status, count(status) as Total, count(status) / (select count(status) from prefs) * 100 as Percent from prefs group by status ;" ;
		$result = $this->db->query($query) or die(mysql_error());
		/* fetch object array */
		while ($row = $result->fetch_row()) {
			echo "<tr>" ;
			printf ("<td>%s</td><td>%s</td><td>%s</td>", $row[0], $row[1], $row[2]);
			echo "</tr>" ;
		}
		echo "</table>" ;
		
		$result->close() ;
	}

}

$api = new Result;
$api->prefs();
$api->tracking();
$api->phone();
$api->top10toys();
$api->top10toys_by_year(2013);
$api->good_and_bad_percentage();

?>
