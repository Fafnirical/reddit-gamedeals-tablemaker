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

<?php //function gets game info
	function get_info($url) {
		$parse = parse_url($url);
		$url_parsed = preg_replace('#^(http(s)?://)?w{3}\.(\w+\.\w+)#', '$3', $parse);
		print_r($url_parsed); print '<br>';
		switch(strtolower($url_parsed['host'])) {
			case "blog.playfire.com":
				include 'gmg.php';
				break;
			case "humblebundle.com":
				include 'hib.php';
				break;
		}
		$games = get_games($url);
		foreach($games as $key=>$game) {
			$games[$key]['metascore'] = get_meta($game['name']);
		}
		return $games;
	}
?>
