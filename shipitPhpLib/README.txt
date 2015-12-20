This library can be used to call restapi call of shipit.mobi push notification
platform. This is a sample readme file for how to use this library. You can set 
various setting like tags, segments, geoZone, badge details etc.  


************ Basic Setting ********** 
store username and shipit secret key i.e. shipitAppSecretkey 
$login = "testaccount";
$appSecret = '3a4d802fabf650a67a158dswtgs34us';
$hshipit = new shipit($login, $appSecret);

You can store appsecret key later using api call.
$hshipit->setApp($appSecret);


************ Broadcast Notification ***********
/* Set message */
$res = $hshipit->setMessage("Hello how are you doing today");

/* send message according to user's timezone */
$res = $hshipit->setUserTimeZone(true);

/* Will push message immediately*/
$res = $hshipit->sendPush(); 




************ Auto Push **************
To create auto push you can do 

/* Name of AutoPush */
$res = $hshipit->setAutoPush("AutoPush 8");
 
/* Scheduling of AutoPush */
$sch = array("endDate"=>"2016-03-31T10:00:00Z", "frequency"=>array('D'));  
$res = $hshipit->setScheduling($sch);
 
/* Set message */
$res = $hshipit->setMessage("This is automessage");

/* Set time when you want to send */
$res = $hshipit->setMsgSendTime("2015-11-28T14:00:00Z");

/* send message according to user's timezone */
$res = $hshipit->setUserTimeZone(true);

/* create the auto push */
$res = $hshipit->autoPush(); 
print_r($res);




*********** Stats Example for App ***********
/* Set start date */
$hshipit->setStatsStartDate("2015-10-16T00:00:00Z");

/* Set End date */
$hshipit->setStatsEndDate("2015-11-25T00:00:00Z");

/* Get app stats */
$res = $hshipit->appStats();




*********** Stats Example for login ID ***********
/* Set start date */
$hshipit->setStatsStartDate("2015-10-16T00:00:00Z");

/* Set End date */
$hshipit->setStatsEndDate("2015-11-25T00:00:00Z");

/* Get login ID stats */
$res = $hshipit->userStats();