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





	// Main method to redeem a prefs
	function prefs() {
		echo "<H4>prefs/uses</H4>" ;
		$hquery = "SHOW COLUMNS FROM prefs" ;
		$headings = $this->db->query($hquery) or die(mysql_error());

		/* Create Table */
		echo "<table border=1>" ;
		
		/* fetch column names */
		
		while ($row = $headings->fetch_row()) {
			printf ("<th>%s</th>", $row[0]);
		}
		
		
		$query = "SELECT id,device_id,shared_code,pref_id,name,age,toy,status,CONVERT_TZ( update_time, '+00:00', '+05:00') FROM prefs order by update_time DESC LIMIT 10 ;" ;
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

	// Main method to redeem count of pref updates per day
	function pref_updates() {
		echo "<H4>Pref updates count</H4>" ;
		/* Create Table */
		echo "<table border=1>" ;
		
		/* fetch column names */
		echo "<th>Date</th><th>Count</th>" ;
		
		
		$query = "select DATE(update_time), count(*) from prefs group by day(update_time), month(update_time) desc ;" ;
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

	// Main method to redeem a uses
	function tracking() {
		echo "<H4>tracking/uses</H4>" ;
		$hquery = "SHOW COLUMNS FROM tracking" ;
		$headings = $this->db->query($hquery) or die(mysql_error());

		/* Create Table */
		echo "<table border=1>" ;
		
		/* fetch column names */
		
		while ($trow = $headings->fetch_row()) {
			printf ("<th>%s</th>", $trow[0]);
		}
		
		
		$query = "SELECT id, device_id, uses, CONVERT_TZ( update_time, '+00:00', '+05:00') FROM tracking" ;
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
		echo "<H4>phone</H4>" ;
		$hquery = "SHOW COLUMNS FROM phone" ;
		$headings = $this->db->query($hquery) or die(mysql_error());

		/* Create Table */
		echo "<table border=1>" ;
		
		/* fetch column names */
		
		while ($trow = $headings->fetch_row()) {
			printf ("<th>%s</th>", $trow[0]);
		}
		
		
		$query = "SELECT id, device_id, make, model, country, sdk, CONVERT_TZ( update_time, '+00:00', '+05:00') FROM phone" ;
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
		echo "<H4>top10toys</H4>" ;
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
		echo "<H4>top10toys_by_year($year)</H4>" ;
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
		echo "<H4>good_and_bad_percentage</H4>" ;
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
$api->pref_updates();

?>
