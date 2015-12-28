<?php
/**
 * Copyright 2015-2016 KickBoard International OPC Pvt Ltd. All rights reserved.
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * 1. Redistributions of source code must retain the above copyright notice, this
 * list of conditions and the following disclaimer.
 * 2. Redistributions in binary form must reproduce the above copyright notice,
 * this list of conditions and the following disclaimer in the documentation
 * and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE KICKBOARD INTERNATIONAL OPC PVT LTD ``AS IS''
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO,
 * THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL KICKBOARD INTERNATIONAL OPC PVT LTD OR
 * CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL,
 * EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT
 * OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING
 * IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY
 * OF SUCH DAMAGE.
 *
 * shipit php library to send push notification, create autopush, create preset message and
 * extracting statistics for various user/notification data.
 * @category  Library
 * @author gdata
 */

class shipit
{

	private $msgData;
 	private $host = "http://api.shipit.mobi/RestApi/";
	public $connectTimeout = 0;
	public $timeout = 0;
	private $sslVerifypeer = false;

	public function __construct($login=null, $appSecret=null) {
		if($login)
			$this->msgData["userName"] = $login;
		if($appSecret)
			$this->msgData["shipitSecretKey"] = $appSecret;
	}

	/**
	 * Set application's secret key
	 * @param	string	$appSecret	shipit Application Secret.
	 */
	public function setApp($appSecret) {
		$this->msgData["shipitSecretKey"] = $appSecret;
		return $this;
	}

	/**
	 * set preset message ID if you want to use for this notification
	 * @param string $ID
	 */
	public function setPresetMessageID($ID) {
		if(isset($this->msgData["presetMessageName"]))
		{
			echo("Can't set presetMessageID when creating presetMessage");
			return;
		}
		$this->msgData["presetMessageID"] = $ID;
		return $this;
	}

	/**
	 * If you want to store this notification as preset message provde some name
	 * @param string $name
	 */
	public function setStorePresetMsg($name) {
		if(isset($this->msgData["presetMessageID"]))
		{
			echo("Can't set presetMessageID when creating presetMessage");
			return;
		}
		$this->msgData["presetMessageName"] = $name;
		return $this;
	}

	/**
	 * Provide ID of already created RichPus
	 * @param string $ID
	 */
	public function setRichPushID($ID) {
		$this->msgData["richPushID"] = $ID;
		return $this;
	}

	/**
	 * Provide pushtoken if you want to send message to specific push token
	 * @param string/array $pushToken
	 */
	public function setpushToken($pushToken) {
		if(!is_array($pushToken))
			$pushToken = array($pushToken);
		$this->msgData["pushToken"] = $pushToken;
		return $this;
	}

	/**
	 * Provide channelID if you want to send message to specific channelID
	 * @param string/array $channelID
	 */
	public function setChannelID($channelID) {
		if(!is_array($channelID))
			$channelID = array($channelID);
		$this->msgData["channelID"] = $channelID;
		return $this;
	}

	/**
	 * Provide one or more platform for which you want to send the message
	 * 0=>Android
	 * 1=>iOS
	 * @param string/array $platform
	 */
	public function setPlatform($platform) {
		if(is_array($platform) != true){
			$platform = array($platform);
		}
		$this->msgData['platform'] = $platform;
		return $this;
	}

	/**
	 * Set the message you want to send for
	 * @param string $msg
	 */
	public function setMessage($msg) {
		$this->msgData["message"] = $msg;
		return $this;
	}

	/**
	 * Set the date & time when you want to send this message. Time should be in UTC
	 * format i.e. YYYY-MM-DDTHH:mm:ssZ
	 * @param string $time
	 */
	public function setMsgSendTime($time) {
		$this->msgData["sendDate"] = $time;
		return $this;
	}

	/**
	 * Enable to push notification according to user timezone
	 * @param boolean $enable
	 */
	public function setUserTimeZone($enable) {
		if($enable)
			$this->msgData["userTimeZoneSensitive"] = true;
		else
			$this->msgData["userTimeZoneSensitive"] = false;
		return $this;
	}

