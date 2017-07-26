<?php

require_once ('db.php');

class Model
{
	private $db;

	public function __construct()
	{
		try {
			$this->db = DB::getInstance();
		}
		catch (Exception $e) {
			throw $e;
		}
		
	}

	public function store($data) 
	{
		$users = "INSERT IGNORE INTO `users` (`id`, `name`) VALUES (" . implode("), (", $data['users']) . ");";
		$albums = "INSERT IGNORE INTO `albums` (`id`, `title`, `user_id`) VALUES (" . implode("), (", $data['albums']) . ");";
		$photos = "INSERT IGNORE INTO `photos` (`album_id`, `src`) VALUES (" . implode("), (", $data['photos']) . ");";

		try{
			$this->db->beginTransaction();
		    
		    $res1 = $this->db->query($users);
		    $res2 = $this->db->query($albums);
		    $res3 = $this->db->query($photos);

		    $this->db->commit();		    
		} 
		catch(PDOException $e){
		    Logger::print($e->getMessage());
		    $this->db->rollBack();
		}
	}

	public function getUsers()
	{
		$res = $this->db->query("SELECT * FROM users");		
		if($data = $res->fetchAll(PDO::FETCH_ASSOC)) {
			return $data;
		}
		else {
			return "There`s no any users yet...";
		}
	}

	public function searchUser($id)
	{
		$q = $this->db->prepare("SELECT users.id, users.name, albums.title, photos.src 
								FROM albums 
								RIGHT JOIN photos ON albums.id = photos.album_id 
								LEFT JOIN users on albums.user_id = users.id
								WHERE users.id=?");
		$q->execute([$id]);
		if($res = $q->fetchAll(PDO::FETCH_ASSOC)) {
			return $res;
		}
		else {
			return "There is no such user in DB...";
		}
	}
}