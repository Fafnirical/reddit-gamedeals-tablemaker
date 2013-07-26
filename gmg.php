<?php //domain is Playfire.com
	function get_games($url) {
		$html = file_get_html('gmg.html');
		foreach($html->find('div.post-body ul li a') as $key=>$link) {
			$games[$key]['name'] = $link->plaintext;
			$games[$key]['url'] = $link->href;
		}
		/*
		foreach($games as $key=>$game) {
			$game_html = file_get_html($game[$key]['url']);
			foreach($game_html->find(
		}*/
		
		return $games;
	}
?>