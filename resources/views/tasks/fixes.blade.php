<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>入力修正</title>
    <style>
        /* edit.blade.php と同じスタイル */
        body {
            font-family: "Segoe UI", sans-serif;
            background: #f5f5f5;
            margin: 0;
            padding: 40px;
            color: #222;
        }

        h1 {
            text-align: center;
            color: #111;
            font-size: 2.2rem;
            margin-bottom: 30px;
            letter-spacing: 2px;
            font-weight: 300;
        }

        .container {
            max-width: 500px;
            margin: 0 auto;
            text-align: center;
        }

        input[type="text"] {
            width: 260px;
            padding: 10px 14px;
            border-radius: 6px;
            border: 1px solid #aaa;
            background: #fff;
            font-size: 1rem;
            transition: 0.2s;
            margin-bottom: 15px;
        }

        input[type="text"]:focus {
            border-color: #000;
            outline: none;
        }

        button {
            padding: 10px 30px;
            border: none;
            border-radius: 6px;
            background-color: #000;
            color: white;
            font-weight: 500;
            cursor: pointer;
            transition: 0.2s;
            display: inline-block;
            margin-top: 10px;
        }

        button:hover {
            background-color: #444;
        }

        .back-link {
            display: block;
            margin-top: 20px;
            color: #555;
            text-decoration: none;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        .input-group {
            margin-bottom: 5px;
        }

        /* エラーメッセージ用のスタイル */
        .error-message {
            color: #d32f2f;
            /* 赤色 */
            font-size: 0.9rem;
            margin-top: -10px;
            margin-bottom: 15px;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <h1>入力内容の修正</h1>

    <div class="container">
        <form action="{{ $task ? route('tasks.update', $task->id) : route('tasks.store') }}" method="POST">
            @csrf
            @if ($task)
                @method('PUT')
            @endif

            <div class="input-group">
                <input type="text" name="title" value="{{ $input_title }}" placeholder="タスク名" required>
            </div>

            <div class="input-group">
                <input type="text" name="due_date" value="{{ $input_date }}" placeholder="例: 2026-01-01"
                    style="border: 2px solid red;">
            </div>

            <div class="error-message">
                @if (isset($error_type) && $error_type === 'empty')
                    日付を入力してください
                @else
                    正しい日付を入力してください (例: 2026-01-01)
                @endif
            </div>

            <button type="submit">{{ $task ? '更新' : '追加' }}</button>
        </form>

        <a href="{{ route('tasks.index') }}" class="back-link">一覧に戻る</a>
    </div>
</body>

</html>
