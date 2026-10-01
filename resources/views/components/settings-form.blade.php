{{--
    Pembungkus form untuk halaman Pengaturan Tampilan.

    Ini dibuat sebagai komponen karena HTML tidak mengizinkan <form> bersarang:
    kalau ada form lain di dalamnya, browser membuang tag penutup form induk dan
    semua field (termasuk input file seperti favicon) jadi terlempar keluar dari
    form sehingga tidak ikut terkirim.

    Dipakai: <x-settings-form :action="..."> ... </x-settings-form>
--}}
@props(['action'])

<form id="settings-form" action="{{ $action }}" method="POST" enctype="multipart/form-data" class="space-y-6">
    {{ $slot }}
</form>
