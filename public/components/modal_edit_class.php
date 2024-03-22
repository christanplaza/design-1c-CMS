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

                <!-- Year Level & Section -->
                <div class="mb-4">
                    <label for="editCourse" class="block text-sm font-medium text-gray-600">Year Level & Section</label>
                    <input type="text" id="editCourse" name="course" class="mt-1 p-2 border rounded-md w-full" placeholder="e.g ENCE4A" required>
                </div>

                <!-- Teacher -->
                <div class="mb-4">
                    <label for="editTeacher" class="block text-sm font-medium text-gray-600">Teacher</label>
                    <select id="editTeacher" name="teacher" class="mt-1 p-2 border rounded-md w-full" required>
                        <?php foreach($teachers as $teacher) : ?>
                            <option value="<?= $teacher['id']; ?>"><?= $teacher['name']; ?></option>
                        <?php endforeach; ?>
                    </select>
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