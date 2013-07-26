<?php //domain is Playfire.com
	function get_games($url) {
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
					if(trim($drm->plaintext) == "Third party DRM: Steam") {
						$games[$key]['drm'] = "Steam";
						break;
					} else if(trim($drm->plaintext) == "Third party DRM: Origin") {
					$games[$key]['drm'] = "Origin";
						break;
					} else {
						$games[$key]['drm'] = ""; //other or "Capsule"
						break;
					}
				}
				break;
			}
			preg_match('/[1-9][0-9]%/', $li->plaintext, $matches);
			$games[$key]['percent'] = $matches[0];
		}

		return $games;
	}
?>