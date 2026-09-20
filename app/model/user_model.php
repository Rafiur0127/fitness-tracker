<?php
function is_email_taken($pdo, $email) {
  $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
  $stmt->execute([$email]);
  return $stmt->fetchColumn() ? true : false;
}

function register_user($pdo, $name, $email, $password, $age, $gender) {
  $hash = password_hash($password, PASSWORD_DEFAULT);
  $name_parts = preg_split('/\s+/', trim($name), 2);
  $first_name = $name_parts[0] ?? '';
  $last_name = $name_parts[1] ?? null;
  $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, password, age, gender) VALUES (?, ?, ?, ?, ?, ?)");
  return $stmt->execute([$first_name, $last_name, $email, $hash, $age, $gender]);
}

function login_user($pdo, $email) {
  $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
  $stmt->execute([$email]);
  return $stmt->fetch(PDO::FETCH_ASSOC);
}

?>
