<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lentera Ilmu - Riwayat Peminjaman</title>
    <link rel="stylesheet" href="../../assets/css/global.css?v=1.2">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="riwayat.css">
</head>
<body>

    <!-- NAVBAR GLOBAL -->
    <div id="navbar-container"></div>

    <main class="container">
        <!-- HEADER -->
        <div class="history-header">
            <div>
                <h1>Riwayat Peminjaman Saya</h1>
                <p class="subtitle">Pantau peminjaman aktif dan catatan baca Anda.</p>
            </div>
        </div>

        <!-- STATISTIK PERSONAL -->
        <section class="user-stats-grid">
            <div class="stat-box">
                <span class="stat-icon"><i class="fa-solid fa-book"></i></span>
                <div>
                    <small>TOTAL BUKU DIBACA</small>
                    <h3 id="totalBaca">-</h3>
                </div>
            </div>
            <div class="stat-box">
                <span class="stat-icon"><i class="fa-solid fa-gauge-high"></i></span>
                <div>
                    <small>TINGKAT TEPAT WAKTU</small>
                    <h3 id="rateTepatWaktu">-</h3>
                </div>
            </div>
            <div class="stat-box">
                <span class="stat-icon"><i class="fa-solid fa-hourglass-half"></i></span>
                <div>
                    <small>SEDANG DIPINJAM</small>
                    <h3 id="sedangDipinjam">-</h3>
                </div>
            </div>
        </section>

        <!-- DAFTAR RIWAYAT -->
        <div class="history-layout">
            <div class="records-column">
                <!-- FILTER TABS -->
                <div class="mini-tabs">
                    <span class="tab-btn active" data-filter="semua">Semua</span>
                    <span class="tab-btn" data-filter="dipinjam">Aktif</span>
                    <span class="tab-btn" data-filter="dikembalikan">Dikembalikan</span>
                    <span class="tab-btn" data-filter="terlambat">Terlambat</span>
                </div>

                <!-- LIST RIWAYAT -->
                <div class="history-list" id="historyList">
                    <div class="loading-placeholder">Memuat riwayat peminjaman...</div>
                </div>

                <!-- PAGINATION -->
                <div class="simple-pagination" id="pagination">
                    <span id="showingInfo">-</span>
                </div>
            </div>

            <!-- SIDEBAR: PERATURAN -->
            <aside class="notice-sidebar">
                <div class="notice-sticky-card">
                    <h4><i class="fa-solid fa-circle-info" style="margin-right: 8px; color: var(--primary);"></i> Peraturan & Denda</h4>
                    <ul class="rule-list">
                        <li>Maksimal peminjaman mandiri adalah <strong>14 hari</strong> sebelum harus diperpanjang.</li>
                        <li>Satu akun dibatasi maksimal <strong>10 buku bersamaan</strong>.</li>
                        <li>Keterlambatan pengembalian dikenakan denda administratif sesuai kebijakan.</li>
                    </ul>
                    <div class="help-box">
                        <p>Butuh bantuan atau ada masalah?</p>
                        <a href="#" class="contact-link">Hubungi Petugas Perpustakaan <i class="fa-solid fa-arrow-right" style="margin-left: 4px;"></i></a>
                    </div>
                </div>
            </aside>
        </div>
    </main>

    <script type="module">
        import { 
            db, cekLogin, formatTanggal,
            collection, getDocs, query, where, orderBy,
            doc, updateDoc, getDoc, Timestamp
        } from '../../firebase/firebase-config.js';

        // Fungsi mengajukan pengembalian buku (menunggu persetujuan admin)
        window.kembalikanBuku = async (peminjamanId) => {
            if (!confirm('Apakah Anda yakin ingin mengajukan pengembalian buku ini? Status akan diubah menjadi Menunggu Persetujuan Admin.')) return;
            
            try {
                // Update status peminjaman menjadi 'proses_kembali'
                const pinjamRef = doc(db, 'peminjaman', peminjamanId);
                await updateDoc(pinjamRef, {
                    status: 'proses_kembali'
                });

                alert('Pengajuan pengembalian berhasil! Silakan serahkan buku ke petugas untuk dikonfirmasi.');
                window.location.reload();
            } catch (error) {
                console.error('Gagal mengajukan pengembalian:', error);
                alert('Gagal mengajukan pengembalian. (Detail: ' + error.message + ')');
            }
        };

        // Fungsi memperpanjang peminjaman buku
        window.perpanjangBuku = async (peminjamanId, seconds) => {
            if (!confirm('Apakah Anda yakin ingin memperpanjang peminjaman buku ini selama 7 hari?')) return;
            
            try {
                // 1. Hitung tanggal kembali baru (lama + 7 hari)
                let dateLama = new Date(seconds * 1000);
                let dateBaru = new Date(dateLama.getTime() + (7 * 24 * 60 * 60 * 1000));
 
                // 2. Update tanggalKembali dan set flag diperpanjang di Firestore
                const pinjamRef = doc(db, 'peminjaman', peminjamanId);
                await updateDoc(pinjamRef, {
                    tanggalKembali: Timestamp.fromDate(dateBaru),
                    diperpanjang: true
                });

                alert('Peminjaman berhasil diperpanjang selama 7 hari!');
                window.location.reload();
            } catch (error) {
                console.error('Gagal memperpanjang peminjaman:', error);
                alert('Gagal memperpanjang peminjaman. (Detail: ' + error.message + ')');
            }
        };

        let semuaPeminjaman = [];
        let filterAktif = 'semua';

        cekLogin().then(async (user) => {
            try {
                const peminjamanQuery = query(
                    collection(db, 'peminjaman'),
                    where('userId', '==', user.uid)
                );
                const snapshot = await getDocs(peminjamanQuery);
                
                semuaPeminjaman = [];
                snapshot.forEach(doc => {
                    semuaPeminjaman.push({ id: doc.id, ...doc.data() });
                });

                // Urutkan berdasarkan tanggalPinjam desc di memory
                semuaPeminjaman.sort((a, b) => {
                    const tA = a.tanggalPinjam?.seconds || 0;
                    const tB = b.tanggalPinjam?.seconds || 0;
                    return tB - tA;
                });

                // Hitung statistik
                let totalBaca = semuaPeminjaman.length;
                let aktif = semuaPeminjaman.filter(p => p.status === 'dipinjam').length;
                let terlambat = semuaPeminjaman.filter(p => p.status === 'terlambat').length;
                let dikembalikan = semuaPeminjaman.filter(p => p.status === 'dikembalikan').length;
                let tepatWaktu = totalBaca > 0 ? Math.round((dikembalikan / totalBaca) * 100) : 0;

                document.getElementById('totalBaca').textContent = `${totalBaca} Buku`;
                document.getElementById('rateTepatWaktu').textContent = `${tepatWaktu}%`;
                document.getElementById('sedangDipinjam').textContent = `${aktif} Aktif`;

                renderRiwayat();
            } catch (error) {
                console.error('Gagal memuat riwayat:', error);
                document.getElementById('historyList').innerHTML = 
                    '<div class="loading-placeholder">Gagal memuat data.</div>';
            }
        });

        // === Filter Tabs ===
        document.querySelector('.mini-tabs').addEventListener('click', (e) => {
            if (e.target.classList.contains('tab-btn')) {
                document.querySelectorAll('.tab-btn').forEach(t => t.classList.remove('active'));
                e.target.classList.add('active');
                filterAktif = e.target.dataset.filter;
                renderRiwayat();
            }
        });

        function renderRiwayat() {
            const list = document.getElementById('historyList');
            
            let filtered = semuaPeminjaman;
            if (filterAktif !== 'semua') {
                filtered = semuaPeminjaman.filter(p => p.status === filterAktif);
            }

            if (filtered.length === 0) {
                list.innerHTML = '<div class="loading-placeholder">Tidak ada data peminjaman.</div>';
                document.getElementById('showingInfo').textContent = '0 catatan';
                return;
            }

            list.innerHTML = filtered.map(p => {
                let statusClass = '';
                let statusText = '';
                let cardClass = '';

                switch(p.status) {
                    case 'dipinjam':
                        statusClass = 'status-borrowed';
                        statusText = 'Sedang Dibaca';
                        cardClass = 'status-active';
                        break;
                    case 'proses_kembali':
                        statusClass = 'status-pending';
                        statusText = 'Menunggu Persetujuan';
                        cardClass = 'status-pending-card';
                        break;
                    case 'dikembalikan':
                        statusClass = 'status-returned';
                        statusText = 'Dikembalikan';
                        cardClass = '';
                        break;
                    case 'terlambat':
                        statusClass = 'status-overdue';
                        statusText = 'Terlambat';
                        cardClass = 'status-late';
                        break;
                    default:
                        statusClass = 'status-borrowed';
                        statusText = p.status || '-';
                }

                const coverStyle = p.gambar 
                    ? `background-image: url('${p.gambar}'); background-size: cover; background-repeat: no-repeat; background-position: center; background-color: ${p.warnaCover || '#1e293b'};` 
                    : `background: ${p.warnaCover || '#1e293b'};`;
                return `
                <div class="history-item-card ${cardClass}">
                    <div class="book-info-side">
                        <div class="book-mini-cover" style="${coverStyle}"></div>
                        <div>
                            <h4>${p.judulBuku || '-'}</h4>
                            <p class="author">${p.penulis || '-'}</p>
                            <div class="date-badge">Dipinjam: <strong>${formatTanggal(p.tanggalPinjam)}</strong></div>
                        </div>
                    </div>
                    <div class="status-action-side">
                        <span class="badge ${statusClass}">${statusText}</span>
                        <small class="due-text">Kembali: ${formatTanggal(p.tanggalKembali)}</small>
                        ${(p.status === 'dipinjam' || p.status === 'terlambat') ? `
                            <div class="action-buttons-row" style="display:flex; gap:6px; margin-top:6px;">
                                <button class="btn-return" onclick="kembalikanBuku('${p.id}')"><i class="fa-solid fa-arrow-rotate-left" style="margin-right: 4px;"></i> Kembalikan</button>
                                ${!p.diperpanjang ? `
                                    <button class="btn-return" onclick="perpanjangBuku('${p.id}', ${p.tanggalKembali?.seconds || 0})"><i class="fa-solid fa-hourglass-start" style="margin-right: 4px;"></i> Perpanjang</button>
                                ` : '<span style="font-size:10px;color:var(--text-light);font-weight:500;align-self:center;margin-top:2px;"><i class="fa-solid fa-hourglass-start" style="margin-right: 4px;"></i> Diperpanjang</span>'}
                            </div>
                        ` : ''}
                    </div>
                </div>
                `;
            }).join('');

            document.getElementById('showingInfo').textContent = 
                `Menampilkan ${filtered.length} dari ${semuaPeminjaman.length} catatan`;
        }
    </script>

    <script type="module" src="../../components/user-navbar.js?v=1.2"></script>
</body>
</html>
