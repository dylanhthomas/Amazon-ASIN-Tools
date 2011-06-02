<?php

// This script will take a list of ASINs from a form and export a CSV containing the ASIN, Title, Author(s), Publisher, and Current Price for each ASIN provided.

//Import required configuration and functions
require 'config_amazon.php';
require 'functions.asin.php';
require 'AmazonECS.class.php';

//Create instance of AmazonECS class
$amazonEcs = new AmazonECS(AWS_API_KEY, AWS_API_SECRET_KEY, 'US', AWS_ASSOCIATE_TAG );

//Form Page
if(!isset($_REQUEST['asin'])){

echo "

<html><head><title>Bulk ASIN to CSV Converter</title>
<style type='text/css'>
*{font-family:Arial,Helvetica,san-serif;}
body{width:600px;margin:0 auto; font-size:.85em;}
</style>


<h1>Bulk ASIN to CSV Converter</h1>
<p>This page will out put a CSV containing the following for each ASIN provided:</p>


<ul>
<li>ASIN</li>
<li>Title</li>
<li>Author(s)</li>
<li>Publisher</li>
<li>Current Price (Formatted)</li>
</ul>

<strong>Instructions</strong>
<ol>
<li>Type or paste a list of ASINs into the text box below.<br><em style='color:blue'>You must only put 1 ASIN per line, or it will not work.</em></li>
<li>Press the \"Download CSV\" button</li>
<li>Be patient. This script can take a while, especially if you're looking up lots of books.</li>
<li>The download should begin automatically</li>

</ol>
<strong>Notes &amp; Troubleshooting</strong>

<p><em>Amazon has a strict limit of only looking up 2,000 books per hour.<br>This is not usually a big deal, but if you're doing a lot of books, or submit the list multiple times, it ma
y give you an error.<br><br>If this happens, wait until the top of the hour and try again.  If parts of your list are fine, but others are not or need to be redone, try to just resubmit
 those portions.</em></p>


<h2>Input ASINs Here:</h2><form method='get' action='".$_SERVER['PHP_SELF']."' name='add-asin'>
<textarea name='asin' cols='25' rows='20'></textarea>
<input type='submit' name='submit' value='Download CSV'/>";
}




else {

    //Remove any leading and trailing whitespace from the submission and put all values into an array
    trimASIN($_REQUEST['asin']);

    //Output a CSV File
    header('Content-type: text/csv');
    header('Content-disposition: attachment;filename=booklist.csv');
    header('Pragma: public');
    //Output header row
    echo "ASIN,Title,Author,Publisher,Price".PHP_EOL;

    //Lookup and output individual records
    foreach($ASINs as $ASIN){
        $response = $amazonEcs->responseGroup('ItemAttributes')->lookup($ASIN);
        processASIN($response);
        //CSV Output
        echo '"'.$ASIN.'","'.$TITLE.'","'.$AUTHORS.'","'.$PUBLISHER.'","'.$PRICE.'"'.PHP_EOL;
    }
}
?>