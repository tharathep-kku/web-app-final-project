<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\Item;
use App\Models\Category;
use App\Models\ReturnUnit;

class ItemController extends Controller
{
    // จำนวนวันหลังคืนเจ้าของ ก่อนย้ายประกาศเข้าคลัง
    private const ARCHIVE_DAYS = 7;

    // หน้าแรก: แสดงรายการทันที (ไม่ต้องค้นหาก่อน) กรองตามวันที่ได้ และซ่อนของที่คืนเจ้าของเกิน 7 วัน
    public function home(Request $request): View
    {
        $from = $request->input('from') ?? '';
        $to = $request->input('to') ?? '';
        $type = $request->input('type') ?? '';
        $sort = $request->input('sort') ?? '';
        $direction = $sort === 'old' ? 'asc' : 'desc';
        $sevenDaysAgo = date('Y-m-d', strtotime('-' . self::ARCHIVE_DAYS . ' days'));

        $query = Item::query()->where(function ($q) use ($sevenDaysAgo) {
        $q->where('status', '!=', 'ได้รับคืนแล้ว')
            ->orWhereNull('returned_date')
            ->orWhere('returned_date', '>', $sevenDaysAgo);
        });

        if ($from !== '') {
            $query->where('event_date', '>=', $from);
        }

        if ($to !== '') {
            $query->where('event_date', '<=', $to);
        }

        if ($type !== '') {
            $query->where('type', $type);
        }

        $items = $query->with(['category', 'reporter'])
            ->orderBy('event_date', $direction)
            ->paginate(5)
            ->withQueryString();

        $this->addNames($items);

        $totalItem = Item::count();
        $waitingOwner = Item::where('status', 'ยังไม่พบเจ้าของ')->count();
        $returnedItem = Item::where('status', 'ได้รับคืนแล้ว')->count();
        $waitingConfirm = Item::where('status', 'รอแอดมินยืนยัน')->count();      
        $returnUnits = \App\Models\ReturnUnit::withCount('items')->get();    
        $returnUnitsForMap = $returnUnits->map(fn ($unit) => [
            'id' => $unit->id,
            'name' => $unit->name,
            'description' => $unit->description,
            'lat' => (float) $unit->latitude,
            'lng' => (float) $unit->longitude,
            'items_count' => $unit->items_count,
            'show_url' => route('return-units.show', $unit->id),
        ])->values();

        return view('home', compact('items', 'from', 'to', 'type', 'sort', 'returnUnits', 'returnUnitsForMap', 'totalItem', 'waitingOwner', 'returnedItem', 'waitingConfirm'));
    }

    // คลังประกาศ: ของที่คืนเจ้าของไปแล้วเกิน 7 วัน
    public function archive(): View
    {
        $sevenDaysAgo = date('Y-m-d', strtotime('-' . self::ARCHIVE_DAYS . ' days'));

        $items = Item::where('status', 'ได้รับคืนแล้ว')
            ->whereNotNull('returned_date')
            ->where('returned_date', '<=', $sevenDaysAgo)
            ->with(['category', 'reporter'])
            ->orderBy('returned_date', 'desc')
            ->paginate(5);

        $this->addNames($items);

        return view('archive', compact('items'));
    }

    // ตาราง home/archive ใช้ category_name
    private function addNames($items): void
    {
        foreach ($items as $item) {
            $item->category_name = $item->category->name ?? 'อื่นๆ';
        }
    }

    public function search(Request $request): View
    {
        $searched = $request->has('searched');

        $item_name = trim($request->input('item_name') ?? '');
        $category = $request->input('category') ?? '';
        $type = $request->input('type') ?? '';
        $status = $request->input('status') ?? '';
        $location = trim($request->input('location') ?? '');
        $start_date = $request->input('start_date') ?? '';
        $end_date = $request->input('end_date') ?? '';

        $items = collect();

        if ($searched) {
            $query = Item::query();

            if ($item_name !== '') {
                $query->where('title', 'like', '%' . $item_name . '%');
            }

            if ($category !== '') {
                $query->where('category_id', $category);
            }

            if ($type !== '') {
                $query->where('type', $type);
            }

            if ($status !== '') {
                $query->where('status', $status);
            }

            if ($location !== '') {
                $query->where('location', 'like', '%' . $location . '%');
            }

            if ($start_date !== '') {
                $query->where('event_date', '>=', $start_date);
            }

            if ($end_date !== '') {
                $query->where('event_date', '<=', $end_date);
            }

            $items = $query->with(['category', 'reporter'])
                ->orderBy('event_date', 'desc')
                ->paginate(5)
                ->withQueryString();

            // บันทึกประวัติการค้นหาไว้ใน Session (เก็บล่าสุด 5 รายการ)
            if (!$request->has('page')) {
                $label = $item_name !== '' ? $item_name : 'ค้นหาทั้งหมด';
                $history = session('search_history', []);
                array_unshift($history, ['label' => $label, 'url' => $request->fullUrl()]);
                session(['search_history' => array_slice($history, 0, 5)]);
            }
        }

        $categories = Category::all();

        $allItems = Item::all();
        $locations = [];
        $statuses = [];
        foreach ($allItems as $allItem) {
            if (!in_array($allItem->location, $locations)) {
                $locations[] = $allItem->location;
            }
            if (!in_array($allItem->status, $statuses)) {
                $statuses[] = $allItem->status;
            }
        }
        sort($locations);
        sort($statuses);

        $history = session('search_history', []);

        return view('search', compact(
            'searched',
            'items',
            'categories',
            'locations',
            'statuses',
            'item_name',
            'category',
            'type',
            'status',
            'location',
            'start_date',
            'end_date',
            'history'
        ));
    }

