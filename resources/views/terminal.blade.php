<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terminal Quest</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-black min-h-screen flex items-center justify-center p-4">
    <div id="terminal" class="terminal-wrapper w-full max-w-3xl">
        <div class="terminal-screen">
            <div class="scanlines"></div>
            <div class="crt-flicker"></div>
            <div class="terminal-content">
                <div id="terminal-log" class="terminal-log"></div>
                <div class="terminal-input-line">
                    <span class="prompt">&gt;</span>
                    <input
                        id="terminal-input"
                        type="text"
                        class="terminal-input"
                        autocomplete="off"
                        spellcheck="false"
                        autofocus
                    >
                </div>
            </div>
        </div>
    </div>
</body>
</html>
