<?php
include_once 'database.php';

$filter = $_GET['filter'] ?? '';

if ($filter != '') {
    $stmt = $pdo->prepare('SELECT * FROM get_catalog_filtered(?)');
    $stmt->execute([$filter]);
} else {
    $stmt = $pdo->prepare('SELECT * FROM get_catalog()');
    $stmt->execute();
}

$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<head>
    <link rel="stylesheet" href="style.css">
</head>

<body>
<?php
include_once "header.php" ?>
<div class="submitForm">
    <form action="/api/createPost.php" method="post" enctype="multipart/form-data">
        <label for="name">Name</label>
        <input type="text" id="name" name="name" value="Anonymous" required><br>

        <label for="subject">Subject</label>
        <input type="text" id="subject" name="subject" required><br>

        <label for="comment">Comment</label>
        <textarea id="comment" name="comment" required></textarea><br>

        <label for="file">File</label>
        <input type="file" id="file" name="file" required accept="image/*"><br>
        <input type="submit">
    </form>
</div>
<div class="catalog">
    <div class="catalog_options">
        <form action="">
            <input type="text" name="filter" id="filter" placeholder="filter" value="<?= $filter ?>">
            <input type="submit" value="Filter">
        </form>
    </div>
    <?php
    foreach ($posts as $post): ?>
        <div class="catalog_post" class="catalog_post">
            <a href="/post.php?post=<?= $post['post_id']; ?>" class="image">
                <img src="/img/<?= $post['image'] . '_thumb.jpg'; ?>" alt="">
            </a>
            <p class="stats">R: <?= $post['replies'] ?> / I: <?= $post['images'] ?></p>
            <p class="name"><?= $post['name']; ?></p>
            <p class="subject"><?= $post['subject']; ?></p>
        </div>
    <?php
    endforeach; ?>
</div>
</body>