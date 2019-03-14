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
	
	echo($array->Device."\n");
	echo($array->Data."\n");
	echo($array->Time."\n");
	echo($array->Sequence."\n");

php?>