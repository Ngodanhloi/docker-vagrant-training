<!DOCTYPE html>
<html>
<head>
    <title>Test LocalStorage</title>
    <?php include 'views/meta.php' ?>
    <style>
        .test-container{max-width:600px;margin:50px auto}
        .test-box{border:1px solid #ddd;padding:20px;margin:20px 0;border-radius:5px}
        .success{background:#d4edda;border-color:#c3e6cb;color:#155724}
        .error{background:#f8d7da;border-color:#f5c6cb;color:#721c24}
        .info{background:#d1ecf1;border-color:#bee5eb;color:#0c5460}
        pre{background:#f8f9fa;padding:10px;border-radius:3px;overflow-x:auto}
    </style>
</head>
<body>
<?php include 'views/header.php'?>

<div class="container test-container">
    <h2>LocalStorage Test Page</h2>
    
    <div class="test-box info">
        <h4>🔍 Test localStorage functionality</h4>
        <p>This page will test if localStorage is working in your browser.</p>
    </div>

    <div class="test-box">
        <h4>Manual Test</h4>
        <input type="text" id="test-key" placeholder="Key" class="form-control" style="margin:5px 0">
        <input type="text" id="test-value" placeholder="Value" class="form-control" style="margin:5px 0">
        <button onclick="saveToLocalStorage()" class="btn btn-primary">Save to localStorage</button>
        <button onclick="loadFromLocalStorage()" class="btn btn-info">Load from localStorage</button>
        <button onclick="clearLocalStorage()" class="btn btn-danger">Clear localStorage</button>
    </div>

    <div class="test-box">
        <h4>Auto Test Results</h4>
        <div id="auto-test-results"></div>
    </div>

    <div class="test-box">
        <h4>Current localStorage Contents</h4>
        <div id="localstorage-contents"></div>
    </div>

    <div class="test-box">
        <h4>Console Messages</h4>
        <pre id="console-messages"></pre>
    </div>
</div>

<script>
// Debug function
function debugLog(message) {
    console.log('[TEST] ' + message);
    const consoleDiv = document.getElementById('console-messages');
    consoleDiv.textContent += '[TEST] ' + message + '\n';
    consoleDiv.scrollTop = consoleDiv.scrollHeight;
}

// Auto test localStorage
function autoTestLocalStorage() {
    const resultsDiv = document.getElementById('auto-test-results');
    let results = '';
    
    try {
        // Test 1: Can we set and get?
        localStorage.setItem('test_auto', 'test_value_123');
        const retrieved = localStorage.getItem('test_auto');
        
        if (retrieved === 'test_value_123') {
            results += '<div class="alert alert-success">✅ Test 1 PASSED: Can set and get values</div>';
            debugLog('Auto test 1 PASSED');
        } else {
            results += '<div class="alert alert-danger">❌ Test 1 FAILED: Cannot retrieve correct value</div>';
            debugLog('Auto test 1 FAILED');
        }
        
        // Test 2: Can we remove?
        localStorage.removeItem('test_auto');
        const afterRemove = localStorage.getItem('test_auto');
        
        if (afterRemove === null) {
            results += '<div class="alert alert-success">✅ Test 2 PASSED: Can remove values</div>';
            debugLog('Auto test 2 PASSED');
        } else {
            results += '<div class="alert alert-danger">❌ Test 2 FAILED: Cannot remove values</div>';
            debugLog('Auto test 2 FAILED');
        }
        
        // Test 3: Check browser support
        if (typeof(Storage) !== "undefined") {
            results += '<div class="alert alert-success">✅ Test 3 PASSED: Browser supports localStorage</div>';
            debugLog('Auto test 3 PASSED: Browser supports localStorage');
        } else {
            results += '<div class="alert alert-danger">❌ Test 3 FAILED: Browser does not support localStorage</div>';
            debugLog('Auto test 3 FAILED: Browser does not support localStorage');
        }
        
    } catch (e) {
        results += '<div class="alert alert-danger">❌ Test ERROR: ' + e.message + '</div>';
        debugLog('Auto test ERROR: ' + e.message);
    }
    
    resultsDiv.innerHTML = results;
}

// Manual test functions
function saveToLocalStorage() {
    const key = document.getElementById('test-key').value;
    const value = document.getElementById('test-value').value;
    
    if (!key || !value) {
        alert('Please enter both key and value');
        return;
    }
    
    try {
        localStorage.setItem(key, value);
        debugLog('Saved: ' + key + ' = ' + value);
        alert('Saved successfully!');
        updateLocalStorageContents();
    } catch (e) {
        debugLog('Save error: ' + e.message);
        alert('Save error: ' + e.message);
    }
}

function loadFromLocalStorage() {
    const key = document.getElementById('test-key').value;
    
    if (!key) {
        alert('Please enter a key');
        return;
    }
    
    const value = localStorage.getItem(key);
    if (value !== null) {
        document.getElementById('test-value').value = value;
        debugLog('Loaded: ' + key + ' = ' + value);
        alert('Loaded: ' + value);
    } else {
        debugLog('Key not found: ' + key);
        alert('Key not found');
    }
}

function clearLocalStorage() {
    if (confirm('Are you sure you want to clear all localStorage?')) {
        localStorage.clear();
        debugLog('localStorage cleared');
        alert('localStorage cleared!');
        updateLocalStorageContents();
    }
}

function updateLocalStorageContents() {
    const contentsDiv = document.getElementById('localstorage-contents');
    let contents = '';
    
    if (localStorage.length === 0) {
        contents = '<div class="alert alert-info">localStorage is empty</div>';
    } else {
        contents = '<table class="table table-striped"><thead><tr><th>Key</th><th>Value</th></tr></thead><tbody>';
        
        for (let i = 0; i < localStorage.length; i++) {
            const key = localStorage.key(i);
            const value = localStorage.getItem(key);
            contents += '<tr><td>' + key + '</td><td>' + value + '</td></tr>';
        }
        
        contents += '</tbody></table>';
    }
    
    contentsDiv.innerHTML = contents;
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    debugLog('Page loaded, starting tests...');
    autoTestLocalStorage();
    updateLocalStorageContents();
    
    // Update contents every 2 seconds
    setInterval(updateLocalStorageContents, 2000);
});
</script>

</body>
</html>

