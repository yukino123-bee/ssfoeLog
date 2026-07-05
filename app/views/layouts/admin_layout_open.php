<?php
/**
 * Opens admin chrome: head, body, top bar, sidebar, main content wrapper.
 * Close with admin_layout_close.php
 */
if (!isset($title)) {
    $title = APP_NAME;
}
if (!isset($body_class)) {
    $body_class = 'min-h-screen bg-[#f1f5f9] font-sans text-gray-900 antialiased';
}
require_once APP_PATH . '/views/layouts/header.php';
?>
<?php require_once APP_PATH . '/views/layouts/admin_navbar.php'; ?>

<div class="flex min-h-screen w-full">
    <?php require_once APP_PATH . '/views/layouts/admin_sidebar.php'; ?>
    <div class="flex-1 min-w-0 lg:ml-72 pt-[5.25rem] min-h-screen flex flex-col">
        <div class="px-4 sm:px-6 lg:px-8 pb-24 max-w-[1600px] mx-auto w-full flex-1">

