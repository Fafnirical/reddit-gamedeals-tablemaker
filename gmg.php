<?php //domain is Playfire.com
	function get_games($url) //function gets game info
	{
		$html = file_get_html($url);
		foreach($html->find('div.post-body ul li') as $key=>$li) {
			foreach($li->find('a') as $link) {
				$games[$key]['name'] = $link->plaintext;
				$games[$key]['url'] = $link->href;
				$game_html = file_get_html($link->href);
				foreach($game_html->find('strong.curPrice') as $price) {
					$games[$key]['price'] = $price->plaintext;
					break;
				}
				foreach($game_html->find('section.description h2 span') as $drm) {
					if(trim($drm->plaintext) == 'Third party DRM: Steam') {
						$games[$key]['drm'] = 'Steam';
						break;
					} else if(trim($drm->plaintext) == 'Third party DRM: Origin') {
					$games[$key]['drm'] = 'Origin';
						break;
					} else {
						$games[$key]['drm'] = ''; //other or 'Capsule'
						break;
					}
				}
				break;
			}
			preg_match('/[1-9][0-9]%/', $li->plaintext, $matches);
			$games[$key]['percent'] = $matches[0];
			$games[$key]['platform'] = ' ';
		}
		$html->clear();
		unset($html);
		return $games;
	}

	function make_table($games) //function takes parsed data and puts it into Reddit table format
	{
		$table[0] = 'Title|Disc.|$USD|EUR€|£GBP|Metacritic|Platform|DRM';
		$table[1] = ':----|----:|---:|---:|---:|---------:|:------:|:-:';
		foreach($games as $key=>$game) {
			$key += 2;
			$table[$key] = "[".$game['name'].']('.$game['url'].')'.'|';
			$table[$key] .= $game['percent'].'|';
			$table[$key] .= $game['price'].'|';
			$table[$key] .= 'x.xx€'.'|';
			$table[$key] .= '£x.xx'.'|';
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
			$table[$key] .= $game['platform'].'|'; //GMG platform not yet implemented
			$table[$key] .= $game['drm'];
		}
		return $table;
	}
?>
