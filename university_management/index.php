<?php
session_start();
require('./config/db.php');

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // Vérification des identifiants
    if ($username === 'user_secretaire' && $password === 'password') {
        $_SESSION['role'] = 'secretaire';
        header('Location: secretary_dashboard.php');
        exit();
    } elseif ($username === 'user_enseignant' && $password === 'password') {
        $_SESSION['role'] = 'professor';
        header('Location: professor_dashboard.php');
        exit();
    } elseif ($username === 'user_eleve' && $password === 'password') {
        $_SESSION['role'] = 'student';
        header('Location: student_dashboard.php');
        exit();
    } else {
        $error = 'Identifiants incorrects';
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Université</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-blue-600">Bienvenue à l'Université</h1>
            <p class="text-gray-600 mt-2">Connectez-vous pour accéder à votre espace</p>
        </div>
        
        <?php if ($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="login.php">
            <div class="mb-4">
                <label for="username" class="block text-gray-700 text-sm font-bold mb-2">Nom d'utilisateur</label>
                <input type="text" id="username" name="username" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="mb-6">
                <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Mot de passe</label>
                <input type="password" id="password" name="password" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <button type="submit" 
                    class="w-full bg-blue-500 text-white font-bold py-2 px-4 rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">
                Se connecter
            </button>
        </form>
    </div>
</body>
</html>