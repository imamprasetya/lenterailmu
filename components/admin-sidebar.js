
import { auth, cekAdmin, logout } from '../firebase/firebase-config.js';

// Tentukan halaman aktif berdasarkan URL
const currentPage = window.location.pathname;
function isActive(page) {
    return currentPage.includes(page) ? 'active' : '';
}

// Cek admin lalu render
cekAdmin().then(user => {
    const namaAdmin = user.nama || user.displayName || user.email?.split('@')[0] || 'Admin';
    const inisial = namaAdmin.charAt(0).toUpperCase();

    // foto profil
    const avatarContent = user.foto
        ? `<img src="${user.foto}" alt="${namaAdmin}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">`
        : inisial;

    // header admin
    const headerHTML = `
    <header class="navbar">
        <div class="nav-left">
            <button class="hamburger-btn" id="adminHamburgerBtn" title="Menu" style="background: none; border: none; color: white; font-size: 20px; display: none; cursor: pointer; align-items: center; justify-content: center; width: 36px; height: 36px; border-radius: var(--radius-md); transition: var(--transition);">
                <i class="fa-solid fa-bars"></i>
            </button>
            <a href="/lenterailmu/pages/admin/dashboard.php" class="logo">
                <img src="../../assets/image/logo.png" alt="Lentera Ilmu Logo" class="logo-img" style="height: 32px; width: auto; object-fit: contain;">
                Lentera Ilmu
            </a>
            <span class="admin-badge">ADMIN PANEL</span>
        </div>
        <div class="nav-right">
            <form id="adminSearchForm" class="search-wrapper-nav" style="position: relative; display: flex; align-items: center;">
                <button type="submit" class="search-icon-btn" style="position: absolute; left: 10px; border: none; background: transparent; color: #cbd5e1; cursor: pointer; padding: 0; display: flex; align-items: center; justify-content: center;">
                    <i class="fa-solid fa-magnifying-glass" style="font-size: 14px;"></i>
                </button>
                <input type="text" placeholder="Cari data..." class="search-bar-nav" id="adminGlobalSearch" style="padding-left: 32px;">
            </form>
            <div class="nav-icons">
                <button class="nav-icon-btn" title="Notifikasi">
                    <i class="fa-regular fa-bell"></i>
                    <span class="notif-dot"></span>
                </button>
            </div>
            <div class="profile-area">
                <div class="profile-pic">${avatarContent}</div>
                <span class="profile-name">${namaAdmin}</span>
            </div>
        </div>
    </header>
    `;

    // sidebar admin
    const sidebarHTML = `
    <aside class="dashboard-sidebar">
        <nav class="side-menu">
            <a href="/lenterailmu/pages/admin/dashboard.php" class="menu-item ${isActive('dashboard')}">
                <i class="fa-solid fa-chart-line" style="margin-right: 8px;"></i> Dashboard
            </a>
            <a href="/lenterailmu/pages/admin/katalog.php" class="menu-item ${isActive('katalog')}">
                <i class="fa-solid fa-book" style="margin-right: 8px;"></i> Katalog
            </a>
            <a href="/lenterailmu/pages/admin/riwayat.php" class="menu-item ${isActive('riwayat')}">
                <i class="fa-solid fa-clock-rotate-left" style="margin-right: 8px;"></i> Riwayat Peminjaman
            </a>
            <a href="/lenterailmu/pages/admin/manajemen-buku.php" class="menu-item ${isActive('manajemen-buku')}">
                <i class="fa-solid fa-book-bookmark" style="margin-right: 8px;"></i> Manajemen Buku
            </a>
            <a href="/lenterailmu/pages/admin/manajemen-pengguna.php" class="menu-item ${isActive('manajemen-pengguna')}">
                <i class="fa-solid fa-users" style="margin-right: 8px;"></i> Manajemen Pengguna
            </a>
        </nav>
        <div class="sidebar-footer">
            <a href="/lenterailmu/pages/admin/profile.php" class="menu-item" title="Edit Profil"><i class="fa-solid fa-user" style="margin-right: 8px;"></i> Edit Profil</a>
            <a href="#" class="menu-item" title="Bantuan"><i class="fa-solid fa-circle-question" style="margin-right: 8px;"></i> Bantuan</a>
            <a href="#" class="menu-item logout" id="btnAdminLogout" title="Keluar"><i class="fa-solid fa-right-from-bracket" style="margin-right: 8px;"></i> Keluar</a>
        </div>
    </aside>
    `;

    // Inject Header
    const headerContainer = document.getElementById('admin-header-container');
    if (headerContainer) {
        headerContainer.innerHTML = headerHTML;

        const profileArea = document.querySelector('.profile-area');
        if (profileArea) {
            profileArea.style.cursor = 'pointer';
            profileArea.addEventListener('click', () => {
                window.location.href = '/lenterailmu/pages/admin/profile.php';
            });
        }

        // Admin Hamburger
        const adminHamburgerBtn = document.getElementById('adminHamburgerBtn');
        if (adminHamburgerBtn) {
            adminHamburgerBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                const sidebar = document.querySelector('.dashboard-sidebar');
                if (sidebar) {
                    sidebar.classList.toggle('show-mobile-sidebar');
                    const isOpen = sidebar.classList.contains('show-mobile-sidebar');
                    adminHamburgerBtn.innerHTML = isOpen
                        ? `<i class="fa-solid fa-xmark"></i>`
                        : `<i class="fa-solid fa-bars"></i>`;
                }
            });

            // Close sidebar when clicking outside
            document.addEventListener('click', (e) => {
                const sidebar = document.querySelector('.dashboard-sidebar');
                if (sidebar && sidebar.classList.contains('show-mobile-sidebar')) {
                    if (!sidebar.contains(e.target) && e.target !== adminHamburgerBtn) {
                        sidebar.classList.remove('show-mobile-sidebar');
                        adminHamburgerBtn.innerHTML = `<i class="fa-solid fa-bars"></i>`;
                    }
                }
            });
        }

        const searchForm = document.getElementById('adminSearchForm');
        const searchInput = document.getElementById('adminGlobalSearch');

        if (searchForm && searchInput) {
            const submitAdminSearch = () => {
                const query = searchInput.value.trim();
                if (query) {
                    window.location.href = `/lenterailmu/pages/admin/katalog.php?cari=${encodeURIComponent(query)}`;
                }
            };

            searchForm.addEventListener('submit', (e) => {
                e.preventDefault();
                submitAdminSearch();
            });

            searchInput.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    submitAdminSearch();
                }
            });
        }
    }

    // Inject Sidebar
    const sidebarContainer = document.getElementById('sidebar-container');
    if (sidebarContainer) {
        sidebarContainer.innerHTML = sidebarHTML;

        // Logout
        document.getElementById('btnAdminLogout').addEventListener('click', (e) => {
            e.preventDefault();
            if (confirm('Apakah Anda yakin ingin keluar?')) {
                logout();
            }
        });
    }
}).catch(err => {
    console.log('Sidebar: Bukan admin atau belum login', err);
});