    public function clearHistory(): RedirectResponse
    {
        session()->forget('search_history');
        return redirect()->route('search');
    }

    public function clearHistoryItem(int $index): RedirectResponse
    {
        $history = session('search_history', []);
        unset($history[$index]);
        session(['search_history' => array_values($history)]);

        return redirect()->route('search');
    }

    public function show($id): View
    {
        $item = Item::with(['category', 'reporter', 'returnUnit'])->findOrFail($id);

        return view('user.item', compact('item'));
    }

    public function create()
    {
        $categories = Category::all();
        $locations = Item::select('location')->distinct()->orderBy('location')->pluck('location');
        $returnUnits = ReturnUnit::orderBy('name')->get();

        return view('user.create', compact('categories', 'locations', 'returnUnits'));
    }

        public function store(Request $request)
    {
            
        foreach (['image' => 'รูปสิ่งของ', 'depositImage' => 'รูปตอนฝากของ'] as $field => $label) {
            $file = $request->file($field);
            if ($file !== null && ! $file->isValid()) {
                return back()->withInput()->withErrors([
                    $field => 'อัปโหลด' . $label . 'ไม่สำเร็จ: ' . $file->getErrorMessage(),
                ]);
            }
        }
        $validated = $request->validate([
            'postType'     => 'required|in:found,lost',
            'itemName'     => 'required|string|max:255',
            'category'     => 'required|exists:categories,id',
            'location'     => 'required|string|max:255',
            'date'         => 'required|date',
            'description'  => 'nullable|string',
            'image'        => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'reporterName' => 'nullable|string|max:255',
            'phone'        => ['nullable', 'string', 'max:12', 'regex:/^0[0-9]{2}-[0-9]{3}-[0-9]{4}$/'],
            'deposit'      => 'nullable|in:yes,no',
            'returnUnit'   => 'required_if:deposit,yes|nullable|exists:return_units,id',
            'depositImage' => 'required_if:deposit,yes|nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'returnUnit.required_if'   => 'กรุณาเลือกจุดที่ฝากของไว้',
            'depositImage.required_if' => 'กรุณาแนบรูปตอนฝากของ',
            'phone.regex'              => 'กรุณากรอกเบอร์โทร เช่น 081-234-5678',
        ]);

        // ฝากของได้เฉพาะประกาศ "พบของ" ที่เลือกว่าฝากไว้แล้ว
        $returnUnitId = null;
        $depositImagePath = null;
        if ($validated['postType'] === 'found' && ($validated['deposit'] ?? 'no') === 'yes') {
            $returnUnitId = $validated['returnUnit'];
            $depositImagePath = 'storage/' . $request->file('depositImage')->store('deposits', 'public');
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = 'storage/' . $request->file('image')->store('items', 'public');
        }

        Item::create([
            'user_id'        => Auth::check() ? Auth::user()->id : null, // ผูกโพสต์กับคนที่ล็อกอิน เพื่อให้แก้ไขโพสต์ตัวเองได้
            'category_id'    => $validated['category'],
            'return_unit_id' => $returnUnitId,
            'deposit_image_url' => $depositImagePath,   
            'type'           => $validated['postType'],
            'title'          => $validated['itemName'],
            'description'    => $validated['description'] ?? null,
            'location'       => $validated['location'],
            'event_date'     => $validated['date'],
            'image_url'      => $imagePath,
            'status'         => 'ยังไม่พบเจ้าของ',
            'reporter_name'  => $validated['reporterName'] ?? null,
            'reporter_phone' => $validated['phone'] ?? null,
        ]);

        return redirect()->route('posts.create')->with('success', 'บันทึกข้อมูลการแจ้งสำเร็จเรียบร้อยแล้ว');
    }
}
