<!doctype html>
<html lang="id">
  <head>
    <meta charset="utf-8">
    <title>Rekap Kepangkatan Dosen</title>
    <style>
      body {
        font-family: DejaVu Sans, Arial, sans-serif;
        font-size: 12px;
        color: #0f172a;
      }
      h1 {
        font-size: 18px;
        margin-bottom: 4px;
      }
      .meta {
        font-size: 11px;
        color: #475569;
        margin-bottom: 16px;
      }
      table {
        width: 100%;
        border-collapse: collapse;
      }
      th,
      td {
        border: 1px solid #e2e8f0;
        padding: 6px 8px;
        vertical-align: top;
      }
      th {
        background: #f1f5f9;
        text-transform: uppercase;
        font-size: 10px;
        letter-spacing: 0.08em;
      }
      .muted {
        color: #64748b;
        font-size: 10px;
      }
    </style>
  </head>
  <body>
    <h1>Rekap Kepangkatan Dosen RIIB</h1>
    <div class="meta">
      Status TMT: {{ $statusLabel }} | Dicetak: {{ now()->format('d/m/Y H:i') }}
    </div>

    <table>
      <thead>
        <tr>
          <th>Nama Dosen</th>
          <th>Kode Dosen</th>
          <th>Jabatan</th>
          <th>Tanggal SK</th>
          <th>Status TMT</th>
          <th>Status Publikasi</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($records as $record)
          <tr>
            <td>{{ $record->profil?->nama_dosen ?? '-' }}</td>
            <td>{{ $record->profil?->kode_dosen ?? '-' }}</td>
            <td>{{ $record->jabatan_fungsional_label }}</td>
            <td>{{ $record->tanggal_sk ? $record->tanggal_sk->format('d/m/Y') : '-' }}</td>
            <td>{{ $record->status_tmt_label }}</td>
            <td>{{ $record->status_publikasi_label }}</td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="muted">Tidak ada data kepangkatan untuk filter ini.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </body>
</html>
