<?php
// Test de connexion direct

$dbPath = __DIR__ . '/../database/database.sqlite';
$db = new PDO('sqlite:' . $dbPath);

echo "<h1>Test de connexion</h1>";

$email = 'admin@test.edu';
$password = 'Admin123!';

// Récupérer l'utilisateur
$stmt = $db->prepare("SELECT id, name, email, password, role FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    echo "<h2>Utilisateur trouvé ✅</h2>";
    echo "ID: " . $user['id'] . "<br>";
    echo "Name: " . $user['name'] . "<br>";
    echo "Email: " . $user['email'] . "<br>";
    echo "Role: " . $user['role'] . "<br>";
    echo "Password Hash: " . substr($user['password'], 0, 30) . "...<br>";
    
    // Tester le mot de passe
    echo "<h2>Test du mot de passe</h2>";
    if (password_verify($password, $user['password'])) {
        echo "✅ Le mot de passe 'Admin123!' est CORRECT<br>";
        echo "<p style='color: green; font-weight: bold;'>La connexion devrait fonctionner !</p>";
    } else {
        echo "❌ Le mot de passe 'Admin123!' est INCORRECT<br>";
        echo "<p style='color: red;'>Il faut réinitialiser le mot de passe</p>";
    }
} else {
    echo "<h2>❌ Utilisateur non trouvé</h2>";
    echo "L'email '$email' n'existe pas dans la base de données.";
}

// Lister tous les emails
echo "<h2>Tous les emails dans la base :</h2>";
$stmt = $db->query("SELECT email, role FROM users");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "- " . $row['email'] . " (" . $row['role'] . ")<br>";
}

