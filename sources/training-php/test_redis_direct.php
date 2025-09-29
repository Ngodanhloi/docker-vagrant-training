<?php
// Test Redis connection with direct credentials
require_once 'vendor/autoload.php';

use Predis\Client;

echo "Testing Redis connection with direct credentials...\n";

try {
    $redis = new Client([
        'host' => 'redis-13920.c340.ap-northeast-2-1.ec2.redns.redis-cloud.com',
        'port' => 13920,
        'database' => 0,
        'username' => 'default',
        'password' => 'lrcARlHUmdmyuJr6wB3gP7Erm6xHcvMk',
    ]);
    
    echo "Redis client created successfully\n";
    
    // Test SET
    $redis->set('test_key', 'hello_world');
    echo "SET operation successful\n";
    
    // Test GET
    $value = $redis->get('test_key');
    echo "GET operation successful\n";
    echo "Value retrieved: " . $value . "\n";
    
    // Test with user data
    $redis->set('user_admin', 'admin123');
    $userValue = $redis->get('user_admin');
    echo "User test value: " . $userValue . "\n";
    
    echo "All Redis tests PASSED!\n";
    
} catch (Exception $e) {
    echo "Redis Error: " . $e->getMessage() . "\n";
    echo "Error details:\n";
    print_r($e);
}
?>


