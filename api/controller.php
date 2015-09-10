<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require 'model.php';
require 'constants.php';

class controller
{
    
    function login($username, $password){
        $login_parameters = array(
                                    "user_auth" => array(
                                    "user_name" => $username,
                                    "password" => md5($password),
                                    "version" => "1"
                                    ),
                                    "application_name" => "RestTest",
                                    "name_value_list" => array(),
                                );
        $model = new Model(LOGIN, $parameters);
        $login_result = $model->call();
        
//        if($login_result){
//            
//        }
        //get session id
        $session_id = $login_result->id;
        return $session_id;

    }
    
    function create_account(){
        $set_entry_parameters = array(
                                        //session id
                                        "session" => $session_id,

                                        //The name of the module from which to retrieve records.
                                        "module_name" => "Accounts",

                                        //Record attributes
                                        "name_value_list" => array(
                                            //to update a record, you will need to pass in a record id as commented below
                                        //  array("name" => "id", "value" => "eb220d1d-8684-82f1-4d94-55e597c27a1c"),
                                            array("name" => "name", "value" => "New Test Account"),
                                            array("name" => "matric_number_c", "value" => "1234567890"),
                                            array("name" => "jjwg_maps_address_c", "value" => "8, watch tower street, Onipanu Lagos"),
                                            array("name" => "billing_address_state", "value" => "Anambra"),
                                            array("name" => "phone_office", "value" => "1234567890"),
                                            array("name" => "email1", "value" => "test@cloudtechng.com"),
                                            array("name" => "description", "value" => "Heard from us through website"),
                                            array("name" => "password_c", "value" => "Password"),
                                            ),
                                      );

        $set_entry_result = call("set_entry", $set_entry_parameters, $url);

        //echo "<pre>";
        echo json_encode($set_entry_result);
        //echo "</pre>";
    }
    
    function update_account(){
        
    }
    
    function search4Account($session_id, $email, $url){
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

        $search_by_module_result = call('search_by_module', $search_by_module_parameters, $url);

        return $search_by_module_result;
    }
    
    function logout($session_id){
        $parameters = array(
                                //session id to expire
                                "session" => $session_id,
                           );        
        $model = new Model(LOGOUT, $parameters);
        $login_result = $model->call();
    }
    
}