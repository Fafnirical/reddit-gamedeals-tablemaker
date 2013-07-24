<?php //display area for input, w/ text area
?>
<html>
	<body>
		<form method='post' maxlength="150">
			<input type="text" placeholder="URL here" name="URL" onfocus="javascript:if(this.placeholder=='URL here'){this.placeholder='';}" onblur="javascript:if(this.placeholder==''){this.placeholder='URL here';}" />
			<input type="submit" value="Convert to Reddit table format" name="submit" />
	</form>
<?php //get URL on "SUBMIT" button
	if(isset($_POST['URL']) && !empty($_POST['URL'])) {
		$url = $_POST['URL'];
		print('$url: '.$url."<br>");
		validate_url($url);
		print_r(parse_url($url)); print "<br>";
		get_schema($url);
	}
?>
<?php //validate URL
	function validate_url($url) {
		if(!filter_var($url, FILTER_VALIDATE_URL)) {
			exit("Error 400: Bad Request. (Please enter a valid URL.)");
		}
		else {
			print("URL is valid, continuing...<br>");
		}
	}
?>

<?php //function checks URL schema (NOTE: disable for initial testing purposes)
	function get_schema($url) {
		$url_parsed = parse_url($url);
		switch(strtolower($url_parsed["host"])) {
			case "blog.playfire.com":
				echo "playfire!";
		}
	}
 /* if domain is Playfire.com
 

  * function gets data from: div.columns-fauxcolumns div.columns-inner div.column-center-outer div.column-center-inner div#main.main.section div.widget.Blog div.blog-posts.hfeed div.date-outer div.date-posts div.post-outer div.post-hentry div.post-body.entry-content ul
  * (NOTE: disable for initial testing purposes)
  *
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