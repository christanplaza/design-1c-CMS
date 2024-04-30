<?php
session_start();

$baseUrl = '';
require $baseUrl . '../config/db.php';

// Fetch all classes
$sql = "SELECT classes.*, teachers.name AS teacher_name 
        FROM classes 
        INNER JOIN teachers ON classes.teacher_id = teachers.id";
$stmt = $pdo->query($sql);
$classes = $stmt->fetchAll(PDO::FETCH_ASSOC);

$sql = "SELECT * FROM teachers";
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
                    <a href="logout.php" class="block px-4 py-2 account-link hover:text-white">Sign Out</a>
                </div>
            </div>
        </header>
        
        <?php include('mobile_sidebar.php'); ?>
    
        <div class="w-full overflow-x-hidden border-t flex flex-col">
            <main class="w-full flex-grow p-6">
                <h1 class="text-3xl text-black pb-2">Classes</h1>
                <?php if (isset($_SESSION['success_message'])): ?>
                    <div class="relative block w-full p-4 mb-4 text-base leading-5 text-grey-700 bg-green-200 rounded-lg opacity-100 font-regular">
                        <?php echo $_SESSION['success_message']; ?>
                    </div>
                    <?php unset($_SESSION['success_message']); ?>
                <?php elseif (isset($_SESSION['error_message'])): ?>
                    <div class="relative block w-full p-4 mb-4 text-base leading-5 text-grey-700 bg-red-200 rounded-lg opacity-100 font-regular">
                        <?php echo $_SESSION['error_message']; ?>
                    </div>
                    <?php unset($_SESSION['error_message']); ?>
                <?php endif; ?>

                <div class="w-full mt-4">
                    <a href="#" onclick="toggleModal('createClassModal')" class="inline-block py-2 px-4 bg-green-600 text-white rounded-md mb-4 hover:bg-green-700 focus:outline-none focus:shadow-outline-blue active:bg-green-800">
                        New Class
                        <i class="fas fa-plus"></i>
                    </a>
                    <div class="bg-white overflow-auto">
                        <table class="min-w-full bg-white">
                            <thead class="bg-gray-800 text-white">
                                <tr>
                                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">ID</th>
                                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Title</th>
                                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Course</th>
                                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Teacher</th>
                                    <th class="w-1/4 text-left py-3 px-4 uppercase font-semibold text-sm">Description</th>
                                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-700">
                                <?php if (empty($classes)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-3 px-4 font-bold">No records found</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($classes as $class): ?>
                                        <tr>
                                            <td class="text-left py-3 px-4"><?= $class['id']; ?></td>
                                            <td class="w-1/4 text-left text-blue-600 underline py-3 px-4">
                                                <a href="classes/class_detail.php?class_id=<?= $class['id']; ?>">
                                                    <?= $class['title']; ?>
                                                </a>
                                            </td>
                                            <td class="text-left py-3 px-4"><?= $class['course']; ?></td>
                                            <td class="text-left py-3 px-4"><?= $class['teacher_name']; ?></td>
                                            <td class="w-1/4 text-left py-3 px-4"><?= $class['description']; ?></td>
                                            <td class="text-left py-3 px-4">
                                                <div class="flex items-center space-x-4">
                                                    <!-- Edit Action -->
                                                    <a href="javascript:void(0);" onclick="openEditModal(<?= $class['id']; ?>)" class="text-yellow-500 hover:text-yellow-700">
                                                        <i class="fas fa-pen"></i>
                                                    </a>
                                                    <!-- Delete Action -->
                                                    <a href="javascript:void(0);" onclick="deleteClass('<?= $class['id']; ?>')" class="text-red-500 hover:text-red-700">
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

    <!-- Create Class Modal -->
    <?php include('components/modal_create_class.php'); ?>

    <!-- Edit Class Modal -->
    <?php include('components/modal_edit_class.php'); ?>

    <script>
        // Function to open the edit modal and populate the form
        function openEditModal(classId) {
            const modal = document.getElementById('editClassModal');
            const form = document.getElementById('editClassForm');
            const titleInput = document.getElementById('editTitle');
            const courseInput = document.getElementById('editCourse');
            const teacherInput = document.getElementById('editTeacher');
            const descriptionInput = document.getElementById('editDescription');
            const classIdInput = document.getElementById('editClassId');

            // You may customize the AJAX request URL and method based on your server-side implementation
            const apiUrl = 'api/get_class_details.php';
            const formData = new FormData();
            formData.append('class_id', classId);

            // Fetch class details via AJAX
            fetch(apiUrl, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                // Populate form fields with class details
                titleInput.value = data.title;
                courseInput.value = data.course;
                descriptionInput.value = data.description;
                classIdInput.value = classId;

                Array.from(teacherInput.options).forEach(option => {
                    if (option.value == data.teacher_id) {
                        option.selected = true;
                    } else {
                        option.selected = false;
                    }
                });

                // Toggle the visibility of the modal
                modal.classList.toggle('hidden');
            })
            .catch(error => {
                console.error('Error fetching class details:', error);
            });
        }

        function deleteClass(classId) {
            // You can add a confirmation dialog here if needed

            // Send the delete request via AJAX with a POST method
            fetch('classes/process_delete_class.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(classId),
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