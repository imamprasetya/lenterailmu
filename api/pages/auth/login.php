<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lentera Ilmu - Masuk</title>
    <link rel="stylesheet" href="login.css?v=1.2">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body>

    <div class="container">
        <div class="left-side">
            <div class="brand-overlay">
                <h1>Lentera Ilmu</h1>
                <p>Cahaya pengetahuan untuk masa depan yang lebih cerah.</p>
            </div>
        </div>

        <div class="right-side">
            <div class="login-box">
                <div class="logo-area">
                    <img src="../../assets/image/logo.png" alt="Lentera Ilmu Logo" class="logo-img-auth" style="height: 36px; object-fit: contain;">
                    <span>Lentera Ilmu</span>
                </div>

                <h2>Selamat Datang</h2>
                <p class="subtitle">Masuk ke akun perpustakaan digital Anda.</p>

                <!-- Pesan Error/Success -->
                <div id="authMessage" class="auth-message" style="display: none;"></div>

                <form id="loginForm">
                    <div class="input-group">
                        <label for="email">Alamat Email</label>
                        <input type="email" id="email" placeholder="contoh@email.com" required>
                    </div>

                    <div class="input-group">
                        <label for="password">Kata Sandi</label>
                        <div class="password-wrapper">
                            <input type="password" id="password" placeholder="••••••••" required minlength="6">
                            <button type="button" class="toggle-password" id="togglePasswordBtn" title="Tampilkan kata sandi">
                                <svg class="eye-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                                    <line x1="1" y1="1" x2="23" y2="23"></line>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="form-options">
                        <label class="checkbox-container">
                            <input type="checkbox"> Ingat saya
                        </label>
                        <a href="#" class="forgot-link">Lupa kata sandi?</a>
                    </div>

                    <button type="submit" class="btn-login" id="btnLogin">
                        <span class="btn-text">Masuk <i class="fa-solid fa-arrow-right" style="margin-left: 6px;"></i></span>
                        <span class="btn-loading" style="display:none;">Memproses...</span>
                    </button>
                </form>

                <div class="divider">ATAU</div>

                <p class="register-text">Belum punya akun? <a href="register.php">Daftar sekarang</a></p>
            </div>

            <div class="footer-links">
                <a href="#">Syarat Layanan</a>
                <a href="#">Kebijakan Privasi</a>
                <a href="#">Pusat Bantuan</a>
            </div>
        </div>
    </div>

    <script type="module">
        import { auth, db, signInWithEmailAndPassword, doc, getDocFromServer } from '../../firebase/firebase-config.js';

        // Password
        const togglePasswordBtn = document.getElementById('togglePasswordBtn');
        const passwordInput = document.getElementById('password');

        togglePasswordBtn.addEventListener('click', () => {
            const isPassword = passwordInput.getAttribute('type') === 'password';
            passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
            
            if (isPassword) {
                togglePasswordBtn.innerHTML = `
                    <svg class="eye-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                `;
                togglePasswordBtn.setAttribute('title', 'Sembunyikan kata sandi');
            } else {
                togglePasswordBtn.innerHTML = `
                    <svg class="eye-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                        <line x1="1" y1="1" x2="23" y2="23"></line>
                    </svg>
                `;
                togglePasswordBtn.setAttribute('title', 'Tampilkan kata sandi');
            }
        });

        //Tampilkan Pesan
        function tampilkanPesan(pesan, tipe = 'error') {
            const msgBox = document.getElementById('authMessage');
            msgBox.textContent = pesan;
            msgBox.className = `auth-message ${tipe}`;
            msgBox.style.display = 'block';
        }

        // Handle Login
        document.getElementById('loginForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;
            const btnText = document.querySelector('.btn-text');
            const btnLoading = document.querySelector('.btn-loading');
            const btnLogin = document.getElementById('btnLogin');
            
            // Loading state
            btnLogin.disabled = true;
            btnText.style.display = 'none';
            btnLoading.style.display = 'inline';

            try {
                const userCredential = await signInWithEmailAndPassword(auth, email, password);
                const user = userCredential.user;
                
                tampilkanPesan('Login berhasil! Mengalihkan...', 'success');
                
                // Cek role user di Firestore (memaksa fetch dari server)
                const userDoc = await getDocFromServer(doc(db, 'users', user.uid));
                
                if (userDoc.exists() && userDoc.data().role === 'admin') {
                    window.location.href = '../admin/dashboard.php';
                } else {
                    window.location.href = '../user/dashboard.php';
                }
            } catch (error) {
                let pesan = 'Gagal masuk. ';
                switch (error.code) {
                    case 'auth/user-not-found':
                        pesan += 'Email tidak terdaftar.';
                        break;
                    case 'auth/wrong-password':
                        pesan += 'Kata sandi salah.';
                        break;
                    case 'auth/invalid-email':
                        pesan += 'Format email tidak valid.';
                        break;
                    case 'auth/too-many-requests':
                        pesan += 'Terlalu banyak percobaan. Coba lagi nanti.';
                        break;
                    case 'auth/invalid-credential':
                        pesan += 'Email atau kata sandi salah.';
                        break;
                    default:
                        pesan += error.message;
                }
                tampilkanPesan(pesan);
                
                btnLogin.disabled = false;
                btnText.style.display = 'inline';
                btnLoading.style.display = 'none';
            }
        });
    </script>
</body>

</html>