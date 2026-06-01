<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lentera Ilmu - Beranda</title>
    <link rel="stylesheet" href="../../assets/css/global.css?v=1.2">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>

    <!-- NAVBAR GLOBAL -->
    <div id="navbar-container"></div>

    <main class="container">
        <!-- SECTION: SAMBUTAN -->
        <section class="welcome-section">
            <h1 id="welcomeText">Selamat datang kembali!</h1>
            <p>Berikut ringkasan aktivitas perpustakaan Anda hari ini.</p>
        </section>

        <!-- SECTION: STATISTIK -->
        <section class="stats-grid">
            <div class="stat-card">
                <div class="stat-info">
                    <span class="stat-title">Total Katalog</span>
                    <span class="stat-value" id="totalKatalog">-</span>
                    <span class="stat-sub positive">Koleksi perpustakaan</span>
                </div>
                <span class="stat-icon"><i class="fa-solid fa-book"></i></span>
            </div>
            <div class="stat-card">
                <div class="stat-info">
                    <span class="stat-title">Buku Tersedia</span>
                    <span class="stat-value" id="bukuTersedia">-</span>
                    <span class="stat-sub">Siap dipinjam</span>
                </div>
                <span class="stat-icon"><i class="fa-solid fa-square-check"></i></span>
            </div>
            <div class="stat-card">
                <div class="stat-info">
                    <span class="stat-title">Total Dipinjam</span>
                    <span class="stat-value" id="totalDipinjam">-</span>
                    <span class="stat-sub">Riwayat peminjaman</span>
                </div>
                <span class="stat-icon"><i class="fa-solid fa-clock-rotate-left"></i></span>
            </div>
            <div class="stat-card active-borrowing">
                <div class="stat-info">
                    <span class="stat-title">Peminjaman Aktif</span>
                    <span class="stat-value" id="peminjamanAktif">-</span>
                    <span class="stat-sub alert" id="nextDue">-</span>
                </div>
                <span class="stat-icon"><i class="fa-solid fa-bookmark"></i></span>
            </div>
        </section>

        <div class="main-layout">
            <!-- KOLOM KIRI: AKTIVITAS TERBARU -->
            <section class="activity-container">
                <div class="section-header">
                    <h2>Aktivitas Terbaru</h2>
                    <a href="riwayat.php" class="view-all">Lihat Semua Riwayat</a>
                </div>
                <table class="activity-table">
                    <thead>
                        <tr>
                            <th>JUDUL & PENULIS</th>
                            <th>TGL PINJAM</th>
                            <th>TGL KEMBALI</th>
                            <th>STATUS</th>
                        </tr>
                    </thead>
                    <tbody id="activityTableBody">
                        <tr>
                            <td colspan="4" class="empty-state">Memuat data...</td>
                        </tr>
                    </tbody>
                </table>
            </section>

            <!-- KOLOM KANAN: SIDEBAR -->
            <aside class="sidebar">
                <div class="side-card">
                    <h3>Aksi Cepat</h3>
                    <div class="actions-grid">
                        <a href="katalog.php" class="action-tile"><span><i class="fa-solid fa-book-open"></i></span> Katalog</a>
                        <a href="riwayat.php" class="action-tile"><span><i class="fa-solid fa-list-check"></i></span> Riwayat</a>
                        <a href="katalog.php" class="action-tile"><span><i class="fa-solid fa-magnifying-glass"></i></span> Cari Buku</a>
                        <a href="riwayat.php" class="action-tile"><span><i class="fa-solid fa-hourglass-half"></i></span> Perpanjang</a>
                    </div>
                </div>

                <div class="side-card">
                    <h3><i class="fa-solid fa-circle-info" style="margin-right: 8px; color: var(--primary);"></i> Peraturan Perpustakaan</h3>
                    <ul class="rule-list">
                        <li>Maksimal peminjaman: <strong>14 hari</strong></li>
                        <li>Batas buku: <strong>5 buku bersamaan</strong></li>
                        <li>Keterlambatan dikenakan denda administratif</li>
                    </ul>
                </div>
            </aside>
        </div>

        <!--REKOMENDASI BUKU -->
        <section class="recommendations">
            <div class="section-header">
                <div>
                    <h2>Rekomendasi Untuk Anda</h2>
                    <p class="sub-text">Berdasarkan riwayat baca Anda</p>
                </div>
                <a href="katalog.php" class="view-all">Lihat Katalog →</a>
            </div>

            <div class="books-grid" id="rekomendasiGrid">
            </div>
        </section>
    </main>

    <!-- FOOTER -->
    <footer class="main-footer">
        <div class="footer-top">
            <div class="footer-brand">
                <div class="footer-logo" style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px;">
                    <img src="../../assets/image/logo.png" alt="Lentera Ilmu Logo" style="height: 32px; object-fit: contain;">
                    <span style="color: white; font-weight: 700; font-size: 1.2rem; font-family: 'Inter', sans-serif;">Lentera Ilmu</span>
                </div>
                <p>Perpustakaan digital modern untuk mendukung kegiatan literasi dan penelitian akademik.</p>
            </div>
            <div class="footer-links">
                <div>
                    <h4>PERPUSTAKAAN</h4>
                    <a href="katalog.php">Katalog</a>
                    <a href="katalog.php">Koleksi Baru</a>
                </div>
                <div>
                    <h4>AKUN</h4>
                    <a href="riwayat.php">Riwayat Peminjaman</a>
                </div>
                <div>
                    <h4>BANTUAN</h4>
                    <a href="#">Pusat Bantuan</a>
                    <a href="#">Hubungi Kami</a>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2026 Lentera Ilmu. Tugas Pemograman Web Lanjut.</p>
        </div>
    </footer>

    <script type="module">
        import { 
            auth, db, cekLogin, formatTanggal,
            collection, getDocs, query, where, orderBy, limit 
        } from '../../firebase/firebase-config.js';

        //  Muat Data Dashboard 
        cekLogin().then(async (user) => {
            // Tampilkan nama user
            const nama = user.nama || user.displayName || 'Pengguna';
            document.getElementById('welcomeText').textContent = `Selamat datang kembali, ${nama}! 👋`;

            try {
                //  Load Statistik Buku 
                const bukuSnapshot = await getDocs(collection(db, 'buku'));
                let totalBuku = 0;
                let bukuTersedia = 0;
                const semuaBuku = [];

                bukuSnapshot.forEach(doc => {
                    const data = doc.data();
                    totalBuku++;
                    bukuTersedia += (data.stokTersedia || data.stok || 0);
                    semuaBuku.push({ id: doc.id, ...data });
                });

                document.getElementById('totalKatalog').textContent = totalBuku.toLocaleString('id-ID');
                document.getElementById('bukuTersedia').textContent = bukuTersedia.toLocaleString('id-ID');

                //  Load Peminjaman User 
                const peminjamanQuery = query(
                    collection(db, 'peminjaman'),
                    where('userId', '==', user.uid)
                );
                const peminjamanSnapshot = await getDocs(peminjamanQuery);
                
                const semuaPeminjamanUser = [];
                peminjamanSnapshot.forEach(doc => {
                    semuaPeminjamanUser.push({ id: doc.id, ...doc.data() });
                });

                // Urutkan berdasarkan tanggalPinjam desc di memory
                semuaPeminjamanUser.sort((a, b) => {
                    const tA = a.tanggalPinjam?.seconds || 0;
                    const tB = b.tanggalPinjam?.seconds || 0;
                    return tB - tA;
                });

                let totalDipinjam = semuaPeminjamanUser.length;
                let peminjamanAktif = semuaPeminjamanUser.filter(p => p.status  'dipinjam').length;
                const aktivitasHTML = [];

                const aktivitasTerbaru = semuaPeminjamanUser.slice(0, 5);
                aktivitasTerbaru.forEach(data => {

                    // Status badge
                    let statusClass = '';
                    let statusText = '';
                    switch(data.status) {
                        case 'dipinjam':
                            statusClass = 'progress';
                            statusText = 'DIPINJAM';
                            break;
                        case 'dikembalikan':
                            statusClass = 'returned';
                            statusText = 'DIKEMBALIKAN';
                            break;
                        case 'terlambat':
                            statusClass = 'overdue';
                            statusText = 'TERLAMBAT';
                            break;
                        default:
                            statusClass = 'progress';
                            statusText = data.status?.toUpperCase() || '-';
                    }

                    const coverStyle = data.gambar 
                        ? `background-image: url('${data.gambar}'); background-size: cover; background-repeat: no-repeat; background-position: center; background-color: ${data.warnaCover || '#1e293b'};` 
                        : `background: ${data.warnaCover || '#1e293b'};`;
                    aktivitasHTML.push(`
                        <tr>
                            <td>
                                <div class="book-cell">
                                    <div class="book-placeholder" style="${coverStyle}"></div>
                                    <div>
                                        <strong>${data.judulBuku || '-'}</strong><br>
                                        <small>${data.penulis || '-'}</small>
                                    </div>
                                </div>
                            </td>
                            <td>${formatTanggal(data.tanggalPinjam)}</td>
                            <td>${formatTanggal(data.tanggalKembali)}</td>
                            <td><span class="status-badge ${statusClass}">${statusText}</span></td>
                        </tr>
                    `);
                });

                document.getElementById('totalDipinjam').textContent = totalDipinjam;
                document.getElementById('peminjamanAktif').textContent = peminjamanAktif;
                
                if (peminjamanAktif > 0) {
                    document.getElementById('nextDue').textContent = `${peminjamanAktif} buku sedang dipinjam`;
                } else {
                    document.getElementById('nextDue').textContent = 'Tidak ada peminjaman aktif';
                }

                // Render tabel aktivitas
                const tbody = document.getElementById('activityTableBody');
                if (aktivitasHTML.length > 0) {
                    tbody.innerHTML = aktivitasHTML.join('');
                } else {
                    tbody.innerHTML = '<tr><td colspan="4" class="empty-state">Belum ada riwayat peminjaman.</td></tr>';
                }

                //  Rekomendasi Buku (5 buku pertama) 
                const rekGrid = document.getElementById('rekomendasiGrid');
                const bukuRekomendasi = semuaBuku.slice(0, 6);
                
                if (bukuRekomendasi.length > 0) {
                    rekGrid.innerHTML = bukuRekomendasi.map(buku => {
                        const coverStyle = buku.gambar 
                            ? `background-image: url('${buku.gambar}');` 
                            : `background: ${buku.warnaCover || '#1e293b'};`;
                        const coverIcon = buku.gambar ? '' : '<i class="fa-solid fa-book" style="font-size: 28px; color: rgba(255,255,255,0.6);"></i>';
                        return `
                        <a href="detail-buku.php?id=${buku.id}" class="book-card">
                            <div class="cover-art" style="${coverStyle}">${coverIcon}</div>
                            <h4>${buku.judul || '-'}</h4>
                            <p>${buku.penulis || '-'}</p>
                        </a>
                        `;
                    }).join('');
                } else {
                    rekGrid.innerHTML = '<p class="empty-state">Belum ada buku dalam katalog.</p>';
                }

            } catch (error) {
                console.error('Error memuat dashboard:', error);
            }
        });
    </script>

    <script type="module" src="../../components/user-navbar.js?v=1.2"></script>
</body>
</html>
