<?php

	include 'authentication.php';
	
	//Connection establish
	try{
		$connection = new PDO($dsn, $user, $pass);
	}
	catch(PDOException $exception){
		http_response_code(500);
		echo(json_encode(array("error" => $exception->getMessage())));
		/*exit();*/

	}
	
	//Acess token check
	$headers = getallheaders();
	
	if($headers["Token"]!=$acesstoken){
		http_response_code(401);
		echo(json_encode(array("error" => "Bad token")));
		exit();
	}
	
	//Get JSON contents
	$json = file_get_contents('php://input');
	$array = json_decode($json);
	
	
	if($array==NULL){
		http_response_code(400);
		echo(json_encode(array("error" => "Bad JSON")));
		exit();
	}

	//Check if devices exists
	$sql = "SELECT free_condition, downlink_update_interval FROM device WHERE name=?";
	$statement = $connection->prepare($sql);
	
	if ($statement == FALSE){
		http_response_code(500);
		$info = $connection->errorInfo();
		echo("<p>Error: {$info[2]}</p>");
		exit();
	}
			
	$statement->execute(array($array->Device));
	
	$result = $statement->fetch();
	
	if($result['free_condition']=='and'){
		$free_condition='00';
	}elseif($result['free_condition']=='or'){
		$free_condition='01';
	}elseif($result['free_condition']=='sensor'){
		$free_condition='02';
	}else{
		$free_condition='03';
	}
	
	$update_interval_temp=dechex($result['downlink_update_interval']);
	
	$update_interval="";
	
	for($i=0; $i<4-strlen($update_interval_temp); $i++){
		$update_interval=$update_interval."0";
	}
	
	$update_interval=$update_interval.$update_interval_temp;
	
	$response = $free_condition.$update_interval."0000000000";
	
	http_response_code(200);
	
	echo(json_encode(array($array->Device=>array("downlinkData"=>$response))));
	
	$connection=NULL;

?>