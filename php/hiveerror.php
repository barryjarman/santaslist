<?php

include 'library.php' ;

class RedeemAPI {


    private $db;

    // Constructor - open DB connection
    function __construct() {
	$this->db = new mysqli('127.2.185.130', 'adminF2Jbm2B', 'nCi6Du5zhr4B' , 'hive');
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
            if ($function == "hiveerror") {
                    // Put parameters into local variables
                   $uname = $_POST["uname"];
		   $password = $_POST["password"];
                   $error = $_POST["error"];

                    // Add entry to database
                    echo "Adding Entry to DB $uname:$password:$error\n";
                    $stmt = $this->db->prepare("INSERT INTO hiveerrors (uname, password, error) VALUES (?, ?, ?)");
                    $stmt->bind_param("ssi", $uname, $password, $error);
                    $stmt->execute();
                    $stmt->close();
            }

	    // Commit to Database
            $this->db->commit();
            
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