	/**
	 * Provide scheduling if you want to autopush this message e.g.
	 * $scheduling = array("endDate"=>"2016-08-21T07:56:00Z", "frequency"=>array('W'=>array("Mon", "Fri", "Sat", "Thu")));
	 * @param array $schedule
	 */
	public function setScheduling($schedule)
	{
		$scdul = array();
		if(isset($schedule["frequency"]) && is_array($schedule["frequency"]))
			$scdul["frequency"] = $schedule["frequency"];
		else {
			echo "frequency should be array e.g.\n";
			echo "array('D') \n";
			echo "array('W'=>array('Mon', 'Fri', 'Sat', 'Thu')) \n";
			echo "array('M'=>array(1, 2, 31), 'W'=>array('Mon', 'Fri', 'Sat', 'Thu')) \n";
			return false;
		}
		if(isset($schedule["endDate"]))
			$scdul["endDate"] = $schedule["endDate"];
		$this->msgData["scheduling"] = $scdul;
		return $this;
	}

	/**
	 * Provide name if you want to store this message as Auto push
	 * @param string $name
	 */
	public function setAutoPush($name)
	{
		$this->msgData["autoPushName"] = $name;
		return $this;
	}

	/**
	 * Details of segment to filter the push notification audience
	 * @param string/array $segIDs
	 */
	public function setSegment($segIDs)
	{
		if(!is_array($segIDs))
			$segIDs = array($segIDs);
		$this->msgData["segmentID"] = $segIDs;
		return $this;
	}

	/**
	 * Details of geozone to send message only to user who are in specified geozone
	 * @param string/array $geoIDs
	 */
	public function setGeoZone($geoIDs)
	{
		if(!is_array($geoIDs))
			$geoIDs = array($geoIDs);
		$this->msgData["geoZoneID"] = $geoIDs;
		return $this;
	}

	/**
	 * Timezone in which you want to send the message as http://php.net/manual/en/timezones.php
	 * @param string $tzone
	 */
	public function setTimeZone($tzone)
	{
		$this->msgData["timeZone"] = $tzone;
		return $this;
	}

	/**
	 * URL which you want to open when user open any notification.
	 * @param string $url
	 */
	public function setLink($url)
	{
		$this->msgData["link"] = $url;
		return $this;
	}

	/**
	 * Tags value and type to filter your audience for the notification
	 * @param array $tags
	 * @param string $condition (ORTRUE, ORFALSE, ANDTRUE, ANDFALSE)
	 */
	public function setTag($tags, $condition = "ORTRUE")
	{
		if(!is_array($tags)){
			echo "Tags must be given in array (tagName, operator, tagValue) e.g. \n";
			echo "array(array('tagNumber', 'Eq', 20), array('tagBool', 'is', true), array('tagBool', 'is', true)) \n";
			return;
		}
		if(count($tags) > 0){
			$this->msgData['tags'] = $tags;
		}
		$this->msgData["tags"] = $tags;
		$this->msgData["tagsLogicalCond"] = $condition;
		return $this;
	}

	/**
	 * Provide parameter details specific to android device for this notification
	 * @param string $title
	 * @param string $sound
	 * @param string $led
	 * @param number $ttl
	 */
	public function setAndSpecific($title=null, $sound="device", $led="device", $ttl=86400){
		if($title != null)
			$this->msgData["androidFeature"]["androidTitle"] = $title;
		if($sound != "device")
			$this->msgData["androidFeature"]["androidSound"] = $sound;
		if($led !== "device")
			$this->msgData["androidFeature"]["androidLed"] = $led;
		if($ttl !== 86400)
			$this->msgData["androidFeature"]["androidGcmTTL"] = $ttl;
		return $this;
	}

	/**
	 * Provide parameter details specific to iOS device for this notification
	 * @param string $contentAvl
	 * @param string $sound
	 * @param string $category
	 * @param number $ttl
	 * @param number $badge
	 */
	public function setiOSSpecific($contentAvl=null, $sound="device", $category=null, $ttl=86400, $badge=1){
		if($contentAvl != null)
			$this->msgData["iOSFeature"]["iOSContentAvailable"] = $contentAvl;
		if($sound != "device")
			$this->msgData["iOSFeature"]["iOSSound"] = $sound;
		if($category !== null)
			$this->msgData["iOSFeature"]["iOSCategory"] = $category;
		if($ttl !== 86400)
			$this->msgData["iOSFeature"]["iOSTTL"] = $ttl;
		if($badge !== 1)
			$this->msgData["iOSFeature"]["iOSBadge"] = $badge;
		return $this;
	}

