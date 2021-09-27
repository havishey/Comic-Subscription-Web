<?php

//getting cred form ini file and linkin DB
$creds = parse_ini_file(__DIR__.'/../Configuration/Config.ini');
$link = mysqli_connect($creds['HOST'],$creds['USERNAME'],$creds['PASSWORD'],$creds['DBNAME']);



   if (isset($_GET['id']) && isset($_GET['hash'])) {

        $id = filter_var(mysqli_real_escape_string($link, strip_tags($_GET['id'])), FILTER_SANITIZE_STRING);
        
        $fetch_sql  = "call get_user_info('".$_GET['id']."',@name,@email,@u_otp);";
        $fetch_result = mysqli_query($link,$fetch_sql);
        $fetch_result1 = mysqli_query($link,"select @email as u_email");
        $fetch_row = mysqli_fetch_assoc($fetch_result1);

        //actual hash for the following email
        $actual_hash = sha1($fetch_row['u_email'].$id.$creds['SALT']);
        
        //verify for with hash check
        if ($actual_hash == $_GET['hash']){

            $unsubscribe_sql = "call user_unsubscribe('".$id."');";
            $unsubscribe_result = mysqli_query($link, $unsubscribe_sql);
            $unsubscribe_result1 = mysqli_query($link, 'select @verified as verified;');

            //unsubscribed template
            include __DIR__.'/html_unsubscribe.html';


        }

        else{
            echo 'Invalid URL';
        }
        
    }

    else{
        echo 'Invalid URL';
    }
    
   


?>