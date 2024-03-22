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