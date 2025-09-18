<?php

namespace App\Http\Requests\Transaction;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Nette\Schema\ValidationException;


// Store adalah class yang turunan dari FormRequest.
//Artinya dia punya semua kemampuan request + validasi otomatis dari Laravel.
class Store extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */

    // Ini dipakai untuk cek siapa yang boleh pakai request ini.
    // true → semua orang boleh.
    // false → nggak boleh.
    // Di sini, hanya user dengan role 'customer' yang boleh pakai request ini.
    // Kalau bukan customer, validasi langsung gagal (403 Forbidden).
    public function authorize(): bool
    {
        return auth()->user()->role === 'customer';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */


    // Ini aturan validasi buat setiap input:
    public function rules(): array {
        return [
            'listing_id' => 'required|exists:listings,id',     // listing_id → wajib ada, dan harus cocok dengan kolom id di tabel listings.
            'start_date' => 'required|date',                   // start_date → wajib ada, dan harus berupa tanggal valid.
            'end_date' => 'required|date|after:start_date',    // end_date → wajib ada, harus berupa tanggal valid, dan HARUS lebih besar dari start_date.
        ];
    }


    protected function failedValidation(Validator $validator) {
        $errors = ( new ValidationException($validator))->errors();

        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'data' => $errors

            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
        );
    }


}

// Store ini dipakai di controller sebagai tipe parameter:
// Laravel otomatis cek:
// 1. Apakah user authorized (boleh pakai request ini).
// 2. Apakah input valid sesuai rules.
// 3. Kalau error → balikin JSON error yang kamu custom.
// 4. Kalau oke → lanjut ke logic controller.


// FormRequest ini kayak "satpam" di depan controller.
// Dia jaga biar yang masuk ke controller sudah pasti:
// - user yang benar,
// - data yang benar formatnya.
