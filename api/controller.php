<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class controller{
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

    function updateLead($session_id, $phone, $email, $course, $referral)
    {
        $exists = $this->search($session_id, $phone, LEADS);

        if ($exists["status"] == 0) {
            return array(
                "status" => 0,
                'message' => "An error occurred during lead creation but the account has been created non the less"
            );
        } else {

//            die(var_dump($exists));

            //update lead -------------------------------------
            $set_entry_parameters = array(

                //session id
                "session" => $session_id,

                //The name of the module from which to retrieve records.
                "module_name" => LEADS,

                //Record attributes
                "name_value_list" => array(
                    //to update a record, you will need to pass in a record id as commented below
                    array("name" => "id", "value" => $exists["data"]->entry_list[0]->records[0]->id->value),
                    array("name" => "email1", "value" => $email),
                    array("name" => "intended_course_of_study_c", "value" => $course),
                    array("name" => "refered_by", "value" => $referral)
                )
            );

            $model = new Model(SET_ENTRY, $set_entry_parameters);
            $set_entry_result = $model->call();

//            die(var_dump($set_entry_result));

            if (isset($set_entry_result->id)) {
                return array(
                    "status" => 1,
                    'accountid' => $set_entry_result->id,
                    'message' => "Lead successfully created"
                );
            } else {
                return array(
                    "status" => 0,
                    'message' => "Lead Creation Unsuccessful"
                );
            }
        }
    }

    function search($session_id, $email, $module){
        $search_by_module_parameters = array(
            //Session id
            "session" => $session_id,

            //The string to search for.
            'search_string' => $email,

            //The list of modules to query.
            'modules' => array(
                $module
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
            ),

            //If the search is to only search modules participating in the unified search.
            //Unified search is the SugarCRM Global Search alternative to Full-Text Search.
            'unified_search_only' => false,

            //If only records marked as favorites should be returned.
            'favorites' => false
        );

        $model = new Model(SEARCH_BY_MODULE, $search_by_module_parameters);
        $search_by_module_result = $model->call();

//        die(var_dump($search_by_module_result));

        if(empty($search_by_module_result->entry_list[0]->records)){
            return array(
                "status" => 0
            );
        }
        else{
            return array(
                "status" => 1,
                "data" => $search_by_module_result
            );
        }
    }

    function createAccount($session_id, $name, $address, $location, $phone, $email, $how, $source){
        $exists = $this->search($session_id, $email, ACCOUNTS);

        if($exists["status"] == 1){
//            $exists = $this->search($session_id, $phone, ACCOUNTS);
//
//            if($exists["status"] == 1){
////                return array(
//                    "status" => 0,
//                    'message' => "Account Already Exists"
//                );

            //update account -------------------------------------
            $set_entry_parameters = array(

                //session id
                "session" => $session_id,

                //The name of the module from which to retrieve records.
                "module_name" => "Accounts",

                //Record attributes
                "name_value_list" => array(
                    //to update a record, you will need to pass in a record id as commented below
                    array("name" => "id", "value" => $exists["data"]->entry_list[0]->records[0]->id->value),
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
//            }
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
        }

        if (isset($set_entry_result->id)) {
            return array(
                "status" => 1,
                'accountid' => $set_entry_result->id,
                'message' => "Lead successfully created"
            );
        } else {
            return array(
                "status" => 0,
                'message' => "Account Creation Unsuccessful"
            );
        }
    }

    function reviveAllAccounts($session_id){
        $accounts = $this->fetchAllAccounts($session_id);

        while($accounts->total_count > 0){
            foreach($accounts->entry_list as $account){
                $this->updateAccount($session_id, $account->id);
            }
            $accounts = $this->fetchAllAccounts($session_id);
        }

        return true;
    }

    function fetchAllAccounts($session_id){
        $get_entry_list_parameters = array(
            //session id
            'session' => $session_id,

            //The name of the module from which to retrieve records
            'module_name' => 'Accounts',

            //The SQL WHERE clause without the word "where".
            'query' => "accounts.account_type IS NULL",

            //The SQL ORDER BY clause without the phrase "order by".
            'order_by' => "",

            //The record offset from which to start.
            'offset' => '0',

            //Optional. A list of fields to include in the results.
            'select_fields' => array(
                'id',
                'name',
//                'billing_address_street',
//                'billing_address_city',
//                'billing_address_postalcode',
//                'billing_address_country',
//                'shipping_address_street',
//                'shipping_address_city',
//                'shipping_address_postalcode',
//                'shipping_address_country',
//                'website',
//                'deleted',
            ),

            /*
            A list of link names and the fields to be returned for each link name.
            Example: 'link_name_to_fields_array' => array(array('name' => 'email_addresses', 'value' => array('id', 'email_address', 'opt_out', 'primary_address')))
            */
            'link_name_to_fields_array' => array(
            ),

            //The maximum number of results to return.
            'max_results' => '',

            //To exclude deleted records
            //'deleted' => false,

            //If only records marked as favorites should be returned.
            //'Favorites' => false,
        );

        $model = new Model(GET_ENTRY_LIST, $get_entry_list_parameters);
        $accounts = $model->call();

//        die(var_dump($accounts));

        return $accounts;
    }

    function updateAccount($session_id, $account_id){
        $set_entry_parameters = array(

            //session id
            "session" => $session_id,

            //The name of the module from which to retrieve records.
            "module_name" => "Accounts",

            //Record attributes
            "name_value_list" => array(
                //to update a record, you will need to pass in a record id as commented below
                array("name" => "id", "value" => $account_id),
                array("name" => "account_type", "value" => LEAD),
            )
        );

        $model = new Model(SET_ENTRY, $set_entry_parameters);
        $set_entry_result = $model->call();

        if (isset($set_entry_result->id)) {
            return true;
        } else {
            return false;
        }
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
                array("name" => "account_type", "value" => APPLICANT)
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