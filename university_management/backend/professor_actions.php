<?php
session_start();
require('../config/db.php');

// Vérification de l'authentification
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'professor') {
    header('HTTP/1.1 403 Forbidden');
    exit('Accès non autorisé');
}

// Récupération de l'action
$action = $_GET['action'] ?? $_POST['action'] ?? '';

try {
    switch ($action) {
        case 'get_seances':
            $num_cours = $_GET['num_cours'] ?? 0;
            $stmt = $conn->prepare("
                SELECT * FROM SEANCE 
                WHERE num_cours = :num_cours 
                ORDER BY date_creation DESC
            ");
            $stmt->bindParam(':num_cours', $num_cours);
            $stmt->execute();
            $seances = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo '<h3 class="text-lg font-semibold mb-4">Séances du cours</h3>';
            
            if (count($seances) > 0) {
                echo '<div class="overflow-x-auto">';
                echo '<table class="min-w-full bg-white">';
                echo '<thead><tr class="bg-gray-100">
                        <th class="py-2 px-4 border-b">Date</th>
                        <th class="py-2 px-4 border-b">Durée</th>
                        <th class="py-2 px-4 border-b">Description</th>
                        <th class="py-2 px-4 border-b">Salle</th>
                      </tr></thead>';
                echo '<tbody>';
                
                foreach ($seances as $seance) {
                    echo '<tr class="hover:bg-gray-50">';
                    echo '<td class="py-2 px-4 border-b">' . htmlspecialchars($seance['date_creation']) . '</td>';
                    echo '<td class="py-2 px-4 border-b">' . htmlspecialchars($seance['duree']) . 'h</td>';
                    echo '<td class="py-2 px-4 border-b">' . htmlspecialchars($seance['description']) . '</td>';
                    echo '<td class="py-2 px-4 border-b">' . htmlspecialchars($seance['salle']) . '</td>';
                    echo '</tr>';
                }
                
                echo '</tbody></table></div>';
            } else {
                echo '<p class="text-gray-500">Aucune séance planifiée pour ce cours.</p>';
            }
            break;
            
        case 'get_students':
            $num_cours = $_GET['num_cours'] ?? 0;
            $stmt = $conn->prepare("
                SELECT e.num_eleve, e.nom, e.prenom, e.annee 
                FROM ELEVE e
                JOIN INSCRIPTION i ON e.num_eleve = i.num_eleve
                WHERE i.num_cours = :num_cours
                ORDER BY e.nom, e.prenom
            ");
            $stmt->bindParam(':num_cours', $num_cours);
            $stmt->execute();
            $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo '<h3 class="text-lg font-semibold mb-4">Étudiants inscrits</h3>';
            
            if (count($students) > 0) {
                echo '<div class="overflow-x-auto">';
                echo '<table class="min-w-full bg-white">';
                echo '<thead><tr class="bg-gray-100">
                        <th class="py-2 px-4 border-b">ID</th>
                        <th class="py-2 px-4 border-b">Nom</th>
                        <th class="py-2 px-4 border-b">Prénom</th>
                        <th class="py-2 px-4 border-b">Année</th>
                      </tr></thead>';
                echo '<tbody>';
                
                foreach ($students as $student) {
                    echo '<tr class="hover:bg-gray-50">';
                    echo '<td class="py-2 px-4 border-b">' . htmlspecialchars($student['num_eleve']) . '</td>';
                    echo '<td class="py-2 px-4 border-b">' . htmlspecialchars($student['nom']) . '</td>';
                    echo '<td class="py-2 px-4 border-b">' . htmlspecialchars($student['prenom']) . '</td>';
                    echo '<td class="py-2 px-4 border-b">' . htmlspecialchars($student['annee']) . '</td>';
                    echo '</tr>';
                }
                
                echo '</tbody></table></div>';
            } else {
                echo '<p class="text-gray-500">Aucun étudiant inscrit à ce cours.</p>';
            }
            break;
            
        case 'get_students_grades':
            $num_cours = $_GET['num_cours'] ?? 0;
            $stmt = $conn->prepare("
                SELECT e.num_eleve, e.nom, e.prenom, n.note, n.type_examen, n.explication
                FROM ELEVE e
                JOIN INSCRIPTION i ON e.num_eleve = i.num_eleve
                LEFT JOIN NOTE n ON e.num_eleve = n.num_eleve AND n.num_cours = :num_cours
                WHERE i.num_cours = :num_cours
                ORDER BY e.nom, e.prenom
            ");
            $stmt->bindParam(':num_cours', $num_cours);
            $stmt->execute();
            $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (count($students) > 0) {
                echo '<form method="POST" action="../backend/professor_actions.php">';
                echo '<input type="hidden" name="action" value="save_grades">';
                echo '<input type="hidden" name="num_cours" value="' . $num_cours . '">';
                
                echo '<div class="overflow-x-auto">';
                echo '<table class="min-w-full bg-white">';
                echo '<thead><tr class="bg-gray-100">
                        <th class="py-2 px-4 border-b">Nom</th>
                        <th class="py-2 px-4 border-b">Prénom</th>
                        <th class="py-2 px-4 border-b">Type Examen</th>
                        <th class="py-2 px-4 border-b">Note</th>
                        <th class="py-2 px-4 border-b">Commentaire</th>
                      </tr></thead>';
                echo '<tbody>';
                
                foreach ($students as $student) {
                    echo '<tr class="hover:bg-gray-50">';
                    echo '<td class="py-2 px-4 border-b">' . htmlspecialchars($student['nom']) . '</td>';
                    echo '<td class="py-2 px-4 border-b">' . htmlspecialchars($student['prenom']) . '</td>';
                    
                    echo '<td class="py-2 px-4 border-b">
                            <select name="type_examen[' . $student['num_eleve'] . ']" class="p-1 border rounded">
                                <option value="Examen"' . ($student['type_examen'] === 'Examen' ? ' selected' : '') . '>Examen</option>
                                <option value="TD"' . ($student['type_examen'] === 'TD' ? ' selected' : '') . '>TD</option>
                                <option value="TP"' . ($student['type_examen'] === 'TP' ? ' selected' : '') . '>TP</option>
                            </select>
                          </td>';
                    
                    echo '<td class="py-2 px-4 border-b">
                            <input type="number" step="0.01" min="0" max="20" 
                                   name="note[' . $student['num_eleve'] . ']" 
                                   value="' . htmlspecialchars($student['note'] ?? '') . '"
                                   class="p-1 border rounded w-20">
                          </td>';
                    
                    echo '<td class="py-2 px-4 border-b">
                            <input type="text" name="explication[' . $student['num_eleve'] . ']" 
                                   value="' . htmlspecialchars($student['explication'] ?? '') . '"
                                   class="p-1 border rounded w-full">
                          </td>';
                    
                    echo '</tr>';
                }
                
                echo '</tbody></table></div>';
                
                echo '<div class="mt-4">
                        <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded-md hover:bg-purple-700">
                            <i class="fas fa-save mr-2"></i> Enregistrer les notes
                        </button>
                      </div>';
                
                echo '</form>';
            } else {
                echo '<p class="text-gray-500">Aucun étudiant inscrit à ce cours.</p>';
            }
            break;
            
        case 'planifier_seance':
            $num_cours = $_POST['num_cours'];
            $duree = $_POST['duree'];
            $description = $_POST['description'];
            $salle = $_POST['salle'];
            
            $stmt = $conn->prepare("
                INSERT INTO SEANCE (num_cours, duree, description, salle, date_creation)
                VALUES (:num_cours, :duree, :description, :salle, NOW())
            ");
            $stmt->bindParam(':num_cours', $num_cours);
            $stmt->bindParam(':duree', $duree);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':salle', $salle);
            $stmt->execute();
            
            header('Location: ../professor_dashboard.php?success=1');
            break;
            
        case 'save_grades':
            $num_cours = $_POST['num_cours'];
            $notes = $_POST['note'] ?? [];
            $types = $_POST['type_examen'] ?? [];
            $explications = $_POST['explication'] ?? [];
            
            foreach ($notes as $num_eleve => $note) {
                // Vérifier si une note existe déjà pour cet étudiant et ce cours
                $stmt = $conn->prepare("
                    SELECT num_note FROM NOTE 
                    WHERE num_eleve = :num_eleve AND num_cours = :num_cours
                    LIMIT 1
                ");
                $stmt->bindParam(':num_eleve', $num_eleve);
                $stmt->bindParam(':num_cours', $num_cours);
                $stmt->execute();
                $exists = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($exists) {
                    // Mise à jour de la note existante
                    $stmt = $conn->prepare("
                        UPDATE NOTE SET 
                            note = :note,
                            type_examen = :type_examen,
                            explication = :explication
                        WHERE num_note = :num_note
                    ");
                    $stmt->bindParam(':num_note', $exists['num_note']);
                } else {
                    // Insertion d'une nouvelle note
                    $stmt = $conn->prepare("
                        INSERT INTO NOTE (num_eleve, num_cours, note, type_examen, explication)
                        VALUES (:num_eleve, :num_cours, :note, :type_examen, :explication)
                    ");
                    $stmt->bindParam(':num_eleve', $num_eleve);
                    $stmt->bindParam(':num_cours', $num_cours);
                }
                
                $stmt->bindParam(':note', $note);
                $stmt->bindParam(':type_examen', $types[$num_eleve]);
                $stmt->bindParam(':explication', $explications[$num_eleve]);
                $stmt->execute();
            }
            
            header('Location: ../professor_dashboard.php?success=1');
            break;
            
        default:
            header('HTTP/1.1 400 Bad Request');
            echo 'Action non reconnue';
            break;
    }
} catch (PDOException $e) {
    header('HTTP/1.1 500 Internal Server Error');
    echo 'Erreur de base de données: ' . $e->getMessage();
}
?>