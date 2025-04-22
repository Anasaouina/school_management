<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require('../config/db.php');

// Fonctions pour récupérer les données
function getCourses($conn) {
    $stmt = $conn->query("SELECT * FROM COURS");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getProfessors($conn) {
    $stmt = $conn->query("SELECT * FROM ENSEIGNANT");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getStudents($conn) {
    $stmt = $conn->query("SELECT * FROM eleve");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Gestion des requêtes GET
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $entity = $_GET['entity'] ?? '';
    try {
        switch ($entity) {
            case 'courses':
                echo json_encode(getCourses($conn));
                break;
            case 'professors':
                echo json_encode(getProfessors($conn));
                break;
            case 'students':
                echo json_encode(getStudents($conn));
                break;
            default:
                echo json_encode([]);
                break;
        }
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit();
}

// Gestion des requêtes POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $entity = $_POST['entity'] ?? '';
    
    try {
        // Création
        if (isset($_POST['create'])) {
            switch ($entity) {
                case 'courses':
                    $stmt = $conn->prepare("INSERT INTO COURS (titre, description, nb_heures, type_cours) VALUES (?, ?, ?, ?)");
                    $stmt->execute([
                        $_POST['titre'],
                        $_POST['description'],
                        $_POST['nb_heures'],
                        $_POST['type_cours']
                    ]);
                    break;
                case 'professors':
                    $stmt = $conn->prepare("INSERT INTO ENSEIGNANT (nom, specialite) VALUES (?, ?)");
                    $stmt->execute([
                        $_POST['nom'],
                        $_POST['specialite']
                    ]);
                    break;
                case 'students':
                    $stmt = $conn->prepare("INSERT INTO eleve (nom, prenom, annee) VALUES (?, ?, ?)");
                    $stmt->execute([
                        $_POST['nom'],
                        $_POST['prenom'],
                        $_POST['annee']
                    ]);
                    break;
            }
            header("Location: ".$_SERVER['HTTP_REFERER']);
            exit();
        }
        
        // Suppression
        if (isset($_POST['delete'])) {
            $id = $_POST['delete'];
            switch ($entity) {
                case 'courses':
                    $stmt = $conn->prepare("DELETE FROM COURS WHERE num_cours = ?");
                    break;
                case 'professors':
                    $stmt = $conn->prepare("DELETE FROM ENSEIGNANT WHERE num_prof = ?");
                    break;
                case 'students':
                    $stmt = $conn->prepare("DELETE FROM eleve WHERE num_eleve = ?");
                    break;
            }
            $stmt->execute([$id]);
            header("Location: ".$_SERVER['HTTP_REFERER']);
            exit();
        }
        
        // Mise à jour
        if (isset($_POST['update'])) {
            $id = $_POST['id'];
            switch ($entity) {
                case 'courses':
                    $stmt = $conn->prepare("UPDATE COURS SET titre = ?, description = ?, nb_heures = ?, type_cours = ? WHERE num_cours = ?");
                    $stmt->execute([
                        $_POST['titre'],
                        $_POST['description'],
                        $_POST['nb_heures'],
                        $_POST['type_cours'],
                        $id
                    ]);
                    break;
                case 'professors':
                    $stmt = $conn->prepare("UPDATE ENSEIGNANT SET nom = ?, specialite = ? WHERE num_prof = ?");
                    $stmt->execute([
                        $_POST['nom'],
                        $_POST['specialite'],
                        $id
                    ]);
                    break;
                case 'students':
                    $stmt = $conn->prepare("UPDATE eleve SET nom = ?, prenom = ?, annee = ? WHERE num_eleve = ?");
                    $stmt->execute([
                        $_POST['nom'],
                        $_POST['prenom'],
                        $_POST['annee'],
                        $id
                    ]);
                    break;
            }
            header("Location: ".$_SERVER['HTTP_REFERER']);
            exit();
        }
    } catch (Exception $e) {
        die("Error: " . $e->getMessage());
    }
}
?>