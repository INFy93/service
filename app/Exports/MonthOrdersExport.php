<?php

namespace App\Exports;

use App\Models\Order;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
class MonthOrdersExport implements WithHeadings, FromCollection, WithMapping
{
    use Exportable;

    public function collection()
    {
        $today = Carbon::today();
        $month = Carbon::now()->subMonth();

        return Order::with(['statuses', 'services', 'users'])
        ->whereBetween('created_at', [$month, $today])
        ->orderBy('id', 'desc')
        ->get();
    }

    public function map($ord) : array {
        return [
           $ord->id,
           $ord->statuses->name,
           $ord->users->name,
           $ord->created_at = date("d.m.Y H:i", strtotime($ord->created_at)),
           $ord->product,
           $ord->model,
           $ord->model_full_name,
           $ord->complection,
           $ord->malfunction,
           $ord->client_login,
           $ord->client_phone
        ] ;


    }

    public function headings(): array
    {
        return [
            '#',
            'Статус',
            'Создан',
            'Дата создания',
            'Изделие',
            'Модель',
            'Полная модель',
            'Комплектация',
            'Неисправность',
            'Клиент',
            'Телефон клиента',
        ];
    }
}