<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserGroupController extends Controller
{
    public function index()
    {
        $items = UserGroup::orderBy('name', 'asc')->get();
        return view('admin.user-groups.index', compact('items'));
    }

    public function edit(Request $request, $id = 0)
    {
        $group = $id ? UserGroup::find($id) : new UserGroup();
        if (!$group)
            return redirect('admin/user-groups')->with('warning', 'Grup Pengguna tidak ditemukan.');

        if ($request->method() == 'POST') {
            $validator = Validator::make($request->all(), [
                'name' => 'required|unique:user_groups,name,' . $request->id . '|max:100',
            ], [
                'name.required' => 'Nama grup harus diisi.',
                'name.unique' => 'Nama grup sudah digunakan.',
                'name.max' => 'Nama grup terlalu panjang, maksimal 100 karakter.',
            ]);

            if ($validator->fails())
                return redirect()->back()->withInput()->withErrors($validator);

            $group->fill($request->all());
            $group->save();

            return redirect('admin/user-groups')->with('info', 'Grup pengguna telah disimpan.');
        }

        return view('admin.user-groups.edit', compact('group'));
    }

    public function delete($id)
    {
        if (!$userGroup = UserGroup::find($id))
            $message = 'Grup pengguna tidak ditemukan.';
        else if ($userGroup->delete($id))
            $message = 'Grup pengguna ' . $userGroup->name . ' telah dihapus.';

        return redirect('admin/user-groups')->with('info', $message);
    }
}
