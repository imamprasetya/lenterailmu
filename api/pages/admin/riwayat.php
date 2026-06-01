<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lentera Ilmu Admin - Riwayat Peminjaman</title>
    <link rel="stylesheet" href="../../assets/css/global.css?v=1.2">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="riwayat.css">
</head>
<body>
    <div id="admin-header-container"></div>
    <div class="dashboard-wrapper">
        <div id="sidebar-container"></div>
        <main class="dashboard-content">
            <div class="content-header">
                <div>
                    <h1>Riwayat Peminjaman</h1>
                    <p class="subtitle">Pantau semua aktivitas peminjaman perpustakaan.</p>
                </div>
                <div class="header-actions">
                    <select class="filter-dropdown" id="filterStatus">
                        <option value="semua">Semua Status</option>
                        <option value="dipinjam">Dipinjam</option>
                        <option value="dikembalikan">Dikembalikan</option>
                        <option value="terlambat">Terlambat</option>
                    </select>
                </div>
            </div>

            <div class="table-card">
                <table class="history-table">
                    <thead>
                        <tr>
                            <th>JUDUL & PENULIS</th>
                            <th>PEMINJAM</th>
                            <th>TGL PINJAM</th>
                            <th>TGL KEMBALI</th>
                            <th>STATUS</th>
                            <th>AKSI</th>
                        </tr>
                    </thead>
                    <tbody id="historyBody">
                        <tr><td colspan="6" style="text-align:center;padding:40px;color:var(--text-light);">Memuat data...</td></tr>
                    </tbody>
                </table>
                <div class="table-footer">
                    <span id="showingInfo">-</span>
                </div>
            </div>

            <!-- STATISTIK -->
            <div class="stats-row">
                <div class="mini-stat-card">
                    <span class="icon"><i class="fa-solid fa-book"></i></span>
                    <div><small>TOTAL DIPINJAM</small><h4 id="statTotal">-</h4></div>
                </div>
                <div class="mini-stat-card">
                    <span class="icon"><i class="fa-solid fa-gauge-high"></i></span>
                    <div><small>TEPAT WAKTU</small><h4 id="statTepatWaktu">-</h4></div>
                </div>
                <div class="mini-stat-card">
                    <span class="icon"><i class="fa-solid fa-clock"></i></span>
                    <div><small>RATA-RATA DURASI</small><h4>14 Hari</h4></div>
                </div>
            </div>
        </main>
    </div>

    <script type="module">
        import { db, cekAdmin, formatTanggal, collection, getDocs, query, orderBy, doc, updateDoc, getDoc, deleteDoc } from '../../firebase/firebase-config.js';

        // Fungsi kembalikan buku dari sisi admin
        window.kembalikanBukuAdmin = async (peminjamanId, bukuId) => {
            if (!confirm('Apakah Anda yakin ingin mengonfirmasi pengembalian buku ini?')) return;
            
            try {
                // 1. Update status peminjaman menjadi 'dikembalikan'
                const pinjamRef = doc(db, 'peminjaman', peminjamanId);
                await updateDoc(pinjamRef, {
                    status: 'dikembalikan',
                    tanggalKembali: new Date()
                });

                // 2. Ambil data buku untuk menambah kembali stokTersedia
                const bukuRef = doc(db, 'buku', bukuId);
                const bukuSnap = await getDoc(bukuRef);
                if (bukuSnap.exists()) {
                    const dataBuku = bukuSnap.data();
                    const stokBaru = (dataBuku.stokTersedia || 0) + 1;
                    await updateDoc(bukuRef, {
                        stokTersedia: Math.min(dataBuku.stok || 1, stokBaru)
                    });
                }

                alert('Buku berhasil dikembalikan dan stok diperbarui!');
                window.location.reload();
            } catch (error) {
                console.error('Gagal mengembalikan buku:', error);
                alert('Gagal mengembalikan buku: ' + error.message);
            }
        };

        // Fungsi edit status peminjaman langsung dari dropdown
        window.updateStatusPeminjaman = async (peminjamanId, bukuId, newStatus, oldStatus) => {
            if (newStatus === oldStatus) return;
            if (!confirm(`Apakah Anda yakin ingin mengubah status peminjaman dari "${oldStatus}" menjadi "${newStatus}"?`)) {
                window.location.reload();
                return;
            }

            try {
                // 1. Update status peminjaman
                const pinjamRef = doc(db, 'peminjaman', peminjamanId);
                const updateData = { status: newStatus };
                if (newStatus === 'dikembalikan') {
                    updateData.tanggalKembali = new Date();
                }
                await updateDoc(pinjamRef, updateData);

                // 2. Sesuaikan stok buku
                const bukuRef = doc(db, 'buku', bukuId);
                const bukuSnap = await getDoc(bukuRef);
                if (bukuSnap.exists()) {
                    const dataBuku = bukuSnap.data();
                    let stokBaru = dataBuku.stokTersedia || 0;

                    if (newStatus === 'dikembalikan' && oldStatus !== 'dikembalikan') {
                        stokBaru += 1;
                    } else if (newStatus !== 'dikembalikan' && oldStatus === 'dikembalikan') {
                        stokBaru = Math.max(0, stokBaru - 1);
                    }

                    await updateDoc(bukuRef, {
                        stokTersedia: Math.min(dataBuku.stok || 1, stokBaru)
                    });
                }

                alert('Status peminjaman berhasil diperbarui!');
                window.location.reload();
            } catch (error) {
                console.error('Gagal memperbarui status:', error);
                alert('Gagal memperbarui status: ' + error.message);
                window.location.reload();
            }
        };

        // Fungsi menghapus riwayat peminjaman
        window.hapusRiwayatAdmin = async (peminjamanId) => {
            if (!confirm('Apakah Anda yakin ingin menghapus data riwayat ini secara permanen? Data yang dihapus tidak dapat dipulihkan.')) return;

            try {
                const pinjamRef = doc(db, 'peminjaman', peminjamanId);
                await deleteDoc(pinjamRef);
                alert('Riwayat peminjaman berhasil dihapus!');
                window.location.reload();
            } catch (error) {
                console.error('Gagal menghapus riwayat:', error);
                alert('Gagal menghapus riwayat: ' + error.message);
            }
        };

        let semuaPeminjaman = [];
        const urlParams = new URLSearchParams(window.location.search);
        const targetUserId = urlParams.get('userId');
        const targetUserName = urlParams.get('userName');

        cekAdmin().then(async () => {
            const snap = await getDocs(query(collection(db, 'peminjaman'), orderBy('tanggalPinjam', 'desc')));
            semuaPeminjaman = [];
            snap.forEach(d => semuaPeminjaman.push({ id: d.id, ...d.data() }));

            // Tampilkan banner filter jika memfilter pengguna tertentu
            if (targetUserId && targetUserName) {
                const banner = document.createElement('div');
                banner.className = 'filter-user-banner';
                banner.innerHTML = `
                    <span>Menampilkan riwayat peminjaman untuk user: <strong>${targetUserName}</strong></span>
                    <a href="riwayat.php" class="clear-filter-btn">Lihat Semua Riwayat</a>
                `;
                document.querySelector('.content-header').after(banner);
            }

            // Hitung statistik berdasarkan data terfilter jika memfilter pengguna
            const dataUntukStat = targetUserId ? semuaPeminjaman.filter(p => p.userId === targetUserId) : semuaPeminjaman;
            let dikembalikan = dataUntukStat.filter(p => p.status === 'dikembalikan').length;
            let rate = dataUntukStat.length > 0 ? Math.round((dikembalikan / dataUntukStat.length) * 100) : 0;
            document.getElementById('statTotal').textContent = `${dataUntukStat.length} Buku`;
            document.getElementById('statTepatWaktu').textContent = `${rate}%`;

            render();
        });

        document.getElementById('filterStatus').addEventListener('change', () => render());

        function render() {
            const filter = document.getElementById('filterStatus').value;
            let filtered = filter === 'semua' ? semuaPeminjaman : semuaPeminjaman.filter(p => p.status === filter);
            
            // Filter berdasarkan user khusus jika ada di URL
            if (targetUserId) {
                filtered = filtered.filter(p => p.userId === targetUserId);
            }

            if (filtered.length === 0) {
                document.getElementById('historyBody').innerHTML = '<tr><td colspan="6" style="text-align:center;padding:40px;color:var(--text-light);">Tidak ada data.</td></tr>';
                document.getElementById('showingInfo').textContent = '0 data';
                return;
            }

            document.getElementById('historyBody').innerHTML = filtered.map(p => {
                return `<tr>
                    <td><strong>${p.judulBuku||'-'}</strong><br><small>${p.penulis||'-'}</small></td>
                    <td><small>${p.namaUser||'-'}</small></td>
                    <td>${formatTanggal(p.tanggalPinjam)}</td>
                    <td>${formatTanggal(p.tanggalKembali)}</td>
                    <td>
                        <select onchange="updateStatusPeminjaman('${p.id}', '${p.bukuId}', this.value, '${p.status}')" class="admin-status-select ${p.status}">
                            <option value="dipinjam" ${p.status === 'dipinjam' ? 'selected' : ''}>Dipinjam</option>
                            <option value="proses_kembali" ${p.status === 'proses_kembali' ? 'selected' : ''}>Menunggu Persetujuan</option>
                            <option value="dikembalikan" ${p.status === 'dikembalikan' ? 'selected' : ''}>Dikembalikan</option>
                            <option value="terlambat" ${p.status === 'terlambat' ? 'selected' : ''}>Terlambat</option>
                        </select>
                    </td>
                    <td>
                        <div style="display:flex; justify-content:space-between; align-items:center; width:100%;">
                            <div style="display:flex; gap:6px;">
                                ${p.status === 'proses_kembali' ? `
                                    <button class="btn-return-admin" style="background:#ecfdf5; color:#10b981; border-color:#a7f3d0;" onclick="kembalikanBukuAdmin('${p.id}', '${p.bukuId}')"><i class="fa-solid fa-check" style="margin-right: 4px;"></i> Setujui</button>
                                ` : ''}
                                ${(p.status === 'dipinjam' || p.status === 'terlambat') ? `
                                    <button class="btn-return-admin" onclick="kembalikanBukuAdmin('${p.id}', '${p.bukuId}')"><i class="fa-solid fa-arrow-rotate-left" style="margin-right: 4px;"></i> Kembalikan</button>
                                ` : ''}
                            </div>
                            <button class="btn-delete-admin" onclick="hapusRiwayatAdmin('${p.id}')" title="Hapus Riwayat"><i class="fa-regular fa-trash-can" style="margin-right: 4px;"></i> Hapus</button>
                        </div>
                    </td>
                </tr>`;
            }).join('');

            const totalDataCatatan = targetUserId ? semuaPeminjaman.filter(p => p.userId === targetUserId).length : semuaPeminjaman.length;
            document.getElementById('showingInfo').textContent = `Menampilkan ${filtered.length} dari ${totalDataCatatan} data`;
        }
    </script>
    <script type="module" src="../../components/admin-sidebar.js?v=1.2"></script>
</body>
</html>
