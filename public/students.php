<?php
session_start();

require '../config/db.php';

// Fetch all students
$sql = "SELECT * FROM students";
$stmt = $pdo->query($sql);
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
                <h1 class="text-3xl text-black pb-2">Students</h1>
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
                    <a href="#" onclick="toggleModal('createClassModal')" class="inline-block py-2 px-4 bg-green-600 text-white rounded-md mb-4 hover:bg-green-700 focus:outline-none focus:shadow-outline-blue active:bg-green-800">
                        Add Student
                        <i class="fas fa-plus"></i>
                    </a>
                    <div class="bg-white overflow-auto">
                        <table class="min-w-full bg-white">
                            <thead class="bg-gray-800 text-white">
                                <tr>
                                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">ID</th>
                                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Student Number</th>
                                    <th class="w-1/3 text-left py-3 px-4 uppercase font-semibold text-sm">First Name</th>
                                    <th class="w-1/3 text-left py-3 px-4 uppercase font-semibold text-sm">Last Name</th>
                                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-700">
                                <?php if (empty($students)) : ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-3 px-4 font-bold">No records found</td>
                                    </tr>
                                <?php else : ?>
                                    <?php foreach ($students as $student) : ?>
                                        <tr>
                                            <td class="text-left py-3 px-4"><?= $student['id']; ?></td>
                                            <td class="w-1/3 text-left py-3 px-4"><?= $student['student_number']; ?></td>
                                            <td class="w-1/3 text-left py-3 px-4"><?= $student['first_name']; ?></td>
                                            <td class="w-1/3 text-left py-3 px-4"><?= $student['last_name']; ?></td>
                                            <td class="text-left py-3 px-4">
                                                <div class="flex items-center space-x-4">
                                                    <!-- Edit Action -->
                                                    <a href="javascript:void(0);" onclick="openEditModal(<?= $student['id']; ?>)" class="text-yellow-500 hover:text-yellow-700">
                                                        <i class="fas fa-pen"></i>
                                                    </a>
                                                    <!-- Delete Action -->
                                                    <a href="javascript:void(0);" onclick="deleteClass('<?= $student['id']; ?>')" class="text-red-500 hover:text-red-700">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
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

    <div id="createClassModal" class="fixed inset-0 overflow-y-auto hidden">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:p-0">
            <!-- Background Overlay -->
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <!-- Modal Content -->
            <div class="inline-block align-bottom p-6 bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <button onclick="toggleModal('createClassModal')" class="absolute top-0 right-0 m-4 text-gray-500 hover:text-gray-700 focus:outline-none">
                    <i class="fas fa-times"></i>
                </button>
                <h1 class="text-3xl text-black pb-6">Add Student</h1>
                <form action="students/process_create_student.php" method="POST">
                    <!-- Student Number -->
                    <div class="mb-4">
                        <label for="student_number" class="block text-sm font-medium text-gray-600">Student Number</label>
                        <input type="text" id="student_number" name="student_number" class="mt-1 p-2 border rounded-md w-full" required>
                    </div>

                    <!-- First Name and Last Name in the same row -->
                    <div class="mb-4 flex">
                        <!-- First Name -->
                        <div class="w-1/2 pr-2">
                            <label for="first_name" class="block text-sm font-medium text-gray-600">First Name</label>
                            <input type="text" id="first_name" name="first_name" class="mt-1 p-2 border rounded-md w-full" required>
                        </div>

                        <!-- Last Name -->
                        <div class="w-1/2 pl-2">
                            <label for="last_name" class="block text-sm font-medium text-gray-600">Last Name</label>
                            <input type="text" id="last_name" name="last_name" class="mt-1 p-2 border rounded-md w-full" required>
                        </div>
                    </div>

                    <!-- Additional fields go here -->

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
    <div id="editStudentModal" class="fixed inset-0 overflow-y-auto hidden">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:p-0">
            <!-- Background Overlay -->
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <!-- Modal Content -->
            <div class="inline-block align-bottom p-6 bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <h1 class="text-3xl text-black pb-6">Edit Student</h1>
                <form id="editStudentForm" action="students/process_edit_student.php" method="POST">
                    <!-- Student Number -->
                    <input type="hidden" id="editStudentId" name="student_id" value="">
                    <div class="mb-4">
                        <label for="editStudentNumber" class="block text-sm font-medium text-gray-600">Student Number</label>
                        <input type="text" id="editStudentNumber" name="editStudentNumber" class="mt-1 p-2 border rounded-md w-full" required>
                    </div>

                    <!-- First Name and Last Name in the same row -->
                    <div class="mb-4 flex">
                        <!-- First Name -->
                        <div class="w-1/2 pr-2">
                            <label for="editStudentFirstName" class="block text-sm font-medium text-gray-600">First Name</label>
                            <input type="text" id="editStudentFirstName" name="editStudentFirstName" class="mt-1 p-2 border rounded-md w-full" required>
                        </div>

                        <!-- Last Name -->
                        <div class="w-1/2 pl-2">
                            <label for="editStudentLastName" class="block text-sm font-medium text-gray-600">Last Name</label>
                            <input type="text" id="editStudentLastName" name="editStudentLastName" class="mt-1 p-2 border rounded-md w-full" required>
                        </div>
                    </div>

                    <!-- Additional fields go here -->
                    <!-- Submit Button -->
                    <div class="flex items-center justify-end">
                        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 focus:outline-none focus:shadow-outline-blue active:bg-blue-800">
                            Update Class
                        </button>
                    </div>
                </form>
                <button onclick="toggleModal('editStudentModal')" class="absolute top-0 right-0 m-4 text-gray-500 hover:text-gray-700 focus:outline-none">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <script>
        // Function to open the edit modal and populate the form
        function openEditModal(studentId) {
            const modal = document.getElementById('editStudentModal');
            const form = document.getElementById('editStudentForm');
            const studentNumberInput = document.getElementById('editStudentNumber');
            const studentFirstNameInput = document.getElementById('editStudentFirstName');
            const studentLastNameInput = document.getElementById('editStudentLastName');
            const studentIdInput = document.getElementById('editStudentId');

            // You may customize the AJAX request URL and method based on your server-side implementation
            const apiUrl = 'api/get_student_details.php';
            const formData = new FormData();
            formData.append('student_id', studentId);

            // Fetch class details via AJAX
            fetch(apiUrl, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    // Populate form fields with class details
                    studentNumberInput.value = data.student_number;
                    studentFirstNameInput.value = data.first_name;
                    studentLastNameInput.value = data.last_name;
                    studentIdInput.value = studentId;

                    // Toggle the visibility of the modal
                    modal.classList.toggle('hidden');
                })
                .catch(error => {
                    console.error('Error fetching student details:', error);
                });
        }

        function deleteClass(studentId) {
            // You can add a confirmation dialog here if needed

            // Send the delete request via AJAX with a POST method
            fetch('students/process_delete_student.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(studentId),
                })
                .then(response => response.json())
                .then(data => {
                    console.log(data);
                    if (data.success) {
                        window.location.reload();
                    }
                })
                .catch(error => {
                    console.error('Error deleting class:', error);
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