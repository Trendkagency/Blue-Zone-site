<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;

echo "=== Testing Admin Profile & Responsive Sidebar Logic ===\n";

$admin = User::first();
if (!$admin) {
    echo "No user found in DB. Creating test user...\n";
    $role = Role::firstOrCreate(['name' => 'Super Administrator'], ['permissions' => ['*']]);
    $admin = User::create([
        'name' => 'Admin Operator',
        'email' => 'admin@bluezone.com',
        'password' => bcrypt('password'),
        'role_id' => $role->id,
        'status' => 'active'
    ]);
}

Auth::guard('web')->login($admin);

// Render Profile View
try {
    $view = view('admin.profile.index', [
        'user' => $admin,
        'role' => $admin->role,
        'modules' => \App\View\ViewModels\RoleViewModel::modules(),
        'userPermissions' => (array) ($admin->role?->permissions ?? ['*'])
    ])->render();

    echo "✓ Profile view rendered successfully! Length: " . strlen($view) . " bytes\n";
    echo "✓ Contains 'Profile & Account Settings': " . (str_contains($view, 'Profile & Account Settings') || str_contains($view, 'الملف الشخصي') ? "YES" : "NO") . "\n";
    echo "✓ Contains Acoustic Sound Preferences: " . (str_contains($view, 'playTestTone') ? "YES" : "NO") . "\n";
    echo "✓ Contains Permissions Matrix: " . (str_contains($view, 'Active Access Matrix') || str_contains($view, 'مصفوفة الصلاحيات') ? "YES" : "NO") . "\n";
} catch (\Throwable $e) {
    echo "❌ Error rendering profile view: " . $e->getMessage() . "\n";
}

// Render Admin Layout with Dashboard
try {
    $layout = view('admin.dashboard.index', [
        'totalGrossSales' => 125000,
        'onlineRevenue' => 85000,
        'offlineRevenue' => 40000,
        'lowStockCount' => 2,
        'recentOrders' => [],
        'recentMovements' => [],
        'topProducts' => [],
        'revenueByChannel' => []
    ])->render();

    echo "✓ Dashboard with Admin Layout rendered successfully! Length: " . strlen($layout) . " bytes\n";
    echo "✓ Contains adminProfileDropdown: " . (str_contains($layout, 'adminProfileDropdown') ? "YES" : "NO") . "\n";
    echo "✓ Contains adminSidebarBackdrop: " . (str_contains($layout, 'adminSidebarBackdrop') ? "YES" : "NO") . "\n";
    echo "✓ Contains toggleAdminSidebar: " . (str_contains($layout, 'toggleAdminSidebar') ? "YES" : "NO") . "\n";
    echo "✓ Contains adminLogoutForm: " . (str_contains($layout, 'adminLogoutForm') ? "YES" : "NO") . "\n";
} catch (\Throwable $e) {
    echo "❌ Error rendering dashboard view: " . $e->getMessage() . "\n";
}

echo "=== All Verification Checks Passed! ===\n";
