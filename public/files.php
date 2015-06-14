<?php
$path = '../data/images';
$file = $_SERVER['PATH_INFO'];
$parts = explode('.', $file);
$extension = strtolower(array_pop($parts));
$availables = ['png', 'jpg', 'jpeg', 'gif'];
if (!in_array($extension, $availables)) {
    header('HTTP/1.1 404 OK');
    die;
}

$file = $path.'/'.implode('.', $parts).'.data';
if (!file_exists($file)) {
    $file = '../data/noimage.jpg';
}

$finfo = finfo_open(FILEINFO_MIME_TYPE);
$type = finfo_file($finfo, $file);
finfo_close($finfo);
header('Content-type: '.$type);
echo file_get_contents($file);


