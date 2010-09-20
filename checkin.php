#!/usr/bin/php
<?
// Foursquare Checkin Automation
// By Scott Wilcox (v0.1) http://dor.ky
//
// I wrote this script a little while ago to automatically check
// me in every day to a certain venue. This saved me time in 
// actually getting in and out of my favourite cafe. There is a
// perl version at http://compbio.cs.uic.edu/~mayank/4sq.html
//
// You need a set of co-ordinates that are close to the venue
// as we need to send this in the header. We'll also need the
// venue ID that we're checking into. You need to set this to a
// latitude and longitude near to the venue
//
// Some minor settings, defaults are fine but fill in your username
// (email address usually) and your password
define("TIME_TO_SLEEP_MIN", 300);
define("TIME_TO_SLEEP_MAX",1200);
define("USER_AGENT","Mozilla/5.0 (iPhone; U; CPU like Mac OS X; en) AppleWebKit/420+(KHTML, like Gecko) Version/3.0 Mobile/1C10 Safari/419.3");
define("FOURSQUARE_USERNAME","");
define("FOURSQUARE_PASSWORD","");

// An array of venues and a latitude & longitude that are close
// to each place (this is required and may require a little
// manual work to get each one). You could plug into the 
// Google GeoCoder, but you would break the API terms.
$venues = array();
$venues[] = array("943157","53.028962","-2.210071"); // Asda, Wolstanton
$venues[] = array("2403240","53.012016","-2.226447"); // Caffe Java, Newcastle
$venues[] = array("2618969","53.010097","-2.226119"); // Subway, Newcastle
$venues[] = array("2526286","53.023949","-2.173053"); // Hanley Bus Station, Hanley
$venues[] = array("2502943","53.010413","-2.224483"); // Newcastle Bus Station, Newcastle
$venues[] = array("1172819","53.009760","-2.225364"); // Hector Garcia's, Newcastle
$venues[] = array("2954870","53.042633","-2.251377"); // Co-Operative, Chesterton
$venues[] = array("1492167","53.013205","-2.229190"); // Sainsburys, Newcastle
$venues[] = array("3294927","53.032658","-2.239399"); // Esso, Chesterton
$venues[] = array("2949365","53.014756","-2.175983"); // Hanley Park, Hanley

// Shuffle the array so we're not checking in the same order over
// and over. Remember, randomisation is the key to this. You don't
// want to look like a robot
shuffle($venues);

foreach ($venues as $venue) {
	// Now adjust the latitude and longitude slightly so that we are not
	// checking in at the same lat/lng each time and send off the HTTP request to 
	// the API and check in each location
	$request = "vid=".$venue[0]."&private=0&geolat=".substr($venue[1],0,-3).rand(100,300)."&geolong=".substr($venue[2],0,-3).rand(100,300);
	$request = "POST /v1/checkin HTTP/1.1\r\nHost: api.foursquare.com\r\nUser-Agent: ".USER_AGENT."\r\nContent-Type: application/x-www-form-urlencoded\r\nAuthorization: Basic ".base64_encode(FOURSQUARE_USERNAME.":".FOURSQUARE_PASSWORD)."\r\nContent-length: ".(strlen($request)+2)."\r\n\r\n".$request."\r\n";
	
	// Now actually push the request out to FourSquare's API
	$fp = fsockopen("api.foursquare.com", 80, $errno, $errstr, 30);
	if (!$fp) {
	    echo "Error checking in to ".$venue[0]."<br />\n";
	} else {
	    fwrite($fp, $request);
	    while (!feof($fp)) {
	    	// Echo out what we get back from the API
	        echo fgets($fp, 128);
	    }
	    fclose($fp);
	}
	
	// Now pause and wait a little while before checking in again
	sleep(rand(TIME_TO_SLEEP_MIN,TIME_TO_SLEEP_MAX));
}
?>