<?php

$url = "http://localhost/vgg/suite/service/v4_1/rest.php";
$username = "caleb";
$password = "password";
$intents = array("apply_portal_registration", "website_registration", "sis_payment");

if(isset($_REQUEST['intent'])){
    $intent = $_REQUEST['intent'];

    if(in_array($intent, $intents)){
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

function createAccount($session_id, $name, $address, $location, $phone, $email, $how, $password, $source, $url){
    //create account -------------------------------------
    $set_entry_parameters = array(

        //session id
        "session" => $session_id,

        //The name of the module from which to retrieve records.
        "module_name" => "Accounts",

        //Record attributes
        "name_value_list" => array(
            //to update a record, you will need to pass in a record id as commented below
            //        array("name" => "id", "value" => "eb220d1d-8684-82f1-4d94-55e597c27a1c"),
            array("name" => "name", "value" => $name),
            array("name" => "jjwg_maps_address_c", "value" => $address),
            array("name" => "billing_address_state", "value" => $location),
            array("name" => "phone_office", "value" => $phone),
            array("name" => "email1", "value" => $email),
            array("name" => "source_c", "value" => $source),
            array("name" => "how_c", "value" => $how),
            array("name" => "password_c", "value" => $password),
        ),
    );

    $set_entry_result = call("set_entry", $set_entry_parameters, $url);

    if($set_entry_result){
        echo json_encode(array(
            "status" => 1,
            'accountid' => $set_entry_result->id,
            'message' => "Lead successfully created"
        ));
    }
    //echo "<pre>";
//    echo json_encode(array("status" => 1, "data" => $set_entry_result));
    //echo "</pre>";
}

function upgradeAccount($session_id, $id, $url){
    //update account -------------------------------------
    $set_entry_parameters = array(

        //session id
        "session" => $session_id,

        //The name of the module from which to retrieve records.
        "module_name" => "Accounts",

        //Record attributes
        "name_value_list" => array(
            //to update a record, you will need to pass in a record id as commented below
            array("name" => "id", "value" => $id),
            array("name" => "account_type", "value" => "Customer")
        )
    );

    $set_entry_result = call("set_entry", $set_entry_parameters, $url);

    if($set_entry_result){
//        die(var_dump($set_entry_result));
        echo json_encode(array(
            "status" => 1,
            'accountid' => $set_entry_result->id,
            'message' => "Lead changed to customer"
        ));
    }
    //echo "<pre>";
//    echo json_encode(array("status" => 1, "data" => $set_entry_result));
    //echo "</pre>";
}




?>