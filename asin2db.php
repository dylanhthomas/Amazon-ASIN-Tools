<?php

/* This script will take a list of ASINs and create a database record containing the ASIN, Title, Author(s), Publisher, and Current Price for each ASIN provided.

It will check to see if the ASIN exists in the database.  If it does, it will move on. It will NOT update the record.  If the ASIN does not exist in the database, it will be added
*/

//Import required configuration and functions
require 'config_db.php';
require 'config_amazon.php';
require 'functions.asin.php';
require 'AmazonECS.class.php';


//Create instance of AmazonECS class
$amazonEcs = new AmazonECS(AWS_API_KEY, AWS_API_SECRET_KEY, 'US', AWS_ASSOCIATE_TAG );

//If there is no form data, show the Form Page
if(!isset($_REQUEST['asin'])){

echo "

<html><head><title>Bulk ASIN to Database Importer</title>
<style type='text/css'>
*{font-family:Arial,Helvetica,san-serif;}
body{width:600px;margin:0 auto; font-size:.85em;}
</style>


<h1>Bulk ASIN to Database Importer</h1>
<p>This page will do a bulk import into the a database from a list of ASINs:</p>


<ul>
<li>ASIN</li>
<li>Title</li>
<li>Author(s)</li>
<li>Publisher</li>
<li>Amazon Link</li>
<li>Cover Image (if provided)
li>Current Price (Formatted)</li>
</ul>

<strong>Instructions</strong>
<ol>
<li>Type or paste a list of ASINs into the text box below.<br><em style='color:blue'>You must only put 1 ASIN per line, or it will not work.</em></li>
<li>Press the \"Import Books\" button</li>
<li>Be patient. This script can take a while, especially if you're looking up lots of books.</li>
<li>You'll be taken to a page listing what the script did.  If a book already exists in the database, it will be left alone. Otherwise, it will be added.</li>

</ol>
<strong>Notes &amp; Troubleshooting</strong>

<p><em>Amazon has a strict limit of only looking up 2,000 books per hour.<br>This is not usually a big deal, but if you're doing a lot of books, or submit the list multiple times, it may give you an error.<br><br>If this happens, wait until the top of the hour and try again.  If parts of your list are fine, but others are not or need to be redone, try to just resubmit those portions.</em></p>


<h2>Input ASINs Here:</h2><form method='get' action='".$_SERVER['PHP_SELF']."' name='add-asin'>
<textarea name='asin' cols='25' rows='20'></textarea>
<input type='submit' name='submit' value='Import Books'/>";
}




else {

trimASIN($_REQUEST['asin']);

foreach($ASINs as $ASIN){

    //Check to see if the ASIN is already in the database.
    $query = mysql_query("SELECT ASIN,TITLE from ".$table." where ASIN='".$ASIN."'");
    $rows = mysql_fetch_row($query);
    $num_rows = mysql_num_rows($rows);

    //If the ASIN is NEW, added it to the database
    if ($num_rows =='0'){
        echo "<p><strong>Added</strong>&nbsp;&nbsp;&nbsp;&nbsp;";

        $response = $amazonEcs->responseGroup('Medium')->lookup($ASIN);

        //Get Item Attributes from ASIN
        processASIN($response);

        $insert_query = mysql_query("INSERT INTO ".$table." (ASIN,TITLE,AUTHOR,PUBLISHER,URL,IMAGE,PRICE) values ('".$ASIN."','".$TITLE."','".$AUTHORS."','".$PUBLISHER."','".$URL."','".$IMAGE."','".$PRICE."')");

        //Print output
        echo "<small>".$ASIN."</small> <em><a href=".$URL.">".$TITLE."</a></em> <small>by ".$AUTHORS." published by ".$PUBLISHER."</small>".$PRICE."</p><img src='".$IMAGE."' style='display:inline-block;' />";


    }

    //If the ASIN is EXISTS, say so and move on to the next ASIN
    else{
        echo "<p><small>".$ASIN."</small> <em><u>".$rows[1]."</u></em> <small>is already in the database</small></p>";


    }
}

}
?>