	/**
	 * If you want the notification to be silent. Default is true.
	 * @param boolean $enable
	 */
	public function setSilent($enable)
	{
		if($enable)
			$this->msgData["silent"] = true;
		else
			$this->msgData["silent"] = false;
		return $this;
	}

	/**
	 * If you want device to vibrate at time of notification. Default is true.
	 * @param unknown $enable
	 */
	public function setVibrate($enable)
	{
		if($enable)
			$this->msgData["vibrate"] = true;
		else
			$this->msgData["vibrate"] = false;
		return $this;
	}

	/**
	 * Custom data which you want to send as a payload in this notification.
	 * @param array $data
	 */
	public function setCustomData($data, $key=null)
	{
		if(!is_array($data))
			$data = array($data);
		$this->msgData["customJson"] = $data;
		if($key)
			$this->msgData["customJsonKey"] = $key;
		return $this;
	}

	/**
	 * Start date from where you want to collect stats.
	 * @param string $date
	 */
	public function setStatsStartDate($date){
		$this->msgData["startDate"] = $date;
		return $this;
	}

	/**
	 * End date till which you want to collect stats.
	 * @param string $date
	 */
	public function setStatsEndDate($date){
		$this->msgData["endDate"] = $date;
		return $this;
	}

	/**
	 * Enable/Disable testPush flag to send notification only to test devices.
	 * @param boolean $testPush
	 */
	public function setTestPush($testPush){
		$this->msgData["testPush"] = $testPush;
		return $this;
	}

	/**
	 * Set the loginID for collecting user level stats.
	 * @param string login
	 */
	public function setLoginID($login){
		$this->msgData["userName"] = $login;
		return $this;
	}

	/**
	 * Send request to server for given command
	 * @param unknown $method
	 * @param unknown $cmd
	 * @param unknown $data
	 * @return array
	 */
	private function sendRequest($method, $cmd, $data) {

		if(!isset($data["shipitSecretKey"]) || empty($data["shipitSecretKey"]))
			return false;

		$host = $this->host.$cmd;
		if(isset($data["silent"]) && ($data["silent"] == true))
		{
			if(!isset($data["androidFeature"]["androidSound"]))
				$data["androidFeature"]["androidSound"] = $data["silent"];
			if(!isset($data["iOSFeature"]["iOSSound"]))
				$data["iOSFeature"]["iOSSound"] = $data["silent"];
		}
		$jsonData = json_encode($data);
		$ci = curl_init();
		$headers = array(
			'X-SHIPIT-SECRET:' . $this->msgData["shipitSecretKey"],
			'Content-Type: application/json',
			'Content-Length: ' . strlen($jsonData)
		);

		curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, $this->connectTimeout);
		curl_setopt($ci, CURLOPT_TIMEOUT, $this->timeout);
		curl_setopt($ci, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, $this->sslVerifypeer);
		curl_setopt($ci, CURLOPT_HTTPHEADER, $headers );
		curl_setopt($ci, CURLOPT_HEADER, FALSE);

		switch ($method) {
		case 'POST':
			curl_setopt($ci, CURLOPT_POST, TRUE);
			if (!empty($jsonData)) {
				curl_setopt($ci, CURLOPT_POSTFIELDS, $jsonData);
			}
			break;
		case 'PUT':
			curl_setopt($ci, CURLOPT_CUSTOMREQUEST, "PUT");
			if (!empty($jsonData)) {
				curl_setopt($ci, CURLOPT_POSTFIELDS, $jsonData);
			}
			break;
		case 'DELETE':
			curl_setopt($ci, CURLOPT_CUSTOMREQUEST, 'DELETE');
			if (!empty($jsonData)) {
				$url = "{$url}?{$jsonData}";
			}
			break;
        }

