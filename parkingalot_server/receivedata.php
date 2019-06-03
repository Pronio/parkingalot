<?php

	include 'authentication.php';
	
	try{
		$connection = new PDO($dsn, $user, $pass);
	}
	catch(PDOException $exception){
		http_response_code(500);
		echo(json_encode(array("error" => $exception->getMessage())));
		/*exit();*/

	}

	$headers = getallheaders();
	
	if($headers["Token"]!=$acesstoken){
		http_response_code(401);
		echo(json_encode(array("error" => "Bad token")));
		exit();
	}
	
	
	$json = file_get_contents('php://input');
	$array = json_decode($json);
	
	
	if($array==NULL){
		http_response_code(400);
		echo(json_encode(array("error" => "Bad JSON")));
		exit();
	}
	
	$sql = "SELECT name FROM device WHERE name = ?";
	$statement = $connection->prepare($sql);
	
	if ($statement == FALSE){
		http_response_code(500);
		$info = $connection->errorInfo();
		echo("<p>Error: {$info[2]}</p>");
		exit();
	}
			
	$statement->execute(array($array->Device));
	
	if($statement->rowCount()==0){
		
		$sql = "INSERT INTO device VALUES (?,?,?,?)";
		$statement = $connection->prepare($sql);
		
		if ($statement == FALSE){
			http_response_code(500);
			$info = $connection->errorInfo();
			echo("<p>Error: {$info[2]}</p>");
			exit();
		}
		
		if($statement->execute(array($array->Device,time(),'image', 5)) == FALSE){
			http_response_code(500);
			$info = $statement->errorInfo();
			echo("<p>Error: {$info[2]}</p>");
			exit();
		}
	
	}
	
	$sql = "UPDATE device SET update_time=? WHERE name=?";
	$statement = $connection->prepare($sql);
	
	if ($statement == FALSE){
		http_response_code(500);
		$info = $connection->errorInfo();
		echo("<p>Error: {$info[2]}</p>");
		exit();
	}
	
	if($statement->execute(array(time(),$array->Device)) == FALSE){
		http_response_code(500);
		$info = $statement->errorInfo();
		echo("<p>Error: {$info[2]}</p>");
		exit();
	}
	
	s=0;
	
	$data = hex2bin($array->Data);
	
	for(i=0; i<strlen($data);i++){
		for(j=0; i<8; i++){
			if((j==0)&&(i==0)){
				next;
			}
			
			
		}
	}
	
	
	
	
	
	echo($array->Device."\n");
	echo($array->Data."\n");
	echo($array->Time."\n");
	echo($array->Sequence."\n");

php?>