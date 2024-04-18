@extends('admin._layouts.default', [
    'title' => 'Hapus Pengguna',
    'menu_active' => 'system',
    'nav_active' => 'users',
])

@section('content')
  <div class="card card-light">
    <form class="form-horizontal quick-form" method="POST" action="{{ url('/admin/users/delete/' . $user->id) }}">
      @csrf
      @include('admin._components.card-header', ['title' => 'Hapus Pengguna'])
      <div class="card-body">
        <h5>Konfirmasi Penghapusan Akun Pengguna</h5>
        <p>Anda benar-benar akan menghapus akun pengguna <b>{{ $user->username }}</b>?</p>
        <table>
          <tr>
            <td>Username</td>
            <td>:</td>
            <td>{{ $user->username }}</td>
          </tr>
          <tr>
            <td>Nama Lengkap</td>
            <td>:</td>
            <td>{{ empty($user->fullname) ? '-' : $user->fullname }}</td>
          </tr>
          {{-- <tr>
            <td>Grup</td>
            <td>:</td>
            <td>{{ $user->group->name }}</td>
          </tr> --}}
          <tr>
            <td>Status Akun</td>
            <td>:</td>
            <td>{{ $user->is_active ? 'Aktif' : 'Tidak Aktif' }}</td>
          </tr>
          <tr>
            <td>Jenis Akun</td>
            <td>:</td>
            <td>{{ $user->is_admin ? 'Administrator' : 'Pengguna Biasa' }}</td>
          </tr>
        </table>
      </div>
      <div class="card-footer">
        <div>
          <a href="{{ url('/admin/users') }}" class="btn btn-default mr-2"><i class="fas fa-arrow-left mr-1"></i>
            Kembali</a>
          <button type="submit" class="btn btn-danger"><i class="fas fa-trash-can mr-1"></i> HAPUS</button>
        </div>
      </div>
    </form>
  </div>
  </div>
@endsection
