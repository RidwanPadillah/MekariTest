<?php

namespace App\Http\Controllers;

use App\Task;
use Ramsey\Uuid\Uuid;
use Illuminate\Http\Request;
use App\Http\Requests\TaskRequest;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\TaskResource;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
             return (TaskResource::collection(Task::get()))
                ->response()
                ->setStatusCode(200);
        } else {
            return view('task');
        }
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TaskRequest $request)
    {
        try {
            $task = [];
            DB::transaction(function () use ($request, &$task) {
                $task = Task::create([
                    'uuid' => (string) Uuid::uuid4()->toString(),
                    'name' => $request->name
                ]);
            
            });    
            return (new TaskResource($task))
                ->additional([
                    'message' => 'TodoList has been added.'
                ])
                ->response()
                ->setStatusCode(200);
        } catch (\Exception $ex) {
            return response()->json([
                'status'  => false,
                'message' => $ex->getMessage()
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $task = Task::whereUuid($id)->firstOrFail();
            $task->delete();
            return (new TaskResource($task))
                ->additional([
                    'message' => 'TodoList has been deleted.'
                ])
                ->response()
                ->setStatusCode(200);
        } catch (\Exception $ex) {
            return response()->json([
                'status'  => false,
                'message' => $ex->getMessage()
            ])->setStatusCode(404);
        }
    }
}
