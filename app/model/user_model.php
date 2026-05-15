<?php
function is_email_taken($pdo, $email) {
  $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
  $stmt->execute([$email]);
  return $stmt->fetchColumn() ? true : false;
}

function register_user($pdo, $name, $email, $password, $age, $weight, $gender) {
  $hash = password_hash($password, PASSWORD_DEFAULT);
  $stmt = $pdo->prepare("INSERT INTO users (name, email, password, age, weight, gender) VALUES (?, ?, ?, ?, ?, ?)");
  return $stmt->execute([$name, $email, $hash, $age, $weight, $gender]);
}

function login_user($pdo, $email) {
  $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
  $stmt->execute([$email]);
  return $stmt->fetch(PDO::FETCH_ASSOC);
}

?>
