<?php if(is_uploaded_file($_FILES['filespic']['tmp_name']))//checks if a file has been selected so it doesnt insert 'Nothing' into the database
	{
	//update image if one has already been set or change the query to an INSERT query
	  $imageupdatequery = 'UPDATE' . $tablename . '  SET ' . $columnname_name . ' = :FILE_NAME , '. $columnname_size . ' = :FILE_SIZE WHERE '.$columnname_user.' = :USERNAME';
    $stmt  = $handler->prepare($imageupdatequery);
    $errors= array();
    //foreach($_FILES['filespic']['tmp_name'] as $key => $error ){
        
//		if ($error != UPLOAD_ERR_OK) {
//            $errors[] = $_FILES['filespic']['name'][$key] . ' was not uploaded.';
//            continue;
//        }
        $file_name = $_FILES['filespic']['name'];
        $file_size = $_FILES['filespic']['size'];
        $file_tmp  = $_FILES['filespic']['tmp_name'];
        //$file_type = $_FILES['files']['type'][$key];
		$username = $current_user;
		//echo ' <br>'. ' <br>'. ' <br>'. ' <br>';
		//echo 'file name ' . $file_name . ' <br>';
		//echo 'file size ' . $file_size . ' <br>';
		//echo 'file tmp ' . $file_tmp. ' <br>';
        if($file_size > 2097152){ //limits the file size to just 2MB which is equal to 2097152 bytes
            $errors[] = 'File size must be less than 2 MB';
            continue;
        }
        try{
			$imgExt = strtolower(pathinfo($file_name,PATHINFO_EXTENSION)); // using strtolower makes it more efficient when comparing cos everything becomes lowercase
			if(($imgExt == 'jpg') || ($imgExt == 'JPG')){
            $user = $current_user;
            $userpic = $user."_"."pic.".$imgExt; // gives the file a customname defined by you before it is saved in the server
			
            $stmt->bindParam( ':FILE_NAME', $userpic , PDO::PARAM_STR );
            $stmt->bindParam( ':FILE_SIZE', $file_size, PDO::PARAM_STR );
            $stmt->bindParam( ':USERNAME', $username, PDO::PARAM_STR );
            $stmt->execute();
            $desired_dir="../../assets/images"; //this specifies the directory within which the image is to be saved
            
            //$imgExt = strtolower(pathinfo($file_name,PATHINFO_EXTENSION));
            //$appno = $row['appno'];
            //$userpic = "userr_".$user."_"."pic.".$imgExt;
            if(is_dir($desired_dir)==false){ //checks if a directory exists
                mkdir($desired_dir, 0700);// Creates a directory if it does not exist
            }
            if(is_file($desired_dir.'/'.$file_name)==false){
                move_uploaded_file($file_tmp,$desired_dir.'/'.$userpic);
				//echo 'desired dir ' .$desired_dir;
            }else{    //rename the file if another one of the same name exist
                $new_file=$desired_dir.'/'.$userpic.time(); //concantenates the value of the time function to the filename to  
				//echo 'new file '.$new_file;
                move_uploaded_file($file_tmp,$new_file) ;               
            } } else { $picerror = "Please upload a .jpg file as your image"  ;  }
        }catch(PDOException $e){
            $errors[] = $userpic . 'not saved in the database.';
            echo $e->getMessage();
        }   ?>
	
