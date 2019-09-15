<?php

if (empty($_POST['token'])) {
  exit ('Error: no token');
}

$token = $_POST['token'];
mquery("INSERT INTO `push`
          SET `token`= '^S'", $token);