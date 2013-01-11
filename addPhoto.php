<?php
    include('dbconfig.php');
    $target = "client_photos";
    $target = $target . basename( $_FILES['photo']['name']);

    $pic=($_FILES['photo']['name']);

    // Connects to your Database
    mysql_connect(HOST, USERNAME, PASSWORD) or die(mysql_error()) ;
    mysql_select_db(DB_NAME) or die(mysql_error()) ;

    //Writes the information to the database
    mysql_query("INSERT INTO client (personal_photo)
    VALUES (‘$pic’)") ;

    //Writes the photo to the server
    if(move_uploaded_file($_FILES['photo']['tmp_name'], $target))
    {
        //Tells you if its all ok
        echo "The file ". basename( $_FILES['uploadedfile']['name']). " has been uploaded, and your information has been added to the directory";
    }
    else {
        //Gives and error if its not
        echo "Sorry, there was a problem uploading your file.";
    }
?>
