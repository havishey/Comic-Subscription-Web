<?php
    session_start();
    require __DIR__.'/Sendgrid/sendgrid-php.php';

    //Parsing credentials
    $creds = parse_ini_file(__DIR__.'/../Configuration/Config.ini');

    //connecting to database
    $link = mysqli_connect($creds['HOST'],$creds['USERNAME'],$creds['PASSWORD'],$creds['DBNAME']);

    //Declaring response variables
    $status = 'SUCCESS';
    $message = '';
    $email_error ='';



    //check if name and email is set in post array
    if (isset($_POST['name']) && isset($_POST['email'])){


        //Sanitizing and validating input
        $name = filter_var(mysqli_real_escape_string($link, strip_tags($_POST['name'])), FILTER_SANITIZE_STRING);
        $email = filter_var(mysqli_real_escape_string($link, strip_tags($_POST['email'])), FILTER_SANITIZE_EMAIL);


        //Capitalizing Name and lowering the email
        $name = ucfirst($name);
        $email = strtolower($email);


        //Check email is valid
        if (filter_var($email,FILTER_VALIDATE_EMAIL)){

            //inserting values in database and storing id in session
            $sql = "call add_users('".$name."','".$email."',@u_id);";
            $result = mysqli_query($link,$sql);
            $result1 = mysqli_query($link,'select @u_id as ID');
            $row =mysqli_fetch_assoc($result1);
            $_SESSION['ID'] = $row['ID'];

            //Checking if given mail is subscribed or not
            $check_sub_sql = "call check_subscribed('".$_POST['email']."',@subscribed);";
            $check_sub_result = mysqli_query($link,$check_sub_sql);
            $check_sub_result1 = mysqli_query($link,'select @subscribed as subscribed');
            $check_sub_row =mysqli_fetch_assoc($check_sub_result1);
            $is_subscribed = $check_sub_row['subscribed'];

            //If subscribed status Fail
            if ($is_subscribed=='1'){
                $status = 'FAIL';
                $message = 'This Email is already subscribed';
            }

            else{

                //OTP creation

                $str = rand(100,1000000);
                //padding function for otp 4 digits
                $OTP = str_pad($str,6,'0',STR_PAD_LEFT);

                
                //Check for if Server time is set
                if (isset($_SERVER['REQUEST_TIME'])){
                    $timestamp_generate = $_SERVER['REQUEST_TIME'];
                }

                else{
                    $timestamp_generate = time();
                }

                $expiry = $timestamp_generate + $creds['TIMEINTERVAL'];
                //setting default timezone to ist
                date_default_timezone_set('UTC');
                $current_time = date('Y/m/d H:i:s',$timestamp_generate);
                $expiry_time = date('Y/m/d H:i:s',$expiry);

                //Adding OTP with id in Database
                $otp_sql = "call add_otp('".$_SESSION["ID"]."','".$OTP."','".$expiry_time."');";
                $otp_result = mysqli_query($link,$otp_sql);

                //fetching info from database to send email
                $fetch_sql  = "call get_user_info('".$_SESSION['ID']."',@name,@email,@uotp);";
                $fetch_result = mysqli_query($link,$fetch_sql);
                $fetch_result1 = mysqli_query($link,"select @name as u_name,@email as u_email, @uotp as u_otp;");
                $fetch_row = mysqli_fetch_assoc($fetch_result1);
            
                //Getting contents of OTP mail template
                $otp_html = file_get_contents('OTP_mail.html');

                //Replacing dynamic variable
                $otp_html = str_replace('{{u_otp}}',$fetch_row['u_otp'],$otp_html);


                //sendgrid mail intergration
                $sendgrid = new \SendGrid($creds['SENDGRID_API_KEY']);

                $email = new \SendGrid\Mail\Mail(); 
                $email->setFrom($creds['SENDER_EMAIL'], 'XKCD comics');
                $email->setSubject('OTP');
                $email->addTo($fetch_row['u_email'], $fetch_row['u_name']);
            
                
                $email->addContent('text/html', $otp_html);


                try{
                    $response = $sendgrid->send($email);
                }
                catch(Exception $e){
                    $e->getMessage();
                    $email_error = $e;
                    $status = 'FAIL';
                }
            } 
        }    
            
        else{

            //Response variables
            $status = 'FAIL';
            $message = 'Invalid mail';
            
        }

    }

    //Response

    
    echo json_encode(
        array(

            'status' => $status,
            'message' => $message,
            'email_error' => $email_error

        )
    );



?>
