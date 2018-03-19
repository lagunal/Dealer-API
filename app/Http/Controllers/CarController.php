<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\JwtAuth;
use App\Car;


class CarController extends Controller
{

 	//function to show all cars
    public function index(Request $request){

    	$cars = Car::all()->load('user');
    	return response()->json(array(
    		'cars' =>$cars,
    		'status' => 'success'
    	), 200);

    }


    //function to show especific car details
    public function show($id){

    	$car = Car::find($id)->load('user');
    	return response()->json(array(
    		'car' => $car,
    		'status' => 'success'
    	), 200);

    }


    //function to save car
    public function store(Request $request){

    	$hash = $request->header('Authorization', null);

    	$jwtAuth = new JwtAuth();
    	$checkToken = $jwtAuth->checkToken($hash);

    	if($checkToken){

    		//get data by POST
    		$json = $request->input('json', null);
    		$params = json_decode($json);
			$params_array = json_decode($json, true);

			//get authenticated user usint JwtAuth helper
			$user = $jwtAuth->checkToken($hash, true);

			//validate params input
	        $validate = \Validator::make($params_array, [
			            'title' => 'required|min:5',
			            'description' => 'required',
			            'price' => 'required',
			            'status' => 'required',
	        ]);
	        if($validate->fails()){
	        	return response()->json($validate->errors(), 400);
	        }


			//save car
			$car = new Car();
			$car->user_id = $user->sub;
			$car->title = $params->title;
			$car->description = $params->description;
			$car->price = $params->price;
			$car->status = $params->status;
			$car->save();

			//array for json response
			$data = array(
				'car' => $car,
				'status' => 'success',
				'code' => 200
			);

    	}else{
            
            //send Error in json
			$data = array(
				'message' => 'could not save car',
				'status' => 'error',
				'code' => 300
			);
    	}

    	return response()->json($data, 200);

    }

    //function to edit/update cars
    public function update($id, Request $request){

    	$hash = $request->header('Authorization', null);

    	$jwtAuth = new JwtAuth();
    	$checkToken = $jwtAuth->checkToken($hash);

    	if($checkToken){
    		
    		//receive params by POST
    		$json = $request->input('json', null);
    		$params = json_decode($json);
    		$params_array = json_decode($json, true);

    		//validate data
	        $validate = \Validator::make($params_array, [
			            'title' => 'required|min:5',
			            'description' => 'required',
			            'price' => 'required',
			            'status' => 'required',
	        ]);

	        if($validate->fails()){
	        	return response()->json($validate->errors(), 400);
	        }

    		//save to DB
    		$car = Car::where('id', $id)->update($params_array);

    		$data = array(
    			'car' => $params,
    			'status' => 'succes',
    			'code' => 200
    		);

    	}else{
            
            //send Error in json
			$data = array(
				'message' => 'could not save car',
				'status' => 'error',
				'code' => 300
			);
    	}

		return response()->json($data, 200);

    }

    //function to delete car
    public function destroy($id, Request $request){

    	$hash = $request->header('Authorization', null);

    	$jwtAuth = new JwtAuth();
    	$checkToken = $jwtAuth->checkToken($hash);

    	if ($checkToken){
    		//test if car exists
    		$car = Car::find($id);


    		//delete car
    		$car->delete();

    		//return car
    		$data = array(
    			'car' => $car,
    			'status' => 'success',
    			'code' => 200
    		);

    	}else{

    		$data = array(
    			'status' => 'error',
    			'message' => 'Not authorized user',
    			'code' => 400
    		);
    	}

    	return response()->json($data, 200);

    }


}
