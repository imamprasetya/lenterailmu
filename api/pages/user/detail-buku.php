<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lentera Ilmu - Detail Buku</title>
    <link rel="stylesheet" href="../../assets/css/global.css?v=1.2">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="detail-buku.css">
</head>
<body>

    <!-- NAVBAR GLOBAL -->
    <div id="navbar-container"></div>

    <main class="container">
        <a href="katalog.php" class="back-link"><i class="fa-solid fa-arrow-left" style="margin-right: 6px;"></i> Kembali ke Katalog</a>

        <section class="detail-layout" id="detailLayout">
            <!-- LOADING STATE -->
            <div class="detail-loading" id="detailLoading">
                <div class="loading-spinner"></div>
                <p>Memuat detail buku...</p>
            </div>
        </section>

        <!-- BUKU TERKAIT -->
        <section class="related-section" id="relatedSection" style="display:none;">
            <div class="section-header">
                <h3>Buku Terkait</h3>
                <a href="katalog.php" class="view-all">Lihat lainnya →</a>
            </div>
            <div class="related-grid" id="relatedGrid"></div>
        </section>
    </main>

    <script type="module">
        import { 
            db, cekLogin, formatTanggal,
            doc, getDoc, collection, getDocs, query, where, limit
        } from '../../firebase/firebase-config.js';

        const urlParams = new URLSearchParams(window.location.search);
        const bukuId = urlParams.get('id');

        cekLogin().then(async (user) => {
            if (!bukuId) {
                document.getElementById('detailLayout').innerHTML = 
                    '<p class="empty-state">ID buku tidak ditemukan. <a href="katalog.php">Kembali ke katalog</a></p>';
                return;
            }

            try {
                const bukuDoc = await getDoc(doc(db, 'buku', bukuId));
                
                if (!bukuDoc.exists()) {
                    document.getElementById('detailLayout').innerHTML = 
                        '<p class="empty-state">Buku tidak ditemukan. <a href="katalog.php">Kembali ke katalog</a></p>';
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

                const buku = bukuDoc.data();
                const tersedia = (buku.stokTersedia || buku.stok || 0) > 0;

                document.title = `Lentera Ilmu - ${buku.judul}`;

                const coverStyle = buku.gambar 
                    ? `background-image: url('${buku.gambar}'); background-size: cover; background-repeat: no-repeat; background-position: center; background-color: ${buku.warnaCover || '#1e3a5f'};` 
                    : `background: ${buku.warnaCover || '#1e3a5f'};`;
                const coverText = buku.gambar ? '' : `
                    <div class="big-cover-text">
                        <h2>${(buku.judul || '').toUpperCase()}</h2>
                        <p>${(buku.penulis || '').toUpperCase()}</p>
                    </div>
                `;

                document.getElementById('detailLayout').innerHTML = `
                    <div class="detail-left">
                        <div class="big-cover-wrapper" style="${coverStyle}">
                            ${coverText}
                        </div>
                    </div>

                    <div class="detail-right">
                        <div class="status-tags">
                            <span class="tag-mini blue">${buku.kategori || 'Umum'}</span>
                            <span class="tag-mini ${tersedia ? 'green' : 'red'}">
                                ${tersedia ? 'Tersedia' : 'Tidak Tersedia'}
                            </span>
                        </div>

                        <h1 class="book-main-title">${buku.judul || '-'}</h1>
                        <p class="book-main-author">oleh <strong>${buku.penulis || '-'}</strong></p>

                        <div class="specs-grid">
                            <div class="spec-box"><small>TAHUN TERBIT</small><strong>${buku.tahunTerbit || '-'}</strong></div>
                            <div class="spec-box"><small>BAHASA</small><strong>${buku.bahasa || 'Indonesia'}</strong></div>
                            <div class="spec-box"><small>HALAMAN</small><strong>${buku.halaman || '-'}</strong></div>
                            <div class="spec-box"><small>STOK</small><strong class="${tersedia ? 'text-green' : 'text-red'}">${buku.stokTersedia || buku.stok || 0} eksemplar</strong></div>
                        </div>

                        <div class="description-box">
                            <h3>Deskripsi</h3>
                            <p>${buku.deskripsi || 'Deskripsi buku belum tersedia.'}</p>
                        </div>

                        <div class="cta-actions">
                            ${sedangDipinjam 
                                ? `<a href="riwayat.php" class="btn-main-borrow active-borrow" style="background: linear-gradient(135deg, #f59e0b, #d97706); box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);"><i class="fa-solid fa-hourglass-half" style="margin-right: 8px;"></i> Sedang Anda Pinjam (Lihat Riwayat)</a>`
                                : tersedia 
                                    ? `<a href="pinjam.php?id=${bukuId}" class="btn-main-borrow"><i class="fa-solid fa-book-bookmark" style="margin-right: 8px;"></i> Pinjam Buku</a>`
                                    : `<button class="btn-main-borrow disabled" disabled><i class="fa-solid fa-ban" style="margin-right: 8px;"></i> Stok Habis</button>`
                            }
                            <button class="btn-secondary-save"><i class="fa-regular fa-bookmark" style="margin-right: 8px;"></i> Simpan Nanti</button>
                        </div>

                        <div class="perks-list">
                            <span><i class="fa-solid fa-shield-halved" style="color: var(--primary); margin-right: 6px;"></i> Salinan Resmi</span>
                            <span><i class="fa-solid fa-calendar-day" style="color: var(--primary); margin-right: 6px;"></i> Peminjaman 14 Hari</span>
                            <span><i class="fa-solid fa-arrows-rotate" style="color: var(--primary); margin-right: 6px;"></i> Perpanjangan Tersedia</span>
                        </div>
                    </div>
                `;

                // === Load Buku Terkait ===
                if (buku.kategori) {
                    const relatedQuery = query(
                        collection(db, 'buku'),
                        where('kategori', '==', buku.kategori),
                        limit(5)
                    );
                    const relatedSnap = await getDocs(relatedQuery);
                    const relatedGrid = document.getElementById('relatedGrid');
                    let relatedHTML = '';

                    relatedSnap.forEach(rdoc => {
                        if (rdoc.id !== bukuId) {
                            const r = rdoc.data();
                            const coverStyleRelated = r.gambar 
                                ? `background-image: url('${r.gambar}');` 
                                : `background: ${r.warnaCover || '#4682b4'};`;
                            relatedHTML += `
                                <a href="detail-buku.php?id=${rdoc.id}" class="mini-card">
                                    <div class="mini-cover" style="${coverStyleRelated}"></div>
                                    <h4>${r.judul || '-'}</h4>
                                </a>
                            `;
                        }
                    });

                    if (relatedHTML) {
                        relatedGrid.innerHTML = relatedHTML;
                        document.getElementById('relatedSection').style.display = 'block';
                    }
                }

            } catch (error) {
                console.error('Gagal memuat detail buku:', error);
                document.getElementById('detailLayout').innerHTML = 
                    '<p class="empty-state">Terjadi kesalahan. Silakan coba lagi.</p>';
            }
        });
    </script>

    <script type="module" src="../../components/user-navbar.js?v=1.2"></script>
</body>
</html>
