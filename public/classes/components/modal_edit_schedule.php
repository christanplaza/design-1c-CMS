<div id="editScheduleModal" class="fixed inset-0 overflow-y-auto hidden">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:p-0">
            <!-- Background Overlay -->
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <!-- Modal Content -->
            <div class="inline-block align-bottom p-6 bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <h1 class="text-3xl text-black pb-6">Edit Schedule</h1>
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