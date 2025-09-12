<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calculator Display</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="h-full flex items-center justify-center">
        <div class="w-full px-4">
            <input type="text" id="display" readonly 
                   class="w-full h-12 px-3 text-right text-lg font-mono bg-white border border-gray-300 rounded-md focus:outline-none"
                   placeholder="0">
        </div>
    </div>

    <script>
        // Listen for messages from parent window
        window.addEventListener('message', function(event) {
            if (event.data.type === 'update-display') {
                document.getElementById('display').value = event.data.value || '0';
            }
        });
    </script>
</body>
</html>
