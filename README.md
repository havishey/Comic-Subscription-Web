# XKCD subscrition 

Tables of Content
1. [Project Aim](#project-aim)
    * [Built with](#built-with)

    * [Services Used](#services-used)

3. [Live Demo](#live-demo)
4. [Problems Faced](#problems-faced)
5. [Assumptions](#assumptions)
6. [About Developer](#about-developer)


 ## Project Aim
 
 The aim of Project is to develop a subscription web application which takes verified email of the user and send a random xkcd comics to the user email id every 5 minutes .

The following points are taken care of during the development:
* Web application is made from basic development langauges .
* No framework of either of the langauges-html, css, javascript, php ([Version details](#built-with)) are used .
* In addition no libraries for php is used .
* Also email verification is done using One Time Password .
* Validation is done on both front and back end .
* Sanitization in php is implemented for any type of injection .

 
 ### Built With
 1. Back end
      * Whole backend script is done in pure PHP only .
      * PHP version - PHP 8.0.8 .
 
 2. Front end 
      * For the front page HTML and CSS styling is used.
      * HTML 5 .
      * CSS 3 .
      * Front end scripting is done with Vanilla Javascript .
      
3. Database
      * MySQL is used Database.
      * MySQL version - MySQL 8.0.26 .
 
  ### Services Used
  1. Webhosting - 000Webhost
  2. Cron-job scheduler - Cron-job.org
  3. For Database - AWS RDS services
  4. Mail services - Sendgrid


## Live Demo
* Live Demo Link: https://demo-havi.000webhostapp.com
      

## Problems Faced 
* Gmail was stripping many tags from my html template, solved by following gmail rules.
* 000wbhost had multiple problems like uploading zip files and accessing the ftp(due to server issues), Solved from 000webhost forums.
* Expiry timer issue due to not clearing the timeout properly.
* Small issues while programming, Debugged.

## Assumptions
* OTP verification for subscribing and link verification during unsubscribing, Could do email link verification also but assumed OTP would be nice.

## About Developer
* Email address - havivagadia@gmail.com


  

