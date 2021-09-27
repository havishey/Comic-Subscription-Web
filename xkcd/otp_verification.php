<?php

    session_start();

    //response variables
    $otp_verify=false;
    $message='';

    //connecting to database
    $creds = parse_ini_file(__DIR__.'/../Configuration/Config.ini');
    $link = mysqli_connect($creds['HOST'],$creds['USERNAME'],$creds['PASSWORD'],$creds['DBNAME']);

    //calling stored procedure from db for verifying otp
    if (isset($_POST['otp'])){
                    $otp = filter_var(mysqli_real_escape_string($link, strip_tags($_POST['otp'])), FILTER_SANITIZE_STRING);

                    $verify_sql = "call verify_otp('".$_SESSION['ID']."','".$otp."',@verified);";
                    $verify_result = mysqli_query($link, $verify_sql);
                    $verify_result1 = mysqli_query($link, "select @verified as verified;");
                    $verify_row = mysqli_fetch_assoc($verify_result1);

                    $otp_verify = $verify_row['verified'];

                }
    else{
        $otp_verify = false;
        $message = 'Please enter valid otp';
    }

        echo json_encode(
            array(
 
                'otp_verify' => $otp_verify,
                'message'=> $message
            )
        );

?>