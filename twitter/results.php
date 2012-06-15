<?php
/**
 * Twitter web feed for Arduino
 *
 * @author     Jason McIver
 * @license    MIT
 * @link       https://github.com/JayMc
 */
require_once 'twitter.class.php';
	$url = parse_url("http" . ((!empty($_SERVER['HTTPS'])) ? "s" : "") . "://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
	$searchlist = array('#thingsthatpissmeoff','#melbourne','#whatidreamabout');
	$keywords = array();
	
	//if 1 find the lastest trends to search for, else 0 uses searchlist words to search for
	//this could be set on the arduino by a button or switch
	$gettrends = 1;
	
	//set location
	$woeid = 1;  // --- where on earth ID --- (1 = global/earth) --- 
	//find more locations here http://isithackday.com/geoplanet-explorer/
	$woeid = 23424748; //Australia
	//$woeid = 1103816; //Melbourne (empty)
	//$woeid = 1105779; //Sydney
	//$woeid = 1100661; //Brisbane
	//$woeid = 23424934; //Philippines
	//$woeid = 1199477; //Manila
	//$woeid = 23424977; //America
	//$woeid = 2436704; //Las Vegas
	//$woeid = 23424975; //UK
	
	//get random location
	$woeids = array('23424748','1','23424934','2436704','23424975');
	//$woeid = $woeids[array_rand($woeids)];

	
	if($gettrends == 1){
		//get latest trends from given location id
		$json = file_get_contents("http://api.twitter.com/1/trends/".$woeid.".json", true); //getting the file content
		if (strpos($http_response_header[0], "200")) {
			echo 'got trends ';
			$decode = json_decode($json, false); //getting the file content as object
		 
			## echo "<pre>\r\n"; 
			## print_r($decode);  // debug view
			## echo "</pre>\r\n"; 
			
			 
			$data = $decode[0]->trends; 
			 
			foreach ($data as $item) { 
				
				array_push($keywords,$item->name);
			}
		}else{
			echo 'failed to get trends';
			
		}
		
	} else {
		//couldn't get trends, check your location id is correct or set, try a different one.
		echo 'using search list ';
		//using custom trends or own keywords
		$keywords = $searchlist;
	}
	
	//pick random keyword
	$keyword = $keywords[array_rand($keywords)];
	
	//search for anything up until 1 month old
	$query = " ".$keyword." since:".date('Y-m-d', strtotime('1 month ago'))." until:".date('Y-m-d', strtotime('now')) ;
	$twitter = new Twitter;
	$results = $twitter->search($query);
	
	//pick random result
	$result = $results[array_rand($results)];
	
	//pick last result
	//$result = $results[count($results)-1];
	
	//pick first result
	//$result = $results[0];
	
	//clean up string
	$result->text = str_replace("&amp", "", $result->text);
	$result->text = str_replace("&alt;33", "", $result->text);
	
	echo '<'.($result->text).'>';
	echo ($result->text);
		
?>