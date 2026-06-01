

import { initializeApp } from "https://www.gstatic.com/firebasejs/10.8.0/firebase-app.js";
import { getAuth, onAuthStateChanged, signOut, signInWithEmailAndPassword, createUserWithEmailAndPassword, updatePassword, updateEmail, verifyBeforeUpdateEmail, reauthenticateWithCredential, EmailAuthProvider } from "https://www.gstatic.com/firebasejs/10.8.0/firebase-auth.js";
import { initializeFirestore, doc, getDoc, getDocFromServer, setDoc, collection, addDoc, getDocs, updateDoc, deleteDoc, query, where, orderBy, limit, Timestamp, serverTimestamp } from "https://www.gstatic.com/firebasejs/10.8.0/firebase-firestore.js";

// firebase config
const firebaseConfig = {
    apiKey: "AIzaSyCpQgNY59g3u7QHAzRruFBaNwsOVmZ3pD0",
    authDomain: "lentera-ilmu-97099.firebaseapp.com",
    projectId: "lentera-ilmu-97099",
    storageBucket: "lentera-ilmu-97099.firebasestorage.app",
    messagingSenderId: "1093645663822",
    appId: "1:1093645663822:web:fcd1f8422a4cc6ddd18d1f",
    measurementId: "G-FY9K84NL5F"
};

// initialize firebase
const app = initializeApp(firebaseConfig);
const auth = getAuth(app);
const db = initializeFirestore(app, {
    experimentalForceLongPolling: true
});

// helper untuk cek login

/**
 * Cek apakah user sudah login.
 * Jika belum login, redirect ke halaman login.
 * @param {string} redirectUrl - URL redirect jika belum login
 * @returns {Promise<Object>} - Data user dari Firestore
 */
function cekLogin(redirectUrl = '/lenterailmu/pages/auth/login.php') {
    return new Promise((resolve, reject) => {
        onAuthStateChanged(auth, async (user) => {
            if (user) {
                try {
                    const userDocRef = doc(db, 'users', user.uid);
                    const userDoc = await getDocFromServer(userDocRef);
                    let userData = userDoc.exists() ? userDoc.data() : {};

                    // Sinkronisasi email jika berbeda antara Auth dan Firestore.
                    if (userDoc.exists() && user.email !== userData.email) {
                        await updateDoc(userDocRef, { email: user.email });
                        userData.email = user.email;
                    }

                    resolve({ ...user, ...userData });
                } catch (error) {
                    console.error('Gagal mengambil atau sinkronisasi data user:', error);
                    resolve(user); // Tetap resolve dengan data auth jika firestore gagal
                }
            } else {
                window.location.href = redirectUrl;
                reject('Belum login');
            }
        });
    });
}

/**
 * Cek apakah user adalah admin.
 * Jika bukan admin, redirect ke dashboard user.
 * @returns {Promise<Object>} - Data user admin
 */
function cekAdmin() {
    return new Promise((resolve, reject) => {
        onAuthStateChanged(auth, async (user) => {
            if (user) {
                try {
                    const userDocRef = doc(db, 'users', user.uid);
                    const userDoc = await getDocFromServer(userDocRef);
                    if (userDoc.exists() && userDoc.data().role === 'admin') {
                        let userData = userDoc.data();

                        // Sinkronisasi email juga untuk admin
                        if (user.email !== userData.email) {
                            await updateDoc(userDocRef, { email: user.email });
                            userData.email = user.email;
                        }

                        resolve({ ...user, ...userData });
                    } else {
                        window.location.href = '/lenterailmu/pages/user/dashboard.php';
                        reject('Bukan admin');
                    }
                } catch (error) {
                    console.error('Gagal verifikasi atau sinkronisasi admin:', error);
                    window.location.href = '/lenterailmu/pages/user/dashboard.php';
                    reject(error);
                }
            } else {
                window.location.href = '/lenterailmu/pages/auth/login.php';
                reject('Belum login');
            }
        });
    });
}


// logout user dan redirect ke halaman login
async function logout() {
    try {
        await signOut(auth);
        window.location.href = '/lenterailmu/pages/auth/login.php';
    } catch (error) {
        console.error('Gagal logout:', error);
        alert('Gagal keluar. Silakan coba lagi.');
    }
}

/**
 * Format tanggal ke format Indonesia
 * @param {Date|Object} date - Date object atau Firestore Timestamp
 * @returns {string} - Tanggal dalam format "1 Jun 2026"
 */
function formatTanggal(date) {
    if (!date) return '-';
    const bulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    let d = date;
    if (date.toDate) d = date.toDate();
    if (typeof date === 'string') d = new Date(date);
    const jam = String(d.getHours()).padStart(2, '0');
    const menit = String(d.getMinutes()).padStart(2, '0');
    return `${d.getDate()} ${bulan[d.getMonth()]} ${d.getFullYear()}, ${jam}:${menit}`;
}

// format tanggal untuk input date HTML
function formatTanggalInput(date) {
    if (!date) return '';
    let d = date;
    if (date.toDate) d = date.toDate();
    if (typeof date === 'string') d = new Date(date);
    return d.toISOString().split('T')[0];
}

// export firebase dan helper functions
export {
    // Firebase instances
    app, auth, db,

    // Auth functions
    signInWithEmailAndPassword,
    createUserWithEmailAndPassword,
    updatePassword,
    updateEmail,
    verifyBeforeUpdateEmail,
    reauthenticateWithCredential,
    EmailAuthProvider,
    onAuthStateChanged,
    signOut,

    // Firestore functions
    doc, getDoc, getDocFromServer, setDoc, collection, addDoc, getDocs,
    updateDoc, deleteDoc, query, where, orderBy, limit,
    Timestamp, serverTimestamp,

    // Helper functions
    cekLogin, cekAdmin, logout,
    formatTanggal, formatTanggalInput
};
