<?php

/* This script on a webserver will allow you to lookup ASIN information using a formula in a Google Docs Spreadsheet

If you put the ASIN in cell A2, put the following formula in cell B2:

    =ImportData("http://pathtoscript/gdoc-asin-lookup-single.php?asin="&A2)

*/


//Uncomment for verbose errors
//error_reporting(E_ALL);
//ini_set('display_errors', 'On');


//Import required configuration and functions
require 'config_amazon.php';
require 'functions.asin.php';
require 'AmazonECS.class.php';



$amazonEcs = new AmazonECS(AWS_API_KEY, AWS_API_SECRET_KEY, 'US', AWS_ASSOCIATE_TAG );

$ASIN=$_REQUEST['asin'];
$response = $amazonEcs->responseGroup('ItemAttributes')->lookup($ASIN);
processASIN($response);
//CSV Output
echo '"'.$TITLE.'","'.$AUTHORS.'","'.$PUBLISHER.'","'.$PRICE.'"';

?>
