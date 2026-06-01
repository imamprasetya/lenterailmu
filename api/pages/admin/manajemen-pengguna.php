<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lentera Ilmu Admin - Manajemen Pengguna</title>
    <link rel="stylesheet" href="../../assets/css/global.css?v=1.2">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="manajemen-pengguna.css?v=1.2">
</head>
<body>
    <div id="admin-header-container"></div>
    <div class="dashboard-wrapper">
        <div id="sidebar-container"></div>
        <main class="dashboard-content">
            <!-- HEADER -->
            <div class="content-header">
                <div>
                    <div class="title-with-badge">
                        <h1>Manajemen Pengguna</h1>
                        <span class="count-badge" id="countBadge">-</span>
                    </div>
                </div>
                <div class="header-search-area">
                    <input type="text" placeholder="Cari nama, email, atau institusi..." class="table-search" id="searchUser">
                    <select class="filter-select" id="filterRole">
                        <option value="">Semua Peran</option>
                        <option value="admin">Admin</option>
                        <option value="user">Anggota</option>
                    </select>
                </div>
            </div>

            <!-- TABEL -->
            <div class="table-card">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>PENGGUNA</th>
                            <th>EMAIL</th>
                            <th>INSTITUSI</th>
                            <th>PERAN</th>
                            <th>STATUS</th>
                            <th>TERDAFTAR</th>
                            <th>AKSI</th>
                        </tr>
                    </thead>
                    <tbody id="userBody">
                        <tr><td colspan="7" style="text-align:center;padding:40px;color:var(--text-light);">Memuat data...</td></tr>
                    </tbody>
                </table>
                <div class="table-footer">
                    <span id="showingUsers">-</span>
                </div>
            </div>

            <!-- INSIGHT CARDS -->
            <div class="insight-grid">
                <div class="insight-card">
                    <div class="insight-info">
                        <small>TOTAL PENGGUNA</small>
                        <h2 id="totalUsers">-</h2>
                        <p>Pengguna terdaftar</p>
                    </div>
                    <span class="insight-icon"><i class="fa-solid fa-users"></i></span>
                </div>
                <div class="insight-card">
                    <div class="insight-info">
                        <small>ADMINISTRATOR</small>
                        <h2 id="totalAdmins">-</h2>
                        <p>Pengelola sistem</p>
                    </div>
                    <span class="insight-icon"><i class="fa-solid fa-user-shield"></i></span>
                </div>
                <div class="insight-card">
                    <div class="insight-info">
                        <small>ANGGOTA PERPUSTAKAAN</small>
                        <h2 id="totalMembers">-</h2>
                        <p>Siswa / Dosen / Umum</p>
                    </div>
                    <span class="insight-icon"><i class="fa-solid fa-user"></i></span>
                </div>
            </div>
        </main>
    </div>

    <!-- MODAL EDIT PENGGUNA -->
    <div class="modal-overlay" id="modalUser" style="display:none;">
        <div class="modal-box">
            <div class="modal-header">
                <h2>Ubah Profil & Hak Akses</h2>
                <button class="modal-close" id="btnCloseModal">&times;</button>
            </div>
            <form id="formUser">
                <div class="input-group">
                    <label>Nama Lengkap</label>
                    <input type="text" id="inputNama" required placeholder="Nama lengkap">
                </div>
                <div class="input-group">
                    <label>Institusi</label>
                    <input type="text" id="inputInstitusi" required placeholder="Institusi">
                </div>
                <div class="input-group">
                    <label>Peran (Role)</label>
                    <select id="inputRole" required>
                        <option value="user">Anggota (User)</option>
                        <option value="admin">Administrator</option>
                    </select>
                </div>
                <div class="input-group">
                    <label>Status</label>
                    <select id="inputStatus" required>
                        <option value="aktif">Aktif</option>
                        <option value="nonaktif">Nonaktif</option>
                    </select>
                </div>
                <input type="hidden" id="editUserId" value="">
                <div class="modal-actions">
                    <button type="button" class="btn-cancel" id="btnBatalModal">Batal</button>
                    <button type="submit" class="btn-save">
                        <span class="btn-text" id="btnSaveText">Simpan Perubahan</span>
                        <span class="btn-loading" id="btnSaveLoading" style="display:none;">Menyimpan...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script type="module">
        import { 
            db, cekAdmin, collection, getDocs, updateDoc, deleteDoc, doc, query, orderBy, formatTanggal
        } from '../../firebase/firebase-config.js';

        let semuaUser = [];

        cekAdmin().then(async () => {
            await loadUsers();
        });

        async function loadUsers() {
            try {
                const snap = await getDocs(collection(db, 'users'));
                semuaUser = [];
                snap.forEach(d => semuaUser.push({ id: d.id, ...d.data() }));
                render();
            } catch (error) {
                console.error("Gagal mengambil data user: ", error);
            }
        }

        // === Event Listeners ===
        document.getElementById('searchUser').addEventListener('input', () => render());
        document.getElementById('filterRole').addEventListener('change', () => render());

        function render() {
            const cari = document.getElementById('searchUser').value.toLowerCase();
            const filter = document.getElementById('filterRole').value;

            const filtered = semuaUser.filter(u => {
                const cocokCari = !cari || 
                    (u.nama || '').toLowerCase().includes(cari) || 
                    (u.email || '').toLowerCase().includes(cari) || 
                    (u.institusi || '').toLowerCase().includes(cari);
                const cocokFilter = !filter || u.role === filter;
                return cocokCari && cocokFilter;
            });

            // Metrik counts
            const total = semuaUser.length;
            const admins = semuaUser.filter(u => u.role === 'admin').length;
            const members = semuaUser.filter(u => u.role !== 'admin').length;

            document.getElementById('countBadge').textContent = `${semuaUser.length} Pengguna`;
            document.getElementById('showingUsers').textContent = `Menampilkan ${filtered.length} dari ${semuaUser.length} pengguna`;

            document.getElementById('totalUsers').textContent = total;
            document.getElementById('totalAdmins').textContent = admins;
            document.getElementById('totalMembers').textContent = members;

            if (filtered.length === 0) {
                document.getElementById('userBody').innerHTML = '<tr><td colspan="7" style="text-align:center;padding:40px;color:var(--text-light);">Tidak ada pengguna ditemukan.</td></tr>';
                return;
            }

            document.getElementById('userBody').innerHTML = filtered.map(u => {
                const inisial = (u.nama || '?').charAt(0).toUpperCase();
                const tglDaftar = u.tanggalDaftar ? formatTanggal(u.tanggalDaftar) : '-';
                const roleClass = u.role === 'admin' ? 'admin' : 'member';
                const roleText = u.role === 'admin' ? 'ADMIN' : 'ANGGOTA';
                const statusClass = u.status === 'nonaktif' ? 'nonaktif' : 'aktif';
                const statusText = u.status === 'nonaktif' ? 'Nonaktif' : 'Aktif';
                return `<tr>
                    <td>
                        <div class="user-cell">
                            ${u.foto 
                                ? `<img src="${u.foto}" class="user-avatar" style="object-fit: cover; border: none;">` 
                                : `<div class="user-avatar">${inisial}</div>`}
                            <div>
                                <strong>${u.nama || '-'}</strong>
                                <span class="id-sub">ID: ${u.id.substring(0, 8)}...</span>
                            </div>
                        </div>
                    </td>
                    <td>${u.email || '-'}</td>
                    <td>${u.institusi || '-'}</td>
                    <td><span class="role-badge ${roleClass}">${roleText}</span></td>
                    <td><span class="status-pill ${statusClass}">${statusText}</span></td>
                    <td>${tglDaftar}</td>
                    <td>
                        <div class="action-icons">
                            <button class="icon-btn" title="Lihat Riwayat Peminjaman" style="background:#eff6ff; border-color:#dbeafe; color:var(--primary);" onclick="lihatRiwayat('${u.id}', '${(u.nama||'').replace(/'/g,"\\'")}')"><i class="fa-solid fa-book"></i></button>
                            <button class="icon-btn" title="Edit" onclick="editUser('${u.id}')"><i class="fa-solid fa-pen-to-square"></i></button>
                            <button class="icon-btn danger" title="Hapus" onclick="hapusUser('${u.id}','${(u.nama||'').replace(/'/g,"\\'")}')"><i class="fa-solid fa-trash-can"></i></button>
                        </div>
                    </td>
                </tr>`;
            }).join('');
        }

        // === LIHAT RIWAYAT ===
        window.lihatRiwayat = function(userId, userName) {
            window.location.href = `riwayat.php?userId=${userId}&userName=${encodeURIComponent(userName)}`;
        };

        // === MODAL ===
        const modal = document.getElementById('modalUser');
        const openModal = () => modal.style.display = 'flex';
        const closeModal = () => { modal.style.display = 'none'; document.getElementById('formUser').reset(); document.getElementById('editUserId').value = ''; };

        document.getElementById('btnCloseModal').addEventListener('click', closeModal);
        document.getElementById('btnBatalModal').addEventListener('click', closeModal);
        modal.addEventListener('click', (e) => { if (e.target === modal) closeModal(); });

        // === SUBMIT EDIT ===
        document.getElementById('formUser').addEventListener('submit', async (e) => {
            e.preventDefault();
            const saveText = document.getElementById('btnSaveText');
            const saveLoading = document.getElementById('btnSaveLoading');
            saveText.style.display = 'none';
            saveLoading.style.display = 'inline';

            const editId = document.getElementById('editUserId').value;
            const data = {
                nama: document.getElementById('inputNama').value.trim(),
                institusi: document.getElementById('inputInstitusi').value.trim(),
                role: document.getElementById('inputRole').value,
                status: document.getElementById('inputStatus').value
            };

            try {
                await updateDoc(doc(db, 'users', editId), data);
                closeModal();
                await loadUsers();
            } catch (error) {
                alert('Gagal memperbarui pengguna: ' + error.message);
            }
            saveText.style.display = 'inline';
            saveLoading.style.display = 'none';
        });

        // === EDIT USER ===
        window.editUser = function(id) {
            const u = semuaUser.find(user => user.id === id);
            if (!u) return;

            document.getElementById('editUserId').value = id;
            document.getElementById('inputNama').value = u.nama || '';
            document.getElementById('inputInstitusi').value = u.institusi || '';
            document.getElementById('inputRole').value = u.role || 'user';
            document.getElementById('inputStatus').value = u.status || 'aktif';

            openModal();
        };

        // === HAPUS USER ===
        window.hapusUser = async function(id, nama) {
            if (!confirm(`Apakah Anda yakin ingin menghapus pengguna "${nama}" dari database? Tindakan ini tidak dapat dibatalkan.`)) return;
            try {
                await deleteDoc(doc(db, 'users', id));
                await loadUsers();
            } catch (error) {
                alert('Gagal menghapus pengguna: ' + error.message);
            }
        };
    </script>
    <script type="module" src="../../components/admin-sidebar.js?v=1.2"></script>
</body>
</html>
