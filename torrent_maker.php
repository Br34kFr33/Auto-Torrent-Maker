<?php

define('ROOT_DIR', '/home');
define('SCAN_DIR', ROOT_DIR.'/scan');
define('COMPLETE_DIR', ROOT_DIR.'/complete');
define('TORRENT_DIR', ROOT_DIR.'/torrent');
define('ANNOUNCE_URL', 'YOUR-TRACKER-ANNOUNCE-AND-PASSKEY-HERE');

function move($source, $dest) {
	$cmd = 'mv "'.$source.'" "'.$dest.'"'; 
	exec($cmd, $output, $return_val); 
	if ($return_val == 0) return 1;
	return 0;
}

function make_torrent($file_full, $ext, $new_dir, $file) {

	$file = pathinfo($file_full, PATHINFO_BASENAME);
	$move_file = $new_dir.'/'.$file;
	
	$rez = move($file_full, $move_file);
	if (!$rez) die('Cannot move file!');
	
	$info = pathinfo($file);
	$output = TORRENT_DIR.'/'.$info['basename'].'.torrent';
	if (file_exists($output)) unlink($output);
	$cmd = "mktorrent '$move_file' -o '$output' -a ".ANNOUNCE_URL;
	echo $cmd."<br /> <br /> \n \n";
	exec($cmd);
	if (file_exists($output)) return $output;
	else die('Cannot make torrent!');
}

function scan_folder() {
	$dir = SCAN_DIR;
	$dir_done = COMPLETE_DIR;
	
	if (!is_dir($dir_done))
	{
		$ok = mkdir($dir_done);
		if (!$ok) die('Cannot create destination folder!');
	}
	
	$dh = opendir($dir);
	while ( $file = readdir($dh) )
	{
		if ($file == '.' || $file == '..') continue;
		$file_full = $dir.'/'.$file;
		if ($file_full == COMPLETE_DIR) continue;
		$ext = pathinfo($file_full, PATHINFO_EXTENSION);
		make_torrent($file_full, $ext, $dir_done, $file);
	}
}

scan_folder();

?>
