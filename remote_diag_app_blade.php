@php($isAdmin = auth()->user()?->isAdmin())
@php($drawioBase = rtrim(str_replace('/index.php', '', request()->getBaseUrl()), '/'))
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="{{ asset('images/TelU Sby Centered.png') }}">
    <title>Diagram Generator by Form</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <script>
      window.mxBasePath = 'https://cdn.jsdelivr.net/npm/mxgraph@4.2.2/javascript/src';
      window.mxImageBasePath = `${window.mxBasePath}/images`;
      window.mxLoadResources = false;
      window.mxLoadStylesheets = false;
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/mxgraph@4.2.2/javascript/mxClient.min.js"></script>
  </head>
  <body
    class="{{ $isAdmin ? 'is-admin' : '' }}"
    data-role="{{ $isAdmin ? 'admin' : 'user' }}"
    data-api-base="{{ rtrim(request()->getBaseUrl(), '/') }}/api/diagrams"
    data-template-fetch="{{ route('api.template.show') }}"
    data-template-store="{{ route('api.template.store') }}"
    data-load-diagram="{{ request('diagram') }}"
  >
    @include('partials.nav')

    <main class="workspace" id="workspace">
      <section class="panel drawio-panel">
        <header class="preview-header">
          <div>
            <h2>Editor Draw.io</h2>
          </div>
        </header>
        <iframe
          id="drawio-frame"
          title="Draw.io Editor"
          allow="fullscreen"
          referrerpolicy="strict-origin-when-cross-origin"
          src="about:blank"
          data-src="{{ $drawioBase }}/drawio/?embed=1&ui=atlas&spin=1&libraries=1&proto=json&configure=1&v=20260227&origin={{ urlencode(request()->getSchemeAndHttpHost()) }}"
        ></iframe>

        @if ($isAdmin)
          <section class="xml-preview">
            <div class="xml-preview-header">
              <h3>Template mxGraph (Admin)</h3>
              <button type="button" class="ghost small" id="apply-template">Simpan Template</button>
            </div>
            <textarea id="template-xml-input" placeholder="Tempel XML/mxGraph di sini...">{{ $templateXml }}</textarea>
          </section>
        @endif
      </section>

      <div class="workspace-grid" id="library">
        <aside class="panel library-panel">
          <header class="library-header">
            <div>
              <h2>Library Diagram</h2>
              <p>Simpan, cari, dan muat blueprint dari backend Laravel.</p>
            </div>
            <button type="button" class="ghost small" id="refresh-library">Refresh</button>
          </header>
          <label class="field search-field">
            <span>Cari</span>
            <input type="search" id="diagram-search" placeholder="Nama diagram...">
          </label>
          <div class="library-list" id="diagram-library">
            <p class="helper">Belum ada diagram tersimpan.</p>
          </div>
          <div class="library-actions">
            <button type="button" class="secondary" id="create-diagram">Diagram Baru</button>
            <button type="button" class="ghost" id="delete-diagram">Hapus Diagram</button>
          </div>
          <p class="helper">
            Diagram aktif: <span id="active-diagram-label">Belum dipilih</span>
          </p>
        </aside>

        <section class="panel form-panel">
          <form id="diagram-form">
            <div class="field-grid">
              <label class="field">
                <span>Judul Penelitian</span>
                <input type="text" id="diagram-name" placeholder="Contoh: Analisis Sistem Informasi" required>
              </label>
            <label class="field">
              <span>Deskripsi / Tujuan</span>
              <input type="text" id="diagram-description" placeholder="Opsional, tampil di metadata diagram">
            </label>
            <label class="field">
              <span>Tanggal Project</span>
              <input type="date" id="diagram-date">
            </label>
          </div>

            <section class="form-section">
              <header>
                <div>
                  <h2>Langkah / Node</h2>
                  <p>Setiap langkah memiliki Reference Key yang digunakan saat membuat koneksi.</p>
                </div>
                <button type="button" class="secondary small" id="add-step">Tambah Langkah</button>
              </header>
              <div id="steps-container" class="stack"></div>
            </section>

            <section class="form-section">
              <header>
                <div>
                  <h2>Koneksi</h2>
                  <p>Gunakan Reference Key yang sama dengan node. Misal <code>S01</code> -> <code>S02</code>.</p>
                </div>
                <button type="button" class="secondary small" id="add-connection">Tambah Koneksi</button>
              </header>
              <div id="connections-container" class="stack"></div>
            </section>

            <section class="form-section">
              <header>
                <div>
                  <h2>Integrasi Editor & Backend</h2>
                  <p>Generate diagram, kirim ke Draw.io, dan simpan blueprint ke database.</p>
                </div>
              </header>
              <div class="action-row">
                <button type="submit" class="primary">Generate &amp; Kirim ke Draw.io</button>
                @if ($isAdmin)
                  <button type="button" class="ghost" id="request-export">Tarik XML dari Editor</button>
                @endif
              </div>
              <p class="helper">
                Status: <span id="status-message">Menunggu input...</span>
              </p>
            </section>
          </form>
        </section>
      </div>
    </main>

    <template id="step-template">
      <article class="card step-card">
        <div class="card-header">
          <p class="step-title">Langkah</p>
          <button type="button" class="icon-button step-remove" aria-label="Hapus langkah">&times;</button>
        </div>
        <div class="field-grid">
          <label class="field">
            <span>Reference Key</span>
            <input type="text" class="step-key" placeholder="S01" required>
          </label>
          <label class="field">
            <span>Nama / Label</span>
            <input type="text" class="step-label" placeholder="Contoh: Validasi Data" required>
          </label>
        </div>
          <label class="field">
            <span>Catatan (opsional)</span>
            <input type="text" class="step-note" placeholder="Ditampilkan di tooltip metadata">
          </label>
          <label class="field step-color-field">
            <span>Warna</span>
            <select class="step-color"></select>
          </label>
      </article>
    </template>

    <template id="connection-template">
      <article class="card connection-card">
        <div class="card-header">
          <p>Koneksi</p>
          <button type="button" class="icon-button connection-remove" aria-label="Hapus koneksi">&times;</button>
        </div>
        <div class="field-grid">
          <label class="field">
            <span>Dari (Reference Key)</span>
            <input type="text" class="connection-from" placeholder="S01" list="step-id-options" required>
          </label>
          <label class="field">
            <span>Ke (Reference Key)</span>
            <input type="text" class="connection-to" placeholder="S02" list="step-id-options" required>
          </label>
          <label class="field">
            <span>Label / Kondisi</span>
            <input type="text" class="connection-label" placeholder="Opsional, misal: Ya">
          </label>
        </div>
      </article>
    </template>

    <datalist id="step-id-options"></datalist>

    <textarea id="xml-output" class="sr-only"></textarea>

    <div id="mxgraph-sandbox" aria-hidden="true"></div>
  </body>
</html>
