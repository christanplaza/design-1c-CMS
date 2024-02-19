<?php
session_start();

require '../config/db.php';

// Fetch all classes
$sql = "SELECT * FROM classes";
$stmt = $pdo->query($sql);
$classes = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
                                    <th class="w-1/3 text-left py-3 px-4 uppercase font-semibold text-sm">Title</th>
                                    <th class="w-1/3 text-left py-3 px-4 uppercase font-semibold text-sm">Description</th>
                                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-700">
                                <?php if (empty($classes)): ?>
                                    <tr>
                                        <td colspan="4" class="text-center py-3 px-4 font-bold">No records found</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($classes as $class): ?>
                                        <tr>
                                            <td class="text-left py-3 px-4"><?= $class['id']; ?></td>
                                            <td class="w-1/3 text-left text-blue-600 underline py-3 px-4">
                                                <a href="classes/class_detail.php?class_id=<?= $class['id']; ?>">
                                                    <?= $class['title']; ?>
                                                </a>
                                            </td>
                                            <td class="w-1/3 text-left py-3 px-4"><?= $class['description']; ?></td>
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
                <h1 class="text-3xl text-black pb-6">Create Class</h1>
                <form action="classes/process_create_class.php" method="POST">
                    <!-- Title -->
                    <div class="mb-4">
                        <label for="title" class="block text-sm font-medium text-gray-600">Title</label>
                        <input type="text" id="title" name="title" class="mt-1 p-2 border rounded-md w-full" required>
                    </div>

                    <!-- Description -->
                    <div class="mb-4">
                        <label for="description" class="block text-sm font-medium text-gray-600">Description</label>
                        <textarea id="description" name="description" rows="4" class="mt-1 p-2 border rounded-md w-full resize-none" required></textarea>
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
    <div id="editClassModal" class="fixed inset-0 overflow-y-auto hidden">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:p-0">
            <!-- Background Overlay -->
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <!-- Modal Content -->
            <div class="inline-block align-bottom p-6 bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <h1 class="text-3xl text-black pb-6">Edit Class</h1>
                <form id="editClassForm" action="classes/process_edit_class.php" method="POST">
                    <!-- Hidden input for class ID -->
                    <input type="hidden" id="editClassId" name="class_id" value="">
                    <!-- Title -->
                    <div class="mb-4">
                        <label for="editTitle" class="block text-sm font-medium text-gray-600">Title</label>
                        <input type="text" id="editTitle" name="title" class="mt-1 p-2 border rounded-md w-full" required>
                    </div>
                    <!-- Description -->
                    <div class="mb-4">
                        <label for="editDescription" class="block text-sm font-medium text-gray-600">Description</label>
                        <textarea id="editDescription" name="description" rows="4" class="mt-1 p-2 border rounded-md w-full resize-none"></textarea>
                    </div>
                    <!-- Submit Button -->
                    <div class="flex items-center justify-end">
                        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 focus:outline-none focus:shadow-outline-blue active:bg-blue-800">
                            Update Class
                        </button>
                    </div>
                </form>
                <button onclick="toggleModal('editClassModal')" class="absolute top-0 right-0 m-4 text-gray-500 hover:text-gray-700 focus:outline-none">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <script>
        // Function to open the edit modal and populate the form
        function openEditModal(classId) {
            const modal = document.getElementById('editClassModal');
            const form = document.getElementById('editClassForm');
            const titleInput = document.getElementById('editTitle');
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
                descriptionInput.value = data.description;
                classIdInput.value = classId;

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