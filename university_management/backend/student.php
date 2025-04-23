<?php
session_start();
require('../config/db.php');

// Vérification de l'authentification
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header('HTTP/1.1 403 Forbidden');
    exit('Accès non autorisé');
}

// Récupération de l'action
$action = $_GET['action'] ?? $_POST['action'] ?? '';

try {
    switch ($action) {
        case 'filter_grades':
            $course_id = $_GET['course_id'] ?? 0;
            $student_id = $_SESSION['user_id'];
            
            $sql = "
                SELECT c.titre, n.type_examen, n.note, n.explication 
                FROM NOTE n
                JOIN COURS c ON n.num_cours = c.num_cours
                WHERE n.num_eleve = :student_id
            ";
            
            if ($course_id) {
                $sql .= " AND n.num_cours = :course_id";
            }
            
            $sql .= " ORDER BY c.titre";
            
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':student_id', $student_id);
            
            if ($course_id) {
                $stmt->bindParam(':course_id', $course_id);
            }
            
            $stmt->execute();
            $grades = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (count($grades) > 0) {
                foreach ($grades as $grade) {
                    echo "<tr class='hover:bg-gray-50'>";
                    echo "<td class='py-2 px-4 border-b'>" . htmlspecialchars($grade['titre']) . "</td>";
                    echo "<td class='py-2 px-4 border-b'>" . htmlspecialchars($grade['type_examen']) . "</td>";
                    echo "<td class='py-2 px-4 border-b'>" . htmlspecialchars($grade['note']) . "</td>";
                    echo "<td class='py-2 px-4 border-b'>" . htmlspecialchars($grade['explication']) . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='4' class='py-4 px-4 text-center text-gray-500'>Aucune note disponible</td></tr>";
            }
            break;
            
        case 'get_schedule':
            $student_id = $_SESSION['user_id'];
            
            // Récupérer les séances de cours de l'étudiant
            $stmt = $conn->prepare("
                SELECT s.num_seance, c.titre, s.duree, s.description, s.salle, 
                       DATE_FORMAT(s.date_creation, '%Y-%m-%d') as date_seance,
                       DATE_FORMAT(s.date_creation, '%H:%i') as heure_seance,
                       e.nom, e.prenom
                FROM SEANCE s
                JOIN COURS c ON s.num_cours = c.num_cours
                JOIN INSCRIPTION i ON c.num_cours = i.num_cours
                JOIN COURS_SEMESTRIEL cs ON c.num_cours = cs.num_cours
                JOIN ENSEIGNANT e ON cs.num_enseignant = e.num_enseignant
                WHERE i.num_eleve = :student_id
                ORDER BY s.date_creation
            ");
            $stmt->bindParam(':student_id', $student_id);
            $stmt->execute();
            $sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (count($sessions) > 0) {
                echo '<div class="overflow-x-auto">';
                echo '<table class="min-w-full bg-white">';
                echo '<thead><tr class="bg-gray-100">
                        <th class="py-2 px-4 border-b">Date</th>
                        <th class="py-2 px-4 border-b">Heure</th>
                        <th class="py-2 px-4 border-b">Cours</th>
                        <th class="py-2 px-4 border-b">Durée</th>
                        <th class="py-2 px-4 border-b">Salle</th>
                        <th class="py-2 px-4 border-b">Enseignant</th>
                      </tr></thead>';
                echo '<tbody>';
                
                foreach ($sessions as $session) {
                    echo '<tr class="hover:bg-gray-50">';
                    echo '<td class="py-2 px-4 border-b">' . htmlspecialchars($session['date_seance']) . '</td>';
                    echo '<td class="py-2 px-4 border-b">' . htmlspecialchars($session['heure_seance']) . '</td>';
                    echo '<td class="py-2 px-4 border-b">' . htmlspecialchars($session['titre']) . '</td>';
                    echo '<td class="py-2 px-4 border-b">' . htmlspecialchars($session['duree']) . 'h</td>';
                    echo '<td class="py-2 px-4 border-b">' . htmlspecialchars($session['salle']) . '</td>';
                    echo '<td class="py-2 px-4 border-b">' . htmlspecialchars($session['prenom'] . ' ' . $session['nom']) . '</td>';
                    echo '</tr>';
                }
                
                echo '</tbody></table></div>';
            } else {
                echo '<p class="text-gray-500">Aucune séance programmée</p>';
            }
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