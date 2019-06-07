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
	
	if($array->name==NULL){
		http_response_code(400);
		echo(json_encode(array("error" => "Bad JSON1")));
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
	
	$statement->execute(array($array->name));
		
	if($statement->rowCount()==0){
		
		http_response_code(400);
		echo(json_encode(array("error" => "Bad JSON")));
		exit();
		
	}else{		
		
		$sql_update = "UPDATE device SET free_condition=?, downlink_update_interval=? WHERE name=?";
		$statement_update = $connection->prepare($sql_update);
		
		if ($statement_update == FALSE){
			http_response_code(500);
			$info = $connection->errorInfo();
			echo("<p>Error: {$info[2]}</p>");
			exit();
		}
		
		if($statement_update->execute(array($array->free_condition,$array->downlink_update_interval,$array->name)) == FALSE){
			http_response_code(500);
			$info = $statement->errorInfo();
			echo("<p>Error: {$info[2]}</p>");
			exit();
		}
		
		http_response_code(200);
	}
	
	$connection=NULL;

?>