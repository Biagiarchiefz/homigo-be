<?php

namespace App\Filament\Widgets;

use App\Models\Listing;
use App\Models\Transaction;
use Carbon\Carbon;
//use Faker\Core\Number;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class StatsOverview extends BaseWidget
{


    private function getPercentage(int $from, int $to)
    {
        return $to - $from / ($to + $from / 2) * 100;
    }


    protected function getStats(): array
    {
        // Listing:: panggil class model linsting (ingat sebuah moodel itu jembatan antara kode kita dengan database)
        // ingat di dalam sebuah class model itu sudah punya banyak method bawaan untuk bikin query SQl ( tanpa nulis SQL secara manual )
        // ini sama aja kayak query ambil data di database berdasar bulan sekarang dan tahun sekarang lalu jumlahkan jumlah datanya (ambil data listing yang terbaru)
        $newListing = Listing::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        $transactions = Transaction::whereStatus('approved')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year);

        // ambil transaction yang statusnya approved dibulan yang lalu di tahun yang sama
        // subMoth digunakan untuk kurangin 1 bulan dari bulan yang sekarang, kalau mau lebih kurangin bulanya bisa dengan menambahkan parameter di dalam methodnya subMonth()nya, contoh subMonth(2);
        $prevTransaction = Transaction::whereStatus('approved')
            ->whereMonth('created_at', Carbon::now()->subMonth()->month)
            ->whereYear('created_at', Carbon::now()->subMonth()->subMonth()->year);

        $transactionPercentange = $this->getPercentage($prevTransaction->count(), $transactions->count());
        $revenuePercentange = $this->getPercentage($prevTransaction->sum('total_price'), $transactions->sum('total_price'));

        return [
            Stat::make('New Listing of the month', $newListing),
            Stat::make('Transaction of the month', $transactions->count())
                ->description($transactionPercentange > 0 ? "{$transactionPercentange}% increased" : "{$transactionPercentange}% decreased")
                ->descriptionIcon($transactionPercentange > 0 ? "heroicon-m-arrow-trending-up" : "heroicon-m-trending-down")
                ->color($transactionPercentange > 0 ? "success" : "danger"),
            Stat::make('Revenue of the month', Number::currency($transactions->sum('total_price'), 'USD'))
                ->description($revenuePercentange > 0 ? "{$revenuePercentange}% increased" : "{$revenuePercentange}% decreased")
                ->descriptionIcon($revenuePercentange > 0 ? "heroicon-m-arrow-trending-up" : "heroicon-m-trending-down")
                ->color($revenuePercentange > 0 ? "success" : "danger"),


        ];

    }


}
