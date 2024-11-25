<?php

namespace App\Http\Controllers;

use App\Filters\TaskFilter;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Resources\TaskResource;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::with('user')->where('user_id', Auth::id())->paginate(10);

        return response()->json([
            'tasks' => TaskResource::collection($tasks),
            'links' => [
                'first' => $tasks->url(1),
                'last' => $tasks->url($tasks->lastPage()),
                'prev' => $tasks->previousPageUrl(),
                'next' => $tasks->nextPageUrl(),
            ],
            'meta' => [
                'current_page' => $tasks->currentPage(),
                'from' => $tasks->firstItem(),
                'last_page' => $tasks->lastPage(),
                'path' => $tasks->path(),
                'per_page' => $tasks->perPage(),
                'to' => $tasks->lastItem(),
                'total' => $tasks->total(),
            ],
        ]);
    }
    public function store(StoreTaskRequest $request)
    {
        $term = Carbon::parse($request->term);
        $time = Carbon::createFromFormat('H:i', $request->time, 'Asia/Tashkent');
        $task = new Task();
        $task->user_id = Auth::id();
        $task->title = $request->title;
        $task->description = $request->description;
        $task->term = $term;
        $task->time = $time->format('H:i');

        $task->save();
        return response()->json([
            'task' => new TaskResource($task),
        ], 201);
    }
    public function show($id)
    {
        $task = Task::with('user')->findOrFail($id);
        if (Auth::id() !== $task->user_id) {
            return response()->json([
                'message' => "This task isn't yours",
            ], 403);
        }
        return response()->json([
            'task' => new TaskResource($task),
        ]);
    }
    public function update(UpdateTaskRequest $request, $id)
    {
        $term = Carbon::parse($request->term);
        $time = Carbon::createFromFormat('H:i', $request->time, 'Asia/Tashkent');

        $task = Task::with('user')->findOrFail($id);
        if (Auth::id() !== $task->user_id) {
            return response()->json([
                'message' => "This task isn't yours",
            ], 403);
        }
        $task->title = $request->title;
        $task->description = $request->description;
        $task->term = $term;
        $task->time = $time->format('H:i');
        $task->save();
        return response()->json([
            'message' => 'Task updated',
            'task' => new TaskResource($task),
        ]);
    }
    public function destroy($id)
    {
        $task = Task::findOrFail($id);
        if (Auth::id() !== $task->user_id) {
            return response()->json([
                'message' => "This task isn't yours",
            ], 403);
        }
        $task->delete();
        return response()->json([
            'message' => 'Task deleted',
        ], 204);
    }
    public function filterTask(Request $request)
    {
        $filter = new TaskFilter();
        $tasks = Task::query()->with('user')->where('user_id', Auth::id());
        $filteredTasks = $filter->apply($tasks, $request->all())->paginate(6);

        return response()->json([
            'tasks' => TaskResource::collection($filteredTasks)
        ]);

    }
    public function markDone($id)
    {
        $task = Task::findOrFail($id);
        if (Auth::id() !== $task->user_id) {
            return response()->json([
                'message' => "This task isn't yours",
            ], 403);
        }
        $task->confirmed = 'Done';
        $task->save();
        return response()->json(data: [
            'message' => 'The task was marked as completed'
        ]);
    }


}
