<?php
// database/seeders/DataImportSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DataImportSeeder extends Seeder
{
    public function run()
    {
        $rows = [
            // 1) Bóng đá
            [
                'bomon'   => 'Bóng đá',
                'masp'    => 'BD00011',
                'ten'     => 'set bóng đá ARES Golden Storm',
                'gia'     => 200000,
                'hinh'    => '1.jpg',
                'mota'    => 'Áo bóng đá',
                'tt'      => 1,
                'tg'      => now(),
                'loai'    => 'bộ',
                'mausac'  => 'vàng',
                'gt'      => 'nam,nữ',
                'sizes'   => 'S,M,L,XL',
                'sl'      => 10,
                'gianhap' => 120000,
            ],
            [
                'bomon'   => 'Bóng đá',
                'masp'    => 'BD00012',
                'ten'     => 'set bóng đá ARES Scarlet Flame',
                'gia'     => 200000,
                'hinh'    => '2.jpg',
                'mota'    => 'Áo bóng đá',
                'tt'      => 1,
                'tg'      => now(),
                'loai'    => 'bộ',
                'mausac'  => 'đỏ',
                'gt'      => 'nam,nữ',
                'sizes'   => 'S,M,L,XL',
                'sl'      => 20,
                'gianhap' => 120000,
            ],

            // 2) Bóng rổ
            [
                'bomon'   => 'Bóng rổ',
                'masp'    => 'BR00001',
                'ten'     => 'Quần áo đồng phục bóng rổ NBA Warriors',
                'gia'     => 300000,
                'hinh'    => 'bongro1.jpg',
                'mota'    => 'Quần áo đồng phục bóng rổ NBA Warriors',
                'tt'      => 1,
                'tg'      => now(),
                'loai'    => 'bộ',
                'mausac'  => 'xanh',
                'gt'      => 'nam,nữ',
                'sizes'   => 'S,M,L,XL',
                'sl'      => 15,
                'gianhap' => 200000,
            ],
            [
                'bomon'   => 'Bóng rổ',
                'masp'    => 'BR00002',
                'ten'     => 'Quần áo đồng phục bóng rổ NBA Warriors',
                'gia'     => 300000,
                'hinh'    => 'bongro2.jpg',
                'mota'    => 'Quần áo đồng phục bóng rổ NBA Warriors',
                'tt'      => 1,
                'tg'      => now(),
                'loai'    => 'bộ',
                'mausac'  => 'vang',
                'gt'      => 'nam,nữ',
                'sizes'   => 'S,M,L,XL',
                'sl'      => 5,
                'gianhap' => 200000,
            ],

            // 3) Cầu lông
            [
                'bomon'   => 'Cầu lông',
                'masp'    => 'CL00001',
                'ten'     => 'Áo cầu lông Candy Pulse',
                'gia'     => 150000,
                'hinh'    => 'aocl1.jpg',
                'mota'    => 'Áo cầu lông Candy Pulse',
                'tt'      => 1,
                'tg'      => now(),
                'loai'    => 'áo',
                'mausac'  => 'hồng xanh',
                'gt'      => 'nam,nữ',
                'sizes'   => 'S,M,L,XL',
                'sl'      => 6,
                'gianhap' => 80000,
            ],
            [
                'bomon'   => 'Cầu lông',
                'masp'    => 'CL00002',
                'ten'     => 'Áo cầu lông Fireline Dash',
                'gia'     => 150000,
                'hinh'    => 'aocl2.jpg',
                'mota'    => 'Áo cầu lông Fireline Dash',
                'tt'      => 1,
                'tg'      => now(),
                'loai'    => 'áo',
                'mausac'  => 'đen',
                'gt'      => 'nam,nữ',
                'sizes'   => 'S,M,L,XL',
                'sl'      => 7,
                'gianhap' => 80000,
            ],

            // 4) Váy cầu lông
            [
                'bomon'   => 'Cầu lông',
                'masp'    => 'VCL0001',
                'ten'     => 'Váy cầu lông Yonex 26130EX Purple',
                'gia'     => 140000,
                'hinh'    => 'vayCL1.jpg',
                'mota'    => 'Váy cầu lông Yonex 26130EX Purple',
                'tt'      => 1,
                'tg'      => now(),
                'loai'    => 'váy',
                'mausac'  => 'tím',
                'gt'      => 'nữ',
                'sizes'   => 'S,M,L,XL',
                'sl'      => 5,
                'gianhap' => 90000,
            ],
            [
                'bomon'   => 'Cầu lông',
                'masp'    => 'VCL0002',
                'ten'     => 'Váy cầu lông Yonex 26130EX Orange',
                'gia'     => 140000,
                'hinh'    => 'vayCL2.jpg',
                'mota'    => 'Váy cầu lông Yonex 26130EX Orange',
                'tt'      => 1,
                'tg'      => now(),
                'loai'    => 'váy',
                'mausac'  => 'đỏ',
                'gt'      => 'nữ',
                'sizes'   => 'S,M,L,XL',
                'sl'      => 4,
                'gianhap' => 90000,
            ],

            // 5) Bóng chuyền
            [
                'bomon'   => 'Bóng chuyền',
                'masp'    => 'BC0001',
                'ten'     => 'Bộ Quần Áo Bóng Chuyền Nam Egan BCE01',
                'gia'     => 219000,
                'hinh'    => 'bc1.jpg',
                'mota'    => 'Bộ Quần Áo Bóng Chuyền Nam Egan BCE01',
                'tt'      => 1,
                'tg'      => now(),
                'loai'    => 'bộ',
                'mausac'  => 'đỏ kem trà',
                'gt'      => 'nam',
                'sizes'   => 'S,M,L,XL',
                'sl'      => 10,
                'gianhap' => 130000,
            ],
            [
                'bomon'   => 'Bóng chuyền',
                'masp'    => 'BC0002',
                'ten'     => 'Bộ Đồ Bóng Chuyền Nam Icon 2025',
                'gia'     => 169000,
                'hinh'    => 'bc6.jpg',
                'mota'    => 'Bộ Đồ Bóng Chuyền Nam Icon 2025',
                'tt'      => 1,
                'tg'      => now(),
                'loai'    => 'bộ',
                'mausac'  => 'đỏ,trắng',
                'gt'      => 'nam',
                'sizes'   => 'S,M,L,XL',
                'sl'      => 35,
                'gianhap' => 100000,
            ],
            [
                'bomon'   => 'Bóng chuyền',
                'masp'    => 'BC0003',
                'ten'     => 'Bộ Áo Bóng Chuyền Nữ Servy Mới Nhất 2025',
                'gia'     => 169000,
                'hinh'    => 'bc14.jpg',
                'mota'    => 'Bộ Áo Bóng Chuyền Nữ Servy Mới Nhất 2025',
                'tt'      => 1,
                'tg'      => now(),
                'loai'    => 'bộ',
                'mausac'  => 'xanh,đỏ',
                'gt'      => 'nữ',
                'sizes'   => 'S,M,L,XL',
                'sl'      => 6,
                'gianhap' => 100000,
            ],

            // 6) Cầu lông + Bóng rổ
            [
                'bomon'   => 'Cầu lông, bóng rổ',
                'masp'    => 'QCL0001',
                'ten'     => 'Quần cầu lông Yonex 15175EX',
                'gia'     => 160000,
                'hinh'    => 'quancl1.jpg',
                'mota'    => 'Quần cầu lông Yonex 15175EX',
                'tt'      => 1,
                'tg'      => now(),
                'loai'    => 'quần',
                'mausac'  => 'đen',
                'gt'      => 'nam',
                'sizes'   => 'S,M,L,XL',
                'sl'      => 11,
                'gianhap' => 90000,
            ],
            [
                'bomon'   => 'Cầu lông, bóng rổ',
                'masp'    => 'QCL0002',
                'ten'     => 'Quần cầu lông Yonex 15175EX White',
                'gia'     => 160000,
                'hinh'    => 'quancl2.jpg',
                'mota'    => 'Quần cầu lông Yonex 15175EX White',
                'tt'      => 1,
                'tg'      => now(),
                'loai'    => 'quần',
                'mausac'  => 'trắng',
                'gt'      => 'nam',
                'sizes'   => 'S,M,L,XL',
                'sl'      => 12,
                'gianhap' => 90000,
            ],
        ];

        foreach ($rows as $row) {
            DB::transaction(function() use($row) {
                // 1) sanpham
                $spId = DB::table('sanpham')->insertGetId([
                    'masanpham'     => $row['masp'],
                    'ten'           => $row['ten'],
                    'mo_ta'         => $row['mota'],
                    'gia_ban'       => $row['gia'],
                    'hinh_anh'      => $row['hinh'],
                    'trang_thai'    => $row['tt'],
                    'thoi_gian_them'=> $row['tg'],
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);

                // 2) bomon_sanpham (có thể nhiều giá trị)
                foreach (explode(',', $row['bomon']) as $bm) {
                    $bm = trim($bm);
                    $bomonId = DB::table('bomon')
                        ->where('bomon', $bm)
                        ->value('id')
                      ?: DB::table('bomon')->insertGetId([
                            'bomon'=> $bm, 'created_at'=>now(), 'updated_at'=>now()
                        ]);
                    DB::table('bomon_sanpham')->insert([
                        'sanpham_id'=> $spId,
                        'bomon_id'  => $bomonId,
                        'created_at'=> now(),
                        'updated_at'=> now(),
                    ]);
                }

                // 3) loai_sanpham
                $loaiId = DB::table('loai')
                    ->where('loai', $row['loai'])
                    ->value('id')
                  ?: DB::table('loai')->insertGetId([
                        'loai'=> $row['loai'], 'created_at'=>now(), 'updated_at'=>now()
                    ]);
                DB::table('loai_sanpham')->insert([
                    'sanpham_id'=> $spId,
                    'loai_id'   => $loaiId,
                    'created_at'=> now(),
                    'updated_at'=> now(),
                ]);

                // 4) gioitinh_sanpham (có thể nhiều giá trị)
                foreach (explode(',', $row['gt']) as $gt) {
                    $gt = trim($gt);
                    $gtId = DB::table('gioitinh')
                        ->where('gioitinh', $gt)
                        ->value('id')
                      ?: DB::table('gioitinh')->insertGetId([
                            'gioitinh'=> $gt, 'created_at'=>now(), 'updated_at'=>now()
                        ]);
                    DB::table('gioitinh_sanpham')->insert([
                        'sanpham_id'=> $spId,
                        'gioitinh_id'=> $gtId,
                        'created_at'=> now(),
                        'updated_at'=> now(),
                    ]);
                }

                // 5) mausac
                $mauId = DB::table('mausac')
                    ->where('mausac', $row['mausac'])
                    ->value('id')
                  ?: DB::table('mausac')->insertGetId([
                        'mausac'=> $row['mausac'], 'created_at'=>now(), 'updated_at'=>now()
                    ]);

                // 6) kichco + sanpham_kichco_mausac
                $sizeIds = [];
                foreach (explode(',', $row['sizes']) as $s) {
                    $s = trim($s);
                    $sid = DB::table('kichco')
                        ->where('size', $s)
                        ->value('id')
                      ?: DB::table('kichco')->insertGetId([
                            'size'=> $s,
                            'loai_size'=> '',
                            'created_at'=>now(),
                            'updated_at'=>now()
                        ]);
                    $sizeIds[] = $sid;
                }
                $totalQty = $row['sl'];
                $count    = count($sizeIds);
                $base     = intdiv($totalQty, $count);
                $rem      = $totalQty % $count;
                foreach ($sizeIds as $i => $sid) {
                    $qty = $base + ($i < $rem ? 1 : 0);
                    DB::table('sanpham_kichco_mausac')->insert([
                        'sanpham_id'=> $spId,
                        'kichco_id' => $sid,
                        'mausac_id' => $mauId,
                        'sl'        => $qty,
                        'created_at'=> now(),
                        'updated_at'=> now(),
                    ]);
                }

                // 7) nhapkho + nhapkho_kichco_mausac
                $nhId = DB::table('nhapkho')->insertGetId([  
                    'nguoidung_id'=> 1,
                    'sanpham_id'  => $spId,
                    'ngaynhap'    => now(),
                    'soluongnhap' => $totalQty,
                    'gianhap'     => $row['gianhap'],
                    'ghichu'      => null,
                    'created_at'=> now(),
                    'updated_at'=> now(),
                ]);
                foreach ($sizeIds as $i => $sid) {
                    $qty = $base + ($i < $rem ? 1 : 0);
                    DB::table('nhapkho_kichco_mausac')->insert([
                        'nhapkho_id'=> $nhId,
                        'kichco_id' => $sid,
                        'mausac_id' => $mauId,
                        'sl'        => $qty,
                        'created_at'=> now(),
                        'updated_at'=> now(),
                    ]);
                }
            });
        }
    }
}
