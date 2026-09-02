<?php

$title = "Register - " . APP_NAME;
$body_class = "font-sans min-h-screen flex items-center justify-center bg-gray-50";
require_once APP_PATH . '/views/layouts/header.php';

?>
<div class="w-full max-w-md">
    <div class="bg-white rounded-lg shadow-lg p-8">
        <!-- Logo/Brand -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900"><?php echo APP_NAME; ?></h1>
            <p class="text-gray-600 mt-2">Create your account</p>
        </div>

        <!-- Register Form -->
        <form method="POST" action="<?php echo base_url('register'); ?>" class="space-y-6">
            <!-- CSRF Token -->
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

            <?php if (isset($error_message)): ?>
                <div class="rounded-xl border border-red-200 bg-red-50 p-4 shadow-sm flex items-start gap-3 text-sm text-red-900" role="alert">
                    <div class="w-8 h-8 rounded-full bg-red-100 text-red-600 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <i class="fas fa-exclamation-circle text-sm"></i>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-bold text-red-950 text-xs uppercase tracking-wider mb-0.5">Registration Error</h4>
                        <p class="text-xs text-red-700 font-medium leading-relaxed"><?php echo htmlspecialchars($error_message); ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="firstname" class="block text-sm font-medium text-gray-700">First Name</label>
                    <input type="text" id="firstname" name="firstname" required
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500"
                           value="<?php echo htmlspecialchars($_POST['firstname'] ?? ''); ?>">
                </div>
                <div>
                    <label for="lastname" class="block text-sm font-medium text-gray-700">Last Name</label>
                    <input type="text" id="lastname" name="lastname" required
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500"
                           value="<?php echo htmlspecialchars($_POST['lastname'] ?? ''); ?>">
                </div>
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                <input type="email" id="email" name="email" required
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500"
                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" id="password" name="password" required
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500"
                       minlength="8">
                <p class="mt-1 text-sm text-gray-500">Must be at least 8 characters</p>
            </div>

            <div>
                <label for="confirm_password" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500"
                       minlength="8">
            </div>

            <div>
                <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    Create Account
                </button>
            </div>
        </form>

        <!-- Login Link -->
        <div class="mt-6 text-center">
            <p class="text-sm text-gray-600">
                Already have an account?
                <a href="<?php echo base_url('login'); ?>" class="font-medium text-red-600 hover:text-red-500">
                    Sign in here
                </a>
            </p>
        </div>
    </div>
</div>

<?php require_once APP_PATH . '/views/layouts/footer.php'; ?>