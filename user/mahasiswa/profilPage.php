<?php
// Mulai sesi jika belum
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Periksa apakah ada sesi username dan user_status
if (!isset($_SESSION['username']) || !isset($_SESSION['user_status'])) {
    // Jika tidak, tampilkan pesan dan tautan ke halaman login
    echo "<p>Anda perlu login terlebih dahulu. <a href='../../index.html'>Menuju halaman Login</a></p>";
    exit(); // Pastikan kode berhenti di sini
}

// Sambungkan ke database
include '../../Koneksi/koneksi.php';
// Mendapatkan username yang sedang login
$username = $_SESSION['username'];

// Query untuk mengambil informasi pengguna dari tabel m_user
$sql_user = "SELECT * FROM m_user WHERE username = '$username'";
$result_user = $conn->query($sql_user);

// Periksa apakah data pengguna ditemukan
if ($result_user->num_rows > 0) {
    // Ambil data pengguna
    $row_user = $result_user->fetch_assoc();

    // Simpan data pengguna
    $nama = $row_user['Nama']; // Pastikan nama kolom sesuai dengan kolom dalam tabel
    $user_status = $row_user['user_status']; // Pastikan nama kolom sesuai dengan kolom dalam tabel
    $user_image = $row_user['user_image']; // Pastikan nama kolom sesuai dengan kolom dalam tabel

    // Jika user adalah mahasiswa, ambil informasi mahasiswa dari tabel t_responden_mahasiswa
    // Query untuk mengambil informasi mahasiswa
    $sql_mahasiswa = "SELECT * FROM t_responden_mahasiswa WHERE responden_nama = '$nama'";
    $result_mahasiswa = $conn->query($sql_mahasiswa);

    // Periksa apakah data mahasiswa ditemukan
    if ($result_mahasiswa->num_rows > 0) {
        // Ambil data mahasiswa
        $row_mahasiswa = $result_mahasiswa->fetch_assoc();

        // Simpan data mahasiswa
        $nim = $row_mahasiswa['responden_nim']; // Pastikan nama kolom sesuai dengan kolom dalam tabel
        $email = $row_mahasiswa['responden_email']; // Pastikan nama kolom sesuai dengan kolom dalam tabel
        $hp = $row_mahasiswa['responden_hp']; // Pastikan nama kolom sesuai dengan kolom dalam tabel
        $prodi = $row_mahasiswa['responden_prodi']; // Pastikan nama kolom sesuai dengan kolom dalam tabel
        $tahun_masuk = $row_mahasiswa['tahun_masuk']; // Pastikan nama kolom sesuai dengan kolom dalam tabel
    }
}

// Tangani update data jika form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form
    $username_new = $_POST['username'];
    $email_new = $_POST['responden_email'];
    $hp_new = $_POST['responden_hp'];

    // Update data di tabel m_user
    $sql_update_user = "UPDATE m_user SET username = '$username_new' WHERE Nama = '$nama'";
    if ($conn->query($sql_update_user) === TRUE) {
        // Perbarui sesi username
        $_SESSION['username'] = $username_new;
    }

    // Update data di tabel t_responden_mahasiswa
    $sql_update_mahasiswa = "UPDATE t_responden_mahasiswa SET responden_email = '$email_new', responden_hp = '$hp_new' WHERE responden_nim = '$nim'";
    $conn->query($sql_update_mahasiswa);

    // Tangani upload gambar
    if (isset($_FILES['user_image']) && $_FILES['user_image']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "../../gambar/";
        $target_file = $target_dir . basename($_FILES["user_image"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Periksa apakah file adalah gambar
        $check = getimagesize($_FILES["user_image"]["tmp_name"]);
        if ($check !== false) {
            // Pindahkan file ke direktori tujuan
            if (move_uploaded_file($_FILES["user_image"]["tmp_name"], $target_file)) {
                // Update path gambar di database
                $sql_update_image = "UPDATE m_user SET user_image = '" . basename($_FILES["user_image"]["name"]) . "' WHERE username = '$username_new'";
                $conn->query($sql_update_image);
            }
        }
    }

    // Set pesan berhasil diperbarui dan redirect
    $_SESSION['update_success'] = true;
    header("Location: profilPage.php");
    exit();
}

$update_success = false;
if (isset($_SESSION['update_success'])) {
    $update_success = $_SESSION['update_success'];
    unset($_SESSION['update_success']);
}

// Tutup koneksi database
$conn->close();
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Survey Kepuasan - Politeknik Negeri Malang</title>
    <link rel="stylesheet" href="../../style.css">
    <script src="../../script.js"></script>
</head>

<body>
    <?php include '../TemplateUser/headerUser.php'; ?>
    <div class="containerMainPP">
        <?php include '../TemplateUser/sidebarUser.php'; ?>
        <div class="containerKananPP">
            <form class="containerPP" id="profileForm" method="POST" action="profilPage.php" enctype="multipart/form-data">
                <input type="hidden" name="update_success" value="<?php echo $update_success ? 'true' : ''; ?>">

                <div class="ubahPassword">
                    <button type="button" name="ubahPassword" onclick="window.location.href='ubahPassword.php'">Ubah Password</button>
                </div>

                <div class="form-group">
                    <img src="../../gambar/<?php echo $row_user['user_image']; ?>" alt="Logo" class="logoPP" id="userLogo">
                    <input type="file" name="user_image" id="user_image" accept="image/*" onchange="previewImage(event)">
                </div>

                <div class="input-groupPP">
                    <label for="nama">Nama:</label>
                    <input type="text" id="nama" name="nama" value="<?php echo $nama; ?>" readonly>
                </div>

                <div class="input-groupPP">
                    <label for="username">Username:</label>
                    <div class="input-container">
                        <input type="text" id="username" name="username" value="<?php echo $username; ?>" readonly>
                        <img src="../../gambar/edit_icon.png" alt="Edit" class="edit-icon" onclick="toggleEdit('username')">
                    </div>
                </div>

                <div class="input-groupPP">
                    <label for="responden_nim">NIM:</label>
                    <input type="text" id="responden_nim" name="responden_nim" value="<?php echo $nim; ?>" readonly>
                </div>

                <div class="input-groupPP">
                    <label for="responden_email">Email:</label>
                    <div class="input-container">
                        <input type="text" id="responden_email" name="responden_email" value="<?php echo $email; ?>" readonly>
                        <img src="../../gambar/edit_icon.png" alt="Edit" class="edit-icon" onclick="toggleEdit('responden_email')">
                    </div>
                </div>

                <div class="input-groupPP">
                    <label for="responden_hp">No. Handphone:</label>
                    <div class="input-container">
                        <input type="text" id="responden_hp" name="responden_hp" value="<?php echo $hp; ?>" readonly>
                        <img src="../../gambar/edit_icon.png" alt="Edit" class="edit-icon" onclick="toggleEdit('responden_hp')">
                    </div>
                </div>

                <div class="input-groupPP">
                    <label for="responden_prodi">Prodi:</label>
                    <input type="text" id="responden_prodi" name="responden_prodi" value="<?php echo $prodi; ?>" readonly>
                </div>

                <div class="input-groupPP">
                    <label for="tahun_masuk">Tahun Masuk:</label>
                    <input type="text" id="tahun_masuk" name="tahun_masuk" value="<?php echo $tahun_masuk; ?>" readonly>
                </div>

                <div class="simpan">
                    <button type="submit" name="simpan" id="simpanButton">
                        <img src="../../gambar/bookmark.png" alt="Simpan">Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>
