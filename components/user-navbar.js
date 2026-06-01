

import { auth, cekLogin, logout } from '../firebase/firebase-config.js';

// Tentukan halaman aktif berdasarkan URL
const currentPage = window.location.pathname;
function isActive(page) {
    return currentPage.includes(page) ? 'active' : '';
}

// Tunggu data user lalu render navbar
cekLogin().then(user => {
    const namaUser = user.nama || user.displayName || user.email?.split('@')[0] || 'Pengguna';
    const inisial = namaUser.charAt(0).toUpperCase();

    //foto profil
    const avatarContent = user.foto
        ? `<img src="${user.foto}" alt="${namaUser}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">`
        : inisial;

    const navbarHTML = `
    <header class="navbar">
        <div class="nav-left">
            <button class="hamburger-btn" id="userHamburgerBtn" title="Menu" style="background: none; border: none; color: white; font-size: 20px; cursor: pointer; align-items: center; justify-content: center; width: 36px; height: 36px; border-radius: var(--radius-md); transition: var(--transition);">
                <i class="fa-solid fa-bars"></i>
            </button>
            <a href="/lenterailmu/pages/user/dashboard.php" class="logo">
                <img src="../../assets/image/logo.png" alt="Lentera Ilmu Logo" class="logo-img" style="height: 32px; width: auto; object-fit: contain;">
                Lentera Ilmu
            </a>
            <nav id="userNavLinks">
                <!-- Mobile Search Bar (Only visible on mobile) -->
                <div class="mobile-search-wrapper">
                    <form id="mobileNavSearchForm" class="search-wrapper-nav">
                        <button type="submit" class="search-icon-btn">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </button>
                        <input type="text" placeholder="Cari buku..." class="search-bar-nav" id="mobileGlobalSearch">
                    </form>
                </div>

                <a href="/lenterailmu/pages/user/dashboard.php" class="${isActive('dashboard')}">
                    <i class="fa-solid fa-house mobile-icon"></i>Beranda
                </a>
                <a href="/lenterailmu/pages/user/katalog.php" class="${isActive('katalog')}">
                    <i class="fa-solid fa-book-open mobile-icon"></i>Katalog
                </a>
                <a href="/lenterailmu/pages/user/riwayat.php" class="${isActive('riwayat')}">
                    <i class="fa-solid fa-clock-rotate-left mobile-icon"></i>Riwayat
                </a>

                <!-- Mobile Profile & Logout (Only visible on mobile) -->
                <hr class="mobile-divider">
                <a href="/lenterailmu/pages/user/profile.php" class="mobile-profile-link">
                    <i class="fa-solid fa-user mobile-icon"></i>Profil Saya
                </a>
                <a href="#" id="btnMobileLogout" class="mobile-logout-link">
                    <i class="fa-solid fa-right-from-bracket mobile-icon"></i>Keluar
                </a>
            </nav>
        </div>
        <div class="nav-right">
            <form id="navSearchForm" class="search-wrapper-nav" style="position: relative; display: flex; align-items: center; margin-right: 16px;">
                <button type="submit" class="search-icon-btn" style="position: absolute; left: 8px; border: none; background: transparent; color: #cbd5e1; cursor: pointer; padding: 0; display: flex; align-items: center; justify-content: center;">
                    <i class="fa-solid fa-magnifying-glass" style="font-size: 14px;"></i>
                </button>
                <input type="text" placeholder="Cari buku, penulis..." class="search-bar-nav" id="globalSearch" style="padding-left: 32px;">
            </form>
            <div class="nav-icons">
                <button class="nav-icon-btn" title="Notifikasi">
                    <i class="fa-regular fa-bell"></i>
                    <span class="notif-dot"></span>
                </button>
            </div>
            <div class="profile-area" id="profileDropdown">
                <div class="profile-pic">${avatarContent}</div>
                <span class="profile-name">${namaUser}</span>
            </div>
            <button class="nav-icon-btn" title="Keluar" id="btnLogout"><i class="fa-solid fa-right-from-bracket"></i></button>
        </div>
    </header>
    `;

    const container = document.getElementById('navbar-container');
    if (container) {
        container.innerHTML = navbarHTML;

        // Profile dropdown
        const profileArea = document.getElementById('profileDropdown');
        profileArea.addEventListener('click', () => {
            window.location.href = '/lenterailmu/pages/user/profile.php';
        });
        profileArea.style.cursor = 'pointer';

        // Hamburger toggle
        const hamburgerBtn = document.getElementById('userHamburgerBtn');
        const navLinks = document.getElementById('userNavLinks');
        if (hamburgerBtn && navLinks) {
            hamburgerBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                navLinks.classList.toggle('show-mobile-nav');
                const isOpen = navLinks.classList.contains('show-mobile-nav');
                hamburgerBtn.innerHTML = isOpen
                    ? `<i class="fa-solid fa-xmark"></i>`
                    : `<i class="fa-solid fa-bars"></i>`;
            });

            // Close menu when clicking outside
            document.addEventListener('click', (e) => {
                if (navLinks.classList.contains('show-mobile-nav') && !navLinks.contains(e.target) && e.target !== hamburgerBtn) {
                    navLinks.classList.remove('show-mobile-nav');
                    hamburgerBtn.innerHTML = `<i class="fa-solid fa-bars"></i>`;
                }
            });
        }

        // Logout (Desktop & Mobile)
        const btnLogout = document.getElementById('btnLogout');
        if (btnLogout) {
            btnLogout.addEventListener('click', () => {
                if (confirm('Apakah Anda yakin ingin keluar?')) {
                    logout();
                }
            });
        }

        const btnMobileLogout = document.getElementById('btnMobileLogout');
        if (btnMobileLogout) {
            btnMobileLogout.addEventListener('click', (e) => {
                e.preventDefault();
                if (confirm('Apakah Anda yakin ingin keluar?')) {
                    logout();
                }
            });
        }

        // Search global (Desktop)
        const searchForm = document.getElementById('navSearchForm');
        const searchInput = document.getElementById('globalSearch');
        if (searchForm && searchInput) {
            function submitNavbarSearch() {
                const query = searchInput.value.trim();
                if (query) {
                    window.location.href = `/lenterailmu/pages/user/katalog.php?cari=${encodeURIComponent(query)}`;
                }
            }

            searchForm.addEventListener('submit', (e) => {
                e.preventDefault();
                submitNavbarSearch();
            });

            searchInput.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    submitNavbarSearch();
                }
            });
        }

        // Search global (Mobile)
        const mobileSearchForm = document.getElementById('mobileNavSearchForm');
        const mobileSearchInput = document.getElementById('mobileGlobalSearch');
        if (mobileSearchForm && mobileSearchInput) {
            function submitMobileNavbarSearch() {
                const query = mobileSearchInput.value.trim();
                if (query) {
                    window.location.href = `/lenterailmu/pages/user/katalog.php?cari=${encodeURIComponent(query)}`;
                }
            }

            mobileSearchForm.addEventListener('submit', (e) => {
                e.preventDefault();
                submitMobileNavbarSearch();
            });

            mobileSearchInput.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    submitMobileNavbarSearch();
                }
            });
        }
    }
}).catch(err => {
    console.log('Navbar: User belum login', err);
});
