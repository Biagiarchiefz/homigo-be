<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'listing_id',
        'start_date',
        'end_date',
        'price_per_day',
        'total_days',
        'fee',
        'total_price',
        'status',
    ];

    public function setListingIdAttribute($value) {

        // melakukan kalkulasi secara otomatis
        $listing = Listing::find($value);
        $totalDays = Carbon::createFromDate($this->attributes['start_date'])->diffInDays($this->attributes['end_date']) + 1;
        $totalPrice = $listing->price_per_day * $totalDays;
        $fee = $totalPrice * 0.1;

        // setelah itu kita simpan ke table database valuenya
        $this->attributes['listing_id'] = $value;
        $this->attributes['price_per_day'] = $listing->price_per_day;
        $this->attributes['total_days'] = $totalDays;
        $this->attributes['fee'] = $fee;
        $this->attributes['total_price'] = $totalPrice + $fee;

    }

    // arti kata Belongsto = milik
    // many to one - satu transaksi hanya dimiliki oleh satu users
    // 1 transaksi dimiliki oleh siapa - bbrti 1 transaksi dimiliki oleh satu user kan brrti nama fungsinya adalah user()
    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    //// many to one - satu transaksi hanya dimiliki oleh satu listing kamar
    public function listing(): BelongsTo {
        return $this->belongsTo(Listing::class);
    }



}
