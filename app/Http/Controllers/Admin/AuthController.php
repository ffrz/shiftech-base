<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SysEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    private function _logout(Request $request)
    {
        $user = Auth::user();
        Auth::logout();
        if ($user) {
            SysEvent::log(SysEvent::AUTHENTICATION, 'Logout', 'Logout sukses. Pengguna ' . e($user->username) . ' telah logout.', null, $user->username);
        }
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }

    public function login()
    {
        return view('admin.auth.login');
    }

    public function authenticate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required',
        ], [
            'username.required' => 'ID Pengguna harus diisi.',
            'password.required' => 'Kata sandi harus diisi.',
        ]);

        if ($validator->fails())
            return redirect()->back()->withInput()->withErrors($validator);

        $error = '';
        $data = $request->only(['username', 'password']);
        if (!Auth::attempt($data)) {
            $error = 'Username atau password salah!';
            SysEvent::log(SysEvent::AUTHENTICATION, 'Login', 'Login gagal. Pengguna dengan username <b>' . e($request->post('username')) . '</b> mencoba login.');
        } else if (!Auth::user()->is_active) {
            $error = 'Akun anda tidak aktif. Silahkan hubungi administrator!';
            SysEvent::log(SysEvent::AUTHENTICATION, 'Login', 'Login gagal. Pengguna tidak aktif dengan username <b>' . e($request->post('username')) . '</b> mencoba login.');
            $this->_logout($request);
        } else {
            $request->session()->regenerate();
            SysEvent::log(SysEvent::AUTHENTICATION, 'Login', 'Login sukses. Pengguna ' . e(Auth::user()->username) . ' telah login.');
            return redirect('/admin/dashboard');
        }

        return redirect()->back()->withInput()->with('error', $error);
    }

    public function logout(Request $request)
    {
        $this->_logout($request);
        return redirect('/admin/login');
    }
}
