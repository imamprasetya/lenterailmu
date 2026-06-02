<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lentera Ilmu - Daftar Akun</title>
    <link rel="stylesheet" href="register.css?v=1.2">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>

    <div class="container">
        <div class="left-side">
            <div class="brand-overlay">
                <div class="brand-title-wrapper" style="display: flex; align-items: center; gap: 16px; margin-bottom: 15px;">
                    <img src="../../assets/image/logo.png" alt="Lentera Ilmu Logo" class="brand-logo-left" style="height: 64px; width: auto; filter: drop-shadow(0 4px 10px rgba(0, 0, 0, 0.3));">
                    <h1 style="margin: 0; line-height: 1;">Lentera Ilmu</h1>
                </div>
                <p>Cahaya pengetahuan untuk masa depan yang lebih cerah.</p>
            </div>
        </div>

        <div class="right-side">
            <div class="login-box">
                <div class="logo-area">
                    <img src="../../assets/image/logo.png" alt="Lentera Ilmu Logo" class="logo-img-auth" style="height: 36px; object-fit: contain;">
                    <span>Lentera Ilmu</span>
                </div>
                
                <h2>Daftar Akun</h2>
                <p class="subtitle">Buat akun perpustakaan digital Anda hari ini.</p>

                <!-- Pesan Error/Success -->
                <div id="authMessage" class="auth-message" style="display: none;"></div>

                <form id="registerForm">
                    <div class="input-group">
                        <label for="namaLengkap">Nama Lengkap</label>
                        <input type="text" id="namaLengkap" placeholder="Masukkan nama lengkap Anda" required>
                    </div>

                    <div class="input-group">
                        <label for="institusi">Nama Institusi</label>
                        <input type="text" id="institusi" placeholder="cth: Universitas Indraprasta PGRI" required>
                    </div>

                    <div class="input-group">
                        <label for="email">Alamat Email</label>
                        <input type="email" id="email" placeholder="contoh@email.com" required>
                    </div>

                    <div class="input-group">
                        <label for="password">Kata Sandi</label>
                        <div class="password-wrapper">
                            <input type="password" id="password" placeholder="Minimal 6 karakter" required minlength="6">
                             <button type="button" class="toggle-password" id="togglePasswordBtn" title="Tampilkan kata sandi">
                                 <svg class="eye-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                     <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                                     <line x1="1" y1="1" x2="23" y2="23"></line>
                                 </svg>
                             </button>
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="confirmPassword">Konfirmasi Kata Sandi</label>
                        <div class="password-wrapper">
                            <input type="password" id="confirmPassword" placeholder="Ulangi kata sandi Anda" required minlength="6">
                             <button type="button" class="toggle-password" id="toggleConfirmPasswordBtn" title="Tampilkan kata sandi">
                                 <svg class="eye-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                     <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                                     <line x1="1" y1="1" x2="23" y2="23"></line>
                                 </svg>
                             </button>
                        </div>
                    </div>

                    <div class="form-options">
                        <label class="checkbox-container">
                            <input type="checkbox" id="agreeTerms" required> Saya menyetujui Syarat & Ketentuan
                        </label>
                    </div>

                    <button type="submit" class="btn-login" id="btnRegister">
                        <span class="btn-text">Daftar Akun <i class="fa-solid fa-arrow-right" style="margin-left: 6px;"></i></span>
                        <span class="btn-loading" style="display:none;">Memproses...</span>
                    </button>
                </form>

                <div class="divider">ATAU</div>

                <p class="register-text">Sudah punya akun? <a href="login.php">Masuk di sini</a></p>
            </div>

            <div class="footer-links">
                <a href="#">Syarat Layanan</a>
                <a href="#">Kebijakan Privasi</a>
                <a href="#">Pusat Bantuan</a>
            </div>
        </div>
    </div>

    <script type="module">
        import { 
            auth, db, createUserWithEmailAndPassword, 
            doc, setDoc, serverTimestamp, signOut 
        } from '../../firebase/firebase-config.js';

        // === Toggle Password ===
        // ... (remaining setupToggle and tampilkanPesan code) ...
        function setupToggle(btnId, inputId) {
            const btn = document.getElementById(btnId);
            const input = document.getElementById(inputId);
            
            btn.addEventListener('click', () => {
                const isPassword = input.getAttribute('type') === 'password';
                input.setAttribute('type', isPassword ? 'text' : 'password');
                if (isPassword) {
                    btn.innerHTML = `<svg class="eye-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>`;
                    btn.setAttribute('title', 'Sembunyikan kata sandi');
                } else {
                    btn.innerHTML = `<svg class="eye-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>`;
                    btn.setAttribute('title', 'Tampilkan kata sandi');
                }
            });
        }

        setupToggle('togglePasswordBtn', 'password');
        setupToggle('toggleConfirmPasswordBtn', 'confirmPassword');

        // === Helper: Tampilkan Pesan ===
        function tampilkanPesan(pesan, tipe = 'error') {
            const msgBox = document.getElementById('authMessage');
            msgBox.textContent = pesan;
            msgBox.className = `auth-message ${tipe}`;
            msgBox.style.display = 'block';
            msgBox.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }

        // === Handle Register ===
        document.getElementById('registerForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const nama = document.getElementById('namaLengkap').value.trim();
            const institusi = document.getElementById('institusi').value.trim();
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            const btnText = document.querySelector('.btn-text');
            const btnLoading = document.querySelector('.btn-loading');
            const btnRegister = document.getElementById('btnRegister');

            // Validasi
            if (password !== confirmPassword) {
                tampilkanPesan('Kata sandi dan konfirmasi kata sandi tidak cocok!');
                return;
            }

            if (password.length < 6) {
                tampilkanPesan('Kata sandi minimal 6 karakter!');
                return;
            }

            // Loading state
            btnRegister.disabled = true;
            btnText.style.display = 'none';
            btnLoading.style.display = 'inline';

            try {
                // Buat akun di Firebase Auth
                const userCredential = await createUserWithEmailAndPassword(auth, email, password);
                const user = userCredential.user;
                
                // Simpan data tambahan ke Firestore
                await setDoc(doc(db, 'users', user.uid), {
                    nama: nama,
                    email: email,
                    institusi: institusi,
                    role: 'user', // Default role adalah user biasa
                    tanggalDaftar: serverTimestamp(),
                    status: 'aktif'
                });

                // Keluar dari sesi otomatis agar user harus login secara manual
                await signOut(auth);

                tampilkanPesan(`Pendaftaran berhasil! Mengalihkan ke halaman login...`, 'success');
                
                // Redirect ke login setelah 1.5 detik
                setTimeout(() => {
                    window.location.href = 'login.php';
                }, 1500);
                
            } catch (error) {
                let pesan = 'Pendaftaran gagal. ';
                switch (error.code) {
                    case 'auth/email-already-in-use':
                        pesan += 'Email sudah terdaftar. Silakan gunakan email lain.';
                        break;
                    case 'auth/invalid-email':
                        pesan += 'Format email tidak valid.';
                        break;
                    case 'auth/weak-password':
                        pesan += 'Kata sandi terlalu lemah. Gunakan minimal 6 karakter.';
                        break;
                    default:
                        pesan += error.message;
                }
                tampilkanPesan(pesan);
                
                btnRegister.disabled = false;
                btnText.style.display = 'inline';
                btnLoading.style.display = 'none';
            }
        });
    </script>
</body>
</html>