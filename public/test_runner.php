<?php
/**
 * SmartHUB Core Automated Integration Test Runner
 */

header('Content-Type: text/plain; charset=utf-8');

echo "==================================================\n";
echo "       SmartHUB Automated Integration Tests        \n";
echo "==================================================\n\n";

$testsPassed = 0;
$testsFailed = 0;

function assertTest(string $name, bool $expression, string $details = '') {
    global $testsPassed, $testsFailed;
    if ($expression) {
        echo "[ PASS ] - {$name}\n";
        $testsPassed++;
    } else {
        echo "[ FAIL ] - {$name}\n";
        if ($details) echo "         Details: {$details}\n";
        $testsFailed++;
    }
}

// Test 1: Config File Load
try {
    require_once __DIR__ . '/../app/config/config.php';
    assertTest("Config file loaded", defined('APP_NAME') && APP_NAME === 'SmartHUB Digital Library');
} catch (Exception $e) {
    assertTest("Config file loaded", false, $e->getMessage());
}

// Test 2: Custom PSR-4 Class Autoloader
try {
    spl_autoload_register(function ($class) {
        $prefix = 'App\\';
        $baseDir = __DIR__ . '/../app/';
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) return;
        $relativeClass = substr($class, $len);
        $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
        if (file_exists($file)) require_once $file;
    });
    
    assertTest("Autoloader configured", class_exists('App\Core\Session'));
} catch (Exception $e) {
    assertTest("Autoloader configured", false, $e->getMessage());
}

// Test 3: OpenSSL Cryptographic Routines
try {
    $originalText = "+256701234567";
    $encrypted = App\Helpers\Encryption::encrypt($originalText);
    $decrypted = App\Helpers\Encryption::decrypt($encrypted);
    
    assertTest(
        "OpenSSL Encryption/Decryption",
        $originalText === $decrypted && $encrypted !== $originalText,
        "Encrypted: {$encrypted} | Decrypted: {$decrypted}"
    );
} catch (Exception $e) {
    assertTest("OpenSSL Encryption/Decryption", false, $e->getMessage());
}

// Test 4: Database Connection singleton
try {
    $userModel = new App\Models\User();
    $db = $userModel->getDb();
    assertTest("Database connectivity on Port " . DB_PORT, $db instanceof PDO);
} catch (Exception $e) {
    assertTest("Database connectivity on Port " . DB_PORT, false, $e->getMessage());
}

// Test 5: Fetch seeded users check
try {
    $userModel = new App\Models\User();
    $admin = $userModel->findByEmail('admin@smarthub.edu');
    assertTest("Seeded Admin user verification", $admin !== null && $admin['role'] === 'admin');
} catch (Exception $e) {
    assertTest("Seeded Admin user verification", false, $e->getMessage());
}

// Test 6: Validator helper boundaries
try {
    $validator = new App\Helpers\Validation();
    $validData = ['email' => 'student@smarthub.edu', 'phone' => '1234567890'];
    $rules = [
        'email' => ['required' => true, 'email' => true],
        'phone' => ['required' => true, 'min' => 10]
    ];
    $isValid = $validator->validate($validData, $rules);
    assertTest("Validator helper checks", $isValid === true);
} catch (Exception $e) {
    assertTest("Validator helper checks", false, $e->getMessage());
}

echo "\n--------------------------------------------------\n";
echo "Test Execution Completed.\n";
echo "Total Tests Passed: {$testsPassed}\n";
echo "Total Tests Failed: {$testsFailed}\n";
echo "==================================================\n";
