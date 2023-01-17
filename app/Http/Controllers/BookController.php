<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Notify;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BookController extends Controller
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
            'published_date' => ['required', 'date'],
            'author_id' => ['required', 'array'],
            'author_id.*' => ['required', 'integer'],
            'categories_id' => ['required', 'array'],
            'categories_id.*' => ['required', 'integer'],
        ];
    }
    public function index()
    {
        $books = Book::with('categories')->paginate(5);

        $data = [
            'code' => 200,
            'books' => $books
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

        $book = Book::create([
            'name' => $validated['name'],
            'published_date' => $validated['published_date']
        ]);

        $book->categories()->attach($validated['categories_id']);
        $book->authors()->attach($validated['author_id']);

        $data = [
            'code' => 201,
            'stored' => $book
        ];

        return response()->json($data, $data['code']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $book = Book::with(['categories'  => function($query) {
            $query->select('id', 'name');
        }, 'authors'])->find($id);

        if(!$book) {

            $data = [
                'code' => 404,
                'message' => 'Not Found book'
            ];

            return response()->json($data, $data['code']);
        }

        $data = [
            'code' => 200,
            'showed' => $book
        ];

        return response()->json($data, $data['code']);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function edit(Book $book)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Book  $book
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

        $book = Book::find($id);

        if(!$book) {
            $data = [
                'code' => 404,
                'message' => 'Book Not Found'
            ];

            return response()->json($data, $data['code']);
        }

        $validated = $validator->validated();

        $book->update([
            'name' => $validated['name'],
            'published_date' => $validated['published_date']
        ]);

        $book->categories()->sync($validated['categories_id']);
        $book->authors()->sync($validated['author_id']);

        $data = [
            'code' => 201,
            'updated' => $book
        ];

        return response()->json($data, $data['code']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Book  $book
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

        $book = Book::where('id', $id)->first();

        $book->categories()->detach();
        $book->authors()->detach();

        $book->delete();

        $data = [
            'code' => 201,
            'book_id_deleted' => $id
        ];

        return response()->json($data, $data['code']);
    }

    public function borrow(Request $request, $id, $status){
        $role = auth()->user()->role;

        if($role !== 1){
            $data = [
                'code' => 403,
                'message' => 'Not Authorized'
            ];

            return response()->json($data, $data['code']);
        }

        $book = Book::find($id);

            if(!$book) {
                $data = [
                    'code' => 404,
                    'message' => 'Book Not Found'
                ];

                return response()->json($data, $data['code']);
            }

        if($status === 1){

            $rules = [
                'user_borrowed_id' => ['required', 'integer']
            ];

            $validator =  Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 400);
            };

            $validated = $validator->validated();

            $book->update([
                'user_borrowed_id' => $validated['user_borrowed_id']
            ]);

            $notifyUser = Notify::where('user_id', '=', $validated['user_borrowed_id'])->where('book_id', '=', $id)->first();

            if($notifyUser){

                $user = User::find($validated['user_borrowed_id']);

                $user->notifies()->detach($id);
            }
        } else {

            $book->update([
                'user_borrowed_id' => null
            ]);

        }


        $notifyController = new NotifyController;

        $followers = $book->followers;

        if($followers){
            foreach($followers as $follower){
                if($follower->phone){
                    $notifyController->sendStatusBook($book->name, $id, $status);

                }

            }

        }

        $data = [
            'code' => 201,
            'message' => 'Status updated successfully'
        ];


        return response()->json($data, $data['code']);


    }

}
