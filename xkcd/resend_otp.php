<?php
    session_start();

     //Parsing credentials
    $creds = parse_ini_file(__DIR__.'/../Configuration/Config.ini');

    //connecting to database
    $link = mysqli_connect($creds['HOST'],$creds['USERNAME'],$creds['PASSWORD'],$creds['DBNAME']);
    
    require __DIR__.'/Sendgrid/sendgrid-php.php';

    //declaring response
    $resend_status='SUCCESS';

    //otp creation    
    $str = rand(100,1000000);
    $OTP = str_pad($str,6,"0",STR_PAD_LEFT);
   
    //Check for if Server time is set
    if (isset($_SERVER['REQUEST_TIME'])){
        $timestamp_generate = $_SERVER['REQUEST_TIME'];
    }

    else{
        $timestamp_generate = time();
    }
    
    //Setting up expiry time

    $expiry = $timestamp_generate + $creds['TIMEINTERVAL'];
    date_default_timezone_set('UTC');
    $current_time = date('Y/m/d H:i:s',$timestamp_generate);
    $expiry_time = date('Y/m/d H:i:s',$expiry);
   
    //Call for resend otp
    $otp_sql = "call resend_otp('".$_SESSION["ID"]."','".$OTP."','".$expiry_time."');";
    $otp_result = mysqli_query($link,$otp_sql);
            
    //fetching user otp
    $fetch_sql  = "call get_user_info('".$_SESSION['ID']."',@name,@email,@uotp);";
    $fetch_result = mysqli_query($link,$fetch_sql);
    $fetch_result1 = mysqli_query($link,"select @name as u_name,@email as u_email, @uotp as u_otp;");
    $fetch_row = mysqli_fetch_assoc($fetch_result1);

    //Getting contents of OTP mail template
    $otp_html = file_get_contents('OTP_mail.html');

    //Replacing dynamic variable
    $otp_html = str_replace('{{u_otp}}',$fetch_row['u_otp'],$otp_html);
           
    $sendgrid = new \SendGrid($creds['SENDGRID_API_KEY']);
   
    $email = new \SendGrid\Mail\Mail(); 
    $email->setFrom($creds['SENDER_EMAIL'], "XKCD comics");
    $email->setSubject("OTP");
    $email->addTo($fetch_row['u_email'], $fetch_row['u_name']);
    
    $email->addContent('text/html', $otp_html);

    try{
        $response = $sendgrid->send($email);
    }
    catch(Exception $e){
        $e->getMessage();
        $email_error = $e;
        $resend_status = 'FAIL';
    } 
      
    echo json_encode(
        array(
            'resend_status' => $resend_status
        )
    );

?>
