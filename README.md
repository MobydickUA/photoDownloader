# photoDownloader
Download photos URL from vk.com by user id and store them in DB.
Console application for unix-like operating systems.

### Requirements:
* PHP 5.* or higher (tested on 7.0 version).
* MySQL server (tested on 5.7.17 version). ATTENTION. ONLY InnoDB engine accepted.
* cURL (tested on 7.49.0 version).
* php-pdo extension.
* php-curl extension.

### Configuration
* Download and unpack project.
* Specify path to yours php file (for example #!/usr/bin/php) in photoDownloader file.
* Specify info for DB connection in db.php.
* Upload db_dump.sql onto your server ( `mysql -u username -p < db_dump.sql` ).

### Usage
Run it as `./photoDownloader` or `php photoDownloader`

### Parameters:
#### Required (one of them):
* `-u <user_id>` - get all photos of user with <user_id>
* `-f <list.csv>` - open csv file with list of user_ids and to get all their photos.You can find sample file in repo.
* `-l` - show list of users, already downloaded into DB
* `-s <user_id>` - find user and all his photos and albums by id
#### Optional:
* `-q` - quiet mode - do not output anything to command line
* `-d` - direct connection. By default it uses proxy, but you can disable it by `-d` flag. You can change proxy server in apiManager.php file
