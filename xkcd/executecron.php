<?php

    //Parsing credentials
    $creds = parse_ini_file(__DIR__.'/../../Configuration/Config.ini');

    //connecting to database
    $link = mysqli_connect($creds['HOST'],$creds['USERNAME'],$creds['PASSWORD'],$creds['DBNAME']);
 
    
    require __DIR__.'/../Sendgrid/sendgrid-php.php';

    //Response
    $status = 'SUCCESS';

    //Call to database for get all Subscribed
    $subscribed_sql  = 'call get_subscribed();';
    $subscribed_result = mysqli_query($link,$subscribed_sql);

    //Decalaration of hash array for hash keys
    $hash_array = Array();
  
    //Storing Subscribed mails in an array
    $storearray = Array();
    while ($row =mysqli_fetch_assoc($subscribed_result)){

        $storearray[$row['user_id']] = $row['user_email_address'];

        //Using sha1 for hash keys
        $hash_array[$row['user_id']] = sha1($row['user_email_address'].$row['user_id'].$creds['SALT']);

    }
  
    //Sending mail to every subscribed email
    foreach ($storearray as $id => $email){

        //Converting the array object into a string
        $convert_email = strval($email);

        //Url for Unsubscribe verification
        $u_url = "http://demo-havi.000webhostapp.com/unsubscribe.php?hash=$hash_array[$id]&id=$id";
      
        $sendgrid = new \SendGrid($creds['SENDGRID_API_KEY']);
    
        $email = new \SendGrid\Mail\Mail(); 
        $email->setFrom($creds['SENDER_EMAIL'], 'XKCD comics');
        $email->setSubject('Your subscription comic');
        $email->addTo($convert_email);
        
        //url to return random comic url
        $url='https://c.xkcd.com/random/comic';

        //Accessing headers for the redirected url
        $headers = get_headers($url);
        $final_url = '';

        foreach ($headers as $h)
        {
        
            if (substr($h,0,10) == 'Location: ')
            {
            $final_url = trim(substr($h,10));
            break;
            }
        }

        //Getting content from random comic json
        $comic_json = file_get_contents("$final_url".'info.0.json');

        $comic_json_format = json_decode($comic_json,true);

        //Getting details from json
        $comic_img = $comic_json_format['img'];
        $comic_name = $comic_json_format['safe_title'];
        $comic_num = $comic_json_format['num'];

        //encoding comic png to send it as attachment
        $image_encode = base64_encode(file_get_contents($comic_img));


        $email->addAttachment(
            $image_encode,'application/png',
            "$comic_name".'.png',
            'attachment'
         );


        //Getting contents for mail template
        $email_html = file_get_contents(__DIR__.'/../subscription_mail.html');

        //Replacing dynamic variables
        $email_html = str_replace('{{comic-number}}',$comic_num,$email_html);
        $email_html = str_replace('{{comic-name}}',$comic_name,$email_html);
        $email_html = str_replace('{{comic-image}}',$comic_img,$email_html);
        $email_html = str_replace('{{unsubscribe-link}}',$u_url,$email_html);
        
        
        $email->addContent(
            'text/html', $email_html);

        try{
            $response = $sendgrid->send($email);
        }
        catch(Exception $e){
            $e->getMessage();
            $email_error = $e;
            $status = 'FAIL';
        } 
    }
      
    echo json_encode(
        array(
            'status' => $status
        )
    );
?>
