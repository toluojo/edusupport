<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require 'model.php';
require 'constants.php';

class Controller{
    function login($username, $password, $source){
        $parameters = array(
            "user_auth" => array(
                "user_name" => $username,
                "password" => md5($password),
                "version" => "1"
            ),
            "application_name" => $source,
            "name_value_list" => array(),
        );
        $model = new Model(LOGIN, $parameters);
        $login_result = $model->call();

        if(isset($login_result->id)){
            return json_encode(array(
                "status" => 1,
                'sessionid' => $login_result->id,
                'message' => "Login successful"
            ));
        }
        else{
            return json_encode(array(
                "status" => 0,
                'message' => "Login unsuccessful"
            ));
        }
    }

    function createAccount($session_id, $name, $address, $location, $phone, $email, $how, $source){
        $exists = $this->searchAccount($session_id, $email);

        if($exists){
            return json_encode(array(
                "status" => 0,
                'message' => "Account Already Exists"
            ));
        }
        else {
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
                    array("name" => "account_type", "value" => LEAD),
                )
            );

            $model = new Model(SET_ENTRY, $set_entry_parameters);
            $set_entry_result = $model->call();

            if (isset($set_entry_result->id)) {
                return json_encode(array(
                    "status" => 1,
                    'accountid' => $set_entry_result->id,
                    'message' => "Lead successfully created"
                ));
            } else {
                return json_encode(array(
                    "status" => 0,
                    'message' => "Account Creation Unsuccessful"
                ));
            }
        }
    }

    function updateAccount(){

    }

    function upgradeAccount($session_id, $account_id){
        //update account -------------------------------------
        $set_entry_parameters = array(

            //session id
            "session" => $session_id,

            //The name of the module from which to retrieve records.
            "module_name" => "Accounts",

            //Record attributes
            "name_value_list" => array(
                //to update a record, you will need to pass in a record id as commented below
                array("name" => "id", "value" => $account_id),
                array("name" => "account_type", "value" => "Customer")
            )
        );

        $model = new Model(SET_ENTRY, $set_entry_parameters);
        $set_entry_result = $model->call();

        if(isset($set_entry_result->id)){
            return json_encode(array(
                "status" => 1,
                'message' => "Lead changed to customer"
            ));
        }
        else{
            return json_encode(array(
                "status" => 0,
                'message' => "Account Upgrade Unsuccessful"
            ));
        }
    }

    function searchAccount($session_id, $email){
        $search_by_module_parameters = array(
            //Session id
            "session" => $session_id,

            //The string to search for.
            'search_string' => $email,

            //The list of modules to query.
            'modules' => array(
                'Accounts',
            ),

            //The record offset from which to start.
            'offset' => 0,

            //The maximum number of records to return.
            'max_results' => 10,

            //Filters records by the assigned user ID.
            //Leave this empty if no filter should be applied.
            'id' => '',

            //An array of fields to return.
            //If empty the default return fields will be from the active listviewdefs.
            'select_fields' => array(
                'id',
                'name',
                'jjwg_maps_address_c',
                'billing_address_state',
                'phone_office',
                'email1',
                'source_c',
                'how_c',
                'password_c'

            ),

            //If the search is to only search modules participating in the unified search.
            //Unified search is the SugarCRM Global Search alternative to Full-Text Search.
            'unified_search_only' => false,

            //If only records marked as favorites should be returned.
            'favorites' => false
        );

        $model = new Model(SEARCH_BY_MODULE, $search_by_module_parameters);
        $search_by_module_result = $model->call();

        if(empty($search_by_module_result->entry_list[0]->records)){
            return false;
        }
        else{
            return true;
        }
    }

    function logout($session_id){
        $parameters = array(
            //session id to expire
            "session" => $session_id,
        );
        $model = new Model(LOGOUT, $parameters);
        $model->call();

        return json_encode(array(
            "status" => 1,
            'message' => "Logout successful"
        ));
    }

}