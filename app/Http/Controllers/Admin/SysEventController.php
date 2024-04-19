<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SysEvent;
use App\Models\User;
use App\Models\UserGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SysEventController extends Controller
{
    public function index()
    {
        $items = SysEvent::all();
        return view('admin.sys-events.index', compact('items'));
    }

    public function show(Request $request, $id = 0)
    {
        $item = SysEvent::findOrFail($id);
        return view('admin.sys-events.show', compact('item'));
    }

    public function delete(Request $request)
    {
        $item = SysEvent::findOrFail($request->post('id', 0));
        $item->delete();
        return redirect('admin/sys-events')->with('info', 'Rekaman log aktivitas <b>#' . $item->id . '</b> telah dihapus.');
    }
}
