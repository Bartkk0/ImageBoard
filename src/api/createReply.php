<?php
include_once '../database.php';
include_once '../thumbnailer.php';

$errors = "";

if(strlen($_POST['name']) < 3)
    $errors .= "Name shorter than 32 characters<br>";
if(strlen($_POST['name']) > 32)
    $errors .= "Name longer than 32 characters<br>";

if(strlen($_POST['subject']) > 64)
    $errors .= "Subject longer than 64 characters<br>";

if(strlen($_POST['comment']) < 3)
    $errors .= "Comment shorter than 3 characters<br>";
if(strlen($_POST['comment']) > 1024)
    $errors .= "Comment longer than 1024 characters<br>";

if(!isset($_POST['parent']))
    $errors .= "No parent<br>";

if($errors != ""){
    echo $errors;
    die(403);
}

$name = $_POST['name'] ?? 'Anonymous';
$subject = substr($_POST['subject'] ?? '', 0, 64);
$comment = substr($_POST['comment'], 0, 1024);
$parent_id = intval($_POST['parent']);
$sage = ($_POST['sage'] || "") == "on";
if($_FILES['file']['name']){
    $org_file = $_FILES['file'];
    $hash = sha1_file($org_file['tmp_name']);
    $ext = pathinfo($org_file['name'])['extension'];

    $filename = "$hash.$ext";
    move_uploaded_file($org_file['tmp_name'], "../img/$filename");
    thumbnail("../img/$filename");
}
else
    $filename = null;

$name = htmlspecialchars($name);
$subject = htmlspecialchars($subject);
//$comment = htmlspecialchars($comment);

$stmt = $pdo->prepare("CALL create_reply(?, ?, ?, ?, ?)");
$stmt->execute([$name, $parent_id, $subject, $comment, $filename]);

$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

if(!$sage) {
    $stmt = $pdo->prepare("CALL bump_thread(CAST(? AS INT))");
    $stmt->execute([$parent_id]);
}

header("Location: /post.php?post=$parent_id");