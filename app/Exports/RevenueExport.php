<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;

class RevenueExport implements FromCollection
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        // collection phải là tập các row associative array
        return collect($this->data);
    }
}
