<?php

require 'controller.php';

$controller = new Controller();

$intents = array("login", "create_account", "upgrade_account", "search4Account", "logout");
$apps = array("application_portal", "website_registration");
$upgrades = array("paid");

if((isset($_REQUEST['intent'])) &&
    (in_array($_REQUEST['intent'], $intents)) &&
    (isset($_REQUEST['application_name'])) &&
    (in_array($_REQUEST['application_name'], $apps)))
{
    $intent = $_REQUEST['intent'];
    $source = $_REQUEST['application_name'];

    switch($source){
        case $apps[0]:
            $source = "Apply Portal";
            break;
        case $apps[1]:
            $source = "Web Site";
            break;
    }

    switch ($intent) {
        case "login":
            if((isset($_REQUEST['username'])) && (isset($_REQUEST['password']))){
                $username = $_REQUEST['username'];
                $password = $_REQUEST['password'];

                $result = $controller->login($username, $password, $source);
                echo $result;
            }else{
                return_error();
            }
            break;
        case "create_account":
            if(isset($_REQUEST['sessionid']) && isset($_REQUEST['email'])){
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
            if(isset($_REQUEST['sessionid']) && isset($_REQUEST['accountid']) && isset($_REQUEST['upgrade'])){
                $session_id = $_REQUEST['sessionid'];
                $account_id = $_REQUEST['accountid'];
                $upgrade = $_REQUEST['upgrade'];
                if(in_array($upgrade, $upgrades)) {
                    $result = $controller->upgradeAccount($session_id, $account_id);
                    echo $result;
                }
                else{
                    return_error();
                }
            }else{
                return_error();
            }
            break;
        case "logout":
            if(isset($_REQUEST['sessionid'])){
                $session_id = $_REQUEST['sessionid'];

                $result = $controller->logout($session_id);
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
        'message' => "Incomplete or Incorrect Parameters"
    ));
    exit();
}
