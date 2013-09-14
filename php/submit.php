<?php

include 'library.php' ;

class RedeemAPI {


    private $db;

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
    
        // Check for required parameters
        if (isset($_POST["function"])) {
              echo "Function=\"" , $_POST["function"]  , "\"\n" ; 
            $function = $_POST["function"] ;
            if ($function == "prefupdate") {
                    // Put parameters into local variables
                   $device_id = $_POST["device_id"];
		   $shared_code = $_POST["shared_code"];
                   $pref_id = $_POST["pref_id"];
                   $name = strtolower($_POST["name"]);
                   $toy = strtolower($_POST["toy"]);
                   $age = $_POST["age"];
                   $status = $_POST["status"];
                   if ($status == "Good") {
                         $stat="G" ;
                   } else if ( $status == "Naughty") {
                         $stat="N" ;
                   } else {
                         $stat="R" ;
	           }
                    // Add entry to database
                    echo "Adding Entry to DB $device_id:$shared_code:$pref_id:$name:$toy:$age:$status($stat)\n";
                    $stmt = $this->db->prepare("INSERT INTO prefs (device_id, shared_code, pref_id, name, toy, age, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("ssissis", $device_id, $shared_code, $pref_id, $name, $toy, $age, $stat);
                    $stmt->execute();
                    $stmt->close();
            }

            if ($function == "phoneupdate") {
                    // Put parameters into local variables
                   $device_id = $_POST["device_id"];
                   $make = $_POST["make"];
                   $model = $_POST["model"];
                   $country = $_POST["country"];
                   $sdk = $_POST["sdk"];
                   $app_version = $_POST["app_version"];

                    // Add entry to database
                    echo "Adding Entry to DB $device_id:$make:$model:$country:$sdk:$app_version\n";
                    $stmt = $this->db->prepare("INSERT INTO phone (device_id, make, model, country, sdk, app_version) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("ssssii", $device_id, $make, $model, $country, $sdk, $app_version);
                    $stmt->execute();
                    $stmt->close();
            }
            
            if ($function == "use") {
                   $device_id = $_POST["device_id"];
                   $adduses = $_POST["uses"];
                   echo "Function = use - Device ID = \"$device_id\"\n" ;
                   $stmt = $this->db->prepare('SELECT id,uses FROM tracking WHERE device_id=?');
                          $stmt->bind_param("s", $device_id);
                          $stmt->execute();
                          $stmt->bind_result($redeemed_id,$uses);
                          while ($stmt->fetch()) {
                          break;
                   }
                   $stmt->close();

                   // If device exists then update
                   if ($redeemed_id > 0) {
                         echo "ID exists incrementing use (Current:$uses adding $adduses)\n";
                                $this->db->query("UPDATE tracking SET uses=uses+$adduses WHERE id=$redeemed_id");
       	           } else {        
              	         echo "ID does not exist creating\n";
       	                 $stmt = $this->db->prepare("INSERT INTO tracking (device_id, uses) VALUES (?, 1)");
       	                 $stmt->bind_param("s", $device_id);
       	                 $stmt->execute();
       	                 $stmt->close();
       	           }
            }

            if ($function == "feedback") {
                    // Put parameters into local variables
                   $device_id = $_POST["device_id"];
                   $comment = $_POST["comment"];

                    // Add entry to database
                    echo "Adding Entry to DB $device_id:$comment\n";
                    $stmt = $this->db->prepare("INSERT INTO feedback (device_id, comment) VALUES (?, ?)");
                    $stmt->bind_param("ss", $device_id, $comment);
                    $stmt->execute();
                    $stmt->close();
            }


	    // Commit to Database
            $this->db->commit();
            
#            echo "Return 200\n" ;
            sendResponse(200, "OK");
            return true;
        }

        sendResponse(400, 'Invalid request');
        return false;
    
    }

}

$api = new RedeemAPI;
$api->redeem();

?>
