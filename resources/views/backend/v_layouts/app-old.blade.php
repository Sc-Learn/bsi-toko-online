<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toko Online</title>
</head>

<body>
    <a href="{{ route('backend.beranda') }}">Beranda</a> |
    <a href="#">User</a> |
    <a href="#">Keluar</a>
    <p></p>
    <!-- @yieldAwal -->
    @yield('content')
    <!-- @yieldAkhir-->

    <!-- Keluar APP -->
    <form id="keluar-app" action="{{ route('backend.logout') }}" method="POST" class="d-none">
    @csrf
    </form>
    <!-- KeluarApp end -->
</body>

</html>