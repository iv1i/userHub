<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Internal Server Error</title>
    <style>
        /* Error Pages Modern Styles */
        :root {
            --primary: #4361ee;
            --primary-dark: #3a56d4;
            --error-400: #ff6b35;
            --error-403: #ff9e00;
            --error-404: #ef476f;
            --error-422: #ffd166;
            --error-500: #c1121f;
            --success: #06d6a0;
            --background: #f8f9fa;
            --card-bg: #ffffff;
            --text: #2b2d42;
            --text-light: #8d99ae;
            --border: #e9ecef;
            --shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            --gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: var(--background);
            color: var(--text);
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .error-container {
            background: var(--card-bg);
            padding: 4rem 3rem;
            border-radius: 24px;
            box-shadow: var(--shadow);
            text-align: center;
            max-width: 600px;
            width: 100%;
            position: relative;
            overflow: hidden;
            animation: slideInUp 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }

        .error-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: var(--primary);
        }

        .error-400 .error-container::before { background: var(--error-400); }
        .error-403 .error-container::before { background: var(--error-403); }
        .error-404 .error-container::before { background: var(--error-404); }
        .error-422 .error-container::before { background: var(--error-422); }
        .error-500 .error-container::before { background: var(--error-500); }

        .error-icon {
            font-size: 5rem;
            margin-bottom: 1.5rem;
            animation: bounce 2s infinite;
            display: block;
        }

        .error-400 .error-icon { content: "🚫"; color: var(--error-400); }
        .error-403 .error-icon { content: "⛔"; color: var(--error-403); }
        .error-404 .error-icon { content: "🔍"; color: var(--error-404); }
        .error-422 .error-icon { content: "📝"; color: var(--error-422); }
        .error-500 .error-icon { content: "⚡"; color: var(--error-500); }

        .error-code {
            font-size: 6rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
            line-height: 1;
            background: linear-gradient(135deg, var(--primary), #7209b7);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .error-400 .error-code { background: linear-gradient(135deg, var(--error-400), #ff8e00); }
        .error-403 .error-code { background: linear-gradient(135deg, var(--error-403), #ff6d00); }
        .error-404 .error-code { background: linear-gradient(135deg, var(--error-404), #ff2e63); }
        .error-422 .error-code { background: linear-gradient(135deg, var(--error-422), #ffb300); }
        .error-500 .error-code { background: linear-gradient(135deg, var(--error-500), #e63946); }

        .error-400 .error-code,
        .error-403 .error-code,
        .error-404 .error-code,
        .error-422 .error-code,
        .error-500 .error-code {
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .error-message {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: var(--text);
        }

        .error-description {
            font-size: 1.1rem;
            color: var(--text-light);
            margin-bottom: 2.5rem;
            line-height: 1.6;
        }

        .error-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
            margin-bottom: 2rem;
        }

        .btn {
            padding: 12px 28px;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
            box-shadow: 0 4px 15px rgba(67, 97, 238, 0.3);
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(67, 97, 238, 0.4);
        }

        .btn-secondary {
            background: transparent;
            border: 2px solid var(--border);
            color: var(--text);
        }

        .btn-secondary:hover {
            background: var(--border);
            transform: translateY(-3px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .error-links {
            display: flex;
            justify-content: center;
            gap: 2rem;
            flex-wrap: wrap;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid var(--border);
        }

        .error-link {
            color: var(--text-light);
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            position: relative;
        }

        .error-link:hover {
            color: var(--primary);
        }

        .error-link::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 1px;
            background: var(--primary);
            transition: width 0.3s ease;
        }

        .error-link:hover::after {
            width: 100%;
        }

        /* Debug Information */
        .debug-info {
            text-align: left;
            margin-top: 2.5rem;
            padding: 1.5rem;
            background: rgba(248, 249, 250, 0.8);
            border-radius: 12px;
            border-left: 4px solid var(--primary);
            animation: slideInUp 0.6s ease-out 0.3s both;
        }

        .debug-info strong {
            color: var(--text);
            margin-bottom: 0.5rem;
            display: block;
        }

        .debug-info pre {
            background: rgba(0, 0, 0, 0.05);
            padding: 1rem;
            border-radius: 8px;
            margin-top: 0.5rem;
            font-size: 0.85rem;
            overflow-x: auto;
            white-space: pre-wrap;
        }

        /* Validation Errors */
        .validation-errors {
            text-align: left;
            margin: 2rem 0;
            padding: 1.5rem;
            background: rgba(255, 209, 102, 0.1);
            border: 1px solid var(--error-422);
            border-left: 4px solid var(--error-422);
            border-radius: 12px;
            animation: slideInUp 0.6s ease-out 0.2s both;
        }

        .validation-errors ul {
            list-style: none;
            padding: 0;
        }

        .validation-errors li {
            padding: 0.5rem 0;
            border-bottom: 1px solid rgba(255, 209, 102, 0.3);
        }

        .validation-errors li:last-child {
            border-bottom: none;
        }

        .validation-errors strong {
            color: var(--error-422);
        }

        /* Animations */
        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(40px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% {
                transform: translateY(0);
            }
            40% {
                transform: translateY(-15px);
            }
            60% {
                transform: translateY(-7px);
            }
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
            100% {
                transform: scale(1);
            }
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-20px);
            }
        }

        /* Decorative Elements */
        .error-decoration {
            position: absolute;
            opacity: 0.1;
            z-index: -1;
            font-size: 8rem;
            animation: float 6s ease-in-out infinite;
        }

        .decoration-1 {
            top: 10%;
            left: 5%;
        }

        .decoration-2 {
            bottom: 10%;
            right: 5%;
            animation-delay: -3s;
        }

        /* Ripple Effect */
        .btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: translate(-50%, -50%);
            transition: width 0.3s, height 0.3s;
        }

        .btn:hover::before {
            width: 100%;
            height: 100%;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .error-container {
                padding: 3rem 2rem;
                margin: 1rem;
            }

            .error-code {
                font-size: 4rem;
            }

            .error-message {
                font-size: 1.5rem;
            }

            .error-icon {
                font-size: 4rem;
            }

            .error-actions {
                flex-direction: column;
                align-items: center;
            }

            .error-actions .btn {
                width: 200px;
            }

            .error-links {
                gap: 1rem;
            }

            .error-decoration {
                font-size: 4rem;
            }
        }

        @media (max-width: 480px) {
            .error-container {
                padding: 2rem 1.5rem;
            }

            .error-code {
                font-size: 3.5rem;
            }

            .error-message {
                font-size: 1.3rem;
            }

            .error-icon {
                font-size: 3rem;
            }

            .btn {
                padding: 10px 20px;
                font-size: 0.95rem;
            }

            .error-decoration {
                display: none;
            }
        }

        /* Dark mode support */
        @media (prefers-color-scheme: dark) {
            :root {
                --background: #1a1a1a;
                --card-bg: #2d2d2d;
                --text: #ffffff;
                --text-light: #b0b0b0;
                --border: #404040;
            }

            .debug-info {
                background: rgba(255, 255, 255, 0.05);
            }

            .debug-info pre {
                background: rgba(0, 0, 0, 0.3);
            }
        }
    </style>
</head>
<body class="error-500">
<div class="error-decoration decoration-1">⚡</div>
<div class="error-decoration decoration-2">🔧</div>

<div class="error-container">
    <div class="error-icon"></div>
    <h1 class="error-code">500</h1>
    <h2 class="error-message">Internal Server Error</h2>
    <p class="error-description">
        Something went wrong on our servers. We're working to fix the issue. Please try again later.
    </p>

    <div class="error-actions">
        <a href="/" class="btn btn-primary">Homepage</a>
        <a href="javascript:location.reload()" class="btn btn-secondary">Try Again</a>
    </div>

    <?php if ($debug): ?>
        <div class="debug-info">
            <strong>Debug Information:</strong><br>
            Message: <?= htmlspecialchars($message) ?><br>
            File: <?= $exception->getFile() ?><br>
            Line: <?= $exception->getLine() ?><br>
            <pre><?= htmlspecialchars($exception->getTraceAsString()) ?></pre>
        </div>
    <?php endif; ?>
</div>
</body>
</html>