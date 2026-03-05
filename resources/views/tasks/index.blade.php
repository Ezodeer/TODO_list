<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>ToDoリスト</title>
    <script src="{{ asset('js/fullcalendar.js') }}"></script>
    <style>
        body {
            font-family: "Segoe UI", sans-serif;
            background: #f5f5f5;
            margin: 0;
            padding: 20px;
        }

        .dashboard {
            display: flex;
            gap: 30px;
            max-width: 1200px;
            margin: 0 auto;
            align-items: flex-start;
        }

        .calendar-section {
            flex: 1;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #ddd;
        }

        .list-section {
            flex: 1;
        }

        h1 {
            text-align: center;
            font-weight: 300;
            letter-spacing: 2px;
        }

        .add-form {
            text-align: center;
            margin-bottom: 20px;
        }

        input[type="text"],
        input[type="date"] {
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #aaa;
            margin-bottom: 5px;
        }

        ul {
            list-style: none;
            padding: 0;
        }

        li {
            background: #fff;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 8px;
            border: 1px solid #ddd;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .btn-primary {
            background: #000;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        .btn-secondary {
            background: #555;
            color: white;
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            text-decoration: none;
            font-size: 0.8rem;
        }

        .button-group {
            display: flex;
            gap: 5px;
        }

        .date-label-wrapper {
            display: inline-flex;
            align-items: center;
            background: #eee;
            padding: 8px 15px;
            border-radius: 20px;
            cursor: pointer;
            transition: background 0.3s;
            font-size: 0.9rem;
            color: #555;
            border: 1px solid transparent;
        }

        .date-label-wrapper:hover {
            background: #e0e0e0;
            border-color: #ccc;
        }

        /* 実際の入力項目は隠す */
        .hidden-date-input {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
            pointer-events: none;
        }

        .date-display::before {
            content: "📅 ";
        }
    </style>
</head>

<body>
    <h1>ToDoリスト</h1>

    <div class="dashboard">
        <div class="calendar-section">
            <div id="calendar"></div>
        </div>

        <div class="list-section">
            @if (session('status'))
                <div
                    style="background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 10px; font-size: 0.8rem;">
                    {{ session('status') }}
                </div>
            @endif

            <div
                style="display: flex; justify-content: flex-end; align-items: center; gap: 15px; margin-bottom: 20px; background: #eee; padding: 10px; border-radius: 8px;">

                <form action="{{ route('tasks.export') }}" method="GET" style="margin: 0;">
                    <button type="submit" class="btn-secondary" style="background-color: #333;">バックアップ作成</button>
                </form>

                <form id="importForm" action="{{ route('tasks.import') }}" method="POST" enctype="multipart/form-data"
                    style="margin: 0; display: flex; align-items: center; gap: 5px;">
                    @csrf
                    <input type="file" id="csvFileInput" name="csv_file" accept=".csv" required
                        style="width: auto; font-size: 0.8rem;">
                    <button type="button" onclick="checkDiffAndSubmit()" class="btn-secondary"
                        style="background-color: #555;">インポート</button>
                </form>

            </div>

            <div class="add-form">
                <form action="{{ route('tasks.store') }}" method="POST">
                    @csrf
                    <input type="text" name="title" value="{{ old('title') }}" placeholder="タスク名" required>

                    <input type="text" name="due_date" value="{{ old('due_date') }}" placeholder="2026-01-01"
                        style="{{ $errors->has('due_date') ? 'border: 2px solid red;' : '' }}">

                    <button type="submit" class="btn-primary">追加</button>

                    @error('due_date')
                        <div style="color: red; font-size: 0.8rem; margin-top: 5px; font-weight: bold;">
                            @if ($message == 'The due date field is required.')
                                日付を入力してください
                            @else
                                正しい日付を入力してください (例: 2026-01-01)
                            @endif
                        </div>
                    @enderror
                </form>
            </div>

            <ul>
                @foreach ($tasks as $task)
                    <li>
                        <div>
                            <strong>{{ $task->title }}</strong><br>
                            <small style="color: #888;">{{ $task->due_date ?? '日付なし' }}</small>
                        </div>
                        <div class="button-group">
                            <a href="{{ route('tasks.edit', $task->id) }}" class="btn-secondary">編集</a>
                            <form action="{{ route('tasks.destroy', $task->id) }}" method="POST">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-secondary"
                                    onclick="return confirm('削除しますか？')">削除</button>
                            </form>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>


    <script>
        function checkDiffAndSubmit() {
            const fileInput = document.getElementById('csvFileInput');
            const file = fileInput.files[0];

            if (!file) {
                alert("ファイルを選択してください。");
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                const text = e.target.result;
                const lines = text.split("\n");
                let hasDifference = false;

                const currentTasks = Array.from(document.querySelectorAll('li span')).map(el => el.innerText.trim());

                for (let i = 1; i < lines.length; i++) {
                    if (lines[i].trim() === "") continue;
                    const columns = lines[i].split(",");
                    const csvTitle = columns[1].trim();

                    if (!currentTasks.includes(csvTitle)) {
                        hasDifference = true;
                        break;
                    }
                }

                if (hasDifference) {
                    if (confirm("インポートすると今のリストの内容が変わってしまいますがよろしいでしょうか？")) {
                        document.getElementById('importForm').submit();
                    }
                } else {
                    document.getElementById('importForm').submit();
                }
            };
            reader.readAsText(file);
        }
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'ja',
                height: 600,
                events: @json($events)
            });
            calendar.render();
        });
    </script>


</body>

</html>
