<?php

function processASIN($response){
    global $TITLE,$AUTHORS,$IMAGE,$PUBLISHER,$URL,$PRICE;

    //Process Title
    $TITLE_RAW=$response->Items->Item->ItemAttributes->Title;
    $TITLE= htmlspecialchars($TITLE_RAW, ENT_QUOTES);

    //Process Authors, check for multiple authors
    if (is_array($response->Items->Item->ItemAttributes->Author)){$AUTHORS=htmlspecialchars(implode(', ',$response->Items->Item->ItemAttributes->Author),ENT_QUOTES);}
    else{$AUTHORS=htmlspecialchars($response->Items->Item->ItemAttributes->Author, ENT_QUOTES);}

    //Process Image, check to see if image exists.
    if (!isset($response->Items->Item->SmallImage->URL)){$IMAGE='http://www.worldreader.org/images/wr-book-cover.png';}
    else{$IMAGE=$response->Items->Item->SmallImage->URL;}

    //Process Publisher
    $PUBLISHER=$response->Items->Item->ItemAttributes->Publisher;
    //Process Item Detail URL
    $URL=$response->Items->Item->DetailPageURL;

    //Process Price. Formatted price is in form "$0.00"
    $PRICE=$response->Items->Item->ItemAttributes->ListPrice->FormattedPrice;

}



//Function: Remove whitespace from the submission (which interferes with processing) and separate all values into an array
function trimASIN($asin){
    //Global variable passed back to script
    global $ASINs;

    //Function: Remove spaces
    function trim_value(&$value){
        $value = trim($value);
    }

    //Put each value into an array
	$ASINs = explode(PHP_EOL,$asin);
    //Remove spaces from each array value
	array_walk($ASINs, 'trim_value');

}
