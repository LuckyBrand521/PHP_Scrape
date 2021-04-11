<?php
include("config.php");
$target_path = ROOTPATH.'/' .APP_NAME. UPLOAD_PATH . basename( $_FILES['uploadedfile']['name']); 

if(move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $target_path)) {
    echo "The file ".  basename( $_FILES['uploadedfile']['name']). 
    " has been uploaded";
    $_SESSION['uploaded'] = TRUE;
    header("Location: http://localhost/".APP_NAME);
    exit();
} else{
    $_SESSION['uploaded'] = FALSE;
    echo "There was an error uploading the file, please try again!";
    header("Location: http://localhost/".APP_NAME);
    exit();
}
?>