<?php

require 'controller.php';
require 'constants.php';
require 'model.php';

$controller = new controller();

$intents = array(LOGIN, "create_account", "upgrade_account", "search4Account", "revive_accounts", LOGOUT);
$apps = array("application_portal", "website_registration", "sudo");
$upgrades = array("paid");

$intent = isset($_REQUEST['intent'])?strtolower($_REQUEST['intent']):"";
$source = isset($_REQUEST['application_name'])?strtolower($_REQUEST['application_name']):"";

if((isset($intent)) && (in_array($intent, $intents)) &&
    (isset($source) && (in_array($source, $apps))))
{     
    switch($source){
        case $apps[0]:
            $source = "apply_portal";
            break;
        case $apps[1]:
            $source = "web_site";
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
                $intended_course_of_study = $_REQUEST['intended_course_of_study']?$_REQUEST['intended_course_of_study']:"";
                $referral = $_REQUEST['referral']?$_REQUEST['referral']:"";

                $result = $controller->createAccount($session_id, $name, $address, $location, $phone, $email, $how, $source);

                if($result['status'] == 1){
                    $update = $controller->updateLead($session_id, $phone, $email, $intended_course_of_study, $referral);

                    if($update["status"] == 0){
                        echo json_encode($update);
                    }
                    else{
                        echo json_encode($result);
                    }

                    exit();
                }
                else{
                    echo json_encode($result);
                    exit();
                }
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
elseif(isset($_REQUEST['page_url']))
{
    file_put_contents("log.txt", time().print_r($_REQUEST, true));

    if (get_magic_quotes_gpc()) {
        $unescaped_REQUEST_data = stripslashes_deep($_REQUEST);
    } else {
        $unescaped_REQUEST_data = $_REQUEST;
    }
    $form_data = json_decode($unescaped_REQUEST_data['data_json']);
    // If your form data has an 'Email Address' field, here's how you extract it:
    $email = $form_data->email_address[0];
    $name = $form_data->first_name[0];
    $name .= " ".$form_data->last_name[0];
    $phone = $form_data->phone_number[0];
    // $courseofintereset = $_REQUEST['which_course_are_you_interested_inText'];
    $how = $form_data->how_did_you_hear_about_us[0];

    // Grab the remaining page data...
    $page_id = $_REQUEST['page_uuidText'];
    $page_url = $_REQUEST['page_url'];
    $variant = $_REQUEST['variantText'];

    //set relevant details
    $source = "web_site";
    $loginresult = $controller->login("tolu.ojo", "password", $source);
    $lr = json_decode($loginresult);

    $session_id = $lr->sessionid;

    $result = $controller->createAccount($session_id, $name, "", "", $phone, $email, $how, $source);
    if($result['status'] == 1){
        $update = $controller->updateLead($session_id, $phone, $email, "", "");

        if($update["status"] == 0){
            echo json_encode($update);
        }
        else{
            echo json_encode($result);
        }

        exit();
    }
    else{
        echo json_encode($result);
        exit();
    }
}
else{
    echo json_encode(array(
        "status" => 0,
        'message' => "Error: Invalid Application or Intent",
        'intent' => $intent
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
