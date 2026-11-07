<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

$request = Illuminate\Http\Request::create('/admin/products/7/edit', 'GET');
$app->instance('request', $request);

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::first();
auth()->login($user);
$request->setUserResolver(fn() => $user);

// Login in session
$guard = auth()->guard('web');
$guard->setUser($user);

$response = $kernel->handle($request);
$content = $response->getContent();
file_put_contents(__DIR__ . '/storage/rendered_edit.html', $content);

$lines = explode("\n", $content);
echo "Status: " . $response->getStatusCode() . "\n";
echo "Total lines: " . count($lines) . "\n";
if (count($lines) > 500) {
    $target = 8745;
    $start = max(0, $target - 25);
    $end = min(count($lines) - 1, $target + 25);
    echo "Lines around $target:\n";
    for ($i = $start; $i <= $end; $i++) {
        echo ($i + 1) . ": " . $lines[$i] . "\n";
    }
} else {
    echo substr($content, 0, 500) . "\n";
}
