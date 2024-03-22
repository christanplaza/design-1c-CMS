<?php
session_start();

$baseUrl = '';
require $baseUrl . '../config/db.php';

// Fetch all teachers
$sql = "SELECT * FROM teachers;";
$stmt = $pdo->query($sql);
$teachers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en" class="h-full bg-white">
<?php include 'head.php'; ?>

<body class="bg-gray-100 font-family-karla flex h-full">
    <?php include('sidebar.php'); ?>

    <div class="relative w-full flex flex-col h-screen overflow-y-hidden">
        <!-- Desktop Header -->
        <header class="w-full items-center bg-white py-2 px-6 hidden sm:flex">
            <div class="w-1/2"></div>
            <div x-data="{ isOpen: false }" class="relative w-1/2 flex justify-end">
                <button @click="isOpen = !isOpen" class="realtive z-10 w-12 h-12 rounded-full overflow-hidden border-4 border-gray-400 hover:border-gray-300 focus:border-gray-300 focus:outline-none">
                    <img src="https://source.unsplash.com/uJ8LNVCBjFQ/400x400">
                </button>
                <button x-show="isOpen" @click="isOpen = false" class="h-full w-full fixed inset-0 cursor-default"></button>
                <div x-show="isOpen" class="absolute w-32 bg-white rounded-lg shadow-lg py-2 mt-16">
                    <a href="#" class="block px-4 py-2 account-link hover:text-white">Account</a>
                    <a href="#" class="block px-4 py-2 account-link hover:text-white">Support</a>
                    <a href="#" class="block px-4 py-2 account-link hover:text-white">Sign Out</a>
                </div>
            </div>
        </header>

        <!-- Mobile Header & Nav -->
        <header x-data="{ isOpen: false }" class="w-full bg-sidebar py-5 px-6 sm:hidden">
            <div class="flex items-center justify-between">
                <a href="index.html" class="text-white text-3xl font-semibold uppercase hover:text-gray-300">Admin</a>
                <button @click="isOpen = !isOpen" class="text-white text-3xl focus:outline-none">
                    <i x-show="!isOpen" class="fas fa-bars"></i>
                    <i x-show="isOpen" class="fas fa-times"></i>
                </button>
            </div>

            <!-- Dropdown Nav -->
            <?php include('mobile_sidebar.php'); ?>
            <!-- <button class="w-full bg-white cta-btn font-semibold py-2 mt-5 rounded-br-lg rounded-bl-lg rounded-tr-lg shadow-lg hover:shadow-xl hover:bg-gray-300 flex items-center justify-center">
                <i class="fas fa-plus mr-3"></i> New Report
            </button> -->
        </header>

        <div class="w-full overflow-x-hidden border-t flex flex-col">
            <main class="w-full flex-grow p-6">
                <h1 class="text-3xl text-black pb-2">Teachers</h1>
                <?php if (isset($_SESSION['success_message'])) : ?>
                    <div class="relative block w-full p-4 mb-4 text-base leading-5 text-grey-700 bg-green-200 rounded-lg opacity-100 font-regular">
                        <?php echo $_SESSION['success_message']; ?>
                    </div>
                    <?php unset($_SESSION['success_message']); ?>
                <?php elseif (isset($_SESSION['error_message'])) : ?>
                    <div class="relative block w-full p-4 mb-4 text-base leading-5 text-grey-700 bg-red-200 rounded-lg opacity-100 font-regular">
                        <?php echo $_SESSION['error_message']; ?>
                    </div>
                    <?php unset($_SESSION['error_message']); ?>
                <?php endif; ?>

                <div class="w-full mt-4">
                    <a href="#" onclick="toggleModal('createTeacherModal')" class="inline-block py-2 px-4 bg-green-600 text-white rounded-md mb-4 hover:bg-green-700 focus:outline-none focus:shadow-outline-blue active:bg-green-800">
                        New Teacher
                        <i class="fas fa-plus"></i>
                    </a>
                    <div class="bg-white overflow-auto">
                        <table class="min-w-full bg-white">
                            <thead class="bg-gray-800 text-white">
                                <tr>
                                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">ID</th>
                                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Name</th>
                                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Email</th>
                                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Status</th>
                                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-700">
                                <?php if (empty($teachers)) : ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-3 px-4 font-bold">No records found</td>
                                    </tr>
                                <?php else : ?>
                                    <?php foreach ($teachers as $teacher) : ?>
                                        <tr>
                                            <td class="text-left py-3 px-4"><?= $teacher['id']; ?></td>
                                            <td class="text-left py-3 px-4"><?= $teacher['name']; ?></td>
                                            <td class="text-left py-3 px-4"><?= $teacher['email_address']; ?></td>
                                            <td class="text-left py-3 px-4">
                                                <?php if ($teacher['status'] == 'inactive'): ?>
                                                    <span class="bg-orange-100 text-orange-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-orange-900 dark:text-orange-300">
                                                        Inactive
                                                    </span>
                                                <?php elseif($teacher['status'] == 'active'): ?>
                                                    <span class="bg-green-100 text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">
                                                        Active
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-left py-3 px-4">
                                                <div class="flex items-center space-x-4">
                                                    <!-- Edit Action -->
                                                    <a href="javascript:void(0);" onclick="openEditModal(<?= $teacher['id']; ?>)" class="text-yellow-500 hover:text-yellow-700">
                                                        <i class="fas fa-pen"></i>
                                                    </a>
                                                    <!-- Delete Action -->
                                                    <!-- <a href="javascript:void(0);" onclick="deleteClass('<?= $teacher['id']; ?>')" class="text-red-500 hover:text-red-700">
                                                        <i class="fas fa-trash"></i>
                                                    </a> -->
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>

    </div>

    <!-- Create Student Modal -->
    <div id="createTeacherModal" class="fixed inset-0 overflow-y-auto hidden">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:p-0">
            <!-- Background Overlay -->
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <!-- Modal Content -->
            <div class="inline-block align-bottom p-6 bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <button onclick="toggleModal('createTeacherModal')" class="absolute top-0 right-0 m-4 text-gray-500 hover:text-gray-700 focus:outline-none">
                    <i class="fas fa-times"></i>
                </button>
                <h1 class="text-3xl text-black pb-6">Add Teacher</h1>
                <form action="teachers/process_create_teacher.php" method="POST">
                    <!-- Teacher Name -->
                    <div class="mb-4">
                        <label for="teacher_name" class="block text-sm font-medium text-gray-600">Name</label>
                        <input type="text" id="teacher_name" name="teacher_name" class="mt-1 p-2 border rounded-md w-full" required>
                    </div>

                    <!-- Email -->
                    <div class="mb-4">
                        <label for="teacher_email" class="block text-sm font-medium text-gray-600">Email Address</label>
                        <input type="email" id="teacher_email" name="teacher_email" class="mt-1 p-2 border rounded-md w-full" required>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex items-center justify-end">
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:shadow-outline-green active:bg-green-800">
                            Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Class Modal -->
    <div id="editTeacherModal" class="fixed inset-0 overflow-y-auto hidden">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:p-0">
            <!-- Background Overlay -->
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <!-- Modal Content -->
            <div class="inline-block align-bottom p-6 bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <h1 class="text-3xl text-black pb-6">Edit Student</h1>
                <form id="editTeacherForm" action="teachers/process_edit_teacher.php" method="POST">
                    <!-- Student Number -->
                    <input type="hidden" id="editTeacherId" name="teacher_id" value="">
                    <div class="mb-4">
                        <label for="editTeacherName" class="block text-sm font-medium text-gray-600">Name</label>
                        <input type="text" id="editTeacherName" name="editTeacherName" class="mt-1 p-2 border rounded-md w-full" required>
                    </div>

                    <!-- Email -->
                    <div class="mb-4">
                        <label for="editTeacherEmail" class="block text-sm font-medium text-gray-600">Email Address</label>
                        <input type="email" id="editTeacherEmail" name="editTeacherEmail" class="mt-1 p-2 border rounded-md w-full" required>
                    </div>

                    <!-- Status -->
                    <div class="mb-4">
                        <label for="editTeacherStatus" class="block text-sm font-medium text-gray-600">Status</label>
                        <select id="editTeacherStatus" name="editTeacherStatus" class="mt-1 p-2 border rounded-md w-full" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>

                    <!-- Additional fields go here -->
                    <!-- Submit Button -->
                    <div class="flex items-center justify-end">
                        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 focus:outline-none focus:shadow-outline-blue active:bg-blue-800">
                            Update Teacher
                        </button>
                    </div>
                </form>
                <button onclick="toggleModal('editTeacherModal')" class="absolute top-0 right-0 m-4 text-gray-500 hover:text-gray-700 focus:outline-none">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <script>
        // Function to open the edit modal and populate the form
        function openEditModal(teacherId) {
            const modal = document.getElementById('editTeacherModal');
            const form = document.getElementById('editTeacherForm');
            const teacherNameInput = document.getElementById('editTeacherName');
            const teacherEmailInput = document.getElementById('editTeacherEmail');
            const teacherStatusInput = document.getElementById('editTeacherStatus');
            const studentIdInput = document.getElementById('editTeacherId');

            // You may customize the AJAX request URL and method based on your server-side implementation
            const apiUrl = 'api/get_teacher_details.php';
            const formData = new FormData();
            formData.append('teacher_id', teacherId);

            // Fetch class details via AJAX
            fetch(apiUrl, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    // Populate form fields with class details
                    teacherNameInput.value = data.name;
                    teacherEmailInput.value = data.email_address;
                    teacherStatusInput.value = data.status;
                    studentIdInput.value = teacherId;

                    // Toggle the visibility of the modal
                    modal.classList.toggle('hidden');
                })
                .catch(error => {
                    console.error('Error fetching teacher details:', error);
                });
        }

        function deleteClass(teacherId) {
            // You can add a confirmation dialog here if needed

            // Send the delete request via AJAX with a POST method
            fetch('teachers/process_delete_teacher.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(teacherId),
                })
                .then(response => response.json())
                .then(data => {
                    console.log(data);
                    if (data.success) {
                        window.location.reload();
                    }
                })
                .catch(error => {
                    console.error('Error deleting teacher:', error);
                });
        }

        function toggleModal(modalId) {
            const modal = document.getElementById(modalId);
            modal.classList.toggle('hidden');
        }
    </script>

    <!-- AlpineJS -->
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>
    <!-- Font Awesome -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/js/all.min.js" integrity="sha256-KzZiKy0DWYsnwMF+X1DvQngQ2/FxF7MF3Ff72XcpuPs=" crossorigin="anonymous"></script>
</body>

</html>