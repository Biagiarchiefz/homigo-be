<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Transaction\Store;
use App\Models\Listing;
use App\Models\Transaction;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TransactionController extends Controller {

    // whereUserId()  itu dapatnya dari nama field mode yang di $filabble
    // get all my transaction  // ambil semua transaction yang berelasi dengan listing dimana userIdnya, id dari user yang sedang login sekarang
    function index() : JsonResponse{
        $transactions = Transaction::with('listing')
            ->whereUserId(auth()->id())
            ->paginate();

        return response()->json([
            'success' => true,
            'message' => 'Get my all transactions',
            'data' => $transactions
        ]);
    }


    // api untukk booking listing
    // dipakai untuk mengecek apakah sebuah listing (misalnya kos, rumah, atau ruang kerja) masih bisa dipesan atau sudah penuh (fully booked).
    private function _fullyBookedChecker(Store $request) {
        // membuat query
        $listing = Listing::find($request->listing_id);
        $runningTransactionCount = Transaction::whereListingId($listing->id)
            ->whereNot('status', 'canceled')
            ->where(function ($query) use ($request) {     // sub Query
                $query->whereBetween('start_date', [
                    $request->start_date,
                    $request->end_date,
                ])->orWhereBetween('end_date', [
                    $request->start_date,
                    $request->end_date,
                ])->orWhere(function ($subquery) use ($request) {
                    $subquery->where('start_date', '<', $request->start_date)
                        ->where('end_date', '>', $request->end_date);
                });
            })->count();

        // Kalau jumlah booking aktif sudah sama atau lebih dari kapasitas (max_person) → return error dengan JSON:
        if ($runningTransactionCount >= $listing->max_person) {
            throw new HttpResponseException(
                response()->json([
                    'success' => false,
                    'message' => 'Listing is Fully Booked!',
                ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
            );
        }
        return true;

    }

    // fungsi ini yang dipanggil dari route (endPoint Api)
    //  Dia pakai _fullyBookedChecker() untuk ngecek apakah listing masih available.
    // Kalau aman (tidak full), maka return JSON:
    public function isAvailable(Store $request):JsonResponse {
        $this->_fullyBookedChecker($request);

        return response()->json([
            'success' => true,
            'message' => "Listing is ready to book!",
        ]);
    }

    // Parameternya Store $request artinya Laravel otomatis pakai FormRequest Store (yang kamu buat sebelumnya).
    // Jadi, sebelum sampai ke sini, request sudah divalidasi dan dicek authorization-nya.
    public function store (Store $request): JsonResponse {
        $this->_fullyBookedChecker($request);      // Gunanya buat ngecek apakah listing yang mau dibooking masih ada slot atau sudah full.
        $transaction = Transaction::create([    // query buat sebuah transaction listing baru // Ini query INSERT ke database.
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'listing_id' => $request->listing_id,
            'user_id' => auth()->id(),         // otomatis isi dengan ID user yang login sekarang (auth()->id()).
        ]);

        // Ini manggil relasi Eloquent antara Transaction dan Listing.
        // Jadi $transaction->listing otomatis load data listing yang berhubungan sama transaksi ini.
        // Walaupun nggak dipakai langsung, biasanya ini buat memastikan eager loading (biar listing ikut keambil).
        $transaction->listing;

        return response()->json([
            'success' => true,
            'message' => "New Transaction has been created!",
            'data' => $transaction
        ]);
    }


    // Get Detail Transaction
    function show (Transaction $transaction): JsonResponse {
        // jika transaction user_id yang diingin detail transactionnya tidak sama dengan id user yang sedang login sekarang
        if ($transaction->user_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized!',
                ], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $transaction->listing;

        return response()->json([
            'success' => true,
            'message' => 'Get Detail Transaction',
            'data' => $transaction
        ]);
    }


}
