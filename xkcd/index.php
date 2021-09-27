
<!DOCTYPE html>

<html>

    <head>
        <meta charset="UTF-8">
        <meta name="author" content="Havishey">
        <meta name="keyword" content="random XKCD, comics, cartoon">
        <meta name="description" content="random XKCD">
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <meta name="application-name" content="XKCD comics">

        <title>XKCD Comics</title>

        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"/>
        <link rel="preconnect" href="https://fonts.gstatic.com"/>
        <link href="https://fonts.googleapis.com/css?family=Noto+Serif:400,400i,700" rel="stylesheet"/>
        <link rel="stylesheet" href="index.css" type="text/css">
    </head>




    <body id="body">

        <h1>Welcome To XKCD Subscription</h1>
        <hr>

        <div id="form-container">

            <div id="icon"><img id="icon-img" src="email.png" ></div>
            
            <div id="info"> 
                <p>Subscribe for entertaining and fun comics on your email</p>
            </div>

            <div id="invalid_form"><span id="invalid_info"></span></div>

            <form id="primary-form" method="POST">
                    <input name= "name" class="holder" type = "text" placeholder="Name" required><br>
                    <input name= "email" class="holder" type="email" placeholder="Email Address" required><br>
                    <input name="submit-subscribe"  id="subscribe" type="submit" value="Subscribe">
                    
            </form>
            
        </div>

        <div id= "Subscribed_modal">

            <span id="Subscribed-close" >&times;</span>
            <div id="icon"><img id="Subscribed-icon" src="party-popper.png" ></div>
            <img id="Subscribed-logo" src="logo.png" alt="LOGO">
            <p id="Subscribed-message">Thank you for subscribing</p>

        </div>


        <div id="modal">
                <span id="close">&times;</span>
                <img id="modal-logo" src="logo.png" alt="LOGO">
                <p id="modal-info">One Time Password is sent to your email</pr>
                
                <div id="invalid_modal"><span id="invalid_otp"></span></div>
                
                <div id="modal-form">
                <form>

                    <p id="label-otp">One Time Password:</p>
                    <input name="OTP" id="OTP-holder" type="number" placeholder="Enter the OTP" required>
                    <input name="OTP-submit" id="OTP-submit" type="submit" value="Verify">

                </form>
                </div>
                <div id = "timer-div"> OTP expires: <span id="timer"></span> </div>
                <div>
                    <span id="resend">Resend OTP</span>
                    <p id="resend-info">Didn't receive the OTP?</p>
                </div>
        </div>

        <script type="text/javascript" src="index.js"></script>

    </body>


</html>


