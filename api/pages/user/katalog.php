<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lentera Ilmu - Katalog Buku</title>
    <link rel="stylesheet" href="../../assets/css/global.css?v=1.2">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="katalog.css">
</head>
<body>

    <!-- NAVBAR GLOBAL -->
    <div id="navbar-container"></div>

    <main class="container">
        <!-- SEARCH & FILTER -->
        <section class="search-section">
            <div style="position: relative; width: 100%; display: flex; align-items: center;">
                <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 16px; color: var(--text-muted); font-size: 16px;"></i>
                <input type="text" class="main-search" id="searchInput" placeholder="Cari buku atau penulis..." style="padding-left: 45px; width: 100%;">
            </div>

            <div class="filter-container">
                <div class="filter-tags" id="filterTags">
                    <span class="tag active" data-kategori="semua">Semua Kategori</span>
                    <span class="tag" data-kategori="Fiksi">Fiksi</span>
                    <span class="tag" data-kategori="Sains">Sains</span>
                    <span class="tag" data-kategori="Sejarah">Sejarah</span>
                    <span class="tag" data-kategori="Filsafat">Filsafat</span>
                    <span class="tag" data-kategori="Teknologi">Teknologi</span>
                    <span class="tag" data-kategori="Kesehatan">Kesehatan</span>
                    <span class="tag" data-kategori="Psikologi">Psikologi</span>
                    <span class="tag" data-kategori="Ekonomi">Ekonomi</span>
                    <span class="tag" data-kategori="Politik">Politik</span>
                    <span class="tag" data-kategori="Agama">Agama</span>
                    <span class="tag" data-kategori="Pemrograman">Pemrograman</span>
                </div>
            </div>
        </section>

        <!-- KATALOG GRID -->
        <section class="catalog-grid" id="catalogGrid">
            <div class="loading-placeholder">Memuat katalog buku...</div>
        </section>

        <!-- PAGINATION -->
        <footer class="pagination-container" id="paginationContainer">
            <span class="showing-text" id="showingText">-</span>
        </footer>
    </main>

    <script type="module">
        import { 
            db, cekLogin, collection, getDocs, query, orderBy 
        } from '../../firebase/firebase-config.js';

        let semuaBuku = [];
        let kategoriAktif = 'semua';
        let pencarian = '';

        cekLogin().then(async () => {
            try {
                // Load semua buku dari Firestore
                const bukuQuery = query(collection(db, 'buku'), orderBy('judul'));
                const snapshot = await getDocs(bukuQuery);
                
                semuaBuku = [];
                snapshot.forEach(doc => {
                    semuaBuku.push({ id: doc.id, ...doc.data() });
                });

                renderKatalog();
            } catch (error) {
                console.error('Gagal memuat katalog:', error);
                document.getElementById('catalogGrid').innerHTML = 
                    '<p class="loading-placeholder">Gagal memuat data. Silakan muat ulang halaman.</p>';
            }
        });

        // === Filter Kategori ===
        document.getElementById('filterTags').addEventListener('click', (e) => {
            if (e.target.classList.contains('tag')) {
                document.querySelectorAll('.tag').forEach(t => t.classList.remove('active'));
                e.target.classList.add('active');
                kategoriAktif = e.target.dataset.kategori;
                renderKatalog();
            }
        });

        // === Pencarian ===
        document.getElementById('searchInput').addEventListener('input', (e) => {
            pencarian = e.target.value.toLowerCase();
            renderKatalog();
        });

        // Cek URL params untuk pencarian dari navbar
        const urlParams = new URLSearchParams(window.location.search);
        const cariParam = urlParams.get('cari');
        if (cariParam) {
            document.getElementById('searchInput').value = cariParam;
            pencarian = cariParam.toLowerCase();
        }

        function renderKatalog() {
            const grid = document.getElementById('catalogGrid');
            
            let bukuFiltered = semuaBuku.filter(buku => {
                const cocokKategori = kategoriAktif === 'semua' || buku.kategori === kategoriAktif;
                const cocokCari = !pencarian || 
                    (buku.judul || '').toLowerCase().includes(pencarian) ||
                    (buku.penulis || '').toLowerCase().includes(pencarian);
                return cocokKategori && cocokCari;
            });

            if (bukuFiltered.length === 0) {
                grid.innerHTML = '<p class="loading-placeholder">Tidak ada buku ditemukan.</p>';
                document.getElementById('showingText').textContent = '0 buku';
                return;
            }

            grid.innerHTML = bukuFiltered.map(buku => {
                const tersedia = (buku.stokTersedia || buku.stok || 0) > 0;
                const coverStyle = buku.gambar 
                    ? `background-image: url('${buku.gambar}'); background-color: ${buku.warnaCover || '#2b4554'};` 
                    : `background: ${buku.warnaCover || '#2b4554'};`;
                const coverText = buku.gambar ? '' : `<div class="book-cover-mock">${buku.judul || ''}</div>`;
                return `
                 <a href="detail-buku.php?id=${buku.id}" class="catalog-card">
                    <div class="card-image-wrapper">
                        <div class="book-cover-portrait" style="${coverStyle}">
                            ${coverText}
                        </div>
                    </div>
                    <div class="card-info">
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                            <small class="meta-cat">${buku.kategori || 'Umum'}</small>
                            <span class="badge-status ${tersedia ? 'in-stock' : 'on-loan'}">
                                ${tersedia ? 'Tersedia' : 'Dipinjam'}
                            </span>
                        </div>
                        <h4>${buku.judul || '-'}</h4>
                        <p class="author">${buku.penulis || '-'}</p>
                        <span class="btn-action ${tersedia ? 'blue' : 'gray'}">
                            ${tersedia ? '<i class="fa-solid fa-book-bookmark" style="margin-right: 6px;"></i> Pinjam' : '<i class="fa-solid fa-hourglass-half" style="margin-right: 6px;"></i> Antrian'}
                        </span>
                    </div>
                </a>
                `;
            }).join('');

            document.getElementById('showingText').textContent = 
                `Menampilkan ${bukuFiltered.length} dari ${semuaBuku.length} buku`;
        }
    </script>

    <script type="module" src="../../components/user-navbar.js?v=1.2"></script>
</body>
</html>
