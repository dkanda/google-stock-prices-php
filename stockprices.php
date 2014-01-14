<?php
/*
functions that use google finance api to return current price only
	(although compatibility for other stock metrics can be added easily)
abstracts google's 100 symbol limitation by splitting up the array
input is a string or array of strings containg the symbol(s)

author David Kanda
date 1/9/2014
*/
function GetPrice($symbol)
{
	//google finance can only handle up to 100 quotes at a time so split up query then merge results
	if(count($symbol) > 100)
	{
		$retArr = array();
		for($j=0; $j< count($symbol); $j +=100)
		{
			$arr = LookUpWithFormattedString(FormatString(array_slice($symbol, $j, 100)));
			$retArr = array_merge($retArr, $arr);
		}
		return $retArr;
	}
	else
		return LookUpWithFormattedString(FormatString($symbol));
}

function LookUpWithFormattedString($symbolString)
{
	$jsonArr = substr (file_get_contents("http://finance.google.com/finance/info?client=ig&q=".$symbolString),4);
	$jsonObj = json_decode($jsonArr, true);
	$retArr = array();
	foreach ($jsonObj as $value)
		array_push($retArr, $value['l']);
	return $retArr;
}

//apply formatting for query string 
function FormatString($symbol)
{
	$symbolString = "";
	if(is_array($symbol))
	{
		$len = count($symbol);
		$i=0;
		foreach ($symbol as $value) {
			$symbolString .= $value;
			if($len-$i != 1)
				$symbolString .= ",";
			$i++;
		}
	}
	else
		return $symbol;
	return $symbolString;
}

echo GetPrice("aapl");
echo GetPrice("aapl","msft");
?>