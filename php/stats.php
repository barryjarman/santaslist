<?php

include 'library.php' ;

class Result {

	private $db;
	
	public $jsonResponse = array() ;
	public $array_appender = array() ;


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

	// Method to return percentage of good and bad list
	function f_percent_good_bad() {
		$query = "select status, count(status) as Total , count(status) / (select count(status) from prefs where status = 'g' or status = 'n') * 100 as Percent from prefs where status = 'g' or status = 'n' group by status ;" ;
                $result = $this->db->query($query) or die(mysql_error());
		unset($percent_good_bad) ;
		$percent_good_bad = array() ;

                /* fetch object array */
                while ($row = $result->fetch_row()) {
			$jsonRow = array(
                                    "list" => $row[0],
                                    "total" => $row[1],
                                    "percentage" => number_format($row[2]),
                                       );
			array_push($percent_good_bad, $jsonRow) ;
                }
		array_push($this->array_appender, array("name" => "percent_good_bad" , "description" => "List Percentages", "type" => "percent", "data" => $percent_good_bad )) ;
	}

	// Method to return top 10 toys
	function f_top10_toys() {
		$query = "select toy,count(*) as cnt from prefs where toy <> '' AND device_id not in (select device_id from banned_device_ids) AND LOWER(toy) not regexp (select group_concat(word SEPARATOR '|') from banned_words) group by toy having cnt >= 3 order by cnt DESC LIMIT 10 ;" ;
                $result = $this->db->query($query) or die(mysql_error());
		unset($top10_toys) ;
		$top10_toys = array() ;

                /* fetch object array */
                while ($row = $result->fetch_row()) {
			$jsonRow = array(
                                    "toy" => ucwords($row[0]),
                                    "total" => $row[1],
                                       );
			array_push($top10_toys, $jsonRow) ;
                }
		array_push($this->array_appender, array("name" => "top10_toys", "description" => "Top Toys", "type" => "toy", "data" => $top10_toys )) ;
	}

	// Method to return top 10 toys
	function f_top10_toys_time($time,$comment,$substring) {
		if ( $time == "" ) { $time = "1 YEAR" ; } 
		$query = "select toy,count(*) as cnt from prefs where toy <> '' AND update_time > DATE_SUB(NOW(), INTERVAL $time) AND device_id not in (select device_id from banned_device_ids) AND LOWER(toy) not regexp (select group_concat(word SEPARATOR '|') from banned_words) group by toy having cnt >= 3 order by cnt DESC LIMIT 10 ;" ;
                $result = $this->db->query($query) or die(mysql_error());
		unset($top10_toys) ;
		$top10_toys_time = array() ;

                /* fetch object array */
                while ($row = $result->fetch_row()) {
			$jsonRow = array(
                                    "toy" => ucwords($row[0]),
                                    "total" => $row[1],
                                       );
			array_push($top10_toys_time, $jsonRow) ;
                }
		array_push($this->array_appender, array("name" => "top10_toys_time_${substring}", "description" => "Top Toys $comment", "type" => "toy", "data" => $top10_toys_time )) ;
	}

	// Method to return top 10 kids who are bad
	function f_top10_kids_name_status($status) {
		if ( $status == "g" ) {
			$comment = "Good" ;
		} else {
			$comment = "Naughty" ;
		}
		$query = "
          SELECT SUBSTRING_INDEX(name,' ',1) as name,
                 count(*) AS cnt, status
          FROM (select distinct device_id, SUBSTRING_INDEX(name,' ',1) as name, status from prefs ) combine
          WHERE name <> ''
            AND status='$status'
            AND device_id NOT IN
              (SELECT device_id
               FROM banned_device_ids)
            AND LOWER(name) NOT regexp
              (SELECT group_concat(word SEPARATOR '|')
               FROM banned_words)
          GROUP BY name
          ORDER BY cnt DESC LIMIT 10;
" ;
                $result = $this->db->query($query) or die(mysql_error());
		unset($top10_kids_name_status) ;
		$top10_kids_name_status = array() ;

                /* fetch object array */
                while ($row = $result->fetch_row()) {
			$jsonRow = array(
                                    "name" => ucwords($row[0]),
                                    "total" => $row[1],
                                       );
			array_push($top10_kids_name_status, $jsonRow) ;
                }
		array_push($this->array_appender, array("name" => "top10_kids_name_${status}", "description" => "Top Names on ${comment} List", "type" => "name","data" => $top10_kids_name_status )) ;
	}

	// Method to return top 10 toys in year
	function f_top10_toys_year($year) {
		$query = "select toy,count(*) as cnt from prefs where toy <> '' AND YEAR (update_time) = '$year' AND device_id not in (select device_id from banned_device_ids) AND LOWER(toy) not regexp (select group_concat(word SEPARATOR '|') from banned_words) group by toy having cnt >= 2 order by cnt DESC LIMIT 10 ;" ;
                $result = $this->db->query($query) or die(mysql_error());
		unset($top10_toys_year) ;
		$top10_toys_year = array() ;

                /* fetch object array */
                while ($row = $result->fetch_row()) {
			$jsonRow = array(
                                    "toy" => ucwords($row[0]),
                                    "total" => $row[1],
                                       );
			array_push($top10_toys_year, $jsonRow) ;
                }
		array_push($this->array_appender, array("name" => "top10_toys_$year", "description" => "Top Toys in $year", "type" => "toy","data" => $top10_toys_year )) ;
	}

