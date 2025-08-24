<!doctype html>
<html>
<head>
    <meta charset="utf-8"/>
    <title>API Docs</title>
    <link rel="stylesheet" href="https://unpkg.com/swagger-ui-dist/swagger-ui.css">
    <style>body{margin:0}</style>
</head>
<body>
<div id="swagger"></div>
<script src="https://unpkg.com/swagger-ui-dist/swagger-ui-bundle.js"></script>
<script>
    window.ui = SwaggerUIBundle({
        url: '/docs/openapi.yaml',      // Scribe кладе файл сюди
        dom_id: '#swagger'
    });
</script>
</body>
</html>
