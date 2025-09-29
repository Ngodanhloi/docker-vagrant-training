<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Redis Demo</title>
    <?php include 'views/meta.php' ?>
    <style>
        .demo-card{max-width:600px;margin:30px auto}
        .row{margin-bottom:10px}
    </style>
    <script src="public/js/jquery-2.1.4.min.js"></script>
</head>
<body>
<?php include 'views/header.php'?>
<div class="container demo-card">
    <div class="panel panel-default">
        <div class="panel-heading">LocalStorage vs Redis Cloud</div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-4"><label for="demo-key">Key</label></div>
                <div class="col-sm-8"><input id="demo-key" class="form-control" placeholder="foo"></div>
            </div>
            <div class="row">
                <div class="col-sm-4"><label for="demo-value">Value</label></div>
                <div class="col-sm-8"><input id="demo-value" class="form-control" placeholder="bar"></div>
            </div>
            <div class="row">
                <div class="col-sm-6"><button id="save-local" class="btn btn-default btn-block">Save to localStorage</button></div>
                <div class="col-sm-6"><button id="load-local" class="btn btn-default btn-block">Load from localStorage</button></div>
            </div>
            <div class="row">
                <div class="col-sm-6"><button id="save-server" class="btn btn-primary btn-block">Save to Redis (server)</button></div>
                <div class="col-sm-6"><button id="load-server" class="btn btn-primary btn-block">Load from Redis (server)</button></div>
            </div>
            <hr>
            <pre id="demo-output">Ready.</pre>
        </div>
    </div>
    <p>
        Server connects to Redis Cloud using Predis. Configure credentials via environment variables.
    </p>
    <p><code>redis_api.php</code> provides POST/GET endpoints.</p>
    <p>Local storage operations stay in the browser only.</p>
    <script src="public/js/redis-demo.js"></script>
    <script>
      document.getElementById('demo-key').value = localStorage.getItem('lastKey') || 'foo';
      document.getElementById('demo-value').value = localStorage.getItem('lastValue') || 'bar';
      ['demo-key','demo-value'].forEach(function(id){
        document.getElementById(id).addEventListener('input', function(){
          localStorage.setItem('lastKey', document.getElementById('demo-key').value);
          localStorage.setItem('lastValue', document.getElementById('demo-value').value);
        });
      });
    </script>
</div>
</body>
</html>



