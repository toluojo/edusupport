<?php

require 'controller.php';

$controller = new controller();

$intents = array("login", "create_account", "update_account", "search4Account", "logout");

if(isset($_REQUEST['intent'])){
    if(in_array($intent, $intents)){
        $intent = $_REQUEST['intent'];
    }

    switch ($intent) {
    case "login":
        if(!empty($REQUEST['name']) && !empty($_REQUEST['password'])){
            $name = $_REQUEST['name'];
            $password = $_REQUEST['password'];

            $result = $controller->login($username, $password);        
        }else{
            $error
        }            
    break;
    case "create_account":
        


    break;
    case "update_account":
        


    break;
    case "search4Account":
        


        break;
    case "logout":
        


        break;
    default:
        break;
}
    
        
        $name = $_REQUEST['name'];
        $phone = $_REQUEST['phone'];
        $email = $_REQUEST['email'];

        if(($intent == "apply_portal_registration") || ($intent == "website_registration")){
            if($intent == "apply_portal_registration"){
                $source = "Application Portal";
            }
            else if($intent == "website_registration"){
                $source = "Website Registration";
            }

            $address = $_REQUEST['address']?$_REQUEST['address']:"";
            $location = $_REQUEST['location']?$_REQUEST['location']:"";
            $how = $_REQUEST['how']?$_REQUEST['how']:"";
            $pass = $_REQUEST['password']?$_REQUEST['password']:"";

            $searchResult = search4Account($session_id, $email, $url);
//            die(json_encode($searchResult->entry_list[0]->records));

            if(empty($searchResult->entry_list[0]->records))
                createAccount($session_id, $name, $address, $location, $phone, $email, $how, $pass, $source, $url);
            else{
                if(strlen($searchResult->entryList[0]->records[0]->name->value) > strlen($name)){
                    $name = $searchResult->entryList[0]->records[0]->name->value;
                }
                if(strlen($searchResult->entryList[0]->records[0]->jjwg_maps_address_c->value) > strlen($address)){
                    $address = $searchResult->entryList[0]->records[0]->jjwg_maps_address_c->value;
                }
                if(strlen($searchResult->entryList[0]->records[0]->billing_address_state->value) > strlen($location)){
                    $location = $searchResult->entryList[0]->records[0]->billing_address_state->value;
                }
                if(strlen($searchResult->entryList[0]->records[0]->phone_office->value) > strlen($phone)){
                    $phone = $searchResult->entryList[0]->records[0]->phone_office->value;
                }
                if(strlen($searchResult->entryList[0]->records[0]->email1->value) > strlen($email)){
                    $email = $searchResult->entryList[0]->records[0]->email1->value;
                }
                if(strlen($searchResult->entryList[0]->records[0]->how_c->value) > strlen($how)){
                    $name = $searchResult->entryList[0]->records[0]->how_c->value;
                }
                if(strlen($searchResult->entryList[0]->records[0]->password_c->value) > strlen($pass)){
                    $pass = $searchResult->entryList[0]->records[0]->password_c->value;
                }
                if(strlen($searchResult->entryList[0]->records[0]->source_c->value) > strlen($source)){
                    $source = $searchResult->entryList[0]->records[0]->source_c->value;
                }
                $account_id = $searchResult->entryList[0]->records[0]->id->value;

                updateAccount($session_id, $account_id, $name, $address, $location, $phone, $email, $how, $pass, $source, $url);
            }
        }
        else if($intent == "sis_payment"){
            if(isset($_REQUEST['accountid'])){
                // update record
                $id = $_REQUEST['accountid'];

                upgradeAccount($session_id, $id, $url);
            }
            else{
                returnError();
            }
        }
    }
    else{
        returnError();
    }
}
else{
    echo returnError();
}

function returnError(){
    return json_encode(array(
        'status' => 0,
        'message' => "Unauthorised access, sorry"
    ));
}




function upgradeAccount($session_id, $id, $url)



?>