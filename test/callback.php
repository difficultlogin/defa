<?php

$post = $_POST;

if ($post['name'] && $post['phone'] && $post['email']) {
    $name    = htmlspecialchars($post['name']);
    $phone   = htmlspecialchars($post['phone']);
    $email   = htmlspecialchars($post['email']);
    $comment = $post['comment'] ? htmlspecialchars($post['comment']) : 'unknown';

    $message  = 'Name: ' . $name . '<br>';
    $message .= 'Phone: ' . $phone . '<br>';
    $message .= 'Email: ' . $email . '<br>';
    $message .= 'Comment: ' . $comment . '<br>';

    $headers  = "Content-type: text/html; charset=utf-8 \r\n";
    $headers .= "From: Test work <test@domain.com>\r\n";

    $to      = 'difficultemailforcommunicat@gmail.com';
    $subject = 'Test work';

    $send = mail($to, $subject, $message, $headers);

    if ($send) {
        email_log($_SERVER['REMOTE_ADDR']);
        echo json_encode(array('status' => 'Success', 'message' => 'Message send !'));
    } else {
        echo json_encode(array('status' => 'Error', 'message' => 'Message don\'t send'));
    }
} else {
    echo json_encode(array('status' => 'error', 'message' => 'Please complete the required fields'));
}

function email_log($ip_client) {
    $log_file_name = 'email_log.txt';
    $log_file = file_get_contents($log_file_name);

    $log_text  = date('d.m.y H:i');
    $log_text .= ' Email send ';
    $log_text .= $ip_client;
    $log_text .= "\n";

    file_put_contents($log_file_name, $log_file . $log_text);
}