<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Dosen Kelompok Keahlian RIIB</title>
</head>
<body>
    <h1>Daftar Profil Dosen Kelompok Keahlian RIIB</h1>
    <table border="1" cellpadding="8">
        <thead>
            <tr>
                <th>Kode Dosen</th>
                <th>Nama Dosen</th>
                <th>Prodi</th>
                <th>Kelompok Keahlian</th>
                <th>Sub Kelompok Keahlian</th>
                <th>NIP</th>
                <th>NIDN</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($profils as $profil)
                <tr>
                    <td>{{ $profil->kode_dosen }}</td>
                    <td>{{ $profil->nama_dosen }}</td>
                    <td>{{ $profil->prodi }}</td>
                    <td>{{ $profil->kelompok_keahlian }}</td>
                    <td>{{ $profil->sub_kelompok_keahlian }}</td>
                    <td>{{ $profil->nip ?? '-' }}</td>
                    <td>{{ $profil->nidn ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">Belum ada data profil.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
