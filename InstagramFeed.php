<?php

include 'Settings.php';

class InstagramFeed {

	static function displayRecentMedia($userId, $count = 6) {
		$jsonData = InstagramFeed::getRecentMedia($userId, $count);
		//echo $jsonData;
		InstagramFeed::displayMedia($jsonData);
	}

	static function getRecentMedia($userId = "self", $count = 6) {
		global $instagramAccessToken, $instagramUserId;
		$cache = './cache/instagram/' . sha1($url) . '.json';
		if (file_exists($cache) && filemtime($cache) > time() - 5 * 60) {
			// If a cache file exists, and it is newer than 5 minutes, use it
			$result = json_decode(file_get_contents($cache));
		} else {
			$url = "https://api.instagram.com/v1/users/".$userId."/media/recent/?access_token=" . $instagramAccessToken . "&count=" . $count;
			$result = json_decode((file_get_contents($url)));
			file_put_contents($cache, json_encode($result));
		}				
		return $result;
	}

	static function displayMedia($mediaArray) {
		$result = "<div class=\"row\">";
		setlocale(LC_TIME, "de_DE.utf8");
		$counter = 0;
		foreach ($mediaArray->data as $key=>$value) {
			$counter++;
			$date = new DateTime(date("F j, Y", $value->caption->created_time));					
			$date = strftime("%e. %B %Y", (int)$date->format("U"));
			$result .= '<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
			<div class="panel panel-default thumbnail">
				<div class="panel-heading">
					<h3 class="panel-title"><a href="'.$value->link .'" >'.htmlentities($date).'</a></h3>
				</div>
				<div class="panel-body">'
					    . '<a href="' . $value->link . '">
							<div data-content="' . htmlentities($value->caption->text) .'" likes="'.$value->likes->count. '" class="instagram-overlay">
			                 <img class="img-responsive" src="' . $value->images->standard_resolution->url . '" alt="' . htmlentities($value->caption->text) . '">
							</div>
					       </a>'
						.'</div>
							<div class="panel-footer hidden-md hidden-lg"><a href="' . $value->link . '">' . htmlentities($value->caption->text) . '</a></div>
						  </div>
						  </div>';			
			if ($counter % 2 == 0){
				$result .= '<div class="clearfix visible-lg-block visible-md-block"></div>';
			}			
		}
		$result .= "</div>";
		echo $result;
	}

}
