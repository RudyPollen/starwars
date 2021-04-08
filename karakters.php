<?php                       
include "exitmelding.inc.php";

switch ($_SERVER['REQUEST_METHOD']){
    case 'GET'    : handle_get_request();     break;
    case 'OPTIONS': handle_options_request(); break;
    default       : exit_melding(405);
}

function handle_options_request(){                      
    header('Access-Control-Allow-Origin: *');  
    header('Access-Control-Allow-Methods', 'GET, OPTIONS');
    header('Access-Control-Allow-Headers: *');    
    exit_melding(204);
}

function handle_get_request(){                      
    if (isset($_GET["id"])) {
        if (!filter_var($_GET["id"], FILTER_VALIDATE_INT)) return exit_melding(400, "id incorrect");        
        $sql = "SELECT * FROM karakters WHERE id = ".$_GET["id"]; 
    }
    else $sql = "SELECT * FROM karakters";

    $resultaat = array();
    $db = new SQLite3('starwars.db');
    $res = $db->query($sql);
    while ($row = $res->fetchArray(SQLITE3_ASSOC)) {
        $resultaat[] = $row;
    }
    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *'); 
    echo json_encode($resultaat);
}
