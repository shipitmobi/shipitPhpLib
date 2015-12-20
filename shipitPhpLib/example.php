<?php

require_once('shipitLib.php');
$login = "testaccount";
$appSecret = '3a4d802fabf650a67a158dswtgs34us';
$hshipit = new shipit($login, $appSecret);
$hshipit->setApp($appSecret);

/**
 * Auto Push
 */
$res = $hshipit->setAutoPush("AutoPush 8");
$sch = array("endDate"=>"2016-03-31T10:00:00Z", "frequency"=>array('D'));
$res = $hshipit->setScheduling($sch);
$res = $hshipit->setMsgSendTime("2015-11-28T14:00:00Z");
$res = $hshipit->setUserTimeZone(true);
$res = $hshipit->autoPush();
print_r($res);

/**
 *  App Stats Example
 */
$hshipit->setStatsStartDate("2015-10-16T00:00:00Z");
$hshipit->setStatsEndDate("2015-11-25T00:00:00Z");
$res = $hshipit->appStats();

/**
 *  User Stats Example
 */
$hshipit->setStatsStartDate("2015-10-16T00:00:00Z");
$hshipit->setStatsEndDate("2015-11-25T00:00:00Z");
$res = $hshipit->userStats();
?>
