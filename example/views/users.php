<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Document</title>
</head>
<body>
    <h3>User list</h3>
    <ul>
    <?php foreach($users as $user): ?>
        <li>
            <?php echo $user['name']; ?> : <?php echo $user['description']; ?>
        </li>
    <?php endforeach; ?>
    </ul>
</body>
</html>
