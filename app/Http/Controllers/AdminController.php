<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Hash;
use App\Exports\RevenueExport;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;
use App\Models\Feedback;


class AdminController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboard');
    }
    public function product()
    {
        // Lấy dữ liệu từ DB, hoặc giả lập
        // $items = Warehouse::all();
        $items = [
            (object) ['id' => 1, 'name' => 'Sản phẩm A', 'image' => 'images/a.png', 'time' => '2025-05-24', 'qty' => 10],
            (object) ['id' => 2, 'name' => 'Sản phẩm B', 'image' => 'images/b.png', 'time' => '2025-05-23', 'qty' => 5],
        ];

        return view('admin.product.index', compact('items'));
    }
    public function productEdit($id)
    {
        // Nếu có Model: $item = Warehouse::findOrFail($id);
        // Tạm giả dữ liệu:
        $items = [
            (object) ['id' => 1, 'name' => 'SP A', 'image' => 'images/a.png', 'time' => '2025-05-24', 'qty' => 10],
            (object) ['id' => 2, 'name' => 'SP B', 'image' => 'images/b.png', 'time' => '2025-05-23', 'qty' => 5],
        ];
        $item = collect($items)->firstWhere('id', $id);

        return view('admin.product.edit', compact('item'));
    }
    public function productUpdate(Request $request, $id)
    {
        // $data = $request->validate([
        //   'name' => 'required|string',
        //   'image' => 'nullable|image',
        //   'time' => 'required|date',
        //   'qty'  => 'required|integer',
        // ]);
        //
        // $item = Warehouse::findOrFail($id);
        // if($request->hasFile('image')){
        //   $path = $request->file('image')->store('images','public');
        //   $data['image'] = 'storage/'.$path;
        // }
        // $item->update($data);

        // tạm redirect về index
        return redirect()->route('admin.product.index')
            ->with('success', 'Cập nhật thành công');
    }
    public function productCreate()
    {
        return view('admin.product.create');
    }
    public function productStore(Request $request)
    {
        // Validate (tuỳ bạn)
        $data = $request->validate([
            'name' => 'required|string',
            'time' => 'required|date',
            'qty' => 'required|integer',
            'image' => 'nullable|image',
            'size' => 'nullable|string',
            'price' => 'nullable|numeric',
            'bomon' => 'nullable|string',
            'category' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        // Lấy mảng hiện tại từ session/demo
        $items = $this->getItems();  // nếu bạn đang dùng session như ví dụ trước

        // Tự sinh ID tiếp theo
        $maxId = collect($items)->max('id');
        $newId = $maxId + 1;

        // Xử lý ảnh nếu có upload
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('images', 'public');
            $data['image'] = 'storage/' . $path;
        } else {
            $data['image'] = null; // hoặc mặc định
        }

        // Thêm trường id và qty kiểu int
        $newItem = array_merge([
            'id' => $newId,
            'qty' => (int) $data['qty'],
        ], $data);

        // Đẩy vào mảng và lưu session
        $items[] = $newItem;
        session(['product_items' => $items]);

        return redirect()
            ->route('admin.product.index')
            ->with('success', "Đã thêm sản phẩm #{$newId}");
    }
    private $initialUsers = [
        ['id' => 1, 'name' => 'Nguyễn Văn A', 'email' => 'a@test.com', 'phone' => '0123456789', 'role' => 'admin', 'is_active' => 1],
        ['id' => 2, 'name' => 'Trần Thị B', 'email' => 'b@test.com', 'phone' => '0987654321', 'role' => 'customer', 'is_active' => 0],
        ['id' => 3, 'name' => 'Lê Văn C', 'email' => 'c@test.com', 'phone' => '0112233445', 'role' => 'customer', 'is_active' => 1],
    ];

    // Lấy mảng users từ session, khởi tạo lần đầu
    private function getUsers()
    {
        if (!session()->has('admin_users')) {
            session(['admin_users' => $this->initialUsers]);
        }
        return session('admin_users');
    }

    // Ghi mảng users vào session
    private function saveUsers(array $users)
    {
        session(['admin_users' => $users]);
    }

    /** 1) Hiển thị danh sách user */
    public function users(Request $request)
    {
        $users = collect($this->getUsers());

        // filter search
        if ($request->filled('search')) {
            $q = mb_strtolower(trim($request->search));
            $users = $users->filter(function ($u) use ($q) {
                return str_contains((string) $u['id'], $q)
                    || str_contains(mb_strtolower($u['name']), $q)
                    || str_contains(mb_strtolower($u['email']), $q);
            });
        }
        // filter status
        if ($request->filled('status')) {
            $status = $request->status === 'active' ? 1 : 0;
            $users = $users->where('is_active', $status);
        }
        // 3) Lọc theo role mới
        if ($request->filled('role')) {
            $users = $users->where('role', $request->role);
        }

        // paginate manual (simple)
        $perPage = 10;
        $page = max(1, (int) $request->page);
        $slice = $users->slice(($page - 1) * $perPage, $perPage)->values();
        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $slice,
            $users->count(),
            $perPage,
            $page,
            ['path' => route('admin.users.index'), 'query' => $request->query()]
        );

        return view('admin.users.index', [
            'users' => $paginator
        ]);
    }

    /** 2) Form chỉnh sửa */
    public function usersEdit($id)
    {
        $user = collect($this->getUsers())->firstWhere('id', (int) $id);
        if (!$user)
            abort(404);
        return view('admin.users.edit', compact('user'));
    }

    /** 3) Xử lý update */
    public function usersUpdate(Request $request, $id)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email',
            'phone' => 'nullable|string',
            'role' => 'required|in:admin,customer',
            'is_active' => 'required|in:0,1',
        ]);

        $users = $this->getUsers();
        foreach ($users as &$u) {
            if ($u['id'] == (int) $id) {
                $u['name'] = $data['name'];
                $u['email'] = $data['email'];
                $u['phone'] = $data['phone'];
                $u['role'] = $data['role'];
                $u['is_active'] = (int) $data['is_active'];
                break;
            }
        }
        $this->saveUsers($users);

        return redirect()->route('admin.users.index')
            ->with('success', 'Cập nhật thành công');
    }

    /** 4) Xóa user */
    public function usersDestroy(Request $request, $id)
    {
        $users = $this->getUsers();
        $users = array_filter($users, fn($u) => $u['id'] !== (int) $id);
        $this->saveUsers(array_values($users));

        return back()->with('success', 'Đã xóa user');
    }
    public function reportrevenue(Request $request)
    {
        // Thu thập dữ liệu tùy filter (vd: từ session hoặc DB)
        $data = $this->getRevenueData($request);

        return view('admin.report.revenue', [
            'reportData' => $data
        ]);
    }

    // Export ra Excel
    public function exportRevenue(Request $request)
    {
        $data = $this->getRevenueData($request);
        $fileName = 'revenue_' . now()->format('Ymd_His') . '.xlsx';
        return Excel::download(new RevenueExport($data), $fileName);
    }

    // Hiển thị phiên bản cho in
    public function printRevenue(Request $request)
    {
        $data = $this->getRevenueData($request);
        return view('admin.report.revenue-print', [
            'reportData' => $data
        ]);
    }

    // Hàm gom dữ liệu theo request filters
    private function getRevenueData(Request $request): array
    {
        // ví dụ dummy: array of rows [ ['date'=>'2025-05-01','product'=>'A','qty'=>5,'total'=>100], … ]
        // Ở đây bạn thay bằng query DB hoặc session-data
        return [
            ['Date' => '2025-05-01', 'Product' => 'A', 'Quantity' => 5, 'Total' => 500],
            ['Date' => '2025-05-02', 'Product' => 'B', 'Quantity' => 3, 'Total' => 300],
            // …
        ];
    }
    private $initialCategories = [
        ['id' => 1, 'name' => 'Áo Thun', 'status' => 1, 'parent' => null, 'created_at' => '2025-05-01'],
        ['id' => 2, 'name' => 'Quần Jean', 'status' => 1, 'parent' => null, 'created_at' => '2025-05-02'],
        ['id' => 3, 'name' => 'Áo Khoác', 'status' => 0, 'parent' => 1, 'created_at' => '2025-05-03'],
        ['id' => 4, 'name' => 'Phụ Kiện', 'status' => 1, 'parent' => null, 'created_at' => '2025-05-04'],
    ];

    // Lấy hoặc seed session
    private function getCategories(): array
    {
        if (!session()->has('admin_categories')) {
            session(['admin_categories' => $this->initialCategories]);
        }
        return session('admin_categories');
    }
    private function saveCategories(array $cats): void
    {
        session(['admin_categories' => $cats]);
    }

    /**
     * Hiển thị trang Danh mục
     */
    public function categories(Request $request)
    {
        // Lấy mảng và convert thành collection
        $cats = collect($this->getCategories());

        // Search theo tên hoặc ID
        if ($request->filled('search')) {
            $q = mb_strtolower(trim($request->search));
            $cats = $cats->filter(function ($c) use ($q) {
                return str_contains((string) $c['id'], $q)
                    || str_contains(mb_strtolower($c['name']), $q);
            });
        }

        // Paginate thủ công
        $perPage = 10;
        $page = max(1, (int) $request->page);
        $total = $cats->count();
        $slice = $cats->slice(($page - 1) * $perPage, $perPage)->values();

        $paginator = new LengthAwarePaginator(
            $slice,
            $total,
            $perPage,
            $page,
            ['path' => route('admin.categories.index'), 'query' => $request->query()]
        );

        return view('admin.categories.index', [
            'categories' => $paginator
        ]);
    }
    public function categoriesCreate()
    {
        // Lấy list các danh mục cha (parent = null) để chọn
        $all = $this->getCategories();
        $parents = collect($all)
            ->whereNull('parent')
            ->values()
            ->all();

        return view('admin.categories.create', compact('parents'));
    }

    /**
     * Xử lý lưu danh mục mới
     */
    public function categoriesStore(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|in:0,1',
            'parent' => 'nullable|integer',   // ID danh mục cha
            'created_at' => 'required|date',
        ]);

        // Lấy mảng hiện tại
        $items = $this->getCategories();

        // Tính ID mới = max hiện tại + 1
        $maxId = collect($items)->max('id');
        $newId = $maxId + 1;

        // Tạo record mới
        $new = [
            'id' => $newId,
            'name' => $data['name'],
            'status' => (int) $data['status'],
            'parent' => $data['parent'] ?? null,
            'created_at' => $data['created_at'],
        ];

        // Lưu lại session
        $items[] = $new;
        session(['admin_categories' => $items]);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', "Đã thêm danh mục #{$newId}");
    }
    public function categoriesEdit($id)
    {
        // Lấy toàn bộ và tìm category theo id
        $all = $this->getCategories();
        $current = collect($all)->firstWhere('id', (int) $id);
        if (!$current) {
            abort(404);
        }

        // Danh sách parent options (những mục cha: parent = null, và không phải chính nó)
        $parents = collect($all)
            ->whereNull('parent')
            ->where('id', '!=', (int) $id)
            ->values()
            ->all();

        return view('admin.categories.edit', compact('current', 'parents'));
    }

    /**
     *  Xử lý lưu cập nhật danh mục
     */
    public function categoriesUpdate(Request $request, $id)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|in:0,1',
            'parent' => 'nullable|integer|not_in:' . $id, // không để parent = chính nó
            'created_at' => 'required|date',
        ]);

        $cats = $this->getCategories();
        foreach ($cats as &$c) {
            if ($c['id'] === (int) $id) {
                $c['name'] = $data['name'];
                $c['status'] = (int) $data['status'];
                $c['parent'] = $data['parent'] ?? null;
                $c['created_at'] = $data['created_at'];
                break;
            }
        }
        $this->saveCategories($cats);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Cập nhật danh mục thành công');
    }
    public function categoriesDestroy($id)
    {
        // Lấy mảng cũ
        $cats = $this->getCategories();

        // Lọc bỏ phần tử có id trùng
        $filtered = array_filter($cats, function ($c) use ($id) {
            return $c['id'] !== (int) $id;
        });

        // Reset lại index và lưu session
        $this->saveCategories(array_values($filtered));

        return back()->with('success', "Đã xóa danh mục #{$id}");
    }
    private function getOrders(): array
    {
        if (!session()->has('admin_orders')) {
            session([
                'admin_orders' => [
                    // --- Đơn hàng mẫu #1 ---
                    [
                        'id' => 'DH001',
                        'customer' => 'Nguyễn Văn A',
                        'payment_status' => 'paid',       // 'paid' hoặc 'unpaid'
                        'order_status' => 'completed',  // 'pending','processing','completed','canceled'
                        'shipping_method' => 'Giao hàng nhanh',
                        'shipping_fee' => 20000,
                        'discount' => 0,
                        'refunded_amount' => 0,
                        'created_at' => '2025-05-20 10:30:00',
                        'items' => [
                            [
                                'name' => 'Quần jeans nam trắng',
                                'sku' => 'Q-0001',
                                'quantity' => 1,
                                'price' => 1100000,
                                // 'image_url' => 'https://link-to-image.jpg', // nếu có
                            ],
                            [
                                'name' => 'Jumpsuit ngắn bẹt vai, tay dài',
                                'sku' => 'J-0001',
                                'quantity' => 1,
                                'price' => 550000,
                            ],
                        ],
                        'notes' => '',
                        'delivery_status' => 'delivered', // 'pending','shipping','delivered','returned'
                    ],

                    // --- Đơn hàng mẫu #2 ---
                    [
                        'id' => 'DH002',
                        'customer' => 'Trần Thị B',
                        'payment_status' => 'unpaid',
                        'order_status' => 'pending',
                        'shipping_method' => 'Giao tiêu chuẩn',
                        'shipping_fee' => 15000,
                        'discount' => 0,
                        'refunded_amount' => 0,
                        'created_at' => '2025-05-21 15:45:00',
                        'items' => [
                            [
                                'name' => 'Áo phông xanh',
                                'sku' => 'A-0001',
                                'quantity' => 2,
                                'price' => 200000,
                            ],
                            [
                                'name' => 'Nón thể thao',
                                'sku' => 'N-0001',
                                'quantity' => 1,
                                'price' => 150000,
                            ],
                        ],
                        'notes' => '',
                        'delivery_status' => 'pending',
                    ],
                ]
            ]);
        }

        return session('admin_orders');
    }

    /**
     * Lưu mảng orders trở lại session
     */
    private function saveOrders(array $orders): void
    {
        session(['admin_orders' => $orders]);
    }

    /**
     * Hiển thị trang Danh sách Đơn hàng (index)
     * Bao gồm filter: search (mã đơn hoặc tên khách), from_date, to_date.
     */
    public function orders(Request $request)
    {
        // 1. Lấy mảng orders từ session (seed nếu chưa có)
        $all = collect($this->getOrders());

        // 2. Tìm kiếm theo 'search' (mã đơn hoặc tên khách)
        if ($request->filled('search')) {
            $q = mb_strtolower(trim($request->search));
            $all = $all->filter(function ($o) use ($q) {
                $haystack = mb_strtolower($o['id'] . ' ' . $o['customer']);
                return str_contains($haystack, $q);
            })->values();
        }

        // 3. Lọc từ ngày (from_date)
        if ($request->filled('from_date')) {
            try {
                $from = Carbon::parse($request->input('from_date'))->startOfDay();
                $all = $all->filter(fn($o) => Carbon::parse($o['created_at'])->gte($from))->values();
            } catch (\Exception $e) {
                // Nếu parse lỗi, bỏ qua
            }
        }

        // 4. Lọc đến ngày (to_date)
        if ($request->filled('to_date')) {
            try {
                $to = Carbon::parse($request->input('to_date'))->endOfDay();
                $all = $all->filter(fn($o) => Carbon::parse($o['created_at'])->lte($to))->values();
            } catch (\Exception $e) {
                // Nếu parse lỗi, bỏ qua
            }
        }

        // 5. Tính toán lại total_amount cho mỗi đơn: tổng tiền hàng - discount + shipping_fee
        $all = $all->map(function ($o) {
            // Nếu key 'items' không tồn tại, đảm bảo là mảng rỗng
            $items = $o['items'] ?? [];
            $sumItems = collect($items)->sum(fn($i) => $i['price'] * $i['quantity']);
            $discount = $o['discount'] ?? 0;
            $shipping = $o['shipping_fee'] ?? 0;

            $o['total_amount'] = $sumItems - $discount + $shipping;
            return $o;
        });

        // 6. Phân trang thủ công với LengthAwarePaginator
        $perPage = 10;
        $page = max(1, (int) $request->page);
        $slice = $all->slice(($page - 1) * $perPage, $perPage)->values();

        $paginator = new LengthAwarePaginator(
            $slice,
            $all->count(),
            $perPage,
            $page,
            [
                'path' => route('admin.orders.index'),
                'query' => $request->query(),
            ]
        );

        return view('admin.orders.index', [
            'orders' => $paginator,
            'search' => $request->input('search', ''),
            'from_date' => $request->input('from_date', ''),
            'to_date' => $request->input('to_date', ''),
        ]);
    }

    /**
     * Hiển thị Chi tiết 1 Đơn hàng
     */
    public function ordersShow($id)
    {
        // 1. Lấy mảng orders từ session
        $orders = $this->getOrders();

        // 2. Tìm đúng order theo id
        $order = collect($orders)->firstWhere('id', $id);
        if (!$order) {
            abort(404);
        }

        // 3. Phòng ngừa khi thiếu các key
        $order['shipping_fee'] = $order['shipping_fee'] ?? 0;
        $order['discount'] = $order['discount'] ?? 0;
        $order['refunded_amount'] = $order['refunded_amount'] ?? 0;
        $order['items'] = $order['items'] ?? [];
        $order['delivery_status'] = $order['delivery_status'] ?? 'pending';
        $order['notes'] = $order['notes'] ?? '';

        // 4. Tính toán lại các giá trị liên quan
        $sumItems = collect($order['items'])->sum(fn($i) => $i['price'] * $i['quantity']);
        $order['total_amount'] = $sumItems - $order['discount'] + $order['shipping_fee'];
        $order['paid_amount'] = ($order['payment_status'] === 'paid') ? $order['total_amount'] : 0;
        $order['received_amount'] = $order['paid_amount'] - $order['refunded_amount'];

        return view('admin.orders.show', compact('order'));
    }

    /**
     * Cập nhật Ghi chú (notes) cho Đơn hàng – chỉ sửa mỗi key 'notes'.
     */
    public function ordersUpdateNotes(Request $request, $id)
    {
        // 1. Validate chỉ có field 'notes'
        $data = $request->validate([
            'notes' => 'nullable|string|max:500',
        ]);

        // 2. Lấy mảng orders từ session
        $orders = $this->getOrders();

        // 3. Duyệt, nếu trùng id thì update 'notes'
        foreach ($orders as &$o) {
            if ($o['id'] === $id) {
                $o['notes'] = $data['notes'] ?? '';
                break;
            }
        }
        unset($o);

        // 4. Lưu lại session (vẫn giữ nguyên toàn bộ key 'items')
        $this->saveOrders($orders);

        return redirect()
            ->route('admin.orders.show', $id)
            ->with('success', 'Cập nhật ghi chú thành công');
    }

    /**
     * Xóa 1 Đơn hàng khỏi session
     */
    public function ordersDestroy($id)
    {
        $orders = array_filter(
            $this->getOrders(),
            fn($o) => $o['id'] !== $id
        );
        $this->saveOrders(array_values($orders));

        return redirect()
            ->route('admin.orders.index')
            ->with('success', 'Đã xóa đơn hàng thành công');
    }
    /******************************************************************************************
     * 1) Hiển thị danh sách thành viên (Index) 
     *    - Có tìm kiếm theo id/name/email
     *    - Có phân trang thủ công
     *******************************************************************************************/
    private function getMembers(): array
    {
        if (! session()->has('admin_members')) {
            session(['admin_members' => [
                // Mẫu thành viên 1
                [
                    'id'               => 1,
                    'name'             => 'Nguyễn Văn A',
                    'email'            => 'a@member.com',
                    'phone'            => '0123456789',
                    'membership_tier'  => 'Gold',        // Ví dụ: Silver/Gold/Platinum
                    'is_active'        => true,          // Active / Inactive
                    'joined_at'        => '2025-01-15',  // Ngày gia nhập
                ],
                // Mẫu thành viên 2
                [
                    'id'               => 2,
                    'name'             => 'Trần Thị B',
                    'email'            => 'b@member.com',
                    'phone'            => '0987654321',
                    'membership_tier'  => 'Silver',
                    'is_active'        => true,
                    'joined_at'        => '2025-03-10',
                ],
                // Mẫu thành viên 3
                [
                    'id'               => 3,
                    'name'             => 'Lê Văn C',
                    'email'            => 'c@member.com',
                    'phone'            => '0911223344',
                    'membership_tier'  => 'Platinum',
                    'is_active'        => false,
                    'joined_at'        => '2025-04-20',
                ],
            ]]);
        }

        return session('admin_members');
    }
    private function saveMembers(array $members): void
    {
        session(['admin_members' => $members]);
    }

    public function membersIndex(Request $request)
    {
        // 1) Lấy mảng thành viên (array) từ session hoặc khởi tạo mẫu
        $all = collect($this->getMembers());

        // 2) Tìm kiếm (search) theo ID, name hoặc email
        if ($request->filled('search')) {
            $q = mb_strtolower(trim($request->input('search')));
            $all = $all->filter(function($m) use ($q) {
                $haystack = mb_strtolower($m['id'] . ' ' . $m['name'] . ' ' . $m['email']);
                return str_contains($haystack, $q);
            })->values();
        }

        // 3) Lọc theo trạng thái Active / Inactive
        if ($request->filled('status')) {
            $status = $request->input('status') === 'active'; 
            $all = $all->filter(fn($m) => $m['is_active'] === $status)->values();
        }

        // 4) Lọc theo Cấp độ thành viên (membership_tier) nếu có
        if ($request->filled('tier')) {
            $tier = $request->input('tier');
            $all = $all->filter(fn($m) => $m['membership_tier'] === $tier)->values();
        }

        // 5) Phân trang thủ công với LengthAwarePaginator
        $perPage = 10;
        $page    = max(1, (int)$request->page);
        $slice   = $all->slice(($page - 1) * $perPage, $perPage)->values();

        $paginator = new LengthAwarePaginator(
            $slice,
            $all->count(),
            $perPage,
            $page,
            [
                'path'  => route('admin.members.index'),
                'query' => $request->query(),
            ]
        );

        return view('admin.members.index', [
            'members'   => $paginator,
            'search'    => $request->input('search',''),
            'status'    => $request->input('status',''),
            'tier'      => $request->input('tier',''),
        ]);
    }

    /******************************************************************************************
     * 2) Hiển thị form tạo mới thành viên (Create)
     *******************************************************************************************/
    public function membersCreate()
    {
        return view('admin.members.create');
    }

    /******************************************************************************************
     * 3) Xử lý lưu thành viên mới (Store)
     *******************************************************************************************/
    public function membersStore(Request $request)
    {
        // 1) Validate đầu vào
        $data = $request->validate([
            'name'             => 'required|string|max:100',
            'email'            => 'required|email|max:150',
            'phone'            => 'nullable|string|max:20',
            'membership_tier'  => 'required|in:Silver,Gold,Platinum',
            'is_active'        => 'required|in:0,1',
            'joined_at'        => 'required|date',
        ]);

        // 2) Lấy mảng cũ, đẩy thêm một phần tử mới
        $members = $this->getMembers();

        // Tự động tạo ID: lấy ID lớn nhất + 1
        $maxId = collect($members)->max('id');
        $newId = $maxId + 1;

        $members[] = [
            'id'               => $newId,
            'name'             => $data['name'],
            'email'            => $data['email'],
            'phone'            => $data['phone'] ?? '',
            'membership_tier'  => $data['membership_tier'],
            'is_active'        => (bool)$data['is_active'],
            'joined_at'        => Carbon::parse($data['joined_at'])->format('Y-m-d'),
        ];

        // 3) Lưu lại session
        $this->saveMembers($members);

        return redirect()
            ->route('admin.members.index')
            ->with('success', 'Đã thêm thành viên mới thành công');
    }

    /******************************************************************************************
     * 4) Hiển thị chi tiết một thành viên (Show)
     *******************************************************************************************/
    public function membersShow($id)
    {
        $members = $this->getMembers();
        $member  = collect($members)->firstWhere('id', (int)$id);

        if (! $member) {
            abort(404);
        }

        return view('admin.members.show', ['member' => $member]);
    }

    /******************************************************************************************
     * 5) Hiển thị form sửa thành viên (Edit)
     *******************************************************************************************/
    public function membersEdit($id)
    {
        $members = $this->getMembers();
        $member  = collect($members)->firstWhere('id', (int)$id);

        if (! $member) {
            abort(404);
        }

        return view('admin.members.edit', ['member' => $member]);
    }

    /******************************************************************************************
     * 6) Xử lý cập nhật thành viên (Update)
     *******************************************************************************************/
    public function membersUpdate(Request $request, $id)
    {
        // 1) Validate đầu vào
        $data = $request->validate([
            'name'             => 'required|string|max:100',
            'email'            => 'required|email|max:150',
            'phone'            => 'nullable|string|max:20',
            'membership_tier'  => 'required|in:Silver,Gold,Platinum',
            'is_active'        => 'required|in:0,1',
            'joined_at'        => 'required|date',
        ]);

        // 2) Lấy existing array
        $members = $this->getMembers();

        // 3) Tìm và cập nhật
        foreach ($members as &$m) {
            if ($m['id'] === (int)$id) {
                $m['name']             = $data['name'];
                $m['email']            = $data['email'];
                $m['phone']            = $data['phone'] ?? '';
                $m['membership_tier']  = $data['membership_tier'];
                $m['is_active']        = (bool)$data['is_active'];
                $m['joined_at']        = Carbon::parse($data['joined_at'])->format('Y-m-d');
                break;
            }
        }
        unset($m);

        // 4) Lưu lại session
        $this->saveMembers($members);

        return redirect()
            ->route('admin.members.index')
            ->with('success', 'Cập nhật thành viên thành công');
    }

    /******************************************************************************************
     * 7) Xóa thành viên (Destroy)
     *******************************************************************************************/
    public function membersDestroy($id)
    {
        $members = array_filter(
            $this->getMembers(),
            fn($m) => $m['id'] !== (int)$id
        );

        $this->saveMembers(array_values($members));

        return redirect()
            ->route('admin.members.index')
            ->with('success', 'Đã xóa thành viên thành công');
    }
    private function getInventory(): array
{
    if (!session()->has('admin_inventory')) {
        session(['admin_inventory' => [
            [
                'id'              => 'P001',
                'type'            => 'Áo',         // <-- thêm
                'name'            => 'Áo Thun Trắng',
                'image_url'       => null,
                'sellable'        => 24,
                'stock'           => 24,
                'created_at'      => '2025-05-01',
                'price_retail'    => 44000,
                'price_import'    => 30000,
                'price_wholesale' => 0,
            ],
            [
                'id'              => 'P002',
                'type'            => 'Quần',       // <-- thêm
                'name'            => 'Quần Jean Xanh',
                'image_url'       => null,
                'sellable'        => 10,
                'stock'           => 10,
                'created_at'      => '2025-05-02',
                'price_retail'    => 75000,
                'price_import'    => 50000,
                'price_wholesale' => 60000,
            ],
            // … 
        ]]);
    }
    return session('admin_inventory');
}

