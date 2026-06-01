<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lentera Ilmu Admin - Edit Profil</title>
    <link rel="stylesheet" href="../../assets/css/global.css?v=1.2">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
       /* profile styles */
        .profile-container {
            max-width: 500px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        .profile-card {
            background: var(--bg-white);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 40px;
        }

        .profile-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .profile-avatar-section {
            margin-bottom: 20px;
        }

        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            margin: 0 auto 20px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
            font-weight: 700;
            position: relative;
            overflow: hidden;
            border: 3px solid var(--bg-primary);
        }

        .profile-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .upload-btn-wrapper {
            position: absolute;
            bottom: 0;
            right: 0;
            width: 40px;
            height: 40px;
            background: var(--primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            opacity: 0;
            transition: all 0.25s ease;
            box-shadow: 0 2px 8px rgba(37, 99, 235, 0.4);
            z-index: 2;
        }

        .profile-avatar:hover .upload-btn-wrapper {
            opacity: 1;
            transform: scale(1.1);
        }

        .upload-btn-wrapper input[type="file"] {
            display: none;
        }

        .upload-btn-wrapper i {
            color: white;
            font-size: 16px;
            pointer-events: none;
        }

        .profile-avatar-section .photo-hint {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 4px;
        }

        .profile-title {
            font-size: 24px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 8px;
        }

        .profile-subtitle {
            font-size: 14px;
            color: var(--text-muted);
        }

        .admin-badge-info {
            display: inline-block;
            background: rgba(37, 99, 235, 0.15);
            color: var(--primary);
            padding: 4px 12px;
            border-radius: var(--radius-full);
            font-size: 11px;
            font-weight: 600;
            margin-top: 12px;
        }

        .form-section {
            margin-bottom: 32px;
        }

        .form-section h3 {
            font-size: 15px;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 16px;
            padding-bottom: 12px;
            border-bottom: 2px solid var(--bg-primary);
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-secondary);
            margin-bottom: 6px;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            font-size: 14px;
            background: var(--bg-white);
            color: var(--text-primary);
            transition: var(--transition);
            font-family: inherit;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .password-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .password-wrapper input {
            flex: 1;
        }

        .toggle-password {
            position: absolute;
            right: 12px;
            background: transparent;
            border: none;
            cursor: pointer;
            color: var(--text-muted);
            font-size: 16px;
            padding: 4px 8px;
            transition: color 0.2s;
        }

        .toggle-password:hover {
            color: var(--text-primary);
        }

        .form-actions {
            display: flex;
            gap: 12px;
            margin-top: 32px;
        }

        .btn-save {
            flex: 1;
            padding: 12px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            border: none;
            border-radius: var(--radius-md);
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: var(--transition);
        }

        .btn-save:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }

        .btn-cancel {
            flex: 1;
            padding: 12px;
            background: var(--bg-primary);
            color: var(--text-secondary);
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: var(--transition);
        }

        .btn-cancel:hover {
            background: var(--bg-white);
            border-color: var(--text-muted);
            color: var(--text-primary);
        }

        .alert {
            padding: 12px 16px;
            border-radius: var(--radius-md);
            margin-bottom: 16px;
            font-size: 13px;
        }

        .alert-success {
            background: rgba(34, 197, 94, 0.1);
            border: 1px solid rgba(34, 197, 94, 0.3);
            color: #16a34a;
        }

        .alert-error {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #dc2626;
        }

        .alert-info {
            background: rgba(59, 130, 246, 0.1);
            border: 1px solid rgba(59, 130, 246, 0.3);
            color: #2563eb;
        }

        @media (max-width: 640px) {
            .profile-container {
                padding: 20px;
            }

            .profile-card {
                padding: 24px;
            }

            .profile-avatar {
                width: 100px;
                height: 100px;
                font-size: 40px;
            }

            .form-actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>

    <div id="admin-header-container"></div>
    <div class="dashboard-wrapper">
        <div id="sidebar-container"></div>
        <main class="dashboard-content">
            <div class="profile-container" style="margin-top: 20px;">
                <div class="profile-card">
                    <!-- PROFILE HEADER -->
                    <div class="profile-header">
                        <div class="profile-avatar-section">
                            <div class="profile-avatar" id="profileAvatar">
                                <span id="avatarText">A</span>
                                <img id="avatarImage" style="display:none;" alt="Foto profil">
                                <div class="upload-btn-wrapper" id="uploadBtnWrapper">
                                    <input type="file" id="photoInput" accept="image/*">
                                    <i class="fa-solid fa-camera"></i>
                                </div>
                            </div>
                            <p class="photo-hint"><i class="fa-solid fa-image" style="margin-right: 4px;"></i> Klik avatar untuk mengganti foto</p>
                        </div>
                        <h1 class="profile-title" id="profileName">Admin</h1>
                        <p class="profile-subtitle" id="profileEmail">admin@email.com</p>
                        <div class="admin-badge-info"><i class="fa-solid fa-shield-halved" style="margin-right: 4px;"></i> Administrator</div>
                    </div>

                    <!-- ALERTS -->
                    <div id="alertContainer"></div>

                    <!-- FORM -->
                    <form id="profileForm">
                        <!-- INFORMASI DASAR -->
                        <div class="form-section">
                            <h3><i class="fa-solid fa-user" style="margin-right: 8px; color: var(--primary);"></i> Informasi Dasar</h3>

                            <div class="form-group">
                                <label for="nama">Nama Lengkap</label>
                                <input type="text" id="nama" placeholder="Masukkan nama lengkap" required>
                            </div>
                        </div>

                        <!-- AKUN & KEAMANAN -->
                        <div class="form-section">
                            <h3><i class="fa-solid fa-shield-halved" style="margin-right: 8px; color: var(--primary);"></i> Akun & Keamanan</h3>

                            <div class="form-group">
                                <label for="email">Alamat Email</label>
                                <input type="email" id="email" disabled style="background-color: var(--bg-primary); color: var(--text-muted); cursor: not-allowed;" title="Email tidak dapat diubah">
                            </div>

                            <div class="form-group">
                                <label for="passwordCurrent">Kata Sandi Saat Ini</label>
                                <div class="password-wrapper">
                                    <input type="password" id="passwordCurrent" placeholder="Masukkan kata sandi saat ini untuk verifikasi">
                                    <button type="button" class="toggle-password" id="toggleCurrentPassword" title="Tampilkan kata sandi">
                                        <i class="fa-regular fa-eye"></i>
                                    </button>
                                </div>
                                <small style="color: var(--text-muted); margin-top: 4px; display: block;">
                                    <i class="fa-solid fa-info-circle" style="margin-right: 4px;"></i>
                                    Diperlukan untuk mengubah kata sandi
                                </small>
                            </div>

                            <div class="form-group">
                                <label for="passwordNew">Kata Sandi Baru (Opsional)</label>
                                <div class="password-wrapper">
                                    <input type="password" id="passwordNew" placeholder="Biarkan kosong jika tidak ingin mengubah">
                                    <button type="button" class="toggle-password" id="toggleNewPassword" title="Tampilkan kata sandi">
                                        <i class="fa-regular fa-eye"></i>
                                    </button>
                                </div>
                                <small style="color: var(--text-muted); margin-top: 4px; display: block;">
                                    Minimal 6 karakter
                                </small>
                            </div>

                            <div class="form-group">
                                <label for="passwordConfirm">Konfirmasi Kata Sandi Baru (Opsional)</label>
                                <div class="password-wrapper">
                                    <input type="password" id="passwordConfirm" placeholder="Ulangi kata sandi baru">
                                    <button type="button" class="toggle-password" id="toggleConfirmPassword" title="Tampilkan kata sandi">
                                        <i class="fa-regular fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- ACTIONS -->
                        <div class="form-actions">
                            <button type="button" class="btn-cancel" onclick="window.history.back()">Batal</button>
                            <button type="submit" class="btn-save">
                                <i class="fa-solid fa-floppy-disk" style="margin-right: 8px;"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <script type="module">
        import {
            auth, db, cekAdmin, updatePassword, updateEmail, reauthenticateWithCredential,
            EmailAuthProvider, doc, updateDoc, getDoc
        } from '../../firebase/firebase-config.js';

        let userData = null;
        let photoBase64 = null;

        // === SHOW ALERT ===
        function showAlert(message, type = 'info') {
            const container = document.getElementById('alertContainer');
            const alert = document.createElement('div');
            alert.className = `alert alert-${type}`;
            alert.innerHTML = message;
            container.appendChild(alert);

            setTimeout(() => alert.remove(), 4000);
        }

        // === LOAD PROFILE ===
        cekAdmin().then(async (user) => {
            const firebaseUser = auth.currentUser;
            if (!firebaseUser) {
                showAlert('Session expired. Silakan login kembali', 'error');
                setTimeout(() => window.location.href = '/lenterailmu/pages/auth/login.php', 2000);
                return;
            }

            // Load Firestore user data
            try {
                const userDocSnap = await getDoc(doc(db, 'users', firebaseUser.uid));
                userData = userDocSnap.exists() ? userDocSnap.data() : {};
            } catch (e) {
                console.error('Error loading user data:', e);
                userData = {};
            }

            const nama = userData.nama || firebaseUser.displayName || 'Admin';
            const email = firebaseUser.email || 'admin@email.com';

            document.getElementById('profileName').textContent = nama;
            document.getElementById('profileEmail').textContent = email;
            document.getElementById('nama').value = userData.nama || '';
            document.getElementById('email').value = firebaseUser.email || '';

            // Set avatar
            if (userData.foto) {
                const img = document.getElementById('avatarImage');
                img.src = userData.foto;
                img.style.display = 'block';
                document.getElementById('avatarText').style.display = 'none';
            } else {
                const inisial = nama.charAt(0).toUpperCase();
                document.getElementById('avatarText').textContent = inisial;
            }
        }).catch(err => {
            console.error('Auth check error:', err);
        });

        // === PHOTO UPLOAD ===
        // Klik avatar atau tombol kamera untuk membuka file picker
        const uploadWrapper = document.getElementById('uploadBtnWrapper');
        const photoInputEl = document.getElementById('photoInput');
        const profileAvatarEl = document.getElementById('profileAvatar');

        uploadWrapper.addEventListener('click', (e) => {
            e.stopPropagation();
            photoInputEl.click();
        });

        profileAvatarEl.addEventListener('click', () => {
            photoInputEl.click();
        });

        photoInputEl.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;

            // Validasi tipe file
            if (!file.type.startsWith('image/')) {
                showAlert('File harus berupa gambar (JPG, PNG, dll)', 'error');
                return;
            }

            if (file.size > 2 * 1024 * 1024) {
                showAlert('Ukuran file terlalu besar (maksimal 2MB)', 'error');
                return;
            }

            // Kompres gambar sebelum simpan ke Firestore
            const reader = new FileReader();
            reader.onload = function(event) {
                const img = new Image();
                img.onload = function() {
                    const canvas = document.createElement('canvas');
                    const MAX_SIZE = 300;
                    let width = img.width;
                    let height = img.height;

                    if (width > height) {
                        if (width > MAX_SIZE) { height *= MAX_SIZE / width; width = MAX_SIZE; }
                    } else {
                        if (height > MAX_SIZE) { width *= MAX_SIZE / height; height = MAX_SIZE; }
                    }

                    canvas.width = width;
                    canvas.height = height;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(img, 0, 0, width, height);
                    photoBase64 = canvas.toDataURL('image/jpeg', 0.8);

                    const avatarImg = document.getElementById('avatarImage');
                    avatarImg.src = photoBase64;
                    avatarImg.style.display = 'block';
                    document.getElementById('avatarText').style.display = 'none';
                    showAlert('Foto berhasil dipilih! Klik "Simpan Perubahan" untuk menyimpan.', 'info');
                };
                img.src = event.target.result;
            };
            reader.readAsDataURL(file);
        });

        // === TOGGLE PASSWORD VISIBILITY ===
        ['Current', 'New', 'Confirm'].forEach(id => {
            const btn = document.getElementById('toggle' + id + 'Password');
            const inputElem = document.getElementById('password' + id);

            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const isPassword = inputElem.type === 'password';
                inputElem.type = isPassword ? 'text' : 'password';
                btn.innerHTML = `<i class="fa-regular fa-eye${isPassword ? '-slash' : ''}"></i>`;
            });
        });

        // === SUBMIT FORM ===
        document.getElementById('profileForm').addEventListener('submit', async (e) => {
            e.preventDefault();

            const firebaseUser = auth.currentUser;
            if (!firebaseUser) {
                showAlert('Session expired. Silakan login kembali', 'error');
                return;
            }

            const nama = document.getElementById('nama').value.trim();
            const passwordCurrent = document.getElementById('passwordCurrent').value;
            const passwordNew = document.getElementById('passwordNew').value;
            const passwordConfirm = document.getElementById('passwordConfirm').value;

            // Validasi
            if (!nama) {
                showAlert('Nama tidak boleh kosong', 'error');
                return;
            }

            if (passwordNew && passwordNew.length < 6) {
                showAlert('Kata sandi baru minimal 6 karakter', 'error');
                return;
            }

            if (passwordNew && passwordNew !== passwordConfirm) {
                showAlert('Konfirmasi kata sandi tidak cocok', 'error');
                return;
            }

            if (passwordNew && !passwordCurrent) {
                showAlert('Kata sandi saat ini diperlukan untuk mengubah kata sandi', 'error');
                return;
            }

            const btn = e.target.querySelector('button[type="submit"]');
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin" style="margin-right: 8px;"></i> Menyimpan...';

            try {
                // Re-authenticate jika perlu
                if (passwordNew && passwordCurrent) {
                    const credential = EmailAuthProvider.credential(firebaseUser.email, passwordCurrent);
                    await reauthenticateWithCredential(firebaseUser, credential);
                }

                // Update password
                if (passwordNew) {
                    await updatePassword(firebaseUser, passwordNew);
                }

                // Update Firestore
                const updateData = {
                    nama,
                    updatedAt: new Date().toISOString()
                };
                if (photoBase64) {
                    updateData.foto = photoBase64;
                }

                await updateDoc(doc(db, 'users', firebaseUser.uid), updateData);

                showAlert('Profil berhasil diperbarui!', 'success');
                setTimeout(() => window.location.href = 'dashboard.php', 2000);

            } catch (error) {
                console.error('Error:', error);
                let pesan = 'Gagal menyimpan perubahan';

                if (error.code === 'auth/wrong-password') {
                    pesan = 'Kata sandi saat ini salah';
                } else if (error.code === 'auth/email-already-in-use') {
                    pesan = 'Email sudah digunakan oleh akun lain';
                } else if (error.code === 'auth/invalid-email') {
                    pesan = 'Format email tidak valid';
                } else if (error.code === 'auth/requires-recent-login') {
                    pesan = 'Terlalu lama tidak aktif. Silakan login kembali dan coba lagi';
                } else if (error.message) {
                    pesan = error.message;
                }

                showAlert(pesan, 'error');
                btn.disabled = false;
                btn.innerHTML = '<i class="fa-solid fa-floppy-disk" style="margin-right: 8px;"></i> Simpan Perubahan';
            }
        });
    </script>

    <script type="module" src="../../components/admin-sidebar.js?v=1.2"></script>
</body>
</html>
