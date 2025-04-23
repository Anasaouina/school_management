<?php
require('../config/db.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secretary Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">Secretary Dashboard</h1>

        <!-- Menu -->
        <div class="flex space-x-4 mb-6">
            <button onclick="showSection('courses')" class="bg-blue-500 text-white p-2 rounded">Courses</button>
            <button onclick="showSection('professors')" class="bg-blue-500 text-white p-2 rounded">Professors</button>
            <button onclick="showSection('students')" class="bg-blue-500 text-white p-2 rounded">Students</button>
        </div>

        <!-- Courses Section -->
        <div id="courses-section" class="hidden">
            <h2 class="text-xl font-semibold mb-4">Manage Courses</h2>
            <button onclick="showCreateForm('course')" class="bg-green-500 text-white p-2 rounded mb-4">Create Course</button>

            <!-- Create Course Form (Hidden by Default) -->
            <div id="create-course-form" class="hidden bg-white p-6 rounded-lg shadow-md mb-6">
                <form method="POST" action="../backend/secre.php">
                    <input type="hidden" name="entity" value="courses">
                    <input type="text" name="titre" placeholder="Course Title" class="w-full p-2 mb-4 border rounded" required>
                    <input type="text" name="description" placeholder="Description" class="w-full p-2 mb-4 border rounded" required>
                    <input type="number" name="nb_heures" placeholder="Hours" class="w-full p-2 mb-4 border rounded" required>
                    <input type="text" name="type_cours" placeholder="Course Type" class="w-full p-2 mb-4 border rounded" required>
                    <button type="submit" name="create" class="bg-blue-500 text-white p-2 rounded">Create</button>
                </form>
            </div>

            <!-- Courses Table -->
            <div id="courses-table" class="bg-white p-6 rounded-lg shadow-md">
                <table class="w-full">
                    <thead>
                        <tr>
                            <th class="p-2 border-b">ID</th>
                            <th class="p-2 border-b">Title</th>
                            <th class="p-2 border-b">Description</th>
                            <th class="p-2 border-b">Hours</th>
                            <th class="p-2 border-b">Type</th>
                            <th class="p-2 border-b">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="courses-list">
                        <!-- Courses will be dynamically loaded here -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Professors Section -->
        <div id="professors-section" class="hidden">
            <h2 class="text-xl font-semibold mb-4">Manage Professors</h2>
            <button onclick="showCreateForm('professor')" class="bg-green-500 text-white p-2 rounded mb-4">Create Professor</button>

            <!-- Create Professor Form (Hidden by Default) -->
            <div id="create-professor-form" class="hidden bg-white p-6 rounded-lg shadow-md mb-6">
                <form method="POST" action="../backend/secre.php">
                    <input type="hidden" name="entity" value="professors">
                    <input type="text" name="nom" placeholder="Professor first Name" class="w-full p-2 mb-4 border rounded" required>
                    <input type="text" name="prenom" placeholder="Professor last Name" class="w-full p-2 mb-4 border rounded" required>
                    <input type="text" name="tele" placeholder="Professor phone" class="w-full p-2 mb-4 border rounded" required>
                    <input type="text" name="specialite" placeholder="Speciality" class="w-full p-2 mb-4 border rounded" required>
                    <button type="submit" name="create" class="bg-blue-500 text-white p-2 rounded">Create</button>
                </form>
            </div>

            <!-- Professors Table -->
            <div id="professors-table" class="bg-white p-6 rounded-lg shadow-md">
                <table class="w-full">
                    <thead>
                        <tr>
                            <th class="p-2 border-b">ID</th>
                            <th class="p-2 border-b">Name</th>
                            <th class="p-2 border-b">surname</th>
                            <th class="p-2 border-b">telephone</th>
                            <th class="p-2 border-b">Speciality</th>
                            <th class="p-2 border-b">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="professors-list">
                        <!-- Professors will be dynamically loaded here -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Students Section -->
        <div id="students-section" class="hidden">
            <h2 class="text-xl font-semibold mb-4">Manage Students</h2>
            <button onclick="showCreateForm('student')" class="bg-green-500 text-white p-2 rounded mb-4">Create Student</button>

            <!-- Create Student Form (Hidden by Default) -->
            <div id="create-student-form" class="hidden bg-white p-6 rounded-lg shadow-md mb-6">
                <form method="POST" action="../backend/secre.php">
                    <input type="hidden" name="entity" value="students">
                    <input type="text" name="nom" placeholder="Student Name" class="w-full p-2 mb-4 border rounded" required>
                    <input type="text" name="prenom" placeholder="Student Surname" class="w-full p-2 mb-4 border rounded" required>
                    <input type="number" name="annee" placeholder="Year" class="w-full p-2 mb-4 border rounded" required>
                    <button type="submit" name="create" class="bg-blue-500 text-white p-2 rounded">Create</button>
                </form>
            </div>

            <!-- Students Table -->
            <div id="students-table" class="bg-white p-6 rounded-lg shadow-md">
                <table class="w-full">
                    <thead>
                        <tr>
                            <th class="p-2 border-b">ID</th>
                            <th class="p-2 border-b">Name</th>
                            <th class="p-2 border-b">Surname</th>
                            <th class="p-2 border-b">Year</th>
                            <th class="p-2 border-b">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="students-list">
                        <!-- Students will be dynamically loaded here -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        // Show the selected section and hide others
        function showSection(section) {
            document.getElementById('courses-section')?.classList.add('hidden');
            document.getElementById('professors-section')?.classList.add('hidden');
            document.getElementById('students-section')?.classList.add('hidden');

            const sectionElement = document.getElementById(`${section}-section`);
            if (sectionElement) {
                sectionElement.classList.remove('hidden');
                fetchData(`../backend/secre.php?entity=${section}`, `${section}-list`);
            } else {
                console.error(`Section with ID "${section}-section" not found.`);
            }
        }

        // Show the create form for the selected entity
        function showCreateForm(entity) {
            document.getElementById(`create-${entity}-form`).classList.toggle('hidden');
        }

        // Fetch and display data
        async function fetchData(endpoint, elementId) {
            try {
                const response = await fetch(endpoint);
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                const data = await response.json();
                const listElement = document.getElementById(elementId);
                listElement.innerHTML = data.map(item => `
                    <tr>
                        <td class="p-2 border-b">${item.num_cours || item.num_enseignant || item.id_eleve}</td>
                        <td class="p-2 border-b">${item.titre || item.prenom || item.prenom}</td>
                        <td class="p-2 border-b">${item.description || item.nom  || item.nom}</td>
                        <td class="p-2 border-b">${item.nb_heures || item.tel  ||item.annee}</td>
                        <td class="p-2 border-b">${item.type_cours || item.fonction || ''}</td>
                        <td class="p-2 border-b">
                            <form method="POST" action="../backend/secre.php" style="display: inline;">
                                <input type="hidden" name="entity" value="${endpoint.split('=')[1]}">
                                <input type="hidden" name="delete" value="${item.num_cours || item.num_enseignant|| item.id_eleve}">
                                <button type="submit" class="bg-red-500 text-white p-1 rounded">Delete</button>
                            </form>
                            <button onclick="editItem('${endpoint}', ${item.num_cours || item.num_enseignant || item.id_eleve})" class="bg-yellow-500 text-white p-1 rounded">Edit</button>
                        </td>
                    </tr>
                `).join('');
            } catch (error) {
                console.error('Error fetching data:', error);
            }
        }

        // Edit an item
        async function editItem(endpoint, id) {
            const entity = endpoint.split('=')[1];
            const response = await fetch(`${endpoint}&id=${id}`);
            const data = await response.json();

            if (data.length > 0) {
                const item = data[0];
                let formHtml = '';

                switch (entity) {
                    case 'courses':
                        formHtml = `
                            <form method="POST" action="../backend/secre.php">
                                <input type="hidden" name="entity" value="courses">
                                <input type="hidden" name="update" value="1">
                                <input type="hidden" name="id" value="${id}">
                                <input type="text" name="titre" value="${item.titre}" class="w-full p-2 mb-4 border rounded" required>
                                <input type="text" name="description" value="${item.description}" class="w-full p-2 mb-4 border rounded" required>
                                <input type="number" name="nb_heures" value="${item.nb_heures}" class="w-full p-2 mb-4 border rounded" required>
                                <input type="text" name="type_cours" value="${item.type_cours}" class="w-full p-2 mb-4 border rounded" required>
                                <button type="submit" class="bg-blue-500 text-white p-2 rounded">Update</button>
                            </form>
                        `;
                        break;
                    case 'professors':
                        formHtml = `
                            <form method="POST" action="../backend/secre.php">
                                <input type="hidden" name="entity" value="professors">
                                <input type="hidden" name="update" value="1">
                                <input type="hidden" name="id" value="${id}">
                                <input type="text" name="nom" value="${item.nom}" class="w-full p-2 mb-4 border rounded" required>
                                <input type="text" name="prenom" value="${item.prenom}" class="w-full p-2 mb-4 border rounded" required>
                                <input type="text" name="tele" value="${item.tel}" class="w-full p-2 mb-4 border rounded" required>
                                <input type="text" name="specialite" value="${item.fonction}" class="w-full p-2 mb-4 border rounded" required>
                                <button type="submit" class="bg-blue-500 text-white p-2 rounded">Update</button>
                            </form>
                        `;
                        break;
                    case 'students':
                        formHtml = `
                            <form method="POST" action="../backend/secre.php">
                                <input type="hidden" name="entity" value="students">
                                <input type="hidden" name="update" value="1">
                                <input type="hidden" name="id" value="${id}">
                                <input type="text" name="nom" value="${item.nom}" class="w-full p-2 mb-4 border rounded" required>
                                <input type="text" name="prenom" value="${item.prenom}" class="w-full p-2 mb-4 border rounded" required>
                                <input type="number" name="annee" value="${item.annee}" class="w-full p-2 mb-4 border rounded" required>
                                <button type="submit" class="bg-blue-500 text-white p-2 rounded">Update</button>
                            </form>
                        `;
                        break;
                }

                const editFormContainer = document.createElement('div');
                editFormContainer.innerHTML = `
                    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
                        <div class="bg-white p-6 rounded-lg shadow-md">
                            <h2 class="text-xl font-semibold mb-4">Edit ${entity}</h2>
                            ${formHtml}
                            <button onclick="this.parentElement.parentElement.remove()" class="bg-red-500 text-white p-2 rounded mt-4">Cancel</button>
                        </div>
                    </div>
                `;
                document.body.appendChild(editFormContainer);
            }
        }

        // Show the courses section by default
        showSection('courses');
    </script>
</body>
</html>