<?php                       
include "exitmelding.inc.php";

switch ($_SERVER['REQUEST_METHOD']){
    case 'GET'    : handle_get_request();     break;
    case 'POST'   : handle_post_request();    break;
    case 'PATCH'  : handle_patch_request();   break;
    case 'DELETE' : handle_delete_request();  break;
    case 'OPTIONS': handle_options_request(); break;
    default       : exit_melding(405);
}

function handle_options_request(){                      
    header('Access-Control-Allow-Origin: *');  
//    header('Access-Control-Allow-Methods', 'GET, POST, OPTIONS, DELETE, PATCH');
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS, DELETE, PATCH');  // dit moet zo
    header('Access-Control-Allow-Headers: *');    
    exit_melding(200);
}

function handle_get_request(){                      
    if (isset($_GET["id"])) {
        if (!filter_var($_GET["id"], FILTER_VALIDATE_INT)) return exit_melding(400, "id incorrect");        
        $sql = "SELECT * FROM spreuken WHERE id = ".$_GET["id"]; 
    }
    elseif (isset($_GET["karakter"])) {      
        if (!filter_var($_GET["karakter"], FILTER_VALIDATE_INT)) return exit_melding(400, "karakter incorrect");        
        $sql = "SELECT * FROM spreuken WHERE karakterid = '".$_GET["karakter"]."'"; 
    }
    else $sql = "SELECT * FROM spreuken";

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

function handle_post_request(){                  
    $body   = file_get_contents('php://input');
    $data   = json_decode($body, TRUE);                                     //convert JSON into array   

    if (!array_key_exists("karakterid", $data)) return exit_melding(400, "parameter karakterid ontbreekt");
    $karakterid = $data["karakterid"];
    if (!filter_var($karakterid, FILTER_VALIDATE_FLOAT)) exit_melding(400, "parameter karakterid incorrect");

    if (!array_key_exists("tekst", $data)) return exit_melding(400, "parameter tekst ontbreekt");
    $tekst = trim(filter_var($data["tekst"], FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES));
	if ($tekst == "") return exit_melding(400, "parameter tekst incorrect"); 

    $db = new SQLite3('starwars.db');
    $res = $db->query("SELECT id FROM karakters WHERE id = ".$karakterid);
    if ($res->fetchArray() == FALSE) return exit_melding(404, $karakterid);
    $res = $db->query('INSERT INTO spreuken VALUES (null, "'.$karakterid.'", "'.$tekst.'", "0")');
    if($res == FALSE) return exit_melding(500, 'insert query');  
    exit_melding(201);  
}

function handle_patch_request(){                  
    if (!isset($_GET["id"])) return exit_melding(400, "id ontbreekt");
    $id = $_GET["id"];
    if (filter_var($id, FILTER_VALIDATE_INT) != TRUE) return exit_melding(400, "id incorrect");

    $body   = file_get_contents('php://input');
    $data   = json_decode($body, TRUE);        

    if ($tekst_aanwezig = array_key_exists("tekst", $data)) {
        $tekst = trim(filter_var($data["tekst"], FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES));
        if ($tekst == "") return exit_melding(400, "parameter tekst incorrect");         
    }
    if ($score_aanwezig = array_key_exists("score", $data)) {
        $score = $data["score"];
        if (!filter_var($score, FILTER_VALIDATE_INT)) return exit_melding(400, "parameter score incorrect");      
    } 
    if (!$tekst_aanwezig && !$score_aanwezig) return exit_melding(400, "parameters ontbreken");   
    if ($tekst_aanwezig)  $sql = 'UPDATE spreuken SET tekst = "'.$tekst.'" WHERE id = '.$_GET["id"];
    if ($score_aanwezig)  $sql = 'UPDATE spreuken SET score = "'.$score.'" WHERE id = '.$_GET["id"];
    if ($tekst_aanwezig && $score_aanwezig) $sql = 'UPDATE spreuken SET tekst = "'.$tekst.'", score = "'.$score.'" WHERE id = '.$_GET["id"];

    $db = new SQLite3('starwars.db');
    $res = $db->query("SELECT id FROM spreuken WHERE id = ".$id);
    if ($res->fetchArray() == FALSE) return exit_melding(404, $id);
    $res = $db->query($sql);
    if($res == FALSE) return exit_melding(500, 'update query');  
    exit_melding(204);                  
}

function handle_delete_request(){                  
    if (!isset($_GET["id"])) return exit_melding(400, "id ontbreekt");
    $id = $_GET["id"];
    if (filter_var($id, FILTER_VALIDATE_INT) != TRUE) return exit_melding(400, "id incorrect");

    $db = new SQLite3('starwars.db');
    $res = $db->query("SELECT id FROM spreuken WHERE id = ".$id);
    if ($res->fetchArray() == FALSE) return exit_melding(404, $id);
    $res = $db->query("DELETE FROM spreuken WHERE id = ".$id);
    if($res == FALSE) return exit_melding(500, 'delete query');  
    exit_melding(204);                  
}
