<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use App\Filters\TaskFilter;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Requests\FilterRequest;
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
    public function filterTask(FilterRequest $request)
    {
        $filter = new TaskFilter();
        $tasks = Auth::user()->tasks();
        $filteredTasks = $filter->apply($tasks, $request->all())->paginate(6);

        return response()->json([
            'tasks' => TaskResource::collection($filteredTasks),
            'links' => [
                'first' => $filteredTasks->url(1),
                'last' => $filteredTasks->url($filteredTasks->lastPage()),
                'prev' => $filteredTasks->previousPageUrl(),
                'next' => $filteredTasks->nextPageUrl(),
            ],
            'meta' => [
                'current_page' => $filteredTasks->currentPage(),
                'from' => $filteredTasks->firstItem(),
                'last_page' => $filteredTasks->lastPage(),
                'path' => $filteredTasks->path(),
                'per_page' => $filteredTasks->perPage(),
                'to' => $filteredTasks->lastItem(),
                'total' => $filteredTasks->total(),
            ],

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
