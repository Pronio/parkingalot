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

	//Check if devices exists
	$sql = "SELECT name, id, utilization FROM parking_space";
	$statement = $connection->prepare($sql);
	
	if ($statement == FALSE){
		http_response_code(500);
		$info = $connection->errorInfo();
		echo("<p>Error: {$info[2]}</p>");
		exit();
	}
	
	$statement->execute();
		
	if($statement->rowCount()==0){
		
		http_response_code(200);
		
		echo(json_encode(array("Parking_space"=>NULL)));
		
	}else{		
		
		$result = $statement->fetchAll();
		
		$i=0;
		foreach($result as $row){
			
			$array[$i]=array('name'=>$row['name'],'id'=>$row['id'],'utilization'=>$row['utilization']);
			$i++;
		}
		
		http_response_code(200);
		
		echo(json_encode(array("Parking_space"=>$array)));
	
	}
	
	$connection=NULL;

?>