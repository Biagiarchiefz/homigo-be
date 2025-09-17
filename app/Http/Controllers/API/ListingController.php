<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;





class ListingController extends Controller {

    // NOTES
   public function index (): JsonResponse {
        $listings = Listing::withCount('transactions')->orderBy('transactions_count', 'desc')->paginate();

        return response()->json([
            'success' => true,
            'message' => 'Get All Listings',
            'data' => $listings
        ]);

    }


    public function show(Listing $listing): JsonResponse {
            return response()->json([
                'success' => true,
                'message' => 'Get Detail Listing',
                'data' => $listing
            ]);
    }



}
