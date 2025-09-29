<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chunked Image Upload</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Uppy CSS is loaded via npm and Vite/Mix --}}
    
    <style>
        body { font-family: sans-serif; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; }
        .status-message { margin-top: 20px; padding: 15px; border-radius: 5px; }
        .success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>

<div class="container">
    <h1>Primary Image Upload for Product: {{ $product->sku ?? 'NEW-SKU' }}</h1>

    <div id="drag-drop-area" style="height: 500px;width:500px;"></div>

    <div id="status-area"></div>
</div>


    {{-- Include compiled JS via Vite or Mix --}}
    @vite('resources/js/app.js')

</body>
</html>