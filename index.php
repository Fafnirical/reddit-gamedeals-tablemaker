<?php //includes
	include 'lib/simple_html_dom.php';
	include 'metacritic.php';
?>
<?php //display area for input, w/ text area ?>
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
		
		//get the game info
		$games = get_info($url);
		
		//make the Reddit table
		$table = make_table($games);
		
		//display the table
		print "<pre><code>";
			foreach ($table as $line) {
				print $line."\n";
			}
		print "</code></pre>";
	}
?>
	</body>
</html>

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
	function get_info($url) {
		$url_parsed = parse_url($url);
		switch(strtolower($url_parsed['host'])) {
			case "blog.playfire.com":
				include 'gmg.php';
		}
		$games = get_games($url);
		foreach($games as $key=>$game) {
			$games[$key]['metascore'] = get_meta($game['name']);
		}
		return $games;
	}
?>

<?php //function takes parsed data and puts it into Reddit table format
	function make_table($games) {
		$table[0] = 'Title|Disc.|$USD|EUR€|£GBP|Metacritic|Platform|DRM';
		$table[1] = ':----|----:|---:|---:|---:|---------:|:------:|:-:';
		foreach($games as $key=>$game) {
			$key += 2;
			$table[$key] = "[".$game['name'].']('.$game['url'].')'.'|';
			$table[$key] .= $game['percent'].'|';
			$table[$key] .= $game['price'].'|';
			$table[$key] .= 'x.xx€'.'|';
			$table[$key] .= '£x.xx'.'|';
			$table[$key] .= '[';
			foreach($game['metascore']['critic'] as $c=>$criticscore) {
				$table[$key] .= $criticscore;
				if(count($game['metascore']['critic'])>$c+1) {
					$table[$key] .= '/';
				}
			}
			$table[$key] .= ']('.$game['metascore']['url'].') ';
			if($game['metascore']['noagg'] == TRUE) {
				$table[$key] .= '\(only '.count($game['metascore']['critic']);
				if(count($game['metascore']['critic']) == 1) {
					$table[$key] .= ' review\)';
				} else {
					$table[$key] .= ' reviews\)';
				}
			}
			$table[$key] .= '|';
			//userscore
			$table[$key] .= "  ".'|'; //platform not yet implemented
			$table[$key] .= $game['drm'];
		}
		return $table;
	}
?>
