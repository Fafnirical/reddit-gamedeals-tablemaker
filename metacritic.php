<?php /*Metacritic function: checks metacritic score
  * if no reviews: return "N/A"
  * if  <4 critic reviews: return each
  * if >=4 critic reviews: return overall
  * return overall user score
  */
	function get_meta($game) {
		$game_mc = urlencode($game);
		$metacritic['url'] = metaURL_fromgoogle($game_mc);
		$metacritic['noagg'] = FALSE;
		$html = file_get_html($metacritic['url']);

		if($ms=$html->find('div.metascore span.score_value')) {
			foreach($ms as $criticscore) {
				$metacritic['critic'][0] = trim($criticscore->plaintext);
			}
		} else if($mses=$html->find('div.critscore')) {
			foreach($mses as $key=>$criticscores) {
				$metacritic['critic'][$key] = trim($criticscores->plaintext);
			}
			$metacritic['noagg'] = TRUE;
		} else {
			$metacritic['critic'][0] = "N/A";
		}

		/*
		foreach($html->find('div.avguserscore') as $score) {
			if($us=$score->find('span.score_value')) {
				foreach($us as $userscore) {
					$metacritic['user'] = trim($userscore->plaintext);
				}
				break;
			} else {
				$metacritic['user'] = "N/A";
				break;
			}
		}
		*/

		return($metacritic);
	}
?>

<?php //Google function for Metacritic
	function metaURL_fromgoogle($game) {
		// The request also includes the userip parameter which provides the end
		// user's IP address. Doing so will help distinguish this legitimate
		// server-side traffic from traffic which doesn't come from an end-user.
		$url = "http://ajax.googleapis.com/ajax/services/search/web?v=1.0&q=".$game."+site%3Ametacritic.com%2Fgame%2Fpc+&userip=68.33.103.129";//.getUserIpAddr();
		//print $url."<br>";
		// sendRequest
		// note how referer is set manually
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_REFERER, 'http://worldpeace.doesntexist.com');
		$body = curl_exec($ch);
		curl_close($ch);

		// now, process the JSON string
		$json = json_decode($body);
		return($json->responseData->results[0]->url);
	}
?>
<?php //function gets user's IP Address to send to Google (to prevent Google from blocking us)
	function getUserIpAddr() {
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) //if from shared
		{
			return $_SERVER['HTTP_CLIENT_IP'];
		}
		else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //if from a proxy
		{
			return $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		else
		{
			return $_SERVER['REMOTE_ADDR'];
		}
	}
?>