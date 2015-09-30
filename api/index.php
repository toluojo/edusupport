<?php

require 'controller.php';
require 'constants.php';

$controller = new controller();

$intents = array(LOGIN, "create_account", "upgrade_account", "search4Account", "revive_accounts", LOGOUT);
$apps = array("application_portal", "website_registration", "sudo");
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
//        case "revive_accounts":
//            if(isset($_REQUEST['sessionid'])){
//                $session_id = $_REQUEST['sessionid'];
//
//                $result = $controller->reviveAllAccounts($session_id);
//                echo $result;
//            }else{
//                return_error();
//            }
//            break;
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
elseif(isset($_REQUEST['page_url']) && ($_REQUEST['page_url'] == ""))
{   
    if (get_magic_quotes_gpc()) {
        $unescaped_REQUEST_data = stripslashes_deep($_REQUEST);
      } else {
        $unescaped_REQUEST_data = $_REQUEST;
      }
      $form_data = json_decode($unescaped_REQUEST_data['data_json']);
      // If your form data has an 'Email Address' field, here's how you extract it:     
      $email_address = $form_data->email_address[0];
      $name = $_REQUEST['first_name'];
      $name .= " ".$_REQUEST['last_name'];
      $phone = $_REQUEST['phone'];
      $how = $_REQUEST['how'];
      
      // Grab the remaining page data...                                                
      $page_id = $_REQUEST['page_id'];
      $page_url = $_REQUEST['page_url'];
      $variant = $_REQUEST['variant'];
      
      //set relevant details
      $source = "website_registration";
      $loginresult = $controller->login("tolu.ojo", "password", $source);
      $lr = json_decode($loginresult);
      $session_id = $lr->sessionid;      
      
      $result = $controller->createAccount($session_id, $name, "", "", $phone, $email, $how, $source);
      echo $result;
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

function stripslashes_deep($value) {
    $value = is_array($value) ?
    array_map('stripslashes_deep', $value) :
    stripslashes($value);
    return $value;
}