		curl_setopt($ci, CURLINFO_HEADER_OUT, TRUE );
		curl_setopt($ci, CURLOPT_URL, $host);
		$content = curl_exec($ci);
		$response = curl_getinfo($ci);
		$content = json_decode($content);
		if($response['http_code'] != 200) {
			$res['status'] = 'ERROR';
			$res['code'] = $response['http_code'];
		}else{
			$res['status'] = 'OK';
			$res['code'] = $response['http_code'];
			$res['data'] = $content;
		}
		curl_close ($ci);
		return $res;
	}

	/**
	 * To create auto Push Notification
	 * @return array
	 */
	public function autoPush() {
		$response = $this->sendRequest( 'POST' , 'createAutoPush', $this->msgData);
		return $response;
	}

	/**
	 * To create Push Notification
	 * @return array
	 */
	public function sendPush() {
		$response = $this->sendRequest( 'POST' , 'createNotification', $this->msgData);
		return $response;
	}

	/**
	 * To create preset Message
	 * @return array
	 */
	public function presetMsg() {
		$response = $this->sendRequest( 'POST' ,'createPresetMessage', $this->msgData);
		return $response;
	}

	static function cmpTimeInt($a, $b) {
		return strcmp($a["TimeIntervalInfo"], $b["TimeIntervalInfo"]);
	}

	/**
	 * This function will return stats of given app. It will tell how many Notification
	 * sent and how many Notification open i.e. notification opened by users. This will
	 * also tell how many times user opened your app who had enabled/disable notification
	 * @return Ambigous <multitype:, boolean, mixed>
	 */
	public function appStats(){
		$response = $this->sendRequest( 'POST' ,'readAppStats', $this->msgData);
		if(!isset($response["data"]->{'Data'}))
			return $response;

		$res = $response["data"];
		$result = array();
		$i=0;

		$count = isset($res->{'AppStats'}) ? count($res->{'AppStats'}) : 0;
		if(isset($res->{'PushStats'}))
		{
			foreach ($res->{'PushStats'} as $pushStats)
			{
				$found = false;
				for($j=0; $j<$count; $j++)
				{
					if((isset($res->{'AppStats'}[$j])) && ($pushStats->{'TimeIntervalInfo'} == $res->{'AppStats'}[$j]->{'TimeIntervalInfo'}))
					{
						$result[$i]["TimeIntervalInfo"] = $pushStats->{'TimeIntervalInfo'};
						$result[$i]["AndroidPushSent"] = $pushStats->{'AndroidPushSent'};
						$result[$i]["IosPushSent"] = $pushStats->{'IosPushSent'};
						$result[$i]["AndroidPushOpen"] = $pushStats->{'AndroidPushOpen'};
						$result[$i]["IosPushOpen"] = $pushStats->{'IosPushOpen'};
						$result[$i]["AndroidAppOpenNotifDis"] = $res->{'AppStats'}[$j]->{'AndroidAppOpenNotifDis'};
						$result[$i]["AndroidAppOpenNotifEnb"] = $res->{'AppStats'}[$j]->{'AndroidAppOpenNotifEnb'};
						$result[$i]["IosAppOpenNotifDis"] = $res->{'AppStats'}[$j]->{'IosAppOpenNotifDis'};
						$result[$i]["IosAppOpenNotifEnb"] = $res->{'AppStats'}[$j]->{'IosAppOpenNotifEnb'};
						unset($res->{'AppStats'}[$j]);
						$found = true;
						break;
					}
				}
				if(!$found)
				{
					$result[$i]["TimeIntervalInfo"] = $pushStats->{'TimeIntervalInfo'};
					$result[$i]["AndroidPushSent"] = $pushStats->{'AndroidPushSent'};
					$result[$i]["IosPushSent"] = $pushStats->{'IosPushSent'};
					$result[$i]["AndroidPushOpen"] = $pushStats->{'AndroidPushOpen'};
					$result[$i]["IosPushOpen"] = $pushStats->{'IosPushOpen'};
					$result[$i]["AndroidAppOpenNotifDis"] = 0;
					$result[$i]["AndroidAppOpenNotifEnb"] = 0;
					$result[$i]["IosAppOpenNotifDis"] = 0;
					$result[$i]["IosAppOpenNotifEnb"] = 0;
				}
				$i++;
			}
		}
		if($count)
		{
			foreach ($res->{'AppStats'} as $appStats)
			{
				$result[$i]["TimeIntervalInfo"] = $appStats->{'TimeIntervalInfo'};
				$result[$i]["AndroidPushSent"] = 0;
				$result[$i]["IosPushSent"] = 0;
				$result[$i]["AndroidPushOpen"] = 0;
				$result[$i]["IosPushOpen"] = 0;
				$result[$i]["AndroidAppOpenNotifDis"] = $appStats->{'AndroidAppOpenNotifDis'};
				$result[$i]["AndroidAppOpenNotifEnb"] = $appStats->{'AndroidAppOpenNotifEnb'};
				$result[$i]["IosAppOpenNotifDis"] = $appStats->{'IosAppOpenNotifDis'};
				$result[$i]["IosAppOpenNotifEnb"] = $appStats->{'IosAppOpenNotifEnb'};
			}
		}
		if(count($result))
		{
			usort($result, array("shipit", "cmpTimeInt"));
			$file = "appStats_".gmdate("Y-m-d_H-i-s").".csv";
			$output = fopen($file, 'w');
			fputcsv($output, array('TimeIntervalInfo', 'AndroidPushSent', 'IosPushSent', 'AndroidPushOpen', 'IosPushOpen', 'AndroidAppOpenNotifDis', 'AndroidAppOpenNotifEnb', 'IosAppOpenNotifDis', 'IosAppOpenNotifEnb'));
			foreach ($result as $res)
			{
				if(!$res['AndroidPushSent'])
					$res['AndroidPushSent'] = 0;
				if(!$res['IosPushSent'])
					$res['IosPushSent'] = 0;
				if(!$res['AndroidPushOpen'])
					$res['AndroidPushOpen'] = 0;
				if(!$res['IosPushOpen'])
					$res['IosPushOpen'] = 0;
				if(!$res['AndroidAppOpenNotifDis'])
					$res['AndroidAppOpenNotifDis'] = 0;
				if(!$res['AndroidAppOpenNotifEnb'])
					$res['AndroidAppOpenNotifEnb'] = 0;
				if(!$res['IosAppOpenNotifDis'])
					$res['IosAppOpenNotifDis'] = 0;
				if(!$res['IosAppOpenNotifEnb'])
					$res['IosAppOpenNotifEnb'] = 0;
				fputcsv($output, array($res['TimeIntervalInfo'], $res['AndroidPushSent'], $res['IosPushSent'], $res['AndroidPushOpen'], $res['IosPushOpen'],
										$res['AndroidAppOpenNotifDis'], $res['AndroidAppOpenNotifEnb'],$res['IosAppOpenNotifDis'], $res['IosAppOpenNotifEnb']));
			}
			fclose($output);
		}
		unset($response["data"]);
		return $response;
	}

	/**
	 * This function will return stats of given user. It will tell how many Notification
	 * sent and how many Notification open i.e. notification opened by users
	 * @return Ambigous <multitype:, boolean, mixed>
	 */
	public function userStats()
	{
		$response = $this->sendRequest( 'POST', 'userStats', $this->msgData);
		if(!isset($response["data"]->{'Data'}))
			return $response;

		$res = $response["data"]->{'Data'};
		$result = array();
		$i=0;
		foreach ($res as $stats)
		{
			$result[$i]["TimeIntervalInfo"] = $stats->{'TimeIntervalInfo'};
			$result[$i]["AndroidPushSent"] = $stats->{'AndroidPushSent'};
			$result[$i]["IosPushSent"] = $stats->{'IosPushSent'};
			$result[$i]["AndroidPushOpen"] = $stats->{'AndroidPushOpen'};
			$result[$i]["IosPushOpen"] = $stats->{'IosPushOpen'};
			$i++;
		}
		if(count($result))
		{
			$file = "userStats_".date("Y-m-d_H-i-s").".csv";
			$output = fopen($file, 'w');
			fputcsv($output, array('TimeIntervalInfo', 'AndroidPushSent', 'IosPushSent', 'AndroidPushOpen', 'IosPushOpen'));
			foreach ($result as $res)
			{
				if(!$res['AndroidPushSent'])
					$res['AndroidPushSent'] = 0;
				if(!$res['IosPushSent'])
					$res['IosPushSent'] = 0;
				if(!$res['AndroidPushOpen'])
					$res['AndroidPushOpen'] = 0;
				if(!$res['IosPushOpen'])
					$res['IosPushOpen'] = 0;
				fputcsv($output, array($res['TimeIntervalInfo'], $res['AndroidPushSent'], $res['IosPushSent'], $res['AndroidPushOpen'], $res['IosPushOpen']));
			}
			fclose($output);
		}
		unset($response["data"]);
		return $response;
	}


}
