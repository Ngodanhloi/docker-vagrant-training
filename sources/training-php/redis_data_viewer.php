<?php
session_start();
require_once 'configs/redis.php';

try {
    $redis = createRedisClient();
    
    // Get all Redis keys
    $allKeys = $redis->keys('*');
    $redisData = [];
    
    foreach ($allKeys as $key) {
        $value = $redis->get($key);
        $ttl = $redis->ttl($key); // Time to live
        
        // Try to decode JSON
        $decodedValue = json_decode($value, true);
        if ($decodedValue !== null) {
            $value = $decodedValue;
        }
        
        $redisData[] = [
            'key' => $key,
            'value' => $value,
            'ttl' => $ttl > 0 ? $ttl . ' seconds' : ($ttl == -1 ? 'No expiration' : 'Expired')
        ];
    }
    
} catch (Exception $e) {
    $error = $e->getMessage();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Redis Data Viewer</title>
    <?php include 'views/meta.php' ?>
    <style>
        .data-container{max-width:1000px;margin:30px auto}
        .data-box{border:1px solid #ddd;padding:20px;margin:20px 0;border-radius:5px}
        .success{background:#d4edda;border-color:#c3e6cb;color:#155724}
        .error{background:#f8d7da;border-color:#f5c6cb;color:#721c24}
        .info{background:#d1ecf1;border-color:#bee5eb;color:#0c5460}
        pre{background:#f8f9fa;padding:10px;border-radius:3px;overflow-x:auto;max-height:300px;overflow-y:auto}
        table{width:100%;border-collapse:collapse}
        th,td{border:1px solid #ddd;padding:8px;text-align:left}
        th{background-color:#f2f2f2}
        .refresh-btn{margin:10px 0}
    </style>
</head>
<body>
<?php include 'views/header.php'?>

<div class="container data-container">
    <h2>Redis Data Viewer - Xem dữ liệu Redis</h2>
    
    <div class="data-box info">
        <h4>📊 Tất cả dữ liệu trong Redis</h4>
        <p>Hiển thị tất cả keys và values đã lưu trong Redis Cloud.</p>
    </div>

    <?php if (isset($error)): ?>
    <div class="data-box error">
        <h4>❌ Redis Error</h4>
        <p><?php echo htmlspecialchars($error); ?></p>
    </div>
    <?php endif; ?>

    <div class="refresh-btn">
        <button onclick="location.reload()" class="btn btn-primary">🔄 Refresh Data</button>
        <button onclick="clearAllRedisData()" class="btn btn-danger">🗑️ Clear All Redis Data</button>
    </div>

    <?php if (!empty($redisData)): ?>
    <div class="data-box">
        <h4>📋 Redis Data (<?php echo count($redisData); ?> items)</h4>
        <table>
            <thead>
                <tr>
                    <th>Key</th>
                    <th>Value</th>
                    <th>TTL</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($redisData as $item): ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($item['key']); ?></strong></td>
                    <td>
                        <?php if (is_array($item['value'])): ?>
                            <pre><?php echo json_encode($item['value'], JSON_PRETTY_PRINT); ?></pre>
                        <?php else: ?>
                            <?php echo htmlspecialchars($item['value']); ?>
                        <?php endif; ?>
                    </td>
                    <td><?php echo $item['ttl']; ?></td>
                    <td>
                        <button onclick="deleteRedisKey('<?php echo htmlspecialchars($item['key']); ?>')" 
                                class="btn btn-sm btn-warning">Delete</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <div class="data-box">
        <h4>📭 Redis is empty</h4>
        <p>Không có dữ liệu nào trong Redis. Hãy đăng nhập và chọn "Remember Me" để lưu dữ liệu.</p>
    </div>
    <?php endif; ?>

    <div class="data-box">
        <h4>ℹ️ Thông tin</h4>
        <ul>
            <li><strong>user_login_[id]</strong>: Thông tin đăng nhập chi tiết (user_id, username, login_time, ip_address)</li>
            <li><strong>user_session_[username]</strong>: Session mapping từ username đến user_id</li>
            <li><strong>TTL</strong>: Thời gian sống của key (7 ngày cho login data)</li>
            <li>Dữ liệu sẽ tự động expire sau 7 ngày</li>
        </ul>
    </div>
</div>

<script>
function deleteRedisKey(key) {
    if (confirm('Bạn có chắc muốn xóa key: ' + key + '?')) {
        fetch('redis_api.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ key: key, value: '', delete: true })
        })
        .then(response => response.text())
        .then(data => {
            alert('Delete response: ' + data);
            location.reload();
        })
        .catch(error => {
            alert('Delete error: ' + error.message);
        });
    }
}

function clearAllRedisData() {
    if (confirm('⚠️ Bạn có chắc muốn xóa TẤT CẢ dữ liệu Redis?\n\nĐiều này sẽ xóa tất cả keys và values!')) {
        fetch('redis_api.php?action=clear_all')
        .then(response => response.text())
        .then(data => {
            alert('Clear all response: ' + data);
            location.reload();
        })
        .catch(error => {
            alert('Clear all error: ' + error.message);
        });
    }
}
</script>

</body>
</html>
