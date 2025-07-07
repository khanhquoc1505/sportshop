<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\DonHang;
use Carbon\Carbon;

class RevenueExport implements FromCollection, WithHeadings
{
    protected $params;

    public function __construct(array $params)
    {
        // nhận các filter từ controller
        $this->params = $params;
    }

    public function collection()
    {
        // lấy lại logic giống reportRevenue()
        $query = DonHang::query()
            ->whereIn('trangthaidonhang', ['dathanhtoan','hoanthanh']);

        if (!empty($this->params['start_date'])) {
            $query->whereDate('ngaydat','>=', $this->params['start_date']);
        }
        if (!empty($this->params['end_date'])) {
            $query->whereDate('ngaydat','<=', $this->params['end_date']);
        }

        // phân theo period
        $period = $this->params['period'] ?? 'day';
        switch($period) {
            case 'month':
                $dateExpr = "DATE_FORMAT(ngaydat,'%Y-%m')";
                break;
            case 'year':
                $dateExpr = "YEAR(ngaydat)";
                break;
            default:
                $dateExpr = "DATE(ngaydat)";
        }

        // build data
        $rows = $query
            ->selectRaw("$dateExpr as label")
            ->selectRaw("COUNT(*) as so_don")
            ->selectRaw("SUM(tongtien) as tong_doanhthu")
            ->groupBy('label')
            ->orderBy('label')
            ->get()
            ->map(function($r) use ($period) {
                // format label thành ngày/tháng/năm
                if ($period=='month') {
                    $lbl = Carbon::createFromFormat('Y-m', $r->label)->format('m/Y');
                } elseif ($period=='year') {
                    $lbl = $r->label;
                } else {
                    $lbl = Carbon::parse($r->label)->format('d/m/Y');
                }
                return [
                    'Thời gian'   => $lbl,
                    'Số Đơn'      => $r->so_don,
                    'Doanh thu ₫' => number_format($r->tong_doanhthu,0,',','.')
                ];
            });

        return $rows;
    }

    public function headings(): array
    {
        return ['Thời gian','Số đơn','Tổng doanh thu'];
    }
}