	// Method to return top 10 toys in year
	function f_top10_toys_year_country($year,$country) {
		$query = "select prefs.toy,count(*) as cnt from prefs,phone  where toy <> '' AND phone.country='$country' AND prefs.device_id = phone.device_id AND prefs.device_id not in (select device_id from banned_device_ids) AND LOWER(toy) not regexp (select group_concat(word SEPARATOR '|') from banned_words) group by toy having cnt >= 2 order by cnt desc limit 10 ;" ;
                $result = $this->db->query($query) or die(mysql_error());
		unset($top10_toys_year_country) ;
		$top10_toys_year_country = array() ;

                /* fetch object array */
                while ($row = $result->fetch_row()) {
			$jsonRow = array(
                                    "toy" => ucwords($row[0]),
                                    "total" => $row[1],
                                       );
			array_push($top10_toys_year_country, $jsonRow) ;
                }
		array_push($this->array_appender, array("name" => "top10_toys_${year}_${country}", "description" => "Top Toys for ${year} in ${country}","type" => "toy", "data" => $top10_toys_year_country )) ;
	}

	// Method to return top 10 toys wanted by kids with certain status
	function f_top10_toys_status($status) {
		if ( $status == "n" ) {
			$comment = "Naughty" ;
		} else {
			$comment = "Good" ;
		}
		$query = "select toy,count(*) as cnt from prefs where toy <> '' AND status = '$status' AND device_id not in (select device_id from banned_device_ids) AND LOWER(toy) not regexp (select group_concat(word SEPARATOR '|') from banned_words) group by toy having cnt >= 2 order by cnt DESC LIMIT 10 ;" ;
                $result = $this->db->query($query) or die(mysql_error());
		unset($top10_toys_status) ;
		$top10_toys_status = array() ;

                /* fetch object array */
                while ($row = $result->fetch_row()) {
			$jsonRow = array(
                                    "toy" => ucwords($row[0]),
                                    "total" => $row[1],
                                       );
			array_push($top10_toys_status, $jsonRow) ;
                }
		array_push($this->array_appender, array("name" => "top10_toys_${status}", "description" => "Top Toys wanted by ${comment} Kids", "type" => "toy", "data" => $top10_toys_status )) ;
	}

	// Method to return top 10 toys for kids from/to
	function f_top10_toys_age($from, $to) {
		$query = "select toy,count(*) as cnt from prefs where toy <> '' AND age >= $from and age <= $to AND device_id not in (select device_id from banned_device_ids) AND LOWER(toy) not regexp (select group_concat(word SEPARATOR '|') from banned_words) group by toy having cnt >= 2 order by cnt DESC LIMIT 10 ;" ;
                $result = $this->db->query($query) or die(mysql_error());
		unset($top10_toys_age) ;
		$top10_toys_age = array() ;
                /* fetch object array */
                while ($row = $result->fetch_row()) {
			$jsonRow = array(
                                    "toy" => ucwords($row[0]),
                                    "total" => $row[1],
                                       );
			array_push($top10_toys_age, $jsonRow) ;
                }
		array_push($this->array_appender, array("name" => "top10_toys_${from}to${to}", "description" => "Top Toys wanted by kids ${from}-${to}", "type" => "toy", "data" => $top10_toys_age )) ;
	}

	function return_json() {
		// Return full array
		echo "\n" ;

		$this->jsonResponse =  array("app" => "slist", "stat" => $this->array_appender) ;
		echo json_encode($this->jsonResponse) ;
	}
}
	

$api = new Result;

### NOT USED FUNCTIONS
# List top 10 toys
#$api->f_top10_toys();
# List top 10 toys past week
#$api->f_top10_toys_time("1 WEEK", "Past week", "week");
# List top 10 toys today
#$api->f_top10_toys_time("1 DAY", "Past day", "day");
# List top 10 toys this year in USA
#$api->f_top10_toys_year_country(date("Y"),"US");




# USED FUNCTIONS
# list good bad percentages
$api->f_percent_good_bad();

# List top toys 2013
$api->f_top10_toys_year("2013");

# List top 10 toys past month
$api->f_top10_toys_time("1 MONTH", "Past month", "month");

# Top toys wanted by good kids
$api->f_top10_toys_status("g");

# Top toys wanted by bad kids
$api->f_top10_toys_status("n");

# Top toys for 0-3's
$api->f_top10_toys_age(0,3);

# Top toys for 3-6's
$api->f_top10_toys_age(3,6);

# Top toys for 6-9's
$api->f_top10_toys_age(6,9);

# Top names on good list
$api->f_top10_kids_name_status('g');

# Top names on naughty list
$api->f_top10_kids_name_status('n');

$api->return_json();

?>