// 3) Trong inventoryIndex(), thêm đoạn lọc type:
public function inventoryIndex(Request $request)
{
    // 1) Load toàn bộ inventory demo
    $all = collect($this->getInventory());

    // 2) Filter theo loại
    if ($request->filled('type')) {
        $all = $all->filter(fn($i) => $i['type'] === $request->type)->values();
    }

    // 3) Search
    if ($request->filled('search')) {
        $q = mb_strtolower(trim($request->search));
        $all = $all->filter(fn($i) =>
            str_contains(mb_strtolower($i['id']), $q) ||
            str_contains(mb_strtolower($i['name']), $q)
        )->values();
    }

    // 4) Filter theo ngày
    if ($request->filled('from_date')) {
        $from = Carbon::parse($request->from_date)->startOfDay();
        $all = $all->filter(fn($i) =>
            Carbon::parse($i['created_at'])->gte($from)
        )->values();
    }
    if ($request->filled('to_date')) {
        $to = Carbon::parse($request->to_date)->endOfDay();
        $all = $all->filter(fn($i) =>
            Carbon::parse($i['created_at'])->lte($to)
        )->values();
    }

    // 5) Phân trang thủ công
    $perPage = 10;
    $page    = max(1, (int) $request->page);
    $slice   = $all->slice(($page - 1) * $perPage, $perPage)->values();

    // **TẠO ĐÚNG BIẾN $paginator**
    $paginator = new LengthAwarePaginator(
        $slice,               // items trang hiện tại
        $all->count(),        // tổng số phần tử
        $perPage,             // số phần tử trên 1 trang
        $page,                // trang hiện tại
        [
          'path'  => route('admin.inventory.index'),
          'query' => $request->query(),
        ]
    );

    // 6) Trả về view với biến $paginator
    return view('admin.inventory.index', [
      'items'     => $paginator,
      'search'    => $request->search,
      'type'      => $request->type,
      'from_date' => $request->from_date,
      'to_date'   => $request->to_date,
      'perPage'   => $perPage,
    ]);
}
/** Lấy mảng feedback từ session (hoặc khởi tạo mẫu) */
    private function getFeedbacks(): array
    {
        if (!session()->has('admin_feedbacks')) {
            session(['admin_feedbacks' => [
                [
                    'id'         => 'FB001',
                    'product'    => 'Quần jeans nam trắng',
                    'customer'   => 'Nguyễn Văn A',
                    'rating'     => 5,
                    'comment'    => 'Sản phẩm rất đẹp!',
                    'created_at' => '2025-05-21 10:30',
                    'is_replied' => false,
                    'reply'      => '',
                ],
                [
                    'id'         => 'FB002',
                    'product'    => 'Jumpsuit ngắn bẹt vai, tay dài',
                    'customer'   => 'Trần Thị B',
                    'rating'     => 4,
                    'comment'    => 'Chất vải đẹp nhưng giao hơi chậm.',
                    'created_at' => '2025-05-22 14:15',
                    'is_replied' => true,
                    'reply'      => 'Cảm ơn bạn đã góp ý, chúng tôi sẽ cải thiện.',
                ],
                // … thêm nếu cần
            ]]);
        }
        return session('admin_feedbacks');
    }

    /** Lưu mảng feedback vào session */
    private function saveFeedbacks(array $feedbacks): void
    {
        session(['admin_feedbacks' => $feedbacks]);
    }

    /**
     * 1) Hiển thị danh sách feedback, có tìm kiếm + lọc + phân trang
     */
    public function feedbackIndex(Request $request)
    {
        $all = collect($this->getFeedbacks());

        // tìm kiếm theo id / product / customer
        if ($request->filled('search')) {
            $q = mb_strtolower(trim($request->search));
            $all = $all->filter(fn($f) => 
                str_contains(mb_strtolower($f['id'].' '.$f['product'].' '.$f['customer']), $q)
            )->values();
        }

        // lọc theo đã trả lời hay chưa
        if ($request->filled('replied') && in_array($request->replied, ['0','1'], true)) {
            $flag = $request->replied === '1';
            $all = $all->where('is_replied', $flag)->values();
        }

        // phân trang thủ công
        $perPage = 10;
        $page    = max(1, (int)$request->page);
        $slice   = $all->slice(($page - 1) * $perPage, $perPage)->values();

        $paginator = new LengthAwarePaginator(
            $slice,
            $all->count(),
            $perPage,
            $page,
            [
                'path'  => route('admin.feedback.index'),
                'query' => $request->query(),
            ]
        );

        return view('admin.feedback.index', [
            'feedbacks' => $paginator,
            'search'    => $request->search,
            'replied'   => $request->replied,
        ]);
    }

    /**
     * 2) Xử lý reply
     */
    public function feedbackReply(Request $request, $id)
    {
        $data = $request->validate([
            'reply' => 'required|string|max:500',
        ]);

        $all = $this->getFeedbacks();
        foreach ($all as &$f) {
            if ($f['id'] === $id) {
                $f['is_replied'] = true;
                $f['reply']      = $data['reply'];
                break;
            }
        }
        unset($f);

        $this->saveFeedbacks($all);

        return redirect()
            ->route('admin.feedback.index')
            ->with('success', 'Đã trả lời feedback!');
    }

    /**
     * 3) Xóa feedback
     */
    public function feedbackDestroy($id)
    {
        $all = array_filter(
            $this->getFeedbacks(),
            fn($f) => $f['id'] !== $id
        );
        $this->saveFeedbacks(array_values($all));

        return back()->with('success', 'Đã xóa feedback!');
    }
    private function getVouchers(): array
    {
        if (! session()->has('admin_vouchers')) {
            session([
                'admin_vouchers' => [
                    [
                        'code'          => 'VCH001',
                        'product_sku'   => 'Q-0001',
                        'type'          => 'fixed',      // fixed | percent
                        'discount'      => 10000,        // số tiền hoặc %
                        'expiration'    => '2025-12-31',
                        'usage_limit'   => 1,
                        'used'          => 0,
                        'is_active'     => true,
                    ],
                    // … thêm mẫu khác …
                ]
            ]);
        }
        return session('admin_vouchers');
    }

    /**
     * Lưu mảng vouchers vào session
     */
    private function saveVouchers(array $vouchers): void
    {
        session(['admin_vouchers' => $vouchers]);
    }

    /**
     * 1. Index – danh sách Voucher
     */
    public function vouchersIndex(Request $request)
    {
        $all = collect($this->getVouchers());

        // search theo code hoặc product_sku
        if ($q = trim($request->input('search', ''))) {
            $q = mb_strtolower($q);
            $all = $all->filter(fn($v) => 
                str_contains(mb_strtolower($v['code']), $q)
             || str_contains(mb_strtolower($v['product_sku']), $q)
            )->values();
        }

        // filter trạng thái
        if ($status = $request->input('status')) {
            $all = $all->filter(fn($v) => $v['is_active'] == ($status==='1'))->values();
        }

        // phân trang thủ công
        $perPage = 10;
        $page    = max(1, (int)$request->page);
        $slice   = $all->slice(($page-1)*$perPage, $perPage)->values();

        $paginator = new LengthAwarePaginator(
            $slice,
            $all->count(),
            $perPage,
            $page,
            ['path'=>route('admin.vouchers.index'), 'query'=>$request->query()]
        );

        return view('admin.vouchers.index', [
            'vouchers' => $paginator,
            'search'   => $request->input('search',''),
            'status'   => $request->input('status',''),
        ]);
    }

    /**
     * 2. Show form tạo
     */
    public function vouchersCreate()
    {
        return view('admin.vouchers.create');
    }

    /**
     * 3. Xử lý lưu mới
     */
    public function vouchersStore(Request $request)
    {
        $data = $request->validate([
            'code'        => 'required|string',
            'product_sku' => 'required|string',
            'type'        => 'required|in:fixed,percent',
            'discount'    => 'required|numeric|min:0',
            'expiration'  => 'required|date',
            'usage_limit' => 'required|integer|min:1',
            'is_active'   => 'nullable',
        ]);

        $vouchers = $this->getVouchers();
        // gán thêm used = 0
        $vouchers[] = array_merge($data, [
            'used'      => 0,
            'is_active' => (bool) $request->has('is_active'),
        ]);

        $this->saveVouchers($vouchers);

        return redirect()->route('admin.vouchers.index')
                         ->with('success', 'Tạo voucher thành công');
    }

    /**
     * 4. Show form edit
     */
    public function vouchersEdit($code)
    {
        $voucher = collect($this->getVouchers())->firstWhere('code',$code);
        if (! $voucher) abort(404);
        return view('admin.vouchers.edit',['voucher'=>$voucher]);
    }

    /**
     * 5. Cập nhật
     */
    public function vouchersUpdate(Request $request, $code)
    {
        $data = $request->validate([
            'product_sku' => 'required|string',
            'type'        => 'required|in:fixed,percent',
            'discount'    => 'required|numeric|min:0',
            'expiration'  => 'required|date',
            'usage_limit' => 'required|integer|min:1',
            'is_active'   => 'nullable',
        ]);

        $vouchers = $this->getVouchers();
        foreach ($vouchers as &$v) {
            if ($v['code']=== $code) {
                $v['product_sku'] = $data['product_sku'];
                $v['type']        = $data['type'];
                $v['discount']    = $data['discount'];
                $v['expiration']  = $data['expiration'];
                $v['usage_limit'] = $data['usage_limit'];
                $v['is_active']   = (bool)$request->has('is_active');
                break;
            }
        }
        unset($v);

        $this->saveVouchers($vouchers);

        return redirect()->route('admin.vouchers.index')
                         ->with('success','Cập nhật voucher thành công');
    }

    /**
     * 6. Xóa
     */
    public function vouchersDestroy($code)
    {
        $vouchers = array_filter(
            $this->getVouchers(),
            fn($v)=> $v['code']!==$code
        );
        $this->saveVouchers(array_values($vouchers));
        return back()->with('success','Đã xóa voucher');
    }
}
