<?php
session_start();
require('../config/db.php');

// Vérification du rôle
if (!isset($_SESSION['role'])) {
    header('Location: ../login.php');
    exit();
}

if ($_SESSION['role'] !== 'professor') {
    header('Location: ../unauthorized.php');
    exit();
}

// Récupérer les informations du professeur
try {
    $stmt = $conn->prepare("SELECT * FROM ENSEIGNANT WHERE num_enseignant = :id");
    $stmt->bindParam(':id', $_SESSION['user_id']);
    $stmt->execute();
    $professor = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur de base de données: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord Professeur</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <!-- Barre de navigation -->
    <nav class="bg-blue-600 text-white p-4 shadow-md">
        <div class="container mx-auto flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <i class="fas fa-chalkboard-teacher text-2xl"></i>
                <h1 class="text-xl font-bold">Tableau de Bord Professeur</h1>
            </div>
            <div class="flex items-center space-x-4">
                <span class="font-medium"><?= htmlspecialchars($professor['prenom']) . ' ' . htmlspecialchars($professor['nom']);?></span>
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
                    <i class="fas fa-user-tie text-blue-600 text-2xl"></i>
                </div>
                <div>
                    <h2 class="text-xl font-semibold">Bienvenue, <?= htmlspecialchars($professor['prenom']) ?></h2>
                    <p class="text-gray-600">Vous pouvez gérer vos cours, séances et notes ici.</p>
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
                    <p class="text-gray-600 mb-4">Consultez et gérez vos cours assignés</p>
                    <a href="#mes-cours" onclick="showSection('mes-cours')" 
                       class="text-blue-600 hover:text-blue-800 font-medium">
                        Voir mes cours <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>

            <!-- Planifier Séance -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                <div class="bg-green-500 text-white p-4">
                    <h3 class="font-semibold"><i class="fas fa-calendar-plus mr-2"></i> Planifier Séance</h3>
                </div>
                <div class="p-4">
                    <p class="text-gray-600 mb-4">Créez une nouvelle séance de cours</p>
                    <a href="#planifier-seance" onclick="showSection('planifier-seance')" 
                       class="text-green-600 hover:text-green-800 font-medium">
                        Planifier <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>

            <!-- Saisir Notes -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                <div class="bg-purple-500 text-white p-4">
                    <h3 class="font-semibold"><i class="fas fa-edit mr-2"></i> Saisir Notes</h3>
                </div>
                <div class="p-4">
                    <p class="text-gray-600 mb-4">Enregistrez les notes des étudiants</p>
                    <a href="#saisir-notes" onclick="showSection('saisir-notes')" 
                       class="text-purple-600 hover:text-purple-800 font-medium">
                        Saisir notes <i class="fas fa-arrow-right ml-1"></i>
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
                            <th class="py-2 px-4 border-b">Semestre</th>
                            <th class="py-2 px-4 border-b">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Récupérer les cours du professeur
                        try {
                            $stmt = $conn->prepare("
                                SELECT c.num_cours, c.titre, c.type_cours, c.nb_heures, cs.semestre 
                                FROM COURS c
                                JOIN COURS_SEMESTRIEL cs ON c.num_cours = cs.num_cours
                                WHERE cs.num_enseignant = :id
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
                                    echo "<td class='py-2 px-4 border-b'>" . htmlspecialchars($course['semestre']) . "</td>";
                                    echo "<td class='py-2 px-4 border-b'>
                                            <a href='#voir-seances' onclick=\"showSection('voir-seances', " . $course['num_cours'] . ")\" 
                                               class='text-blue-600 hover:text-blue-800 mr-2'>
                                                <i class='fas fa-calendar-alt'></i> Séances
                                            </a>
                                            <a href='#voir-etudiants' onclick=\"showSection('voir-etudiants', " . $course['num_cours'] . ")\" 
                                               class='text-green-600 hover:text-green-800'>
                                                <i class='fas fa-users'></i> Étudiants
                                            </a>
                                          </td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='6' class='py-4 px-4 text-center text-gray-500'>Aucun cours assigné</td></tr>";
                            }
                        } catch (PDOException $e) {
                            echo "<tr><td colspan='6' class='py-4 px-4 text-center text-red-500'>Erreur: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Section Planifier Séance -->
        <div id="planifier-seance-section" class="hidden bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4"><i class="fas fa-calendar-plus mr-2 text-green-500"></i> Planifier une Séance</h2>
            
            <form id="planifier-seance-form" method="POST" action="../backend/professor_actions.php">
                <input type="hidden" name="action" value="planifier_seance">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-gray-700 mb-2">Cours</label>
                        <select name="num_cours" class="w-full p-2 border rounded-md" required>
                            <option value="">Sélectionner un cours</option>
                            <?php
                            foreach ($courses as $course) {
                                echo "<option value='" . $course['num_cours'] . "'>" 
                                     . htmlspecialchars($course['titre']) . " (" . htmlspecialchars($course['type_cours']) . ")"
                                     . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-700 mb-2">Durée (heures)</label>
                        <input type="number" name="duree" min="1" max="8" class="w-full p-2 border rounded-md" required>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Description</label>
                    <textarea name="description" rows="3" class="w-full p-2 border rounded-md"></textarea>
                </div>
                
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Salle</label>
                    <input type="text" name="salle" class="w-full p-2 border rounded-md">
                </div>
                
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
                    <i class="fas fa-save mr-2"></i> Planifier la séance
                </button>
            </form>
        </div>

        <!-- Section Saisir Notes -->
        <div id="saisir-notes-section" class="hidden bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4"><i class="fas fa-edit mr-2 text-purple-500"></i> Saisir Notes</h2>
            
            <div class="mb-4">
                <label class="block text-gray-700 mb-2">Sélectionner un cours</label>
                <select id="select-cours-notes" class="w-full p-2 border rounded-md" onchange="loadStudentsForGrades(this.value)">
                    <option value="">-- Sélectionner un cours --</option>
                    <?php
                    foreach ($courses as $course) {
                        echo "<option value='" . $course['num_cours'] . "'>" 
                             . htmlspecialchars($course['titre']) . " (" . htmlspecialchars($course['type_cours']) . ")"
                             . "</option>";
                    }
                    ?>
                </select>
            </div>
            
            <div id="students-grades-container">
                <p class="text-gray-500 text-center py-4">Sélectionnez un cours pour afficher la liste des étudiants</p>
            </div>
        </div>

        <!-- Section Voir Séances (chargée dynamiquement) -->
        <div id="voir-seances-section" class="hidden bg-white rounded-lg shadow-md p-6 mb-6">
            <!-- Contenu chargé via AJAX -->
        </div>

        <!-- Section Voir Étudiants (chargée dynamiquement) -->
        <div id="voir-etudiants-section" class="hidden bg-white rounded-lg shadow-md p-6 mb-6">
            <!-- Contenu chargé via AJAX -->
        </div>
    </div>

    <script>
        // Fonction pour afficher/masquer les sections
        function showSection(section, courseId = null) {
            // Masquer toutes les sections
            document.querySelectorAll('[id$="-section"]').forEach(el => {
                el.classList.add('hidden');
            });
            
            // Afficher la section demandée
            const sectionElement = document.getElementById(section + '-section');
            if (sectionElement) {
                sectionElement.classList.remove('hidden');
                
                // Chargement dynamique pour certaines sections
                if (section === 'voir-seances' && courseId) {
                    loadSeances(courseId);
                } else if (section === 'voir-etudiants' && courseId) {
                    loadStudents(courseId);
                }
            }
        }

        // Charger les séances d'un cours
        function loadSeances(courseId) {
            fetch(`../backend/professor_actions.php?action=get_seances&num_cours=${courseId}`)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('voir-seances-section').innerHTML = data;
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('voir-seances-section').innerHTML = 
                        '<p class="text-red-500">Erreur lors du chargement des séances</p>';
                });
        }

        // Charger les étudiants d'un cours
        function loadStudents(courseId) {
            fetch(`../backend/professor_actions.php?action=get_students&num_cours=${courseId}`)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('voir-etudiants-section').innerHTML = data;
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('voir-etudiants-section').innerHTML = 
                        '<p class="text-red-500">Erreur lors du chargement des étudiants</p>';
                });
        }

        // Charger les étudiants pour la saisie des notes
        function loadStudentsForGrades(courseId) {
            if (!courseId) {
                document.getElementById('students-grades-container').innerHTML = 
                    '<p class="text-gray-500 text-center py-4">Sélectionnez un cours pour afficher la liste des étudiants</p>';
                return;
            }
            
            fetch(`../backend/professor_actions.php?action=get_students_grades&num_cours=${courseId}`)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('students-grades-container').innerHTML = data;
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('students-grades-container').innerHTML = 
                        '<p class="text-red-500">Erreur lors du chargement des étudiants</p>';
                });
        }

        // Afficher la section Mes Cours par défaut
        document.addEventListener('DOMContentLoaded', function() {
            showSection('mes-cours');
        });
    </script>
</body>
</html>