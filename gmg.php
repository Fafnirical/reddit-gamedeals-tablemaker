<?php //domain is Playfire.com
	include 'proxy.php';


	function get_games($url) //function gets game info
	{
		$locs = array("us","uk");
		$html = file_get_html($url);
		foreach($html->find('div.post-body ul li') as $key=>$li) {
			foreach($li->find('a') as $link) {
				$games[$key]['name'] = $link->plaintext;
				$games[$key]['url'] = $link->href;
				foreach($locs as $loc=>$location) {
					proxy($location);
					print "<pre>"; print_r(get_headers($link->href)); print "</pre>";
					if(!in_array('HTTP/1.1 404 NOT FOUND', get_headers($link->href))) {
						$game_html = file_get_html($link->href);
						print_r($games[$key]['name']);
						foreach($game_html->find('strong.curPrice') as $price) {
							$games[$key]['price'][$location] = $price->plaintext;
							break;
						}
						if($game_html->find('span.lt')) {
							foreach($game_html->find('span.lt') as $price) {
								$games[$key]['norm_price'][$location] = $price->plaintext;
								break;
							}
						} else {
							$games[$key]['norm_price'][$location] = $games[$key]['price'][$location];
						}
						$games[$key]['price-num'][$location] = str_replace(array('$','€','£'), '', $games[$key]['price'][$location]);
						$games[$key]['price-num'][$location] = str_replace(',', '.', $games[$key]['price-num'][$location]);
						$games[$key]['norm_price-num'][$location] = str_replace(array('$','€','£'), '', $games[$key]['norm_price'][$location]);
						$games[$key]['norm_price-num'][$location] = str_replace(',', '.', $games[$key]['norm_price-num'][$location]);
						foreach($game_html->find('section.description h2 span') as $drm) {
							if(trim($drm->plaintext) == 'Third party DRM: Steam') {
								$games[$key]['drm'] = 'Steam';
								break;
							} else if(trim($drm->plaintext) == 'Third party DRM: Origin') {
							$games[$key]['drm'] = 'Origin';
								break;
							} else {
								$games[$key]['drm'] = ' '; //other or 'Capsule'
								break;
							}
						}
					} else {
						$games[$key]['price'][$location] = 'N/A';
						$games[$key]['norm_price'][$location] = 'N/A';
						$games[$key]['drm'] = 'N/A';
					}
				}
				if($games[$key]['price']['us'] == 'N/A') {
					$games[$key]['percent'] = 'N/A';
				} else {
					$games[$key]['percent'] = (int)(($games[$key]['norm_price-num']['us']-$games[$key]['price-num']['us'])/($games[$key]['norm_price-num']['us'])*100).'%';
				}
			}
			$games[$key]['platform'] = ' ';
		}
		proxy('us');
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
			$table[$key] .= $game['price']['us'].'|';
			$table[$key] .= $game['price']['uk'].'|';
			$table[$key] .= $game['price']['uk'].'|';
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
			$table[$key] .= $game['platform'].'|'; //GMG platforms not yet implemented (too many exceptions)
			$table[$key] .= $game['drm'];
		}
		return $table;
	}
?>
