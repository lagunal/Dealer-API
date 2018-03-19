<?php

namespace App\Http\Controllers;

use App\Helpers\JwtAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\User;


class UserController extends Controller
{
	//save user to DB
    public function register(Request $request){
    	
    	//request Post
    	$json = $request->input('json', null);
    	$params = json_decode($json);

    	$email = (!is_null($json) && isset($params->email)) ? $params->email : null; 
    	$name = (!is_null($json) && isset($params->name)) ? $params->name : null; 
    	$surname = (!is_null($json) && isset($params->surname)) ? $params->surname : null; 
    	$role = 'ROLE_USER'; 
    	$password = (!is_null($json) && isset($params->password)) ? $params->password : null; 

    	if (!is_null($email) && !is_null($name) && !is_null($password)){
    		//create user
    		$user = new User();
    		$user->email = $email;
    		$user->name = $name;
    		$user->surname = $surname;
    		$user->role = $role;

    		$pwd = hash('sha256', $password);
    		$user->password = $pwd;

    		//check dulicated user
    		$isset_user = User::where('email', '=', $email)->first();
    		if (is_null($isset_user)){
 				//save to DB
				$user->save();

	    		$data = array(
	    			'status' => 'error',
	    			'code' => 200,
	    			'message' => 'User created successfully'
	    		);

    		} else {
    			//do not save
	    		$data = array(
	    			'status' => 'error',
	    			'code' => 400,
	    			'message' => 'User duplicated'
	    		);
    		}	

    	} else {
    		$data = array(
    			'status' => 'error',
    			'code' => 400,
    			'message' => 'User not created'
    		);
    	}

    	return response()->json($data, 200);


    }

    //function to login user
    public function login(Request $request){
    	
    	$jwtAuth = new JwtAuth();

    	//Receive POST
    	$json = $request->input('json' , null);
    	$params = json_decode($json);

    	$email = (!is_null($json) && isset($params->email)) ? $params->email : null;
    	$password = (!is_null($json) && isset($params->password)) ? $params->password : null;
    	$getToken = (!is_null($json) && isset($params->getToken)) ? $params->getToken : null;

    	//code Password
    	$pwd = hash('sha256', $password);

    	if(!is_null($email) && !is_null($password) && ($getToken == null || $getToken == 'false')){
    		$signup = $jwtAuth->signup($email, $pwd);
    		
    	}elseif($getToken != null){
			$signup = $jwtAuth->signup($email, $pwd, $getToken);    		
			
    	}else{
    		$signup = array(
    			'status' => 'error',
    			'message' => 'Send by POST'	
    		);
    	}

    	return response()->json($signup, 200);


    }

}
