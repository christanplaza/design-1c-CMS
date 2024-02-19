<?php
session_start();

$baseUrl = '../';
require $baseUrl . '../config/db.php';
// require '../../config/db.php';

// Fetch class details
if (isset($_GET['class_id'])) {
    $classId = $_GET['class_id'];

    $classSql = "SELECT * FROM classes WHERE id = :class_id";
    $classStmt = $pdo->prepare($classSql);
    $classStmt->bindParam(':class_id', $classId, PDO::PARAM_INT);
    $classStmt->execute();
    $class = $classStmt->fetch(PDO::FETCH_ASSOC);

    // Fetch schedules associated with the class
    $scheduleSql = "SELECT * FROM schedules WHERE class_id = :class_id";
    $scheduleStmt = $pdo->prepare($scheduleSql);
    $scheduleStmt->bindParam(':class_id', $classId, PDO::PARAM_INT);
    $scheduleStmt->execute();
    $schedules = $scheduleStmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    // Redirect or handle the case when class_id is not provided
    header("Location: index.php"); // Redirect to the class listing page
    exit();
}

$days_of_week = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
?>
<!DOCTYPE html>
<html lang="en" class="h-full bg-white">
<?php include $baseUrl . 'head.php'; ?>
<body class="bg-gray-100 font-family-karla flex h-full">
    <?php include $baseUrl . 'sidebar.php'; ?>

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
        
        <?php include('../mobile_sidebar.php'); ?>

        <div class="w-full overflow-x-hidden border-t flex flex-col">
            <main class="w-full flex-grow p-6">

                <div class="bg-white p-6 rounded-md shadow-md mb-6">
                    <h1 class="text-3xl text-black pb-2"><?= $class['title']; ?></h1>
                    <p class="text-gray-700"><?= $class['description']; ?></p>
                    <!-- Add more details as needed -->
                </div>

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

                <!-- Schedules Section -->
                <div class="mb-6">
                    <h2 class="text-2xl text-black mb-2">Schedules</h2>
                    <a href="#" onclick="toggleModal('createScheduleModal')" class="inline-block py-2 px-4 bg-green-600 text-white rounded-md mb-4 hover:bg-green-700 focus:outline-none focus:shadow-outline-blue active:bg-green-800">
                        New Schedule
                        <i class="fas fa-plus"></i>
                    </a>

                    <div class="bg-white overflow-auto">
                        <table class="min-w-full bg-white">
                            <thead class="bg-gray-800 text-white">
                                <tr>
                                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Day Of Week</th>
                                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Start Time</th>
                                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">End time</th>
                                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Location</th>
                                    <th class="w-1/4 text-left py-3 px-4 uppercase font-semibold text-sm">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-700">
                                <?php if (empty($schedules)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-3 px-4 font-bold">No records found</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($schedules as $schedule): ?>
                                        <tr>
                                            <td class="text-left py-3 px-4"><?= $schedule['day_of_week']; ?></td>
                                            <td class="text-left py-3 px-4"><?= $schedule['start_time']; ?></td>
                                            <td class="text-left py-3 px-4"><?= $schedule['end_time']; ?></td>
                                            <td class="text-left py-3 px-4"><?= $schedule['location']; ?></td>
                                            <td class="w-1/4 text-left py-3 px-4">
                                                <div class="flex items-center space-x-4">
                                                    <!-- Edit Action -->
                                                    <a href="javascript:void(0);" onclick="openEditModal(<?= $schedule['id']; ?>)" class="text-yellow-500 hover:text-yellow-700">
                                                        <i class="fas fa-pen"></i>
                                                    </a>
                                                    <!-- Delete Action -->
                                                    <a href="javascript:void(0);" onclick="deleteSchedule('<?= $schedule['id']; ?>')" class="text-red-500 hover:text-red-700">
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

    <!-- Create Schedule Modal -->
    <?php include('components/modal_create_schedule.php'); ?>

    <!-- Edit Schedule Modal -->
    <?php include('components/modal_edit_schedule.php'); ?>

    <script>
        // Function to open the edit modal and populate the form
        function openEditModal(scheduleId) {
            const modal = document.getElementById('editScheduleModal');
            const form = document.getElementById('editScheduleForm');
            const dayOfWeekInput = document.getElementById('edit_day_of_week');
            const startTimeInput = document.getElementById('edit_start_time');
            const endTimeInput = document.getElementById('edit_end_time');
            const locationInput = document.getElementById('edit_location');
            const scheduleIdInput = document.getElementById('edit_schedule_id');
            const classIdInput = document.getElementById('edit_class_id');

            // You may customize the AJAX request URL and method based on your server-side implementation
            const apiUrl = '../api/get_schedule_details.php';
            const formData = new FormData();
            formData.append('schedule_id', scheduleId);

            // Fetch Schedule details via AJAX
            fetch(apiUrl, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                // Populate form fields with schedule details
                dayOfWeekInput.value = data.day_of_week;
                startTimeInput.value = data.start_time;
                endTimeInput.value = data.end_time;
                locationInput.value = data.location;
                scheduleIdInput.value = scheduleId;
                classIdInput.value = data.class_id;

                // Toggle the visibility of the modal
                modal.classList.toggle('hidden');
            })
            .catch(error => {
                console.error('Error fetching schedule details:', error);
            });
        }

        function deleteSchedule(scheduleId) {
            // You can add a confirmation dialog here if needed

            // Send the delete request via AJAX with a POST method
            fetch('process_delete_schedule.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(scheduleId),
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