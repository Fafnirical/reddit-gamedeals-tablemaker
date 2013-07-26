<?php //includes

include 'lib/simple_html_dom.php';

//display area for input, w/ text area
?>
<html>
	<body>
		<form method="post" maxlength="150">
			<input type="text" placeholder="URL here" name="URL" onfocus="javascript:if(this.placeholder=='URL here'){this.placeholder='';}" onblur="javascript:if(this.placeholder==''){this.placeholder='URL here';}" />
			<input type="submit" value="Convert to Reddit table format" name="submit" />
	</form>
<?php //get URL on "SUBMIT" button
	if(isset($_POST['URL']) && !empty($_POST['URL'])) {
		$url = $_POST['URL'];
		print('$url: '.$url.'<br>');
		validate_url($url);
		print_r(parse_url($url)); print '<br>';
		print "<pre>";
		get_schema($url);
		print "</pre>";
	}
?>
<?php //validate URL
	function validate_url($url) {
		if(!filter_var($url, FILTER_VALIDATE_URL)) {
			exit('Error 400: Bad Request. (Please enter a valid URL.)');
		} else {
			print('URL is valid, continuing...<br>');
		}
	}
?>

<?php //function checks URL schema (NOTE: disable for initial testing purposes)
	function get_schema($url) {
		$url_parsed = parse_url($url);
		switch(strtolower($url_parsed['host'])) {
			case "blog.playfire.com":
				include 'gmg.php';
		}
		$games = get_games($url);
		foreach($games as $key=>$game) {
			$games[$key]['metascore'] = get_meta($game['name']);
		}
		print_r($games);
	}
?>

<?php /*Metacritic function: checks metacritic score
  * if no reviews: return "N/A"
  * if less than 4 critic reviews: return each
  * if more than 4 critic reviews: return overall
  * return overall user score
  */
	function get_meta($game) {
		$game_mc = urlencode($game);
		$html = file_get_html(metaURL_fromgoogle($game_mc));

		foreach($html->find('div.metascore') as $score) {
			if($ms=$score->find('span.score_value')) {
				foreach($ms as $criticscore) {
					$metacritic['critic'][0] = trim($criticscore->plaintext);
				}
				break;
			} else if($mses=$score->find('li.grade')) {
				foreach($mses as $key=>$criticscores) {
					$metacritic['critic'][$key] = trim($criticscores->plaintext);
				}
				break;
			} else {
				$metacritic['critic'][0] = "N/A";
			}
		}

		foreach($html->find('div.avguserscore') as $score) {
			if($us=$score->find('span.score_value')) {
				foreach($us as $userscore) {
					$metacritic['user'] = trim($userscore->plaintext);
				}
				break;
			} else {
				$metacritic['user'] = "N/A";
			}
		}

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
<?php
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
<?php 
 /*
  * function then does a foreach li in ul[]; this is each game

  * function parses for: a->href; a->text
  * function parses for: \" is \".*$

  * Metacritic function: checks metacritic score
  * if no reviews: return "N/A"
  * if less than 4 critic reviews: return each
  * if more than 4 critic reviews: return overall
  * return overall user score
  */

//function takes parsed data and puts it into Reddit table format

//display parsed data in non-editable text area underneath "SUBMIT" button
?>
	</body>
</html>
