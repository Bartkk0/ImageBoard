<?php
include_once '../database.php';
include_once '../thumbnailer.php';

$errors = "";

if(strlen($_POST['name']) < 3)
    $errors .= "Name shorter than 32 characters<br>";
if(strlen($_POST['name']) > 32)
    $errors .= "Name longer than 32 characters<br>";

if(strlen($_POST['subject']) < 3)
    $errors .= "Subject longer than 3 characters<br>";
if(strlen($_POST['subject']) > 64)
    $errors .= "Subject longer than 64 characters<br>";

if(strlen($_POST['comment']) > 1024)
    $errors .= "Comment longer than 1024 characters<br>";

if(!$_FILES['file']['name'])
    $errors .= "No file<br>";

if($errors != ""){
    echo $errors;
    die(403);
}

$name = substr($_POST['name'], 0, 32);
$subject = substr($_POST['subject'], 0, 64);
$comment = substr($_POST['comment'], 0, 1024);

$name = htmlspecialchars($name);
$subject = htmlspecialchars($subject);
//$comment = htmlspecialchars($comment);

$org_file = $_FILES['file'];

$hash = sha1_file($org_file['tmp_name']);
$ext = pathinfo($org_file['name'])['extension'];

$filename = "$hash.$ext";
move_uploaded_file($org_file['tmp_name'], "../img/$filename");
thumbnail("../img/$filename");

$stmt = $pdo->prepare("CALL create_post(?, ?, ?, ?)");
$stmt->execute([$name, $subject, $comment, $filename]);

$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pdo->exec('CALL clear_old_posts()');

header('Location: /catalog.php');