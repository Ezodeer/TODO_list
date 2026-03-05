<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ToDo App</title>
    <style>
        body {
            font-family: "Segoe UI", sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        .container {
            padding: 20px;
        }

        h1 {
            font-weight: 300;
            letter-spacing: 4px;
            color: #333;
            margin-bottom: 30px;
        }

        .start-btn {
            display: inline-block;
            padding: 18px 45px;
            background-color: #000;
            color: #fff;
            font-size: 1.2rem;
            text-decoration: none;
            border-radius: 6px;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        .start-btn:hover {
            background-color: #444;
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body>

    <div class="container">
        
        <a href="{{ route('tasks.index') }}" class="start-btn">
            TODOリストを使う
        </a>
    </div>

</body>
</html>
