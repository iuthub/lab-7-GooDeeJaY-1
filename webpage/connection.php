<?php

$db = new PDO('mysql:host=localhost;dbname=blog', 'jasur', '1234');

$reg       = $db->prepare("INSERT INTO users(username, email, password, fullname, dob) VALUES (?, ?, ?, ?, CURRENT_TIMESTAMP())");
$exists    = $db->prepare("SELECT TRUE FROM users WHERE username=? OR email=?");
$login     = $db->prepare("SELECT id, username, email, fullname FROM users WHERE username=? AND password=?");
$update    = $db->prepare("UPDATE users SET username=?, email=?, fullname=?, password=? WHERE id=?");
$get_posts = $db->prepare("SELECT * FROM posts WHERE userId=?");
$add_post  = $db->prepare("INSERT INTO posts(TITLE, BODY, PUBLISHDATE, USERID) VALUES (?, ?, CURRENT_TIMESTAMP(), ?)");
