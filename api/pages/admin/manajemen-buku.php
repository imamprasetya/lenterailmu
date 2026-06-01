<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lentera Ilmu Admin - Manajemen Buku</title>
    <link rel="stylesheet" href="../../assets/css/global.css?v=1.2">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="manajemen-buku.css">
</head>
<body>
    <div id="admin-header-container"></div>
    <div class="dashboard-wrapper">
        <div id="sidebar-container"></div>
        <main class="dashboard-content">
            <!-- HEADER -->
            <div class="content-header">
                <div>
                    <div class="title-with-badge">
                        <h1>Manajemen Buku</h1>
                        <span class="count-badge" id="countBadge">-</span>
                    </div>
                </div>
                <div class="header-search-area">
                    <input type="text" placeholder="Cari judul, ISBN, atau penulis..." class="table-search" id="searchBuku">
                    <button class="btn-primary-add" id="btnTambahBuku"><i class="fa-solid fa-plus" style="margin-right: 6px;"></i> Tambah Buku</button>
                </div>
            </div>

            <!-- TABEL -->
            <div class="table-card">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>DETAIL BUKU</th>
                            <th>KATEGORI</th>
                            <th>STOK</th>
                            <th>STATUS</th>
                            <th>AKSI</th>
                        </tr>
                    </thead>
                    <tbody id="bukuBody">
                        <tr><td colspan="5" style="text-align:center;padding:40px;color:var(--text-light);">Memuat data...</td></tr>
                    </tbody>
                </table>
                <div class="table-footer">
                    <span id="showingBuku">-</span>
                </div>
            </div>

            <!-- INSIGHT CARDS -->
            <div class="insight-grid">
                <div class="insight-card blue-gradient">
                    <small>PALING BANYAK DIPINJAM</small>
                    <h3 id="mostBorrowed">-</h3>
                </div>
                <div class="insight-card gray-card">
                    <small>TOTAL STOK</small>
                    <h2 id="totalStok">-</h2>
                </div>
                <div class="insight-card outline-card" id="quickAddCard" style="cursor:pointer;">
                    <span class="plus-icon"><i class="fa-solid fa-plus"></i></span>
                    <strong>Tambah Cepat</strong>
                    <p>Tambah buku baru</p>
                </div>
            </div>
        </main>
    </div>

    <!-- MODAL TAMBAH/EDIT BUKU -->
    <div class="modal-overlay" id="modalBuku" style="display:none;">
        <div class="modal-box">
            <div class="modal-header">
                <h2 id="modalTitle">Tambah Buku Baru</h2>
                <button class="modal-close" id="btnCloseModal">&times;</button>
            </div>
            <form id="formBuku">
                <div class="form-row">
                    <div class="input-group">
                        <label>Judul Buku</label>
                        <input type="text" id="inputJudul" required placeholder="Masukkan judul buku">
                    </div>
                    <div class="input-group">
                        <label>Penulis</label>
                        <input type="text" id="inputPenulis" required placeholder="Nama penulis">
                    </div>
                </div>
                <div class="form-row">
                    <div class="input-group">
                        <label>Kategori</label>
                        <select id="inputKategori" required>
                            <option value="">Pilih kategori</option>
                            <option value="Fiksi">Fiksi</option>
                            <option value="Sains">Sains</option>
                            <option value="Sejarah">Sejarah</option>
                            <option value="Filsafat">Filsafat</option>
                            <option value="Teknologi">Teknologi</option>
                            <option value="Kesehatan">Kesehatan</option>
                            <option value="Psikologi">Psikologi</option>
                            <option value="Ekonomi">Ekonomi</option>
                            <option value="Politik">Politik</option>
                            <option value="Agama">Agama</option>
                            <option value="Pemrograman">Pemrograman</option>
                            <option value="Pendidikan">Pendidikan</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>
                    <div class="input-group">
                        <label>Stok</label>
                        <input type="number" id="inputStok" required min="0" placeholder="0">
                    </div>
                </div>
                <div class="form-row">
                    <div class="input-group">
                        <label>Tahun Terbit</label>
                        <input type="number" id="inputTahun" placeholder="2024">
                    </div>
                    <div class="input-group">
                        <label>Jumlah Halaman</label>
                        <input type="number" id="inputHalaman" placeholder="300">
                    </div>
                </div>
                <div class="form-row">
                    <div class="input-group">
                        <label>Bahasa</label>
                        <input type="text" id="inputBahasa" placeholder="Indonesia" value="Indonesia">
                    </div>
                    <div class="input-group">
                        <label>Warna Cover (hex)</label>
                        <input type="color" id="inputWarna" value="#1e3a5f">
                    </div>
                </div>
                <div class="form-row">
                    <div class="input-group full">
                        <label>Cover Gambar Buku</label>
                        <input type="file" id="inputGambar" accept="image/*" style="padding: 6px;">
                        <small style="color:var(--text-light);margin-top:4px;display:block;">Pilih gambar cover (akan dikonversi ke Base64 otomatis)</small>
                        <div id="previewGambarContainer" style="margin-top:10px; display:none;">
                            <img id="previewGambar" src="" alt="Preview" style="max-height:100px; border-radius: var(--radius-sm); border: 1px solid var(--border);">
                        </div>
                    </div>
                </div>
                <div class="input-group full">
                    <label>Deskripsi</label>
                    <textarea id="inputDeskripsi" rows="3" placeholder="Deskripsi singkat buku..."></textarea>
                </div>
                <input type="hidden" id="editBukuId" value="">
                <div class="modal-actions">
                    <button type="button" class="btn-cancel" id="btnBatalModal">Batal</button>
                    <button type="submit" class="btn-save">
                        <span class="btn-text" id="btnSaveText">Simpan Buku</span>
                        <span class="btn-loading" id="btnSaveLoading" style="display:none;">Menyimpan...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script type="module">
        import { 
            db, cekAdmin, collection, getDocs, addDoc, updateDoc, deleteDoc, doc, query, orderBy, serverTimestamp
        } from '../../firebase/firebase-config.js';

        let semuaBuku = [];
        let base64Gambar = '';

        document.getElementById('inputGambar').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = function(event) {
                const img = new Image();
                img.onload = function() {
                    const canvas = document.createElement('canvas');
                    const MAX_WIDTH = 300;
                    const scaleSize = MAX_WIDTH / img.width;
                    canvas.width = MAX_WIDTH;
                    canvas.height = img.height * scaleSize;

                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(img, 0, 0, canvas.width, canvas.height);

                    base64Gambar = canvas.toDataURL('image/jpeg', 0.7);

                    const previewImg = document.getElementById('previewGambar');
                    previewImg.src = base64Gambar;
                    document.getElementById('previewGambarContainer').style.display = 'block';
                };
                img.src = event.target.result;
            };
            reader.readAsDataURL(file);
        });

        cekAdmin().then(async () => {
            await loadBuku();
        });

        async function loadBuku() {
            const snap = await getDocs(query(collection(db, 'buku'), orderBy('judul')));
            semuaBuku = [];
            snap.forEach(d => semuaBuku.push({ id: d.id, ...d.data() }));
            render();
        }

        // === Search ===
        document.getElementById('searchBuku').addEventListener('input', () => render());

        function render() {
            const cari = document.getElementById('searchBuku').value.toLowerCase();
            const filtered = semuaBuku.filter(b => 
                !cari || (b.judul||'').toLowerCase().includes(cari) || (b.penulis||'').toLowerCase().includes(cari)
            );

            document.getElementById('countBadge').textContent = `${semuaBuku.length} Buku`;
            document.getElementById('showingBuku').textContent = `Menampilkan ${filtered.length} dari ${semuaBuku.length} buku`;

            let totalStok = 0;
            semuaBuku.forEach(b => totalStok += (b.stok || 0));
            document.getElementById('totalStok').textContent = totalStok;

            if (filtered.length === 0) {
                document.getElementById('bukuBody').innerHTML = '<tr><td colspan="5" style="text-align:center;padding:40px;color:var(--text-light);">Tidak ada buku ditemukan.</td></tr>';
                return;
            }

            document.getElementById('bukuBody').innerHTML = filtered.map(b => {
                const tersedia = (b.stokTersedia ?? b.stok ?? 0);
                const stok = b.stok || 0;
                const persen = stok > 0 ? Math.round((tersedia / stok) * 100) : 0;
                const statusDot = tersedia > 0 ? 'green' : 'red';
                const statusText = tersedia > 0 ? 'Tersedia' : 'Habis';

                const coverStyle = b.gambar 
                    ? `background-image: url('${b.gambar}'); background-size: cover; background-repeat: no-repeat; background-position: center; background-color: ${b.warnaCover || '#1e293b'};` 
                    : `background: ${b.warnaCover || '#1e293b'};`;

                return `<tr>
                    <td>
                        <div class="book-cell">
                            <div class="mini-cover" style="${coverStyle}"></div>
                            <div><strong>${b.judul || '-'}</strong><br><small>${b.penulis || '-'}</small></div>
                        </div>
                    </td>
                    <td><span class="cat-pill">${b.kategori || 'Umum'}</span></td>
                    <td>
                        <div class="stock-progress-container">
                            <span>${tersedia} / ${stok}</span>
                            <div class="progress-bar"><div class="progress-fill ${statusDot}" style="width:${persen}%;"></div></div>
                        </div>
                    </td>
                    <td><span class="status-dot ${statusDot}">● ${statusText}</span></td>
                    <td>
                        <div class="action-icons">
                            <button class="icon-btn" title="Edit" onclick="editBuku('${b.id}')"><i class="fa-solid fa-pen-to-square"></i></button>
                            <button class="icon-btn danger" title="Hapus" onclick="hapusBuku('${b.id}','${(b.judul||'').replace(/'/g,"\\'")}')"><i class="fa-solid fa-trash-can"></i></button>
                        </div>
                    </td>
                </tr>`;
            }).join('');
        }

        // === MODAL ===
        const modal = document.getElementById('modalBuku');
        const openModal = () => modal.style.display = 'flex';
        const closeModal = () => { 
            modal.style.display = 'none'; 
            document.getElementById('formBuku').reset(); 
            document.getElementById('editBukuId').value = ''; 
            document.getElementById('modalTitle').textContent = 'Tambah Buku Baru'; 
            document.getElementById('btnSaveText').textContent = 'Simpan Buku'; 
            base64Gambar = '';
            document.getElementById('previewGambar').src = '';
            document.getElementById('previewGambarContainer').style.display = 'none';
        };
        
        document.getElementById('btnTambahBuku').addEventListener('click', () => { closeModal(); openModal(); });
        document.getElementById('quickAddCard').addEventListener('click', () => { closeModal(); openModal(); });
        document.getElementById('btnCloseModal').addEventListener('click', closeModal);
        document.getElementById('btnBatalModal').addEventListener('click', closeModal);
        modal.addEventListener('click', (e) => { if (e.target === modal) closeModal(); });

        // === SIMPAN BUKU ===
        document.getElementById('formBuku').addEventListener('submit', async (e) => {
            e.preventDefault();
            const saveText = document.getElementById('btnSaveText');
            const saveLoading = document.getElementById('btnSaveLoading');
            saveText.style.display = 'none';
            saveLoading.style.display = 'inline';

            const data = {
                judul: document.getElementById('inputJudul').value.trim(),
                penulis: document.getElementById('inputPenulis').value.trim(),
                kategori: document.getElementById('inputKategori').value,
                stok: parseInt(document.getElementById('inputStok').value) || 0,
                stokTersedia: parseInt(document.getElementById('inputStok').value) || 0,
                tahunTerbit: document.getElementById('inputTahun').value || '',
                halaman: document.getElementById('inputHalaman').value || '',
                bahasa: document.getElementById('inputBahasa').value || 'Indonesia',
                warnaCover: document.getElementById('inputWarna').value || '#1e3a5f',
                gambar: base64Gambar || (document.getElementById('editBukuId').value ? (semuaBuku.find(b => b.id === document.getElementById('editBukuId').value)?.gambar || '') : ''),
                deskripsi: document.getElementById('inputDeskripsi').value || ''
            };

            try {
                const editId = document.getElementById('editBukuId').value;
                if (editId) {
                    await updateDoc(doc(db, 'buku', editId), data);
                } else {
                    data.tanggalDitambahkan = serverTimestamp();
                    await addDoc(collection(db, 'buku'), data);
                }
                closeModal();
                await loadBuku();
            } catch (error) {
                alert('Gagal menyimpan: ' + error.message);
            }
            saveText.style.display = 'inline';
            saveLoading.style.display = 'none';
        });

        // === EDIT BUKU ===
        window.editBuku = function(id) {
            const buku = semuaBuku.find(b => b.id === id);
            if (!buku) return;
            document.getElementById('modalTitle').textContent = 'Edit Buku';
            document.getElementById('btnSaveText').textContent = 'Perbarui Buku';
            document.getElementById('editBukuId').value = id;
            document.getElementById('inputJudul').value = buku.judul || '';
            document.getElementById('inputPenulis').value = buku.penulis || '';
            document.getElementById('inputKategori').value = buku.kategori || '';
            document.getElementById('inputStok').value = buku.stok || 0;
            document.getElementById('inputTahun').value = buku.tahunTerbit || '';
            document.getElementById('inputHalaman').value = buku.halaman || '';
            document.getElementById('inputBahasa').value = buku.bahasa || 'Indonesia';
            document.getElementById('inputWarna').value = buku.warnaCover || '#1e3a5f';
            document.getElementById('inputDeskripsi').value = buku.deskripsi || '';

            // Set image preview
            base64Gambar = buku.gambar || '';
            const previewImg = document.getElementById('previewGambar');
            if (base64Gambar) {
                previewImg.src = base64Gambar;
                document.getElementById('previewGambarContainer').style.display = 'block';
            } else {
                previewImg.src = '';
                document.getElementById('previewGambarContainer').style.display = 'none';
            }
            openModal();
        };

        // === HAPUS BUKU ===
        window.hapusBuku = async function(id, judul) {
            if (!confirm(`Apakah Anda yakin ingin menghapus buku "${judul}"?`)) return;
            try {
                await deleteDoc(doc(db, 'buku', id));
                await loadBuku();
            } catch (error) {
                alert('Gagal menghapus: ' + error.message);
            }
        };
    </script>
    <script type="module" src="../../components/admin-sidebar.js?v=1.2"></script>
</body>
</html>
