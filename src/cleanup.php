<?php
include_once 'database.php';

$stmt = $pdo->prepare('SELECT array_to_json(ARRAY(SELECT image FROM posts WHERE image IS NOT NULL)) AS files');
$stmt -> execute();

$used_files = json_decode($stmt->fetch()['files'],true);
foreach ($used_files as &$f){
    $f = 'img/'.$f;
}

$files = array_filter(glob('img/*', GLOB_BRACE), function($file) {
    return preg_match('/^(?!.*_thumb\.jpg$).*/', basename($file));
});
$to_rm = array_diff($files, $used_files);
//print_r($to_rm);
foreach ($to_rm as $f){
    unlink("$f");
    unlink($f."_thumb.jpg");
    echo "rm $f <br>";
}
