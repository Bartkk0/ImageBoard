<?php
include_once 'database.php';

$post_id = intval($_GET['post']);
$stmt = $pdo->prepare('SELECT * FROM get_thread(:id)');
$stmt->execute(["id" => $post_id]);

$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (sizeof($posts) == 0) {
    echo "404";
    echo '<a href="/catalog.php">Return to catalog</a>';
    die(404);
}

$post_id = intval($posts[0]['post_id']);

if (sizeof($posts) == 1 && $posts[0]["parent_id"] != null)
    header("Location: /post.php?post=" . $posts[0]["parent_id"] . "#" . $post_id);

?>

<html lang="en">
<head>
    <link rel="stylesheet" href="style.css">
</head>

<body>
<?php
include "header.php" ?>

<div class="submitForm">
    <form action="/api/createReply.php" method="post" enctype="multipart/form-data">
        <label for="name">Name</label>
        <input type="text" id="name" name="name" value="Anonymous" required><br>

        <label for="subject">Subject</label>
        <input type="text" id="subject" name="subject"><br>

        <label for="comment">Comment</label>
        <textarea id="comment" name="comment" required></textarea><br>

        <label for="sage">Don't bump(sage)</label>
        <input type="checkbox" name="sage" id="sage"><br>

        <input type="hidden" id="parent" name="parent" value="<?= $post_id; ?>">

        <label for="file">File</label>
        <input type="file" id="file" name="file" accept="image/*"><br>
        <input type="submit">
    </form>
</div>
<div class="postview">
    <?php
    foreach ($posts as $post): ?>
        <div id="<?= $post['post_id']; ?>" class="post">
            <?php if($post['image']):?>
            <a href="/img/<?= $post['image']; ?>">
                <img src="/img/<?= $post['image'].'_thumb.jpg'; ?>" alt="">
            </a>
            <?php endif ?>
            <div>
                <p class="name"><?= $post['name']; ?></p>
                <p class="id">ID:
                    <a href="<?= "post.php?post=" . $post['post_id'] . "#" . $post['post_id'] ?>"
                       class="id"><?= $post['post_id'] ?></a>
                </p>
                <p class="time"><?= date('d-m-Y H:i:s', strtotime($post['creation_time'])) ?></p>
                <div class="mentions">
                    <?php
                    $mentioned = json_decode($post['mentioned']);

                    foreach ($mentioned as $m){
                        echo '<a href="post.php?post='.$m.'#'.$m.'">>>'.$m.'</a>';
                    }
                    ?>
                </div>

                <p class="subject"><?= $post['subject']; ?></p>
                <?php
                $comment = $post['comment'];
                $comment = htmlspecialchars($comment);
                $comment = preg_replace('/&gt;&gt;(\d*)/', '<a href="post.php?post=$1#$1">>>$1</a>', $comment);
                $comment = preg_replace("/(^|\n)&gt;.*/", '<span class="greentext">$0</span>', $comment);

                $comment = preg_replace("/\*\*(.+?)\*\*/", '<span class="bold">$1</span>', $comment);
                $comment = preg_replace("/\*(.+?)\*/", '<span class="italic">$1</span>', $comment);
                $comment = preg_replace("/`(.+?)`/", '<code>$1</code>', $comment);

                $comment = str_replace("\n", '<br>', $comment);
                ?>
                <blockquote class="comment"> <?= $comment; ?></blockquote>
            </div>
        </div>
    <?php
    endforeach; ?>
</div>
</body>
</html>
