<?php
session_start();
require('../config/db.php');

// Vérification du rôle
if (!isset($_SESSION['role'])) {
    header('Location: ../login.php');
    exit();
}

if ($_SESSION['role'] !== 'student') {
    header('Location: ../unauthorized.php');
    exit();
}

// Récupérer les informations de l'étudiant
try {
    $stmt = $conn->prepare("SELECT * FROM eleve WHERE id_eleve = :id");
    $stmt->bindParam(':id', $_SESSION['user_id']);
    $stmt->execute();
    $student = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur de base de données: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord Étudiant</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <!-- Barre de navigation -->
    <nav class="bg-blue-600 text-white p-4 shadow-md">
        <div class="container mx-auto flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <i class="fas fa-user-graduate text-2xl"></i>
                <h1 class="text-xl font-bold">Tableau de Bord Étudiant</h1>
            </div>
            <div class="flex items-center space-x-4">
                <span class="font-medium"><?= htmlspecialchars($student['prenom']) . ' ' . htmlspecialchars($student['nom']);?></span>
                <a href="../logout.php" class="bg-red-500 hover:bg-red-600 px-3 py-1 rounded-md text-sm">
                    <i class="fas fa-sign-out-alt"></i> Déconnexion
                </a>
            </div>
        </div>
    </nav>

    <div class="container mx-auto p-4">
        <!-- Section de bienvenue -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex items-center space-x-4">
                <div class="bg-blue-100 p-4 rounded-full">
                    <i class="fas fa-user-graduate text-blue-600 text-2xl"></i>
                </div>
                <div>
                    <h2 class="text-xl font-semibold">Bienvenue, <?= htmlspecialchars($student['prenom']) ?></h2>
                    <p class="text-gray-600">Consultez vos cours, notes et emploi du temps.</p>
                </div>
            </div>
        </div>

        <!-- Menu principal -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <!-- Mes Cours -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                <div class="bg-blue-500 text-white p-4">
                    <h3 class="font-semibold"><i class="fas fa-book mr-2"></i> Mes Cours</h3>
                </div>
                <div class="p-4">
                    <p class="text-gray-600 mb-4">Consultez vos cours inscrits</p>
                    <a href="#mes-cours" onclick="showSection('mes-cours')" 
                       class="text-blue-600 hover:text-blue-800 font-medium">
                        Voir mes cours <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>

            <!-- Mes Notes -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                <div class="bg-green-500 text-white p-4">
                    <h3 class="font-semibold"><i class="fas fa-chart-bar mr-2"></i> Mes Notes</h3>
                </div>
                <div class="p-4">
                    <p class="text-gray-600 mb-4">Consultez vos résultats académiques</p>
                    <a href="#mes-notes" onclick="showSection('mes-notes')" 
                       class="text-green-600 hover:text-green-800 font-medium">
                        Voir mes notes <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>

            <!-- Emploi du temps -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                <div class="bg-purple-500 text-white p-4">
                    <h3 class="font-semibold"><i class="fas fa-calendar-alt mr-2"></i> Emploi du temps</h3>
                </div>
                <div class="p-4">
                    <p class="text-gray-600 mb-4">Consultez votre planning</p>
                    <a href="#emploi-du-temps" onclick="showSection('emploi-du-temps')" 
                       class="text-purple-600 hover:text-purple-800 font-medium">
                        Voir planning <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Sections de contenu -->
        <div id="mes-cours-section" class="hidden bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4"><i class="fas fa-book mr-2 text-blue-500"></i> Mes Cours</h2>
            
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="py-2 px-4 border-b">Code</th>
                            <th class="py-2 px-4 border-b">Titre</th>
                            <th class="py-2 px-4 border-b">Type</th>
                            <th class="py-2 px-4 border-b">Heures</th>
                            <th class="py-2 px-4 border-b">Enseignant</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Récupérer les cours de l'étudiant
                        try {
                            $stmt = $conn->prepare("
                                SELECT c.num_cours, c.titre, c.type_cours, c.nb_heures, e.nom, e.prenom 
                                FROM COURS c
                                JOIN INSCRIPTION i ON c.num_cours = i.num_cours
                                JOIN COURS_SEMESTRIEL cs ON c.num_cours = cs.num_cours
                                JOIN ENSEIGNANT e ON cs.num_enseignant = e.num_enseignant
                                WHERE i.num_eleve = :id
                            ");
                            $stmt->bindParam(':id', $_SESSION['user_id']);
                            $stmt->execute();
                            $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

                            if (count($courses) > 0) {
                                foreach ($courses as $course) {
                                    echo "<tr class='hover:bg-gray-50'>";
                                    echo "<td class='py-2 px-4 border-b'>" . htmlspecialchars($course['num_cours']) . "</td>";
                                    echo "<td class='py-2 px-4 border-b'>" . htmlspecialchars($course['titre']) . "</td>";
                                    echo "<td class='py-2 px-4 border-b'>" . htmlspecialchars($course['type_cours']) . "</td>";
                                    echo "<td class='py-2 px-4 border-b'>" . htmlspecialchars($course['nb_heures']) . "</td>";
                                    echo "<td class='py-2 px-4 border-b'>" . htmlspecialchars($course['prenom'] . ' ' . $course['nom']) . "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='5' class='py-4 px-4 text-center text-gray-500'>Aucun cours inscrit</td></tr>";
                            }
                        } catch (PDOException $e) {
                            echo "<tr><td colspan='5' class='py-4 px-4 text-center text-red-500'>Erreur: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Section Mes Notes -->
        <div id="mes-notes-section" class="hidden bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4"><i class="fas fa-chart-bar mr-2 text-green-500"></i> Mes Notes</h2>
            
            <div class="mb-4">
                <label class="block text-gray-700 mb-2">Filtrer par cours:</label>
                <select id="filter-course" class="w-full p-2 border rounded-md" onchange="filterGrades(this.value)">
                    <option value="">Tous les cours</option>
                    <?php
                    foreach ($courses as $course) {
                        echo "<option value='" . $course['num_cours'] . "'>" 
                             . htmlspecialchars($course['titre']) . "</option>";
                    }
                    ?>
                </select>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="py-2 px-4 border-b">Cours</th>
                            <th class="py-2 px-4 border-b">Type</th>
                            <th class="py-2 px-4 border-b">Note</th>
                            <th class="py-2 px-4 border-b">Commentaire</th>
                        </tr>
                    </thead>
                    <tbody id="grades-table-body">
                        <?php
                        try {
                            $stmt = $conn->prepare("
                                SELECT c.titre, n.type_examen, n.note, n.explication 
                                FROM NOTE n
                                JOIN COURS c ON n.num_cours = c.num_cours
                                WHERE n.num_eleve = :id
                                ORDER BY c.titre
                            ");
                            $stmt->bindParam(':id', $_SESSION['user_id']);
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
                        } catch (PDOException $e) {
                            echo "<tr><td colspan='4' class='py-4 px-4 text-center text-red-500'>Erreur: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Section Emploi du temps -->
        <div id="emploi-du-temps-section" class="hidden bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4"><i class="fas fa-calendar-alt mr-2 text-purple-500"></i> Emploi du temps</h2>
            
            <div id="schedule-container">
                <p class="text-gray-500 text-center py-4">Chargement de l'emploi du temps...</p>
            </div>
        </div>
    </div>

    <script>
        // Fonction pour afficher/masquer les sections
        function showSection(section) {
            // Masquer toutes les sections
            document.querySelectorAll('[id$="-section"]').forEach(el => {
                el.classList.add('hidden');
            });
            
            // Afficher la section demandée
            const sectionElement = document.getElementById(section + '-section');
            if (sectionElement) {
                sectionElement.classList.remove('hidden');
                
                // Charger dynamiquement l'emploi du temps si nécessaire
                if (section === 'emploi-du-temps') {
                    loadSchedule();
                }
            }
        }

        // Filtrer les notes par cours
        function filterGrades(courseId) {
            fetch(`../backend/student.php?action=filter_grades&course_id=${courseId}`)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('grades-table-body').innerHTML = data;
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }

        // Charger l'emploi du temps
        function loadSchedule() {
            fetch('../backend/student.php?action=get_schedule')
                .then(response => response.text())
                .then(data => {
                    document.getElementById('schedule-container').innerHTML = data;
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('schedule-container').innerHTML = 
                        '<p class="text-red-500">Erreur lors du chargement de l\'emploi du temps</p>';
                });
        }

        // Afficher la section Mes Cours par défaut
        document.addEventListener('DOMContentLoaded', function() {
            showSection('mes-cours');
        });
    </script>
</body>
</html>