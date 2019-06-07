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
	
	//Get JSON contents
	$json = file_get_contents('php://input');
	$array = json_decode($json);
	
	
	if($array==NULL){
		http_response_code(400);
		echo(json_encode(array("error" => "Bad JSON")));
		exit();
	}
	
	if($array->Device==NULL){
		http_response_code(400);
		echo(json_encode(array("error" => "Bad JSON")));
		exit();
	}
	

	//Check if devices exists
	$sql = "SELECT name, update_time, free_condition, downlink_update_interval, n_spaces FROM device WHERE name=?";
	$statement = $connection->prepare($sql);
	
	if ($statement == FALSE){
		http_response_code(500);
		$info = $connection->errorInfo();
		echo("<p>Error: {$info[2]}</p>");
		exit();
	}
	
	$statement->execute(array($array->Device));
		
	if($statement->rowCount()==0){
		
		http_response_code(200);
		
		echo(json_encode(array("Device"=>NULL)));
		
	}else{		
		
		$result = $statement->fetch();
		
		http_response_code(200);
		
		echo(json_encode(array("name"=>$result['name'],"update_time"=>$result['update_time'],"free_condition"=>$result['free_condition'],"downlink_update_interval"=>$result['downlink_update_interval'],"n_spaces"=>$result['n_spaces'])));
	
	}
	
	$connection=NULL;

?>