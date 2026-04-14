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
      }
      th {
        background: #f1f5f9;
        text-transform: uppercase;
        font-size: 10px;
        letter-spacing: 0.08em;
      }
      td {
        vertical-align: middle;
      }
      .text-center {
        text-align: center;
      }
      .muted {
        color: #64748b;
        font-size: 10px;
      }
      .indicator {
        display: inline-block;
        width: 10px;
        height: 10px;
        border-radius: 999px;
        margin-right: 6px;
        vertical-align: middle;
      }
      .indicator-green {
        background: #10b981;
      }
      .indicator-yellow {
        background: #f59e0b;
      }
      .indicator-red {
        background: #ef4444;
      }
      .indicator-default {
        background: #94a3b8;
      }
      .indicator-cell {
        text-align: center;
        vertical-align: middle;
      }
    </style>
  </head>
  <body>
    <h1>Rekap Kepangkatan Dosen SDM</h1>
    <div class="meta">
      Status TMT: {{ $statusLabel }} | Dicetak: {{ now()->format('d/m/Y H:i') }}
    </div>

    <table>
      <thead>
        <tr>
          <th>Nama Dosen</th>
          <th class="text-center">Kode Dosen</th>
          <th>Jabatan</th>
          <th class="text-center">Tanggal SK</th>
          <th>Status TMT</th>
          <th class="text-center">Status Publikasi</th>
          <th class="text-center">Indikator</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($records as $record)
          <tr>
            <td>{{ $record->profil?->nama_dosen ?? '-' }}</td>
            <td class="text-center">{{ $record->profil?->kode_dosen ?? '-' }}</td>
            <td>{{ $record->jabatan_fungsional_label }}</td>
            <td class="text-center">{{ $record->tanggal_sk ? $record->tanggal_sk->format('d/m/Y') : '-' }}</td>
            <td>{{ $record->status_tmt_label }}</td>
            <td class="text-center">{{ $record->status_publikasi_label }}</td>
            <td class="indicator-cell">
              <span class="indicator indicator-{{ $record->indicator_color ?? 'default' }}"></span>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="7" class="muted">Tidak ada data kepangkatan untuk filter ini.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </body>
</html>
