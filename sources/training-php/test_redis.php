<?php
// Test Redis connection
require_once 'vendor/autoload.php';
require_once 'configs/redis.php';

echo "Testing Redis connection...\n";

try {
    $redis = createRedisClient();
    echo "Redis client created successfully\n";
    
    // Test SET
    $redis->set('test_key', 'hello_world');
    echo "SET operation successful\n";
    
    // Test GET
    $value = $redis->get('test_key');
    echo "GET operation successful\n";
    echo "Value retrieved: " . $value . "\n";
    
    // Test with your credentials
    $redis->set('user_test', 'admin123');
    $userValue = $redis->get('user_test');
    echo "User test value: " . $userValue . "\n";
    
    echo "All Redis tests PASSED!\n";
    
} catch (Exception $e) {
    echo "Redis Error: " . $e->getMessage() . "\n";
    echo "Error details:\n";
    print_r($e);
}
?>


