<?php
function simulateLogin($email, $password, $role) {
    $_POST['email'] = $email;
    $_POST['password'] = $password;
    $_POST['role'] = $role;
    $_SESSION = [];

    ob_start();
    include 'login_process_test.php';
    $output = ob_get_clean();

    return [
        'session' => $_SESSION,
        'output' => $output,
        'headers' => headers_list()
    ];
}

function assertEqual($expected, $actual, $message) {
    if ($expected === $actual) {
        echo "✅ $message\n";
    } else {
        echo "❌ $message\nExpected: " . print_r($expected, true) . "\nGot: " . print_r($actual, true) . "\n";
    }
}

// Test 1: Valid donor login
$result = simulateLogin('donor@example.com', 'donor123', 'donor');
assertEqual('donor@example.com', $result['session']['user']['email'] ?? null, 'Valid donor login sets session');
assertEqual(true, in_array('Location: dashboard.php', $result['headers']), 'Valid donor login redirects');

// Test 2: Invalid password
$result = simulateLogin('donor@example.com', 'wrongpass', 'donor');
assertEqual([], $result['session'], 'Invalid password does not set session');
assertEqual('Invalid login', trim($result['output']), 'Invalid password shows error');

// Test 3: Wrong role
$result = simulateLogin('donor@example.com', 'donor123', 'admin');
assertEqual([], $result['session'], 'Wrong role does not set session');
assertEqual('Invalid login', trim($result['output']), 'Wrong role shows error');
?>