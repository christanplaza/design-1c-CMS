<?php
session_start();

require '../../config/db.php';

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
<?php include '../head.php'; ?>
<body class="bg-gray-100 font-family-karla flex h-full">
    <?php include('../sidebar.php'); ?>

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
    <div id="createScheduleModal" class="fixed inset-0 overflow-y-auto hidden">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:p-0">
            <!-- Background Overlay -->
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <!-- Modal Content -->
            <div class="inline-block align-bottom p-6 bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <button onclick="toggleModal('createScheduleModal')" class="absolute top-0 right-0 m-4 text-gray-500 hover:text-gray-700 focus:outline-none">
                    <i class="fas fa-times"></i>
                </button>
                <h1 class="text-3xl text-black pb-6">Create Schedule</h1>
                <form action="process_create_schedule.php" method="POST">
                    <input type="hidden" name="class_id" value="<?= $classId ?>">
                    <!-- Day of Week -->
                    <div class="mb-4">
                        <label for="day_of_week" class="block text-sm font-medium text-gray-600">Day of Week</label>
                        <select id="day_of_week" name="day_of_week" class="mt-1 p-2 border rounded-md w-full" required>
                            <option label="Select a Day"></option>
                            <?php foreach ($days_of_week as $day) : ?>
                                <option value="<?= $day ?>"><?= $day ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Start time -->
                    <div class="mb-4">
                        <label for="start_time" class="block text-sm font-medium text-gray-600">Start Time</label>
                        <input type="time" class="mt-1 p-2 border rounded-md w-full" name="start_time" id="start_time" required>
                    </div>

                    <!-- End Time -->
                    <div class="mb-4">
                        <label for="end_time" class="block text-sm font-medium text-gray-600">End Time</label>
                        <input type="time" class="mt-1 p-2 border rounded-md w-full" name="end_time" id="end_time" required>
                    </div>

                    <!-- Description -->
                    <div class="mb-4">
                        <label for="location" class="block text-sm font-medium text-gray-600">Location</label>
                        <input type="text" class="mt-1 p-2 border rounded-md w-full" name="location" id="location" required>
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

    <!-- Edit Schedule Modal -->
    <div id="editScheduleModal" class="fixed inset-0 overflow-y-auto hidden">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:p-0">
            <!-- Background Overlay -->
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <!-- Modal Content -->
            <div class="inline-block align-bottom p-6 bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <h1 class="text-3xl text-black pb-6">Edit Class</h1>
                <form id="editScheduleForm" action="process_edit_schedule.php" method="POST">
                    <input type="hidden" name="schedule_id" id="edit_schedule_id">
                    <input type="hidden" name="class_id" id="edit_class_id">
                    <!-- Day of Week -->
                    <div class="mb-4">
                        <label for="day_of_week" class="block text-sm font-medium text-gray-600">Day of Week</label>
                        <select id="edit_day_of_week" name="day_of_week" class="mt-1 p-2 border rounded-md w-full" required>
                            <option label="Select a Day"></option>
                            <?php foreach ($days_of_week as $day) : ?>
                                <option value="<?= $day ?>"><?= $day ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Start time -->
                    <div class="mb-4">
                        <label for="start_time" class="block text-sm font-medium text-gray-600">Start Time</label>
                        <input type="time" class="mt-1 p-2 border rounded-md w-full" name="start_time" id="edit_start_time" required>
                    </div>

                    <!-- End Time -->
                    <div class="mb-4">
                        <label for="end_time" class="block text-sm font-medium text-gray-600">End Time</label>
                        <input type="time" class="mt-1 p-2 border rounded-md w-full" name="end_time" id="edit_end_time" required>
                    </div>

                    <!-- Description -->
                    <div class="mb-4">
                        <label for="location" class="block text-sm font-medium text-gray-600">Location</label>
                        <input type="text" class="mt-1 p-2 border rounded-md w-full" name="location" id="edit_location" required>
                    </div>
                    <!-- Submit Button -->
                    <div class="flex items-center justify-end">
                        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 focus:outline-none focus:shadow-outline-blue active:bg-blue-800">
                            Update Schedule
                        </button>
                    </div>
                </form>
                <button onclick="toggleModal('editScheduleModal')" class="absolute top-0 right-0 m-4 text-gray-500 hover:text-gray-700 focus:outline-none">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

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