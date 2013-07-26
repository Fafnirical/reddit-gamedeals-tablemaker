<?php //domain is HumbleBundle.com
	function get_games($url) //function gets game info
	{
		$html = file_get_html($url);
		foreach($html->find('ul.game-boxes li[class^=g]') as $key=>$li) {
			foreach($li->find('a') as $link) {
				$games[$key]['name'] = preg_replace('/\s+/', ' ', trim($link->plaintext));
				//$games[$key]['url'] = $url;
				if($li->find('span.bta-popup')) {
					$games[$key]['bta'] = 'X';
				} else {
					$games[$key]['bta'] = '';
				}
				$games[$key]['steam'] = '';
				foreach($link->find('.icon') as $p=>$plat) {
					if($plat->class == 'icon windows') {
						$games[$key]['platform'][$p] = 'PC';
					} else if($plat->class == 'icon mac') {
						$games[$key]['platform'][$p] = 'Mac';
					} else if($plat->class == 'icon linux') {
						$games[$key]['platform'][$p] = 'Linux';
					} else if($plat->class == 'icon steam') {
						$games[$key]['steam'] = 'X';
					}
				}
				break;
			}
		}
		$html->clear();
		unset($html);
		return $games;
	}

	function make_table($games) //function takes parsed data and puts it into Reddit table format
	{
		$table[0] = 'Title|BTA|Metacritic|Platform|Steam';
		$table[1] = ':----|:-:|---------:|:------:|:---:';
		foreach($games as $key=>$game) {
			$key += 2;
			$table[$key] = $game['name'].'|';
			//$table[$key] = "[".$game['name'].']('.$game['url'].')'.'|';
			$table[$key] .= $game['bta'].'|';
			if($game['metascore'] == 'N/A') {
				$table[$key] .= 'N/A';
			} else {
				$table[$key] .= '[';
				foreach($game['metascore']['critic'] as $c=>$criticscore) {
					$table[$key] .= $criticscore;
					if(count($game['metascore']['critic'])>$c+1) {
						$table[$key] .= '/';
					}
				}
				$table[$key] .= ']('.$game['metascore']['url'].')';
				if($game['metascore']['noagg'] == TRUE) {
					$table[$key] .= ' \(only '.count($game['metascore']['critic']);
					if(count($game['metascore']['critic']) == 1) {
						$table[$key] .= ' review\)';
					} else {
						$table[$key] .= ' reviews\)';
					}
				}
			}
			$table[$key] .= '|';
			//userscore
			foreach($game['platform'] as $p=>$plat) {
				$table[$key] .= $plat;
				if(count($game['platform'])>$p+1) {
					$table[$key] .= '/';
				}
			}
			$table[$key] .= '|';
			$table[$key] .= $game['steam'];
		}
		return $table;
	}
?>
