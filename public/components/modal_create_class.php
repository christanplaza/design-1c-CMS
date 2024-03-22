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
                    <input type="text" id="title" name="title" class="mt-1 p-2 border rounded-md w-full" placeholder="e.g Basic Programming" required>
                </div>

                <!-- Year Level & Section -->
                <div class="mb-4">
                    <label for="course" class="block text-sm font-medium text-gray-600">Year Level & Section</label>
                    <input type="text" id="course" name="course" class="mt-1 p-2 border rounded-md w-full" placeholder="e.g ENCE4A" required>
                </div>

                <!-- Teacher -->
                <div class="mb-4">
                    <label for="teacher_id" class="block text-sm font-medium text-gray-600">Teacher</label>
                    <select id="teacher_id" name="teacher_id" class="mt-1 p-2 border rounded-md w-full" required>
                        <option value="" disabled selected>Select a Teacher</option>
                        <?php foreach($teachers as $teacher) : ?>
                            <option value="<?= $teacher['id']; ?>"><?= $teacher['name']; ?></option>
                        <?php endforeach; ?>
                    </select>
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