<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    private const VALIDATION_RULE_NAME = 'required|max:100';
    private const VALIDATION_RULE_PASSWORD = 'min:5|max:40';

    protected $base_url = '/admin/users';
    protected $view_path = '/admin/user';

    private $validation_messages = [
        'fullname.required' => 'Nama harus diisi.',
        'fullname.max' => 'Nama terlalu panjang, maksimal 100 karakter.',
        'username.required' => 'ID Pengguna harus diisi.',
        'username.unique' => 'ID Pengguna harus unik.',
        'username.min' => 'ID Pengguna terlalu pendek, minial 5 karakter.',
        'username.max' => 'ID Pengguna terlalu panjang, maksimal 40 karakter.',
        'password.min' => 'Kata sandi terlalu pendek, minimal 5 karakter.',
        'password.max' => 'Kata sandi terlalu panjang, maksimal 40 karakter.',
        'password.confirmed' => 'Kata sandi yang anda konfirmasi salah.',
        'password_confirmation.required' => 'Anda belum mengkonfirmasi kata sandi.',
    ];

    public function index()
    {
        $items = User::orderBy('fullname', 'asc')->get();
        return $this->view('index', compact('items'));
    }

    public function edit(Request $request, $id = 0)
    {
        $user = (int)$id == 0 ? new User() : User::find($id);

        if (!$user)
            return $this->redirect()->with('warning', 'Pengguna tidak ditemukan.');
        else if ($user->username == 'admin')
            return $this->redirect()->with('warning', 'Akun <b>' . $user->username . '</b> tidak boleh diubah.');

        if ($request->method() == 'POST') {
            $rules = ['fullname' => self::VALIDATION_RULE_NAME];

            if (!$id)
                $rules['username'] = 'required|unique:users,username,' . $id . '|min:3|max:40';
            else if (!empty($request->password))
                $rules['password'] = self::VALIDATION_RULE_PASSWORD;

            $data = $request->all();

            $validator = Validator::make($data, $rules, $this->validation_messages);

            if ($validator->fails())
                return redirect()->back()->withInput()->withErrors($validator);

            if (empty($data['is_active']))
                $data['is_active'] = false;

            if (empty($data['is_admin']))
                $data['is_admin'] = false;

            if (empty($data['group_id']))
                $data['group_id'] = null;

            $user->fill($data);

            if (!$id)
                $message = 'Akun pengguna ' . $data['username'] . ' telah dibuat.';
            else
                $message = 'Akun pengguna ' . $data['username'] . ' telah diperbarui.';

            $user->save();

            return $this->redirect()->with('info', $message);
        }

        $groups = UserGroup::orderBy('name', 'asc')->get();

        return $this->view('edit', compact('user', 'groups'));
    }

    public function profile(Request $request)
    {
        if (!$user = User::find(Auth::user()->id))
            return redirect('/admin/login');

        if ($request->method() == 'POST') {
            $changedFields = ['fullname'];
            $rules = [
                'fullname' => self::VALIDATION_RULE_NAME,
            ];

            if (!empty($request->password)) {
                $changedFields[] = 'password';

                $rules['password'] = self::VALIDATION_RULE_PASSWORD . '|confirmed';
                $rules['password_confirmation'] = 'required';
            }

            $validator = Validator::make($request->all(), $rules, $this->validation_messages);

            if ($validator->fails())
                return redirect()->back()->withInput()->withErrors($validator);

            $user->update($request->only($changedFields));

            return $this->redirect('profile')->with('info', 'Profil anda telah diperbarui.');
        }

        return $this->view('profile', compact('user'));
    }

    public function delete(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if ($user->username == 'admin')
            return $this->redirect()->with('error', 'Akun ' . e($user->username) . ' tidak boleh dihapus.');
        else if ($user->id == Auth::user()->id)
            return $this->redirect()->with('error', 'Anda tidak dapat menghapus akun sendiri.');

        if ($request->method() == 'POST') {
            $user->delete();
            return $this->redirect()->with('info', 'Akun ' . e($user->username) . ' telah dihapus.');
        }

        return $this->view('delete', compact('user'));
    }
}
