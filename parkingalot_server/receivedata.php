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
	$sql = "SELECT name, n_spaces FROM device WHERE name = ?";
	$statement = $connection->prepare($sql);
	
	if ($statement == FALSE){
		http_response_code(500);
		$info = $connection->errorInfo();
		echo("<p>Error: {$info[2]}</p>");
		exit();
	}
			
	$statement->execute(array($array->Device));
	
	if($statement->rowCount()==0){
		
		http_response_code(400);
		echo("<p>Error: No device with that name</p>");
		exit();
	}
	
	$device_data = $statement->fetch();

	//Update device update_time
	$sql = "UPDATE device SET update_time=now() WHERE name=?";
	$statement = $connection->prepare($sql);
	
	if ($statement == FALSE){
		http_response_code(500);
		$info = $connection->errorInfo();
		echo("<p>Error: {$info[2]}</p>");
		exit();
	}
	
	if($statement->execute(array($array->Device)) == FALSE){
		http_response_code(500);
		$info = $statement->errorInfo();
		echo("<p>Error: {$info[2]}</p>");
		exit();
	}
	
	//Prepare SQL statement for parking space update
	$sql_update = "UPDATE parking_space SET full=?, utilization=? WHERE name=? and id=?";
	$statement_update = $connection->prepare($sql_update);
	
	if ($statement_update == FALSE){
		http_response_code(500);
		$info = $connection->errorInfo();
		echo("<p>Error: {$info[2]}</p>");
		exit();
	}
	
	//Prepare SQL statement for parking space status check
	$sql_check = "SELECT full, utilization FROM parking_space WHERE name=? and id=?";
	$statement_check = $connection->prepare($sql_check);
	
	if ($statement_check == FALSE){
		http_response_code(500);
		$info = $connection->errorInfo();
		echo("<p>Error: {$info[2]}</p>");
		exit();
	}
	
	
	
	$s=0;
	
	for($i=0; $i<strlen($array->Data); $i++){
		
		$data=hexdec($array->Data[$i]);
		
		//echo($data."\n");
		
		for($j=0; $j<4; $j++){
			if(($j==0)&&($i==0)){
				continue;
			}
			if($s<$device_data['n_spaces']){
				
				if($j==0){
					if($data>7){
						$value=1;
					}else{
						$value=0;
					}
				}elseif($j==1){
					if( ( ($data>3)&&($data<8) ) || ($data>11) ){
						$value=1;
					}else{
						$value=0;
					}
				}elseif($j==2){
					$aux=$data%4;
					if($aux>1){
						$value=1;
					}else{
						$value=0;
					}
				}else{
					if($data%2==1){
						$value=1;
					}else{
						$value=0;
					}
				}	

				$statement_check->execute(array($array->Device,$s));
				
				$full_state_row = $statement_check->fetch();
				
				//echo($full_state_row['full']." - ".$value."\n");
				
				if($full_state_row['full']!=$value){
					
					if($statement_update->execute(array($value,$full_state_row['utilization']+1,$array->Device,$s)) == FALSE){
						http_response_code(500);
						$info = $statement->errorInfo();
						echo("<p>Error: {$info[2]}</p>");
						exit();
					}
				}
			
			}
			
			$s++;
		}
	}
	
	http_response_code(200);
	
	$connection=NULL;

?>