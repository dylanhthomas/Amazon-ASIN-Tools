<?php

/* This script on a webserver will allow you to lookup ASIN information for a large number of ASINs using a formula in a Google Docs Spreadsheet

This assumes the following:

- Row 1 in your spreadsheet is a title row.
- Cell A1 contains "ASIN"
- You put all the ASINs you want to look up in column A
- You will be retreiving 4 fields, (Title, Author(s), Publisher, Price)
(if you want to retreive more or less, just modify the formulas)

* Put the following formula in F1:

    =concatenate(index(A2:A300)&{";"})

  This will create a semi-colon separated list of all the ASINs in column A.


* Put the following formula in cell B1:

    =ImportData("http://pathtoscript/gdoc-asin-lookup-bulk.php?asin="&F1)

*/


//Uncomment for verbose errors
//error_reporting(E_ALL);
//ini_set('display_errors', 'On');


//Import required configuration and functions
require 'config_amazon.php';
require 'functions.asin.php';
require 'AmazonECS.class.php';

//Create instance of AmazonECS class
$amazonEcs = new AmazonECS(AWS_API_KEY, AWS_API_SECRET_KEY, 'US', AWS_ASSOCIATE_TAG );



function trimASINgoogle($asin){
    global $ASINs;

    //FUNCTION: remove spaces from array values
    function trim_value(&$value){$value = trim($value);}

	//separate submitted data into an array
	$ASINs = explode(";",$asin);
	//remove spaces from array values
	array_walk($ASINs, 'trim_value');
}


//Output a CSV File
header('Content-type: text/csv');
header('Content-disposition: attachment;filename=booklist.csv');
header('Pragma: public');
//Output header row
echo "Title,Author,Publisher,Price".PHP_EOL;

//Remove any leading and trailing whitespace from the submission and put all values into an array
trimASINgoogle($_REQUEST['asin']);

//Lookup and output individual records
foreach($ASINs as $ASIN){
    if (empty($ASIN)){exit;}
    $response = $amazonEcs->responseGroup('ItemAttributes')->lookup($ASIN);
    processASIN($response);
    //CSV Output
    echo '"'.$TITLE.'","'.$AUTHORS.'","'.$PUBLISHER.'","'.$PRICE.'"'.PHP_EOL;
}

?>
