<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lentera Ilmu - Perpustakaan Digital</title>
    <link rel="stylesheet" href="assets/css/global.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background: linear-gradient(135deg, #1e3a5f 0%, #2563eb 50%, #0ea5e9 100%);
        }
        .loading-box {
            text-align: center;
            color: white;
        }
        .loading-box h1 {
            font-size: 32px;
            margin-bottom: 8px;
        }
        .loading-box p {
            opacity: 0.8;
            font-size: 14px;
            margin-bottom: 24px;
        }
        .loading-box .loading-spinner {
            border-color: rgba(255,255,255,0.3);
            border-top-color: white;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div class="loading-box">
        <h1><i class="fa-solid fa-book-open" style="margin-right: 8px;"></i> Lentera Ilmu</h1>
        <p>Memuat perpustakaan digital...</p>
        <div class="loading-spinner"></div>
    </div>

    <script type="module">
        import { auth, onAuthStateChanged, doc, getDoc, db } from './firebase/firebase-config.js';

        onAuthStateChanged(auth, async (user) => {
            if (user) {
                // Sudah login, cek role
                try {
                    const userDoc = await getDoc(doc(db, 'users', user.uid));
                    if (userDoc.exists() && userDoc.data().role === 'admin') {
                        window.location.href = 'pages/admin/dashboard.php';
                    } else {
                        window.location.href = 'pages/user/dashboard.php';
                    }
                } catch (e) {
                    window.location.href = 'pages/user/dashboard.php';
                }
            } else {
                // Belum login, ke halaman login
                window.location.href = 'pages/auth/login.php';
            }
        });
    </script>
</body>
</html>
