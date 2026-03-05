<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    public function index()
{
    $tasks = Task::orderBy('due_date', 'asc')->get();

    $events = $tasks->map(function($task) {
        return [
            'title' => $task->title,
            'start' => $task->due_date ?? $task->created_at->format('Y-m-d'),
            'color' => '#555'
        ];
    });

    return view('tasks.index', compact('tasks', 'events'));
}

public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'title' => 'required|max:255',
        'due_date' => 'required|date',
    ]);

    if ($validator->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
    }

    Task::create($request->all());
    return redirect()->back();
}
    public function edit($id)
    {
        $task = Task::findOrFail($id);
        return view('tasks.edit', compact('task'));
    }
    public function update(Request $request, $id)
{
    $task = Task::findOrFail($id);

    $validator = Validator::make($request->all(), [
        'title' => 'required|max:255',
        'due_date' => 'required|date',
    ]);

    if ($validator->fails()) {
        $error_type = empty($request->due_date) ? 'empty' : 'invalid';

        return view('tasks.fixes', [
            'task'        => $task,
            'input_title' => $request->title,
            'input_date'  => $request->due_date,
            'error_type'  => $error_type
        ]);
    }

    $task->update([
        'title' => $request->title,
        'due_date' => $request->due_date,
    ]);
    
    return redirect()->route('tasks.index')->with('status', '更新しました。');
}

    public function destroy($id)
    {
        Task::findOrFail($id)->delete();
        return redirect()->back();
    }
    public function export()
    {
        $tasks = Task::all();
        $csvHeader = ['ID', 'Title', 'Due Date', 'Created At'];
        $timestamp = date('Ymd_His'); 
        $fileName = "backups/tasks_backup_{$timestamp}.csv";

        $content = implode(',', $csvHeader) . "\n";

        foreach ($tasks as $task) {
            $content .= implode(',', [
                $task->id,
                $task->title,
                $task->due_date,
                $task->created_at,
            ]) . "\n";
        }
        Storage::put($fileName, $content);
        return redirect()->back()->with('status', "バックアップ ({$fileName}) を保存しました。");
    }
    public function import(Request $request)
{
    $request->validate([
        'csv_file' => 'required|file',
    ]);

    $file = $request->file('csv_file');
    $handle = fopen($file->getPathname(), 'r');
    
    fgetcsv($handle); 

    \App\Models\Task::truncate(); 

    while (($data = fgetcsv($handle)) !== false) {
        \App\Models\Task::create([
            'title'    => $data[1],
            'due_date' => ($data[2] === '' || $data[2] === 'null') ? null : $data[2]
        ]);
    }
    
    fclose($handle);

    return redirect()->back()->with('status', 'インポートを完了しました。');
}
}