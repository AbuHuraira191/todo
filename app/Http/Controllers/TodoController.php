<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\ToDo;
use App\Models\User;
use Mockery\Exception;

class TodoController extends Controller
{
    public function index()
    {
        try {
            /** @var User $user */
            $user = Auth::user();
            $todos = $user->todos()->paginate(10);

            if ($todos->isEmpty()){
                return response()->json(['message' => 'Todo list is empty add todo first'], 201);
            }
            return response()->json($todos);

        }catch (\Exception $e){
            return response()->json(['message' => $e->getMessage()], 500);
        }

    }

    public function store(Request $request)
    {
        try {
            /** @var User $user */
            $user = Auth::user();
            $data = $request->validate([
                'title' => 'required|string',
                'description' => 'required|string',
            ]);

            $data['user_id'] = $user->id;
            $todo = $user->todos()->create($data);

            return response()->json([ 'message' =>'Todo Create successfully',$todo], 201);
        }catch (Exception $e){
            return response()->json(['message' => $e->getMessage()], 500);
        }

    }

    public function show($id)
    {
        try {
            /** @var User $user */
            $user = Auth::user();
            $todo = $user->todos()->find($id);

            if (!$todo) {
                return response()->json(['message' => 'ToDo not found'], 404);
            }

            return response()->json(['data' => $todo],201);
        }catch (\Exception $e){
            return response()->json(['message' => $e->getMessage()], 500);
        }

    }

    public function update(Request $request, $id)
    {
        try {
            /** @var User $user */
            $user = Auth::user();
            $todo = $user->todos()->find($id);

            if (!$todo) {
                return response()->json(['message' => 'ToDo not found'], 404);
            }

            $data = $request->validate([
                'title' => 'required|string',
                'description' => 'required|string',
            ]);

            $todo->update($data);

            return response()->json(['message' => 'Todo Update Successfully','data' => $todo],201);
        }catch (Exception $e){
            return response()->json(['message' => $e->getMessage()], 500);
        }

    }

    public function destroy($id)
    {
        try {
            /** @var User $user */
            $user = Auth::user();
            $todo = $user->todos()->find($id);

            if (!$todo) {
                return response()->json(['message' => 'ToDo not found'], 404);
            }

            $todo->delete();

            return response()->json(['message' => 'ToDo deleted successfully'], 201);
        }catch (\Exception $e){
            return response()->json(['message' => $e->getMessage()], 500);
        }

    }
}
