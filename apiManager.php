<?php

class API_Manager
{
	private $ch;
	private $v = '5.67';
	private $proxy = '66.70.191.215:8080';
	// private $proxy = '45.55.231.178:8118';

	public function __construct($proxy)
	{
	    if(!function_exists("curl_init")) {
	    	throw new Exception("You need to install cURL");
	    }

	    $this->ch = curl_init();
	    curl_setopt_array($this->ch, [CURLOPT_RETURNTRANSFER => true, 
	    	CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4 , 
	    	CURLOPT_TIMEOUT => 30]);

		if ($proxy) {
			curl_setopt($this->ch, CURLOPT_PROXY, $this->proxy);
		}

		curl_setopt($this->ch, CURLOPT_URL, "https://api.vk.com/method/users.get?user_ids=1");
		$res = curl_exec($this->ch);
		if($res == false) {
			throw new Exception("Can`t establish connection to vk.com");
		}
	    
	}

	public function getData($user_ids)
	{
		if (!is_array($user_ids)) {
			$user_ids = [$user_ids];
		}
		
		$albums = []; $photos = [];
		$users = $this->getUsers($user_ids);

		foreach ($users as $id => $user) {
			$tmp_albums = $this->getAlbums($id);
			$albums = array_merge($albums,$tmp_albums);
			foreach ($tmp_albums as $album_id => $album) {
				$photos = array_merge($photos, $this->getPhotos($id, $album_id));
			}
		}	
		return ['users' => $users, 'albums' => $albums, 'photos' => $photos];
	}

	private function getUsers($user_ids)
	{
		$users = [];
		$i = 0;
		do {
			$url = "https://api.vk.com/method/users.get?user_ids=" . implode(",", array_slice($user_ids, $i, 1000));
			if($res = $this->getResponse($url)) {
				foreach ($res as $u) {
					$users[$u->uid] = $u->uid . ", '" . addslashes($u->first_name . " " . $u->last_name) . "'";
				}
			}
			$i += 1000;
		}
		while ($i < count($user_ids));

		Logger::print("Retrieve " . count($users) . " user(s)");
		return $users;
	}

	private function getAlbums($user_id)
	{
		$albums = ['wall_'. $user_id => "'wall_" . $user_id . "', 'wall', $user_id", 				// DEFAULT ALBUMS FOR EVERY USER
					'profile_' . $user_id => "'profile_" . $user_id . "', 'profile' , $user_id", 
					'saved_' . $user_id => "'saved_" . $user_id . "', 'saved' , $user_id"];			
		$url = "https://api.vk.com/method/photos.getAlbums?owner_id=$user_id&v=$this->v";

		if($res = $this->getResponse($url)) {
			foreach ($res->items as $album) {
				$albums[$album->id] = "$album->id, '" . addslashes($album->title) . "', $user_id";
			}
		}

		Logger::print("Retrieve " . count($albums) . " albums(s) for user $user_id");
		return $albums;	
	}

	public function getPhotos($user_id, $album_id)
	{
		$total = 0;		// total number of photos in album
		$count = 0;		// already downloaded number of photos
		$photos = [];
		do {
			$url = "https://api.vk.com/method/photos.get?owner_id=$user_id&album_id=" . explode("_", $album_id)[0] . "&v=$this->v&offset=$count&photo_sizes=1&access_token=8e52a8328e52a8328e52a832788e0fc01d88e528e52a832d720a82152cce74b4926f4af";

			if($res = $this->getResponse($url)) {
				$count += count($res->items);
				$total = $res->count;
				foreach ($res->items as $img) {
					array_push($photos, "'" . $album_id . "', '" . $img->sizes[count($img->sizes) - 1]->src . "'");
				}
			}
		}
		while ($total > $count);

		Logger::print("Retrieve " . count($photos) . " photo(s) for album $album_id");

		return $photos;
	}

	private function getResponse($url)
	{
		curl_setopt($this->ch, CURLOPT_URL, $url);
		$res = curl_exec($this->ch);

		if(!$res) {
			Logger::apiError(curl_error($this->ch));
			return null;
		}
		else {
			$parsed = json_decode($res);
			if(!property_exists($parsed, 'response')) {
				Logger::apiError($parsed->error);
				return null;
			}
			return $parsed->response;
		}
	}

	public function __destruct() 
	{
		curl_close($this->ch);
	}
}