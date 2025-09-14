<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;


class Listing extends Model
{
    use HasFactory, softDeletes;

//    $fillable adalah daftar kolom yang boleh diisi sekaligus ketika kita membuat atau menupdate data lewat method seperti Model::create([...])
// ini digunakan untuk keamanan agar field yang tidak diizinkin tidak bisa sembarang diisi
// intinya kta kasi tau ke si laravel kolom mana saja yang boleh diisi datanya dari request user
// Daftar field di bwh berarti hanya kolom itu saja yang boleh diisi pakai create() atau update().
    protected $fillable = [
        'title',
        'slug',
        'description',
        'address',
        'sqft',
        'wifi_speed',
        'max_person',
        'price',
        'price_per_day',
        'attachments',
        'full_support_available',
        'gym_area_available',
        'mini_cafe_available',
        'cinema_available',
    ];

    // ketika datanya di lempar ke frontend berupa api JSON data si attachmentsnya berupa array
    // $casts = otomatis ubah tipe data dari database jadi bentuk yang enak dipakai di PHP.
    // attachments otomatis diubah dari string JSON jadi array sama seperti JSON.parse() di js
    //  '["file1.jpg","file2.jpg","file3.jpg"]' to ["file1.jpg", "file2.jpg", "file3.jpg"]
    protected $casts = [
        'attachments' => 'array',
    ];

    // Kalau return 'slug', berarti semua route model binding untuk Listing bakal pakai slug sebagai pencarian.
    // dilaravel kalau kita menggunakan route model binding nnti untuk route Listing slug bawaanyakan id,
    // nah fungsi getRouteKeyName() tersebut digunakan untuk memeberti tau si laravel bahwanya slug yang di terima bukan id lagi tetapi data slug yang dari database
    public function getRouteKeyName() {
        return 'slug';
    }

    // kalau di kolom title diisi datanya si kolom slugnya ikutan diisi datanya sesuai dengan $value
    // fungsi ini merupkan mutator
    // mutator adalah fungsi yang dijalankan ketika kita mengisi kolom data tertentu contohnys title
    // parameter $value adalah data yang kta masukkan ke kolom title
    public function setTitleAttribute($value) {
        $this->attributes['title'] = $value;
        // Sekaligus otomatis bikin kolom slug berdasarkan title.
        // Str::slug() = helper Laravel yang mengubah string jadi format URL-friendly
        $this->attributes['slug'] = Str::slug($value);
    }

    // hasMany artinya = memiliki banyak
    // listing memiliki beberapa transaksi
    // hasMany apa? // cara baca 1 listing memliki banyak transaction
    public function transactions(): HasMany {
        return $this->hasMany(Transaction::class);
    }

}
