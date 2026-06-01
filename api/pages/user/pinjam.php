<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lentera Ilmu - Konfirmasi Peminjaman</title>
    <link rel="stylesheet" href="../../assets/css/global.css?v=1.2">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="pinjam.css">
</head>
<body>

    <!-- NAVBAR GLOBAL -->
    <div id="navbar-container"></div>

    <main class="container">
        <div class="breadcrumb">
            <a href="katalog.php">Katalog</a> &gt; <span id="breadcrumbTitle">Detail Buku</span> &gt; <span>Pinjam</span>
        </div>

        <h1 class="page-title">Konfirmasi Peminjaman</h1>

        <!-- STEPPER -->
        <div class="stepper">
            <div class="step done"><span class="step-num">1</span> Pilih Buku</div>
            <div class="step-line active"></div>
            <div class="step active"><span class="step-num">2</span> Info Peminjaman</div>
            <div class="step-line"></div>
            <div class="step"><span class="step-num">3</span> Selesai</div>
        </div>

        <div class="borrow-layout">
            <!-- PREVIEW BUKU -->
            <aside class="book-preview-card" id="bookPreview">
                <div class="book-cover-large">
                    <div class="cover-design" id="coverDesign">
                        <h3>Memuat...</h3>
                    </div>
                </div>
                <h2 id="previewTitle">-</h2>
                <p class="author-name" id="previewAuthor">-</p>

                <div class="tags" id="previewTags"></div>

                <div class="status-stock">
                    <span>Status</span>
                    <strong class="text-green" id="previewStatus">-</strong>
                </div>

                <div class="policy-box">
                    <span><i class="fa-solid fa-circle-info" style="color: var(--primary); font-size: 18px;"></i></span>
                    <p><strong>Kebijakan Peminjaman:</strong> Anda dapat meminjam hingga 5 buku sekaligus. Pengembalian harus dilakukan sebelum pukul 18:00 di tanggal jatuh tempo.</p>
                </div>
            </aside>

            <!-- FORM PEMINJAMAN -->
            <section class="form-card">
                <h3><i class="fa-solid fa-calendar-days" style="margin-right: 8px; color: var(--primary);"></i> Durasi & Pengambilan</h3>

                <div class="form-grid">
                    <div class="input-group">
                        <label>Tanggal Pinjam (Hari Ini)</label>
                        <input type="text" id="tanggalPinjam" disabled class="input-disabled">
                    </div>
                    <div class="input-group">
                        <label>Tanggal Pengembalian</label>
                        <input type="date" id="tanggalKembali" class="input-date">
                        <small class="hint-text">Default: 14 hari masa peminjaman</small>
                    </div>
                </div>

                <label class="section-label">Metode Pengambilan</label>
                <div class="method-grid">
                    <div class="method-option active">
                        <input type="radio" name="metode" id="meja" value="Meja Perpustakaan" checked>
                        <label for="meja">
                            <strong><i class="fa-solid fa-shop" style="margin-right: 6px;"></i> Meja Perpustakaan</strong>
                            <p>Ambil di resepsionis utama saat jam operasional.</p>
                        </label>
                    </div>
                    <div class="method-option">
                        <input type="radio" name="metode" id="loker" value="Loker Pintar">
                        <label for="loker">
                            <strong><i class="fa-solid fa-key" style="margin-right: 6px;"></i> Loker Pintar</strong>
                            <p>Ambil kapan saja dari Loker menggunakan kartu anggota.</p>
                        </label>
                    </div>
                </div>

                <div class="agreement-check">
                    <input type="checkbox" id="agree">
                    <label for="agree">Saya mengkonfirmasi bahwa saya bertanggung jawab untuk mengembalikan buku ini sebelum tanggal jatuh tempo. Saya memahami bahwa denda berlaku untuk keterlambatan.</label>
                </div>

                <div class="form-actions">
                    <a href="katalog.php" class="btn-back"><i class="fa-solid fa-arrow-left" style="margin-right: 6px;"></i> Batal</a>
                    <button class="btn-confirm" id="btnConfirm" disabled>
                        <span class="btn-text"><i class="fa-solid fa-check" style="margin-right: 6px;"></i> Konfirmasi Pinjam</span>
                        <span class="btn-loading" style="display:none;"><i class="fa-solid fa-circle-notch fa-spin" style="margin-right: 6px;"></i> Memproses...</span>
                    </button>
                </div>
            </section>
        </div>

        <!-- SUCCESS MODAL -->
        <div class="success-modal" id="successModal" style="display:none;">
            <div class="modal-content">
                <div class="modal-icon" style="color: var(--success); margin-bottom: 16px;"><i class="fa-solid fa-circle-check" style="font-size: 54px;"></i></div>
                <h2>Peminjaman Berhasil!</h2>
                <p id="successMessage">-</p>
                <div class="modal-actions">
                    <a href="riwayat.php" class="btn-modal-primary">Lihat Riwayat</a>
                    <a href="katalog.php" class="btn-modal-secondary">Kembali ke Katalog</a>
                </div>
            </div>
        </div>
    </main>

    <script type="module">
        import { 
            auth, db, cekLogin, formatTanggal, formatTanggalInput,
            doc, getDoc, updateDoc, addDoc, collection, Timestamp,
            getDocs, query, where
        } from '../../firebase/firebase-config.js';

        const urlParams = new URLSearchParams(window.location.search);
        const bukuId = urlParams.get('id');

        // Set tanggal default
        const today = new Date();
        const kembaliDefault = new Date(today);
        kembaliDefault.setDate(kembaliDefault.getDate() + 14);

        document.getElementById('tanggalPinjam').value = formatTanggal(today);
        document.getElementById('tanggalKembali').value = formatTanggalInput(kembaliDefault);

        let bukuData = null;

        cekLogin().then(async (user) => {
            if (!bukuId) {
                alert('ID buku tidak ditemukan!');
                window.location.href = 'katalog.php';
                return;
            }

            try {
                const bukuDoc = await getDoc(doc(db, 'buku', bukuId));
                
                if (!bukuDoc.exists()) {
                    alert('Buku tidak ditemukan!');
                    window.location.href = 'katalog.php';
                    return;
                }

                // Cek apakah user sedang meminjam buku ini dan belum dikembalikan
                const peminjamanQuery = query(
                    collection(db, 'peminjaman'),
                    where('userId', '==', user.uid),
                    where('bukuId', '==', bukuId)
                );
                const peminjamanSnapshot = await getDocs(peminjamanQuery);
                let sedangDipinjam = false;
                peminjamanSnapshot.forEach(doc => {
                    const status = doc.data().status;
                    if (status === 'dipinjam' || status === 'proses_kembali' || status === 'terlambat') {
                        sedangDipinjam = true;
                    }
                });

                if (sedangDipinjam) {
                    alert('Anda sedang meminjam buku ini dan belum mengembalikannya. Anda tidak dapat meminjam kembali sebelum buku dikembalikan.');
                    window.location.href = 'riwayat.php';
                    return;
                }

                bukuData = bukuDoc.data();
                const tersedia = (bukuData.stokTersedia || bukuData.stok || 0) > 0;

                // Update preview
                document.getElementById('breadcrumbTitle').textContent = bukuData.judul;
                if (bukuData.gambar) {
                    document.querySelector('.book-cover-large').style.backgroundImage = `url('${bukuData.gambar}')`;
                    document.querySelector('.book-cover-large').style.backgroundColor = bukuData.warnaCover || '#1e293b';
                    document.getElementById('coverDesign').innerHTML = '';
                } else {
                    document.getElementById('coverDesign').innerHTML = `
                        <h3>${(bukuData.judul || '').toUpperCase()}</h3>
                        <p>${(bukuData.penulis || '').toUpperCase()}</p>
                    `;
                    document.querySelector('.book-cover-large').style.background = bukuData.warnaCover || '#1e293b';
                    document.querySelector('.book-cover-large').style.backgroundImage = 'none';
                }
                document.getElementById('previewTitle').textContent = bukuData.judul || '-';
                document.getElementById('previewAuthor').textContent = `${bukuData.penulis || '-'} • ${bukuData.tahunTerbit || '-'}`;
                document.getElementById('previewStatus').textContent = tersedia ? '● Tersedia' : '● Tidak Tersedia';
                document.getElementById('previewStatus').className = tersedia ? 'text-green' : 'text-red';
                document.getElementById('previewTags').innerHTML = `
                    <span class="tag-blue">${bukuData.kategori || 'Umum'}</span>
                    <span class="tag-blue">${bukuData.halaman || '-'} Halaman</span>
                `;

                if (!tersedia) {
                    document.getElementById('btnConfirm').textContent = 'Stok Habis';
                }

            } catch (error) {
                console.error('Gagal memuat buku:', error);
            }

            // === Agreement checkbox ===
            document.getElementById('agree').addEventListener('change', (e) => {
                document.getElementById('btnConfirm').disabled = !e.target.checked;
            });

            // === Method option ===
            document.querySelectorAll('.method-option').forEach(opt => {
                opt.addEventListener('click', () => {
                    document.querySelectorAll('.method-option').forEach(o => o.classList.remove('active'));
                    opt.classList.add('active');
                    opt.querySelector('input[type="radio"]').checked = true;
                });
            });

            // === Submit Peminjaman ===
            document.getElementById('btnConfirm').addEventListener('click', async () => {
                if (!bukuData) return;
                
                const btnText = document.querySelector('.btn-text');
                const btnLoading = document.querySelector('.btn-loading');
                const btnConfirm = document.getElementById('btnConfirm');
                
                btnConfirm.disabled = true;
                btnText.style.display = 'none';
                btnLoading.style.display = 'inline';

                const tanggalKembaliInput = document.getElementById('tanggalKembali').value;
                const metode = document.querySelector('input[name="metode"]:checked').value;

                try {
                    const tglPinjam = new Date();
                    const tglKembali = new Date(tanggalKembaliInput);
                    // Atur jam dan menit kembali agar sama dengan waktu peminjaman sekarang
                    tglKembali.setHours(tglPinjam.getHours(), tglPinjam.getMinutes(), 0, 0);

                    // Simpan peminjaman ke Firestore
                    try {
                        await addDoc(collection(db, 'peminjaman'), {
                            userId: user.uid,
                            namaUser: user.nama || user.displayName || user.email,
                            bukuId: bukuId,
                            judulBuku: bukuData.judul,
                            penulis: bukuData.penulis,
                            warnaCover: bukuData.warnaCover || '#1e293b',
                            gambar: bukuData.gambar || '',
                            tanggalPinjam: Timestamp.fromDate(tglPinjam),
                            tanggalKembali: Timestamp.fromDate(tglKembali),
                            status: 'dipinjam',
                            metode: metode
                        });
                    } catch (dbErr) {
                        console.error('Error addDoc peminjaman:', dbErr);
                        throw new Error('Gagal mencatat data peminjaman di database. (Detail: ' + dbErr.message + ')');
                    }

                    // Kurangi sisa stok buku
                    try {
                        const stokBaru = Math.max(0, (bukuData.stokTersedia || bukuData.stok || 1) - 1);
                        await updateDoc(doc(db, 'buku', bukuId), {
                            stokTersedia: stokBaru
                        });
                    } catch (dbErr) {
                        console.error('Error updateDoc buku:', dbErr);
                        throw new Error('Gagal memperbarui sisa stok buku di database. (Detail: ' + dbErr.message + ')');
                    }

                    // Tampilkan modal sukses
                    document.getElementById('successMessage').textContent = 
                        `Buku "${bukuData.judul}" berhasil dipinjam. Harap dikembalikan sebelum ${formatTanggal(tglKembali)}.`;
                    document.getElementById('successModal').style.display = 'flex';

                } catch (error) {
                    console.error('Gagal meminjam buku:', error);
                    alert('Gagal meminjam buku: ' + error.message);
                    
                    btnConfirm.disabled = false;
                    btnText.style.display = 'inline';
                    btnLoading.style.display = 'none';
                }
            });
        });
    </script>

    <script type="module" src="../../components/user-navbar.js?v=1.2"></script>
</body>
</html>
