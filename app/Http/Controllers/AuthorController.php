<?php

namespace App\Http\Controllers;

use App\Models\Author;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class AuthorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    protected $rules;

    public function __construct()
    {
        $this->rules = [
            'name' => ['required', 'string']
        ];
    }
    public function index()
    {
        $authors = Author::paginate(5);

        $data = [
            'code' => 200,
            'authors' => $authors
        ];

        return response()->json($data, $data['code']);
    }

    public function all()
    {
        $authors = Author::all();

        $data = [
            'code' => 200,
            'authors' => $authors
        ];

        return response()->json($data, $data['code']);
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
    public function store(Request $request)
    {
        $role = auth()->user()->role;

        if($role !== 1){
            $data = [
                'code' => 403,
                'message' => 'Not Authorized'
            ];

            return response()->json($data, $data['code']);
        }

        $validator =  Validator::make($request->all(), $this->rules);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        };

        $validated = $validator->validated();

        $author = Author::create($validated);

        $data = [
            'code' => 201,
            'stored' => $author
        ];

        return response()->json($data, $data['code']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Author  $author
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $author = Author::with('books')->find($id);

        if(!$author) {

            $data = [
                'code' => 404,
                'message' => 'Not Found Author'
            ];

            return response()->json($data, $data['code']);
        }

        $data = [
            'code' => 200,
            'showed' => $author
        ];

        return response()->json($data, $data['code']);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Author  $author
     * @return \Illuminate\Http\Response
     */
    public function edit(Author $author)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Author  $author
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $role = auth()->user()->role;

        if($role !== 1){
            $data = [
                'code' => 403,
                'message' => 'Not Authorized'
            ];

            return response()->json($data, $data['code']);
        }

        $validator =  Validator::make($request->all(), $this->rules);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        };

        $author = Author::find($id);

        if(!$author) {
            $data = [
                'code' => 404,
                'message' => 'Author Not Found'
            ];

            return response()->json($data, $data['code']);
        }


        $validated = $validator->validated();

        $author->update($validated);

        $data = [
            'code' => 201,
            'updated' => $author
        ];

        return response()->json($data, $data['code']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Author  $author
     * @return \Illuminate\Http\Response
     */
    public function destroy(Author $author)
    {
        //
    }
}
