//derived variable for every needed Element

var modal = document.getElementById("modal");

var sub_btn = document.getElementById("subscribe");

var close_btn = document.getElementById("close");

var form = document.getElementById("form-container");

var input_form_name = document.getElementsByClassName("holder")[0];

var input_form_email = document.getElementsByClassName("holder")[1];

var invalid_message = document.getElementById("invalid_info");

var invalid_div = document.getElementById("invalid_form");

var input_otp = document.getElementById("OTP-holder");

var btn_otp = document.getElementById("OTP-submit");

var invalid_modal = document.getElementById("invalid_modal");

var invalid_otp_message = document.getElementById("invalid_otp");

var resend_btn = document.getElementById("resend");

var Subscribed_modal = document.getElementById("Subscribed_modal");

var Subscribed_close = document.getElementById("Subscribed-close");

var name = '';

var email = '';

interval_handler = '';


//making an new object for ajax request
var xmlhttp = new XMLHttpRequest;

//making a regex for otp verification
const regex_otp = /^\d{6}$/;

//making regex for email verification
const regex_email = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;


//Script for subscribe button
sub_btn.onclick = function (){

    

    clearTimeout(interval_handler);

    var name = input_form_name.value;
    var email = input_form_email.value;
    
    document.getElementById('timer').innerHTML =
    05 + ":" + 00;
    startTimer();
   



    //Front end validation
    if(!front_end_validation(name,email)){
        return false;
    }

    //Ajax call
    xmlhttp.onload = function(){
 
        //Storing response
        var valid_response = JSON.parse(xmlhttp.responseText);

        if (valid_response.status == "SUCCESS"){

            //displaying the otp modal
            modal.style.display = "block";
            form.style.opacity = "0.5";

        }

        else{

            //Displaying invalid message according to response message

            if (valid_response.message == "This Email is already subscribed"){

                invalid_message.innerHTML ="This Email is already subscribed";
                invalid_div.style.display = 'block';
                input_form_email.focus();
                
            }      
        

            else if (valid_response.message =="Invalid mail"){

                invalid_message.innerHTML ="Invalid mail";
                invalid_div.style.display = 'block';
                input_form_email.focus();
            }


            else if (valid_response.email_error.length >0){

                invalid_message.innerHTML ="couldn't send the mail try again";
                invalid_div.style.display = 'block';
                input_form_email.focus();
            }

        }   

    }

    //Opening Request in main.php    
    xmlhttp.open("POST", "main.php",true);
    xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    xmlhttp.send("name="+name+ "&email="+ email);

    return false;
}



//Script for verify button

btn_otp.onclick = function(){
    var otp = input_otp.value;

    //frontendvalidation
    if(!check_otp(otp)){
        invalid_otp_message.style.color = "red";
        invalid_modal.style.display = "block";
        input_otp.focus();
        invalid_otp_message.innerHTML = "Invalid OTP";
        return false;
    }

    //Ajax call for otp verification
    xmlhttp.onload = function(){

        var valid_response_otp = JSON.parse(xmlhttp.responseText);
        
        //show message as per the otp_verify flag from database
        if (valid_response_otp.otp_verify == 1){
            
            invalid_otp_message.style.color = "#79C295";
            invalid_modal.style.display= "block";
            invalid_otp_message.innerHTML = "Verified";
            
            show_subscribed();
        }

        else if(valid_response_otp.otp_verify == 0){
            invalid_otp_message.style.color = "red";
            invalid_modal.style.display = "block";
            input_otp.focus();
            invalid_otp_message.innerHTML = "Invalid OTP";
        }
    }

    xmlhttp.open("POST", "otp_verification.php",true);
    xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    xmlhttp.send("otp="+otp);

    return false;
}

//function for showing a subscribed modal
function show_subscribed(){
    
    modal.style.display = "none";
    input_form_name.value = "";
    input_form_email.value= "";
    input_otp.value="";
    Subscribed_modal.style.display = "block";
    form.style.opacity = "0.5";
}


//Script for the cross button

close_btn.onclick = function(){
    
    //emtying the value when close button is clicked
    
    input_otp.value="";

    //closing modal and invalid messages
    modal.style.display = "none";
    form.style.display = "flex";
    form.style.opacity = "1";
    invalid_div.style.display = "none";
    invalid_modal.style.display = "none";

}

//Close button onclick defination
Subscribed_close.onclick = function(){

    Subscribed_modal.style.display = "none";
    form.style.display = "flex";
    form.style.opacity = "1";
    invalid_modal.style.display = "none";
    invalid_div.style.display = "none";

}

//Resend buttom function
resend_btn.onclick = function(){


    clearTimeout(interval_handler);

    document.getElementById('timer').innerHTML =
    05 + ":" + 00;
    startTimer();

    
    xmlhttp.onload = function(){

        valid_response = JSON.parse(xmlhttp.responseText);

        if (valid_response.status == "FAIL"){
            invalid_otp_message.style.color = "red";
            invalid_modal.style.display = "block";
            invalid_otp_message.innerHTML = "Could'nt send OTP please try again";
        }
    
    }

    xmlhttp.open("POST", "resend_otp.php",true);
    xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    xmlhttp.send();
    invalid_otp_message.style.color = "#79C295";
    invalid_modal.style.display= "block";
    invalid_otp_message.innerHTML = "OTP sent succesfully";

    return false;

}

//func for name and email validation
function front_end_validation(name,email){

    if(!check_name(name)){
        invalid_message.innerHTML ="Enter name";
        invalid_div.style.display = 'block';
        input_form_name.focus();
        return false;  
    }

    else if (!check_email(email)){
        invalid_message.innerHTML ="Invalid mail";
        invalid_div.style.display = 'block';
        input_form_email.focus();
        return false;
    }

    else {
        return true;
    }

}


//Individual functions for checking name, email and otp
function check_name(name){
    if (name.trim().length==0){
        return false;
    }
    else {
        return true;
    }
}

function check_email(email){
    if (email.trim().length==0){
        return false;
    }

    else if (email.match(regex_email)){
        return true;
    }

    else{
        return false;
    }
    
}

function check_otp(otp){
    if (otp.trim().length==0){
        return false;
    }

    else if (otp.match(regex_otp)){
        return true;
    }

    else{
        return false;
    }


}

function startTimer() {

    var presentTime = document.getElementById('timer').innerHTML;
    var timeArray = presentTime.split(/[:]+/);
    var m = timeArray[0];
    var s = checkSecond((timeArray[1] - 1));
    if(s==59){m=m-1}
    if(m<0){
      return
    }
    
    document.getElementById('timer').innerHTML = m + ":" + s;
  
    interval_handler = setTimeout(startTimer, 1000);
  
  
  }
  
  function checkSecond(sec) {
    if (sec < 10 && sec >= 0) {
  
        sec = "0" + sec
  
      } // add zero in front of numbers < 10
  
    if (sec < 0) {
  
        sec = "59"
  
      }
    return sec;
  }
  

