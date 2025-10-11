<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Profil Dosen</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            color-scheme: light dark;
            font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --border: rgba(148, 163, 184, 0.4);
            --bg: #f8fafc;
            --card: #ffffff;
            --text: #0f172a;
            --muted: #64748b;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: var(--bg);
            color: var(--text);
        }

        .dashboard {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 280px 1fr;
        }

        .sidebar {
            background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
            color: white;
            padding: 2rem 1.5rem;
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }

        .sidebar h1 {
            font-size: 1.4rem;
            font-weight: 700;
            margin: 0;
        }

        .nav-links {
            display: grid;
            gap: 0.5rem;
        }

        .nav-links a {
            color: rgba(241, 245, 249, 0.8);
            text-decoration: none;
            padding: 0.75rem 1rem;
            border-radius: 0.75rem;
            transition: background 0.2s ease, color 0.2s ease;
        }

        .nav-links a.active,
        .nav-links a:hover {
            background: rgba(148, 163, 184, 0.16);
            color: white;
        }

        .content {
            padding: 2.5rem 3rem;
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }

        .card {
            background: var(--card);
            border-radius: 1.5rem;
            padding: 2rem;
            box-shadow: 0 20px 45px -25px rgba(15, 23, 42, 0.35);
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
        }

        .title {
            font-size: 2rem;
            font-weight: 700;
            margin: 0;
        }

        .subtitle {
            font-size: 0.95rem;
            color: var(--muted);
            margin-top: 0.25rem;
        }

        .button {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 999px;
            padding: 0.75rem 1.25rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s ease;
        }

        .button:hover {
            background: var(--primary-dark);
        }

        .table-wrapper {
            border: 1px solid var(--border);
            border-radius: 1.25rem;
            overflow: hidden;
            margin-top: 1.5rem;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 960px;
        }

        thead {
            background: rgba(148, 163, 184, 0.1);
        }

        th, td {
            text-align: left;
            padding: 1rem 1.25rem;
            border-bottom: 1px solid rgba(148, 163, 184, 0.25);
            font-size: 0.95rem;
        }

        tbody tr:hover {
            background: rgba(148, 163, 184, 0.08);
        }

        .actions {
            display: inline-flex;
            gap: 0.5rem;
        }

        .action-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.25rem;
            border: none;
            border-radius: 999px;
            padding: 0.5rem 0.9rem;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .action-btn.edit {
            background: rgba(37, 99, 235, 0.12);
            color: #1d4ed8;
        }

        .action-btn.delete {
            background: rgba(239, 68, 68, 0.12);
            color: #b91c1c;
        }

        .action-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 18px -14px rgba(15, 23, 42, 0.5);
        }

        .alert {
            padding: 1rem 1.25rem;
            border-radius: 1rem;
            background: rgba(34, 197, 94, 0.12);
            color: #15803d;
            border: 1px solid rgba(34, 197, 94, 0.25);
        }

        .modal {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.55);
            display: none;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            z-index: 100;
        }

        .modal.active {
            display: flex;
        }

        .modal-card {
            width: min(640px, 100%);
            background: var(--card);
            border-radius: 1.25rem;
            padding: 2rem;
            box-shadow: 0 30px 60px -28px rgba(15, 23, 42, 0.6);
            position: relative;
        }

        .modal-close {
            position: absolute;
            top: 1rem;
            right: 1rem;
            border: none;
            background: none;
            font-size: 1.2rem;
            cursor: pointer;
            color: var(--muted);
        }

        .form-grid {
            display: grid;
            gap: 1rem;
        }

        .input-group {
            display: flex;
            flex-direction: column;
            gap: 0.35rem;
        }

        label {
            font-weight: 600;
            font-size: 0.9rem;
            color: var(--muted);
        }

        input {
            border: 1px solid var(--border);
            border-radius: 0.85rem;
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
            transition: border 0.2s ease, box-shadow 0.2s ease;
        }

        input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.25);
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 0.75rem;
            margin-top: 1.5rem;
        }

        .btn-secondary {
            border: 1px solid var(--border);
            background: transparent;
            border-radius: 999px;
            padding: 0.7rem 1.2rem;
            font-weight: 600;
            cursor: pointer;
        }

        .btn-danger {
            background: #ef4444;
            color: white;
            border: none;
            border-radius: 999px;
            padding: 0.75rem 1.25rem;
            font-weight: 600;
            cursor: pointer;
        }

        .error-text {
            color: #b91c1c;
            font-size: 0.85rem;
        }

        @media (max-width: 1080px) {
            .dashboard {
                grid-template-columns: 1fr;
            }

            .sidebar {
                flex-direction: row;
                align-items: center;
                justify-content: space-between;
            }

            .nav-links {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 768px) {
            .content {
                padding: 1.5rem;
            }

            .card {
                padding: 1.5rem;
            }

            .table-wrapper {
                overflow-x: auto;
            }
        }
    </style>
</head>
<body>
<div class="dashboard">
    <aside class="sidebar">
        <div>
            <h1>Admin Fakultas</h1>
            <p style="color: rgba(241,245,249,0.75); margin-top: 0.35rem;">Kelola profil dosen dengan mudah.</p>
        </div>
        <nav class="nav-links">
            <a href="{{ route('profils.index') }}" class="active">Profil Dosen</a>
            <a href="#">Manajemen Kepangkatan</a>
            <a href="#">Laporan Akademik</a>
            <a href="#">Pengaturan Sistem</a>
        </nav>
    </aside>
    <main class="content">
        <div class="card">
            <div class="header">
                <div>
                    <h2 class="title">Profil Dosen</h2>
                    <p class="subtitle">Pantau dan kelola informasi profil dosen dalam satu tempat.</p>
                </div>
                <button type="button" class="button" data-modal-target="create-modal">
                    + Tambah Profil
                </button>
            </div>

            @if (session('success'))
                <div class="alert" role="alert" style="margin-top: 1.5rem;">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert" role="alert" style="margin-top: 1.5rem; background: rgba(239,68,68,0.12); color: #b91c1c; border-color: rgba(239,68,68,0.3);">
                    <strong>Terjadi kesalahan:</strong>
                    <ul style="margin: 0.75rem 0 0 1.25rem; padding: 0;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="table-wrapper">
                <table>
                    <thead>
                    <tr>
                        <th>Kode Dosen</th>
                        <th>Nama Dosen</th>
                        <th>Program Studi</th>
                        <th>Kelompok Keahlian</th>
                        <th>Sub Kelompok</th>
                        <th>NIP</th>
                        <th>NIDN</th>
                        <th>Aksi</th>
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
                            <td>{{ $profil->nip ?? '—' }}</td>
                            <td>{{ $profil->nidn ?? '—' }}</td>
                            <td>
                                <div class="actions">
                                    <button
                                        type="button"
                                        class="action-btn edit"
                                        data-action="edit"
                                        data-update-template="{{ route('profils.update', ['profil' => '__ID__']) }}"
                                        data-profile='@json($profil)'
                                        data-modal-target="edit-modal"
                                    >
                                        Edit
                                    </button>
                                    <button
                                        type="button"
                                        class="action-btn delete"
                                        data-action="delete"
                                        data-destroy-template="{{ route('profils.destroy', ['profil' => '__ID__']) }}"
                                        data-profile='@json(['id' => $profil->id, 'nama_dosen' => $profil->nama_dosen])'
                                        data-modal-target="delete-modal"
                                    >
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 2rem; color: var(--muted);">
                                Belum ada data profil dosen.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<div class="modal" id="create-modal">
    <div class="modal-card">
        <button type="button" class="modal-close" data-modal-close>&times;</button>
        <h3 style="margin: 0 0 1.5rem; font-size: 1.5rem;">Tambah Profil Dosen</h3>
        <form action="{{ route('profils.store') }}" method="POST" class="form-grid">
            @csrf
            <div class="input-group">
                <label for="create-kode_dosen">Kode Dosen</label>
                <input type="text" id="create-kode_dosen" name="kode_dosen" value="{{ old('kode_dosen') }}" required>
            </div>
            <div class="input-group">
                <label for="create-nama_dosen">Nama Dosen</label>
                <input type="text" id="create-nama_dosen" name="nama_dosen" value="{{ old('nama_dosen') }}" required>
            </div>
            <div class="input-group">
                <label for="create-prodi">Program Studi</label>
                <input type="text" id="create-prodi" name="prodi" value="{{ old('prodi') }}" required>
            </div>
            <div class="input-group">
                <label for="create-kelompok_keahlian">Kelompok Keahlian</label>
                <input type="text" id="create-kelompok_keahlian" name="kelompok_keahlian" value="{{ old('kelompok_keahlian') }}" required>
            </div>
            <div class="input-group">
                <label for="create-sub_kelompok_keahlian">Sub Kelompok Keahlian</label>
                <input type="text" id="create-sub_kelompok_keahlian" name="sub_kelompok_keahlian" value="{{ old('sub_kelompok_keahlian') }}" required>
            </div>
            <div class="input-group">
                <label for="create-nip">NIP</label>
                <input type="text" id="create-nip" name="nip" value="{{ old('nip') }}">
            </div>
            <div class="input-group">
                <label for="create-nidn">NIDN</label>
                <input type="text" id="create-nidn" name="nidn" value="{{ old('nidn') }}">
            </div>
            <div class="form-actions">
                <button type="button" class="btn-secondary" data-modal-close>Batal</button>
                <button type="submit" class="button">Simpan</button>
            </div>
        </form>
    </div>
</div>

<div class="modal" id="edit-modal">
    <div class="modal-card">
        <button type="button" class="modal-close" data-modal-close>&times;</button>
        <h3 style="margin: 0 0 1.5rem; font-size: 1.5rem;">Edit Profil Dosen</h3>
        <form id="edit-form" method="POST" class="form-grid">
            @csrf
            @method('PUT')
            <input type="hidden" name="profil_id" id="edit-profil_id">
            <div class="input-group">
                <label for="edit-kode_dosen">Kode Dosen</label>
                <input type="text" id="edit-kode_dosen" name="kode_dosen" required>
            </div>
            <div class="input-group">
                <label for="edit-nama_dosen">Nama Dosen</label>
                <input type="text" id="edit-nama_dosen" name="nama_dosen" required>
            </div>
            <div class="input-group">
                <label for="edit-prodi">Program Studi</label>
                <input type="text" id="edit-prodi" name="prodi" required>
            </div>
            <div class="input-group">
                <label for="edit-kelompok_keahlian">Kelompok Keahlian</label>
                <input type="text" id="edit-kelompok_keahlian" name="kelompok_keahlian" required>
            </div>
            <div class="input-group">
                <label for="edit-sub_kelompok_keahlian">Sub Kelompok Keahlian</label>
                <input type="text" id="edit-sub_kelompok_keahlian" name="sub_kelompok_keahlian" required>
            </div>
            <div class="input-group">
                <label for="edit-nip">NIP</label>
                <input type="text" id="edit-nip" name="nip">
            </div>
            <div class="input-group">
                <label for="edit-nidn">NIDN</label>
                <input type="text" id="edit-nidn" name="nidn">
            </div>
            <div class="form-actions">
                <button type="button" class="btn-secondary" data-modal-close>Batal</button>
                <button type="submit" class="button">Perbarui</button>
            </div>
        </form>
    </div>
</div>

<div class="modal" id="delete-modal">
    <div class="modal-card" style="max-width: 480px;">
        <button type="button" class="modal-close" data-modal-close>&times;</button>
        <h3 style="margin: 0 0 1rem; font-size: 1.4rem;">Hapus Profil</h3>
        <p id="delete-message" style="color: var(--muted); margin-bottom: 1.5rem;">
            Apakah Anda yakin ingin menghapus profil ini?
        </p>
        <form id="delete-form" method="POST" class="form-actions" style="justify-content: flex-end;">
            @csrf
            @method('DELETE')
            <button type="button" class="btn-secondary" data-modal-close>Batal</button>
            <button type="submit" class="btn-danger">Hapus</button>
        </form>
    </div>
</div>

<script>
    const body = document.body;
    const modals = document.querySelectorAll('.modal');

    function openModal(modal) {
        modal.classList.add('active');
        body.style.overflow = 'hidden';
    }

    function closeModal(modal) {
        modal.classList.remove('active');
        if (![...modals].some(m => m.classList.contains('active'))) {
            body.style.overflow = '';
        }
    }

    document.querySelectorAll('[data-modal-target]').forEach(trigger => {
        trigger.addEventListener('click', () => {
            const target = document.getElementById(trigger.dataset.modalTarget);
            if (!target) return;

            if (trigger.dataset.action === 'edit') {
                const profileData = JSON.parse(trigger.dataset.profile);
                const template = trigger.dataset.updateTemplate;
                const form = document.getElementById('edit-form');
                form.action = template.replace('__ID__', profileData.id);
                form.querySelector('#edit-kode_dosen').value = profileData.kode_dosen ?? '';
                form.querySelector('#edit-nama_dosen').value = profileData.nama_dosen ?? '';
                form.querySelector('#edit-prodi').value = profileData.prodi ?? '';
                form.querySelector('#edit-kelompok_keahlian').value = profileData.kelompok_keahlian ?? '';
                form.querySelector('#edit-sub_kelompok_keahlian').value = profileData.sub_kelompok_keahlian ?? '';
                form.querySelector('#edit-nip').value = profileData.nip ?? '';
                form.querySelector('#edit-nidn').value = profileData.nidn ?? '';
                const idField = form.querySelector('#edit-profil_id');
                if (idField) {
                    idField.value = profileData.id;
                }
            }

            if (trigger.dataset.action === 'delete') {
                const profileData = JSON.parse(trigger.dataset.profile);
                const template = trigger.dataset.destroyTemplate;
                const form = document.getElementById('delete-form');
                form.action = template.replace('__ID__', profileData.id);
                const message = document.getElementById('delete-message');
                message.textContent = `Apakah Anda yakin ingin menghapus ${profileData.nama_dosen}?`;
            }

            openModal(target);
        });
    });

    document.querySelectorAll('[data-modal-close]').forEach(button => {
        button.addEventListener('click', () => {
            const modal = button.closest('.modal');
            if (modal) {
                closeModal(modal);
            }
        });
    });

    modals.forEach(modal => {
        modal.addEventListener('click', event => {
            if (event.target === modal) {
                closeModal(modal);
            }
        });
    });

    const initialModal = @json($openModal ?? null);
    const initialEditingProfil = @json($editingProfil ?? null);
    const hasErrors = @json($errors->any());
    const wasUpdate = @json(old('_method') === 'PUT');
    const oldProfilId = @json(old('profil_id'));

    if (initialModal === 'create') {
        const modal = document.getElementById('create-modal');
        if (modal) {
            openModal(modal);
        }
    }

    if (initialModal === 'edit' && initialEditingProfil) {
        const modal = document.getElementById('edit-modal');
        const editTrigger = document.querySelector('[data-action="edit"]');
        if (modal && editTrigger) {
            const template = editTrigger.dataset.updateTemplate;
            const form = document.getElementById('edit-form');
            form.action = template.replace('__ID__', initialEditingProfil.id);
            form.querySelector('#edit-kode_dosen').value = initialEditingProfil.kode_dosen ?? '';
            form.querySelector('#edit-nama_dosen').value = initialEditingProfil.nama_dosen ?? '';
            form.querySelector('#edit-prodi').value = initialEditingProfil.prodi ?? '';
            form.querySelector('#edit-kelompok_keahlian').value = initialEditingProfil.kelompok_keahlian ?? '';
            form.querySelector('#edit-sub_kelompok_keahlian').value = initialEditingProfil.sub_kelompok_keahlian ?? '';
            form.querySelector('#edit-nip').value = initialEditingProfil.nip ?? '';
            form.querySelector('#edit-nidn').value = initialEditingProfil.nidn ?? '';
            const idField = form.querySelector('#edit-profil_id');
            if (idField) {
                idField.value = initialEditingProfil.id;
            }
            openModal(modal);
        }
    }

    if (hasErrors && !wasUpdate) {
        const modal = document.getElementById('create-modal');
        if (modal) {
            openModal(modal);
        }
    }

    if (hasErrors && wasUpdate) {
        const modal = document.getElementById('edit-modal');
        const editTrigger = document.querySelector('[data-action="edit"]');
        if (modal && editTrigger) {
            const template = editTrigger.dataset.updateTemplate;
            const form = document.getElementById('edit-form');
            const targetId = oldProfilId ?? initialEditingProfil?.id;
            if (targetId) {
                form.action = template.replace('__ID__', targetId);
            }
            form.querySelector('#edit-kode_dosen').value = @json(old('kode_dosen') ?? '');
            form.querySelector('#edit-nama_dosen').value = @json(old('nama_dosen') ?? '');
            form.querySelector('#edit-prodi').value = @json(old('prodi') ?? '');
            form.querySelector('#edit-kelompok_keahlian').value = @json(old('kelompok_keahlian') ?? '');
            form.querySelector('#edit-sub_kelompok_keahlian').value = @json(old('sub_kelompok_keahlian') ?? '');
            form.querySelector('#edit-nip').value = @json(old('nip') ?? '');
            form.querySelector('#edit-nidn').value = @json(old('nidn') ?? '');
            const idField = form.querySelector('#edit-profil_id');
            if (idField && targetId) {
                idField.value = targetId;
            }
            openModal(modal);
        }
    }
</script>
</body>
</html>
