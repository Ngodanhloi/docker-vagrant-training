<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Redis Debug</title>
    <?php include 'views/meta.php' ?>
    <style>
        .debug-container{max-width:800px;margin:30px auto}
        .debug-box{border:1px solid #ddd;padding:20px;margin:20px 0;border-radius:5px}
        .success{background:#d4edda;border-color:#c3e6cb;color:#155724}
        .error{background:#f8d7da;border-color:#f5c6cb;color:#721c24}
        .info{background:#d1ecf1;border-color:#bee5eb;color:#0c5460}
        pre{background:#f8f9fa;padding:10px;border-radius:3px;overflow-x:auto;max-height:300px;overflow-y:auto}
        .test-btn{margin:5px}
    </style>
</head>
<body>
<?php include 'views/header.php'?>

<div class="container debug-container">
    <h2>Redis Debug Page</h2>
    
    <div class="debug-box info">
        <h4>🔍 Redis Connection Debug</h4>
        <p>This page will help debug Redis connection issues.</p>
    </div>

    <div class="debug-box">
        <h4>Environment Variables</h4>
        <pre id="env-vars">Loading...</pre>
    </div>

    <div class="debug-box">
        <h4>Redis Connection Test</h4>
        <button onclick="testRedisConnection()" class="btn btn-primary test-btn">Test Redis Connection</button>
        <button onclick="testRedisSet()" class="btn btn-success test-btn">Test Redis SET</button>
        <button onclick="testRedisGet()" class="btn btn-info test-btn">Test Redis GET</button>
        <button onclick="clearRedisTest()" class="btn btn-warning test-btn">Clear Test Data</button>
        <div id="redis-test-results"></div>
    </div>

    <div class="debug-box">
        <h4>Direct API Test</h4>
        <button onclick="testDirectAPI()" class="btn btn-primary test-btn">Test redis_api.php directly</button>
        <div id="api-test-results"></div>
    </div>

    <div class="debug-box">
        <h4>Manual Test</h4>
        <input type="text" id="manual-key" placeholder="Key" class="form-control" style="margin:5px 0" value="manual_test">
        <input type="text" id="manual-value" placeholder="Value" class="form-control" style="margin:5px 0" value="hello_world">
        <button onclick="manualSet()" class="btn btn-success test-btn">SET</button>
        <button onclick="manualGet()" class="btn btn-info test-btn">GET</button>
        <div id="manual-test-results"></div>
    </div>
</div>

<script>
function log(message, type = 'info') {
    const timestamp = new Date().toLocaleTimeString();
    const logMessage = `[${timestamp}] ${message}`;
    console.log(logMessage);
    
    // Also show in page
    const resultsDiv = document.getElementById('redis-test-results');
    const div = document.createElement('div');
    div.className = `alert alert-${type === 'error' ? 'danger' : type === 'success' ? 'success' : 'info'}`;
    div.innerHTML = logMessage;
    resultsDiv.appendChild(div);
    resultsDiv.scrollTop = resultsDiv.scrollHeight;
}

function testRedisConnection() {
    log('Testing Redis connection...');
    
    fetch('redis_api.php?key=connection_test')
        .then(response => {
            log(`Response status: ${response.status}`);
            return response.text();
        })
        .then(data => {
            log(`Raw response: ${data}`);
            try {
                const json = JSON.parse(data);
                log(`Parsed JSON: ${JSON.stringify(json)}`);
                if (json.error) {
                    log(`Redis Error: ${json.error}`, 'error');
                } else {
                    log('Redis connection successful!', 'success');
                }
            } catch (e) {
                log(`JSON Parse Error: ${e.message}`, 'error');
                log(`Raw data: ${data}`, 'error');
            }
        })
        .catch(error => {
            log(`Fetch Error: ${error.message}`, 'error');
        });
}

function testRedisSet() {
    log('Testing Redis SET...');
    
    fetch('redis_api.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ key: 'test_set', value: 'test_value_123' })
    })
    .then(response => response.text())
    .then(data => {
        log(`SET Response: ${data}`);
        try {
            const json = JSON.parse(data);
            if (json.ok) {
                log('Redis SET successful!', 'success');
            } else {
                log(`Redis SET Error: ${json.error}`, 'error');
            }
        } catch (e) {
            log(`SET Parse Error: ${e.message}`, 'error');
        }
    })
    .catch(error => {
        log(`SET Fetch Error: ${error.message}`, 'error');
    });
}

function testRedisGet() {
    log('Testing Redis GET...');
    
    fetch('redis_api.php?key=test_set')
        .then(response => response.text())
        .then(data => {
            log(`GET Response: ${data}`);
            try {
                const json = JSON.parse(data);
                if (json.value !== undefined) {
                    log(`Redis GET successful! Value: ${json.value}`, 'success');
                } else {
                    log(`Redis GET Error: ${json.error}`, 'error');
                }
            } catch (e) {
                log(`GET Parse Error: ${e.message}`, 'error');
            }
        })
        .catch(error => {
            log(`GET Fetch Error: ${error.message}`, 'error');
        });
}

function clearRedisTest() {
    log('Clearing test data...');
    
    fetch('redis_api.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ key: 'test_set', value: '' })
    })
    .then(response => response.text())
    .then(data => {
        log(`Clear Response: ${data}`);
    })
    .catch(error => {
        log(`Clear Error: ${error.message}`, 'error');
    });
}

function testDirectAPI() {
    log('Testing direct API access...');
    
    fetch('redis_api.php')
        .then(response => {
            log(`Direct API Status: ${response.status}`);
            return response.text();
        })
        .then(data => {
            log(`Direct API Response: ${data}`);
        })
        .catch(error => {
            log(`Direct API Error: ${error.message}`, 'error');
        });
}

function manualSet() {
    const key = document.getElementById('manual-key').value;
    const value = document.getElementById('manual-value').value;
    
    log(`Manual SET: ${key} = ${value}`);
    
    fetch('redis_api.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ key: key, value: value })
    })
    .then(response => response.text())
    .then(data => {
        document.getElementById('manual-test-results').innerHTML = `<div class="alert alert-info">SET Response: ${data}</div>`;
    })
    .catch(error => {
        document.getElementById('manual-test-results').innerHTML = `<div class="alert alert-danger">SET Error: ${error.message}</div>`;
    });
}

function manualGet() {
    const key = document.getElementById('manual-key').value;
    
    log(`Manual GET: ${key}`);
    
    fetch(`redis_api.php?key=${encodeURIComponent(key)}`)
        .then(response => response.text())
        .then(data => {
            document.getElementById('manual-test-results').innerHTML = `<div class="alert alert-info">GET Response: ${data}</div>`;
        })
        .catch(error => {
            document.getElementById('manual-test-results').innerHTML = `<div class="alert alert-danger">GET Error: ${error.message}</div>`;
        });
}

// Load environment variables on page load
document.addEventListener('DOMContentLoaded', function() {
    fetch('redis_debug.php?action=env')
        .then(response => response.text())
        .then(data => {
            document.getElementById('env-vars').textContent = data;
        })
        .catch(error => {
            document.getElementById('env-vars').textContent = 'Error loading environment variables: ' + error.message;
        });
});
</script>

<?php
// Handle environment variables request
if (isset($_GET['action']) && $_GET['action'] === 'env') {
    echo "REDIS_HOST: " . (getenv('REDIS_HOST') ?: 'NOT SET') . "\n";
    echo "REDIS_PORT: " . (getenv('REDIS_PORT') ?: 'NOT SET') . "\n";
    echo "REDIS_USERNAME: " . (getenv('REDIS_USERNAME') ?: 'NOT SET') . "\n";
    echo "REDIS_PASSWORD: " . (getenv('REDIS_PASSWORD') ? '***SET***' : 'NOT SET') . "\n";
    echo "REDIS_DATABASE: " . (getenv('REDIS_DATABASE') ?: 'NOT SET') . "\n";
    exit;
}
?>

</body>
</html>

