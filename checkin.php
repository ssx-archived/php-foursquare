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
$latitude = 53.010.rand(0,200);
$longitude = -2.228.rand(0,200);
$venue = "943157";

// Also needed are your FourSquare username and password
$username = "user@email.tld";
$password = "password";

// We now generate a base64_encode of this to send as the Basic
// Auth header in our checkin request
$auth = base64_encode($username.":".$password);
$request = "vid=".$venue."&private=0&geolat=".$latitude."&geolong=".$longitude;
$request = "POST /v1/checkin HTTP/1.1\r\nHost: api.foursquare.com\r\nUser-Agent: Mozilla/5.0 (iPhone; U; CPU like Mac OS X; en) AppleWebKit/420+(KHTML, like Gecko) Version/3.0 Mobile/1C10 Safari/419.3\r\nContent-Type: application/x-www-form-urlencoded\r\nAuthorization: Basic ".$auth."\r\nContent-length: ".(strlen($request)+2)."\r\n\r\n".$request."\r\n";

// Now actually push the request out to FourSquare's API
$fp = fsockopen("api.foursquare.com", 80, $errno, $errstr, 30);
if (!$fp) {
    echo "$errstr ($errno)<br />\n";
} else {
    fwrite($fp, $request);
    while (!feof($fp)) {
        echo fgets($fp, 128);
    }
    fclose($fp);
}
?>