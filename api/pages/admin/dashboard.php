<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lentera Ilmu Admin - Dashboard</title>
    <link rel="stylesheet" href="../../assets/css/global.css?v=1.2">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>

    <!-- HEADER ADMIN -->
    <div id="admin-header-container"></div>

    <div class="dashboard-wrapper">
        <!-- SIDEBAR ADMIN -->
        <div id="sidebar-container"></div>

        <main class="dashboard-content">
            <!-- WELCOME -->
            <div class="welcome-section">
                <h1 id="welcomeAdmin">Selamat datang, Admin!</h1>
                <p>Berikut ringkasan perpustakaan hari ini.</p>
            </div>

            <!-- METRIK -->
            <section class="metrics-row">
                <div class="metric-card">
                    <div class="m-left"><span class="m-icon"><i class="fa-solid fa-chart-line"></i></span></div>
                    <div class="m-right">
                        <small>Total Katalog</small>
                        <h3 id="totalKatalog">-</h3>
                        <span class="trend positive" id="trendKatalog">Koleksi perpustakaan</span>
                    </div>
                </div>
                <div class="metric-card">
                    <div class="m-left"><span class="m-icon"><i class="fa-solid fa-boxes-stacked"></i></span></div>
                    <div class="m-right">
                        <small>Buku Tersedia</small>
                        <h3 id="bukuTersedia">-</h3>
                        <span class="trend subtitle">Siap dipinjam</span>
                    </div>
                </div>
                <div class="metric-card">
                    <div class="m-left"><span class="m-icon"><i class="fa-solid fa-clock-rotate-left"></i></span></div>
                    <div class="m-right">
                        <small>Total Peminjaman</small>
                        <h3 id="totalPeminjaman">-</h3>
                        <span class="trend subtitle">Riwayat keseluruhan</span>
                    </div>
                </div>
                <div class="metric-card active-borrowing">
                    <div class="m-left"><span class="m-icon text-blue"><i class="fa-solid fa-book"></i></span></div>
                    <div class="m-right">
                        <small class="text-blue">Peminjaman Aktif</small>
                        <h3 class="text-blue" id="peminjamanAktif">-</h3>
                        <span class="trend alert-text" id="alertAktif">-</span>
                    </div>
                </div>
            </section>

            <!-- TABEL AKTIVITAS -->
            <div class="split-layout">
                <section class="activity-section">
                    <div class="section-title-row">
                        <h3>Aktivitas Peminjaman Terbaru</h3>
                        <a href="riwayat.php" class="view-all">Lihat Semua</a>
                    </div>
                    <div class="table-container">
                        <table class="activity-table">
                            <thead>
                                <tr>
                                    <th>JUDUL & PENULIS</th>
                                    <th>PEMINJAM</th>
                                    <th>TGL PINJAM</th>
                                    <th>STATUS</th>
                                </tr>
                            </thead>
                            <tbody id="activityBody">
                                <tr><td colspan="4" style="text-align:center;color:var(--text-light);padding:30px;">Memuat data...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <!-- SIDEBAR AKSI CEPAT -->
                <aside class="side-controls">
                    <div class="quick-actions-card">
                        <h3>Aksi Cepat</h3>
                        <div class="actions-grid">
                            <a href="manajemen-buku.php" class="action-item"><span class="a-icon"><i class="fa-solid fa-book-bookmark"></i></span><br>Kelola Buku</a>
                            <a href="manajemen-pengguna.php" class="action-item"><span class="a-icon"><i class="fa-solid fa-users-gear"></i></span><br>Kelola User</a>
                            <a href="riwayat.php" class="action-item"><span class="a-icon"><i class="fa-solid fa-list-check"></i></span><br>Riwayat</a>
                            <a href="katalog.php" class="action-item"><span class="a-icon"><i class="fa-solid fa-book"></i></span><br>Katalog</a>
                        </div>
                    </div>
                </aside>
            </div>
        </main>
    </div>

    <script type="module">
        import { 
            db, cekAdmin, formatTanggal,
            collection, getDocs, query, orderBy, limit, where
        } from '../../firebase/firebase-config.js';

        cekAdmin().then(async (admin) => {
            const nama = admin.nama || admin.displayName || 'Admin';
            document.getElementById('welcomeAdmin').textContent = `Selamat datang, ${nama}! 👋`;

            try {
                // === Statistik Buku ===
                const bukuSnap = await getDocs(collection(db, 'buku'));
                let totalBuku = 0, bukuTersedia = 0;
                bukuSnap.forEach(doc => {
                    totalBuku++;
                    bukuTersedia += (doc.data().stokTersedia || doc.data().stok || 0);
                });

                document.getElementById('totalKatalog').textContent = totalBuku.toLocaleString('id-ID');
                document.getElementById('bukuTersedia').textContent = bukuTersedia.toLocaleString('id-ID');

                // === Statistik Peminjaman ===
                const peminjamanSnap = await getDocs(collection(db, 'peminjaman'));
                let totalPeminjaman = 0, peminjamanAktif = 0;
                peminjamanSnap.forEach(doc => {
                    totalPeminjaman++;
                    if (doc.data().status === 'dipinjam') peminjamanAktif++;
                });

                document.getElementById('totalPeminjaman').textContent = totalPeminjaman.toLocaleString('id-ID');
                document.getElementById('peminjamanAktif').textContent = peminjamanAktif;
                document.getElementById('alertAktif').textContent = peminjamanAktif > 0 
                    ? `⚠️ ${peminjamanAktif} buku sedang dipinjam` 
                    : 'Tidak ada peminjaman aktif';

                // === Aktivitas Terbaru (5 terakhir) ===
                const recentQuery = query(collection(db, 'peminjaman'), orderBy('tanggalPinjam', 'desc'), limit(5));
                const recentSnap = await getDocs(recentQuery);

                let rows = '';
                recentSnap.forEach(doc => {
                    const d = doc.data();
                    let statusClass = d.status === 'dipinjam' ? 'in-progress' : d.status === 'terlambat' ? 'overdue' : 'returned';
                    let statusText = d.status === 'dipinjam' ? 'DIPINJAM' : d.status === 'terlambat' ? 'TERLAMBAT' : 'KEMBALI';

                    rows += `
                    <tr>
                        <td><strong>${d.judulBuku || '-'}</strong><br><small>${d.penulis || '-'}</small></td>
                        <td><small>${d.namaUser || '-'}</small></td>
                        <td>${formatTanggal(d.tanggalPinjam)}</td>
                        <td><span class="p-status ${statusClass}">${statusText}</span></td>
                    </tr>`;
                });

                document.getElementById('activityBody').innerHTML = rows || 
                    '<tr><td colspan="4" style="text-align:center;color:var(--text-light);padding:30px;">Belum ada data peminjaman.</td></tr>';

            } catch (error) {
                console.error('Gagal memuat dashboard admin:', error);
            }
        });
    </script>

    <script type="module" src="../../components/admin-sidebar.js?v=1.2"></script>
</body>
</html>
