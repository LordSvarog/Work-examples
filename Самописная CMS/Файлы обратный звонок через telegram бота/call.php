<?php
$name= $_POST['name']; $email= $_POST['email']; $phone= $_POST['phone'];
send2telegram("<b>{$name}</b> заказал обратный звонок по номеру <b>{$phone}</b> | почта - {$email}");

$subject= 13;
mquery("INSERT INTO `feedback_list` SET
                    `fullname`='^S',
                    `from`='^S',
                    `date`=UNIX_TIMESTAMP(),
                    `data`='^S',
                    `id_subject` = '^N'",
                    $name, $email, $phone, $subject
                    );

Redirect($_REQUEST[go]);