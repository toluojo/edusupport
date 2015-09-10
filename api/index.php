<?php

require 'controller.php';

$controller = new Controller();

if(isset($_REQUEST['intent']) && in_array($_REQUEST['intent'], INTENTS) && isset($_REQUEST['application_name']) && in_array($_REQUEST['application_name'], APPS))
{    
    $intent = $_REQUEST['intent'];
    $source = $_REQUEST['application_name'];
   
    switch ($intent) {
        case "login":
            if(!empty($REQUEST['username']) && !empty($_REQUEST['password'])){
                $name = $_REQUEST['username'];
                $password = $_REQUEST['password'];

                $result = $controller->login($username, $password);  
                echo $result;
            }else{
                return_error();
            }            
        break;
        case "create_account":
            if(!empty($REQUEST['sessionid']) && !empty($_REQUEST['email'])){
                $session_id = $_REQUEST['sessionid'];
                $email = $_REQUEST['email'];
                $name = $_REQUEST['name']?$_REQUEST['name']:"";
                $phone = $_REQUEST['phone']?$_REQUEST['phone']:"";
                $address = $_REQUEST['address']?$_REQUEST['address']:"";
                $location = $_REQUEST['location']?$_REQUEST['location']:"";
                $how = $_REQUEST['how']?$_REQUEST['how']:"";
                
                $result = $controller->createAccount($session_id, $name, $address, $location, $phone, $email, $how, $source);
                echo $result;
            }else{
                return_error();
            }   
        break;
        case "upgrade_account":
            if(!empty($REQUEST['sessionid']) && !empty($_REQUEST['accountid'])){
                $session_id = $_REQUEST['sessionid'];
                $accountid = $_REQUEST['accountid'];
                $upgrade = $_REQUEST['uprade']?$_REQUEST['upgrade']:"";
                
                $result = $controller->updateAccount($session_id, $accountid);  
                echo $result;
            }else{
                return_error();
            }   
        break;        
        case "logout":
            if(!empty($REQUEST['sessionid'])){
                $session_id = $_REQUEST['sessionid'];
                               
                $result = $controller->logout($session_id, $source);  
                echo $result;
            }else{
                return_error();
            }   
        break;
        default:
            return_error();            
        break;
    }    
}
else{
        echo json_encode(array(
                    "status" => 0,
                    'message' => "Error: Invalid Application or Intent"
             ));        
}
       
function return_error(){
    echo json_encode(array(
                    "status" => 0,
                    'message' => "Incomplete/Incorrect Parameters"
             ));   
    exit();
}
