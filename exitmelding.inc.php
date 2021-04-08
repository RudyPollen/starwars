<?php

function exit_melding($nr, $txt=""){
    if ($txt == ""){
        header('Content-Type: text/html');
        header('Access-Control-Allow-Origin: *');  
    } else {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *'); 
        echo '{"melding" : "'.$txt.'"}';
    }
    http_response_code($nr); 
}

function tekst($nr){
    switch($nr){
        case 200: return 'OK'; 
        case 201: return 'Created'; 
        case 202: return 'Accepted'; 
        case 203: return 'Non-Authoritative Information'; 
        case 204: return 'No Content'; 
        case 205: return 'Reset Content'; 
        case 206: return 'Partial Content'; 
        case 300: return 'Multiple Choices'; 
        case 301: return 'Moved Permanently'; 
        case 302: return 'Moved Temporarily'; 
        case 303: return 'See Other'; 
        case 304: return 'Not Modified'; 
        case 305: return 'Use Proxy'; 
        case 400: return 'Bad Request'; 
        case 401: return 'Unauthorized'; 
        case 402: return 'Payment Required'; 
        case 403: return 'Forbidden'; 
        case 404: return 'Not Found'; 
        case 405: return 'Method Not Allowed'; 
        case 406: return 'Not Acceptable'; 
        case 412: return 'Precondition Failed'; 
        case 500: return 'Internal Server Error'; 
        case 501: return 'Not Implemented'; 
        case 503: return 'Service Unavailable'; 
        default:  return 'Unknown error'; 
    }
}
