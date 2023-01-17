<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Notify;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PhpParser\Node\Stmt\TryCatch;
use Twilio\Rest\Client;

class NotifyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    private $client;
    private $phoneNumber;
    protected $rules;

    function __construct()
    {
        $this->client = new Client(config('services.twilio.sid'), config('services.twilio.token'));
        $this->phoneNumber = config('services.twilio.phoneNumber');
        $this->rules = [
            'book_id' => ['required', 'integer'],
            'user_id' => ['required', 'integer'],
        ];
    }

    public function store(Request $request)
    {
        $validator =  Validator::make($request->all(), $this->rules);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        };

        $validated = $validator->validated();

        $notifySearched = Notify::where('user_id', '=', $validated['user_id'])->where('book_id', '=', $validated['book_id'])->first();

        if ($notifySearched) {
            $data = [
                'code' => 401,
                'message' => 'This user has already notify for this book'
            ];

            return response()->json($data, $data['code']);
        }

        $notify = Notify::create($validated);

        $user = User::find($validated['user_id']);


        if ($user->phone) {
            $book = Book::find($validated['book_id']);

            try {
                $messageSended = $this->client->messages
                    ->create(

                        "whatsapp:+5219841533143", // to
                        [
                            "from" => "whatsapp:" . $this->phoneNumber,
                            "body" => "Te has suscrito para las notificaciones del libro:\n" . $book->name
                        ]
                    );
            } catch (\Throwable $th) {
                throw $th;
            }
        }

        $data = [
            'code' => 201,
            'notify' => $notify
        ];

        return response()->json($data, $data['code']);
    }

    public function sendStatusBook($bookName, $phone, $status)
    {

        $messageStatus = $status === 0 ? 'No disponible' : 'Disponible';

        try {
            $messageSended = $this->client->messages
                ->create(

                    "whatsapp:" . $phone, // to
                    [
                        "from" => "whatsapp:" . $this->phoneNumber,
                        "body" => "El libro: " . $bookName . " tiene el estatus de " . $messageStatus
                    ]
                );
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
