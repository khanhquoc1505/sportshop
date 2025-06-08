<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RevenueExport;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;


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
            'brand' => 'nullable|string',
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
    public function members(Request $request)
    {
        // Ví dụ: giả lập mảng thành viên (thay bằng DB nếu có)
        $all = collect([
            [
                'id' => 1,
                'name' => 'Nguyễn Văn A',
                'email' => 'a@test.com',
                'phone' => '0123456789',
                'role' => 'admin',      // 'admin' / 'member' / 'guest'
                'is_active' => true,
            ],
            [
                'id' => 2,
                'name' => 'Trần Thị B',
                'email' => 'b@test.com',
                'phone' => '0987654321',
                'role' => 'member',
                'is_active' => true,
            ],
            [
                'id' => 3,
                'name' => 'Lê Văn C',
                'email' => 'c@test.com',
                'phone' => '0112233445',
                'role' => 'guest',
                'is_active' => false,
            ],
        ]);

        // 1. Tìm kiếm theo keyword (ID, name, email)
        if ($request->filled('search')) {
            $q = mb_strtolower(trim($request->search));
            $all = $all->filter(function ($u) use ($q) {
                return str_contains(mb_strtolower($u['id'] . ' ' . $u['name'] . ' ' . $u['email']), $q);
            })->values();
        }

        // 2. Lọc status
        if ($request->filled('status')) {
            $status = $request->status === 'active';
            $all = $all->filter(fn($u) => $u['is_active'] === $status)->values();
        }

        // 3. Lọc role
        if ($request->filled('role')) {
            $role = $request->role;
            $all = $all->filter(fn($u) => $u['role'] === $role)->values();
        }

        // 4. Phân trang thủ công (LengthAwarePaginator)
        $perPage = 10;
        $page = max(1, (int) $request->page);
        $slice = $all->slice(($page - 1) * $perPage, $perPage)->values();

        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $slice,
            $all->count(),
            $perPage,
            $page,
            [
                'path' => route('admin.members.index'),
                'query' => $request->query(),
            ]
        );

        return view('admin.members.index', [
            'members' => $paginator
        ]);
    }

    // Xóa thành viên
    public function membersDestroy($id)
    {
        // Ví dụ làm thủ công: lọc array ra, cập nhật lại (nếu bạn lưu session)
        // Ở đây giả sử chúng ta vẫn lưu vào session giống orders
        $members = collect($this->getMembersFromSession())
            ->filter(fn($u) => $u['id'] != $id)
            ->values()
            ->toArray();

        $this->saveMembersToSession($members);

        return redirect()->route('admin.members.index')
            ->with('success', 'Đã xóa thành viên thành công');
    }

    // … Phương thức phụ (nếu bạn dùng session để lưu tạm) …
    private function getMembersFromSession(): array
    {
        if (!session()->has('admin_members')) {
            session([
                'admin_members' => [
                    [
                        'id' => 1,
                        'name' => 'Nguyễn Văn A',
                        'email' => 'a@test.com',
                        'phone' => '0123456789',
                        'role' => 'admin',
                        'is_active' => true,
                    ],
                    [
                        'id' => 2,
                        'name' => 'Trần Thị B',
                        'email' => 'b@test.com',
                        'phone' => '0987654321',
                        'role' => 'member',
                        'is_active' => true,
                    ],
                    [
                        'id' => 3,
                        'name' => 'Lê Văn C',
                        'email' => 'c@test.com',
                        'phone' => '0112233445',
                        'role' => 'guest',
                        'is_active' => false,
                    ],
                ]
            ]);
        }
        return session('admin_members');
    }

    private function saveMembersToSession(array $members): void
    {
        session(['admin_members' => $members]);
    }
}
