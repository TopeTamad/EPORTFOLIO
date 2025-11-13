<?php
require_once __DIR__ . '/db.php';

function get_profile()
{
    global $pdo;
    $stmt = $pdo->query('SELECT * FROM profile ORDER BY id DESC LIMIT 1');
    return $stmt->fetch();
}

function get_skills()
{
    global $pdo;
    $stmt = $pdo->query('SELECT * FROM skills ORDER BY sort_order ASC, id ASC');
    return $stmt->fetchAll();
}

function get_projects()
{
    global $pdo;
    $stmt = $pdo->query('SELECT * FROM projects ORDER BY sort_order ASC, id ASC');
    return $stmt->fetchAll();
}

function get_certificates()
{
    global $pdo;
    $stmt = $pdo->query('SELECT * FROM certificates ORDER BY sort_order ASC, id ASC');
    return $stmt->fetchAll();
}

function save_contact_message($name, $email, $message)
{
    global $pdo;
    $name = trim((string)$name);
    $email = trim((string)$email);
    $message = trim((string)$message);
    if ($name === '' || $email === '' || $message === '') {
        return [false, 'All fields are required.'];
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return [false, 'Please enter a valid email address.'];
    }
    $stmt = $pdo->prepare('INSERT INTO contact_messages(name, email, message) VALUES(?,?,?)');
    $stmt->execute([$name, $email, $message]);
    return [true, 'Thank you! Your message has been sent.'];
}
