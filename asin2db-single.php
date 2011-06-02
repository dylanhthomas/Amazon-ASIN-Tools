<?php
//Import required configuration and functions
require 'config_db.php';
require 'config_amazon.php';
require 'functions.asin.php';
require 'AmazonECS.class.php';


//Create instance of AmazonECS class
$amazonEcs = new AmazonECS(AWS_API_KEY, AWS_API_SECRET_KEY, 'US', AWS_ASSOCIATE_TAG );

//If there is no form data, show the Form Page
if(!isset($_REQUEST['asin'])){

echo "<h1>Add Book by Amazon ID (ASIN)</h1>
<p>To retrieve the book metadata directly from Amazon, just put in the book's ASIN (Amazon ID number).  You'll have an opportunity to review the information and edit if need be before you add it into the database.</p>
<form method='get' action='".$_SERVER['PHP_SELF']."' name='add-asin'>
<input type='text' name='asin'>
<input type='submit' name='submit' value='Retrieve Book Metadata'/>";
}


//If an ASIN has been provided, retrieve and display the metadata, allowing the user to make edits.
else if( (isset($_REQUEST['asin'])) && (!isset($_REQUEST['action'])) ){

    $ASIN=$_REQUEST['asin'];
    $response = $amazonEcs->responseGroup('Medium')->lookup($ASIN);

    //Get Item Attributes from ASIN
    processASIN($response);


    echo "
<h1>Add Book by Amazon ID (ASIN)</h1>
<p>This is the information pulled directly from Amazon.  Edit it if you need to, then press submit.</p>

<form method='post' action='add-asin.php' name='ADD'>
		<input type='hidden' name='action' value='ADD'/>



<span style='width:100px;display:inline-block;'><strong>ASIN: </strong></span>".$ASIN."<br>
<input type='hidden' name='asin' value='".$ASIN."'>

<span style='width:100px;display:inline-block;'><strong>Title: </strong></span><input type='text' size='100' name='title' value='".$TITLE."'><br>
<span style='width:100px;display:inline-block;'><strong>Author: </strong></span><input type='text' size='100' name='author' value='".$AUTHORS."'><br>
<span style='width:100px;display:inline-block;'><strong>Publisher: </strong></span><input type='text' size='100' name='publisher' value='".$PUBLISHER."'><br>
<span style='width:100px;display:inline-block;'><strong>URL: </strong></span><input type='text' size='100' name='url' value='".$URL."'><br>
<span style='width:100px;display:inline-block;'><strong>Test URL: </strong></span> <a href=".$URL.">This is the link</a><br>
<input type='hidden' name='image' value='".$IMAGE."'><br>
<input type='submit' name='submit' value='Add Book' /></form><br><a href=index.php>Cancel/Return to list</a>.";

}



//If form data from Metadata editing form is submitted, process it and add a record to the database
else if( (isset($_REQUEST['submit'])) && ($_REQUEST['action'] == 'ADD') ){
	$query = mysql_query("INSERT INTO ".$table." (ASIN,TITLE,AUTHOR,PUBLISHER,URL,IMAGE,PRICE) values ('".$ASIN."','".$_REQUEST['title']."','".$_REQUEST['author']."','".$_REQUEST['publisher']."','".$_REQUEST['url']."','".$_REQUEST['image']."','".$_REQUEST['price']."')");

	if ($query == '1'){echo "The record was successfully added. <br><a href=index.php>Return to list</a>.";}
	else{echo "Whoops! Something went wrong.<br><a href=index.php>Return to list</a>.";}
}
?>
