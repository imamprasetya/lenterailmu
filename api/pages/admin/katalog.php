<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lentera Ilmu Admin - Katalog</title>
    <link rel="stylesheet" href="../../assets/css/global.css?v=1.2">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="katalog.css">
</head>
<body>
    <div id="admin-header-container"></div>
    <div class="dashboard-wrapper">
        <div id="sidebar-container"></div>
        <main class="dashboard-content">
            <section class="search-filter-area">
                <div style="position: relative; width: 100%; display: flex; align-items: center;">
                    <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 16px; color: var(--text-muted); font-size: 16px;"></i>
                    <input type="text" placeholder="Cari katalog berdasarkan judul, penulis, kode..." class="big-search" id="searchKatalog" style="padding-left: 45px; width: 100%;">
                </div>
                <div class="tag-row-wrapper">
                    <button class="scroll-arrow scroll-left" id="scrollLeft" aria-label="Geser kiri"><i class="fa-solid fa-chevron-left"></i></button>
                    <div class="tag-row" id="tagRow">
                        <span class="pill-tag active" data-kategori="semua">Semua Kategori</span>
                        <span class="pill-tag" data-kategori="Fiksi">Fiksi</span>
                        <span class="pill-tag" data-kategori="Sains">Sains</span>
                        <span class="pill-tag" data-kategori="Sejarah">Sejarah</span>
                        <span class="pill-tag" data-kategori="Filsafat">Filsafat</span>
                        <span class="pill-tag" data-kategori="Teknologi">Teknologi</span>
                        <span class="pill-tag" data-kategori="Kesehatan">Kesehatan</span>
                        <span class="pill-tag" data-kategori="Psikologi">Psikologi</span>
                        <span class="pill-tag" data-kategori="Ekonomi">Ekonomi</span>
                        <span class="pill-tag" data-kategori="Politik">Politik</span>
                        <span class="pill-tag" data-kategori="Agama">Agama</span>
                        <span class="pill-tag" data-kategori="Pemrograman">Pemrograman</span>
                    </div>
                    <button class="scroll-arrow scroll-right" id="scrollRight" aria-label="Geser kanan"><i class="fa-solid fa-chevron-right"></i></button>
                </div>
            </section>

            <section class="admin-catalog-grid" id="catalogGrid">
                <div style="grid-column:1/-1;text-align:center;padding:60px;color:var(--text-light);">Memuat katalog...</div>
            </section>
        </main>
    </div>

    <script type="module">
        import { db, cekAdmin, collection, getDocs, query, orderBy } from '../../firebase/firebase-config.js';

        let semuaBuku = [];
        let filter = 'semua';

        cekAdmin().then(async () => {
            const urlParams = new URLSearchParams(window.location.search);
            const cariParam = urlParams.get('cari');
            if (cariParam) {
                document.getElementById('searchKatalog').value = cariParam;
            }

            const snap = await getDocs(query(collection(db, 'buku'), orderBy('judul')));
            semuaBuku = [];
            snap.forEach(d => semuaBuku.push({ id: d.id, ...d.data() }));
            render();
        });

        document.getElementById('tagRow').addEventListener('click', e => {
            if (e.target.classList.contains('pill-tag')) {
                document.querySelectorAll('.pill-tag').forEach(t => t.classList.remove('active'));
                e.target.classList.add('active');
                filter = e.target.dataset.kategori;
                render();
            }
        });

        document.getElementById('searchKatalog').addEventListener('input', () => render());

        function render() {
            const cari = document.getElementById('searchKatalog').value.toLowerCase();
            const filtered = semuaBuku.filter(b => {
                const cocok = filter === 'semua' || b.kategori === filter;
                const cocokCari = !cari || (b.judul||'').toLowerCase().includes(cari) || (b.penulis||'').toLowerCase().includes(cari);
                return cocok && cocokCari;
            });

            const grid = document.getElementById('catalogGrid');
            if (filtered.length === 0) {
                grid.innerHTML = '<div style="grid-column:1/-1;text-align:center;padding:60px;color:var(--text-light);">Tidak ada buku ditemukan.</div>';
                return;
            }

            grid.innerHTML = filtered.map(b => {
                const tersedia = (b.stokTersedia || b.stok || 0) > 0;
                const coverStyle = b.gambar 
                    ? `background-image: url('${b.gambar}'); background-color: ${b.warnaCover || '#1d3557'};` 
                    : `background: ${b.warnaCover || '#1d3557'};`;
                const titleOverlay = b.gambar ? '' : `<div class="inner-title">${b.judul || '-'}</div>`;
                return `
                 <div class="admin-book-card" onclick="window.location.href='../user/detail-buku.php?id=${b.id}'" style="cursor: pointer;">
                    <div class="cover-wrapper">
                        <div class="book-cover-portrait" style="${coverStyle}">
                            ${titleOverlay}
                        </div>
                    </div>
                    <div class="badge-status-wrapper">
                        <span class="status-tag ${tersedia ? 'available' : 'checkout'}">${tersedia ? 'Tersedia' : 'Dipinjam'}</span>
                    </div>
                    <div class="info-body">
                        <div class="info-header-row">
                            <small>${b.kategori || 'Umum'}</small>
                            <span class="status-tag badge-desktop ${tersedia ? 'available' : 'checkout'}">${tersedia ? 'Tersedia' : 'Dipinjam'}</span>
                        </div>
                        <h4>${b.judul || '-'}</h4>
                        <p>${b.penulis || '-'}</p>
                        <div class="control-row">
                            <span class="stock-info">Stok: ${b.stokTersedia || b.stok || 0}</span>
                        </div>
                    </div>
                </div>`;
            }).join('');
        }
    </script>

    <script>
        // === Drag-to-scroll & Arrow buttons for tag row ===
        (function() {
            const slider = document.getElementById('tagRow');
            const btnLeft = document.getElementById('scrollLeft');
            const btnRight = document.getElementById('scrollRight');
            let isDown = false, startX, scrollLeft;

            slider.addEventListener('mousedown', (e) => {
                isDown = true;
                startX = e.pageX;
                scrollLeft = slider.scrollLeft;
                slider.style.cursor = 'grabbing';
            });
            slider.addEventListener('mouseleave', () => { isDown = false; slider.style.cursor = 'grab'; });
            slider.addEventListener('mouseup', () => { isDown = false; slider.style.cursor = 'grab'; });
            slider.addEventListener('mousemove', (e) => {
                if (!isDown) return;
                e.preventDefault();
                const walk = (e.pageX - startX) * 2;
                slider.scrollLeft = scrollLeft - walk;
            });

            const scrollAmount = 200;
            btnLeft.addEventListener('click', () => {
                slider.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
            });
            btnRight.addEventListener('click', () => {
                slider.scrollBy({ left: scrollAmount, behavior: 'smooth' });
            });

            function updateArrows() {
                btnLeft.style.display = slider.scrollLeft > 0 ? 'flex' : 'none';
                btnRight.style.display = slider.scrollLeft < (slider.scrollWidth - slider.clientWidth - 1) ? 'flex' : 'none';
            }
            slider.addEventListener('scroll', updateArrows);
            window.addEventListener('resize', updateArrows);
            setTimeout(updateArrows, 100);
        })();
    </script>

    <script type="module" src="../../components/admin-sidebar.js?v=1.2"></script>
</body>
</html>
