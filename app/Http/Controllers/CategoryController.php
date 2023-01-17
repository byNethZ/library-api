<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
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
            'name' => ['required', 'string'],
            'description' => ['required', 'string']
        ];
    }

    public function index()
    {
        $categories = Category::paginate(5);

        $data = [
            'code' => 200,
            'categories' => $categories
        ];

        return response()->json($data, $data['code']);
    }

    public function all()
    {
        $categories = Category::all();

        $data = [
            'code' => 200,
            'categories' => $categories
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

        $category = Category::create($validated);

        $data = [
            'code' => 201,
            'stored' => $category
        ];

        return response()->json($data, $data['code']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $cateogory = Category::with('books')->find($id);

        if(!$cateogory) {

            $data = [
                'code' => 404,
                'message' => 'Not Found cateogory'
            ];

            return response()->json($data, $data['code']);
        }

        $data = [
            'code' => 200,
            'showed' => $cateogory
        ];

        return response()->json($data, $data['code']);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function edit(Category $category)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Category  $category
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

        $category = Category::find($id);

        if(!$category) {
            $data = [
                'code' => 404,
                'message' => 'category Not Found'
            ];

            return response()->json($data, $data['code']);
        }


        $validated = $validator->validated();

        $category->update($validated);

        $data = [
            'code' => 201,
            'updated' => $category
        ];

        return response()->json($data, $data['code']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $role = auth()->user()->role;

        if($role !== 1){
            $data = [
                'code' => 403,
                'message' => 'Not Authorized'
            ];

            return response()->json($data, $data['code']);
        }

        $category = Category::where('id', $id)->first();
        $category->books()->detach();
        $category->delete();

        $data = [
            'code' => 201,
            'category_id_deleted' => $id
        ];

        return response()->json($data, $data['code']);
    }
}
