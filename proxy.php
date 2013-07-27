<?php
	function proxy($location) {
		switch($location) {
			case "us":
				$context = array('http' =>
					array(
						'proxy' => '',
						'request_fulluri' => true,
					),
				);
				break;
			case "uk":
				$context = array('http' =>
					array(
						'proxy' => 'tcp://87.236.212.152:8090',
						'request_fulluri' => true, 
					),
				);
				break;
			case "eu":
				$context = array('http' =>
					array(
						'proxy' => 'tcp://87.236.212.152:8090',
						'request_fulluri' => true, 
					),
				);
				break;
		}
		//$context = stream_context_create($context);
		$default = stream_context_set_default($context);
	}
?>