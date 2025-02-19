<?php
// Mulai sesi jika belum
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Koneksi ke database
include '../../Koneksi/koneksi.php';

// Ambil user_id dari session
$username = isset($_SESSION['username']) ? $_SESSION['username'] : '';
if (empty($username)) {
    die("Error: Username tidak tersedia");
}

$query_user = "SELECT user_id FROM m_user WHERE username = '$username'";
$result_user = $conn->query($query_user);
$user_id = $result_user->fetch_assoc()['user_id'];

// Fungsi untuk mengecek apakah survey sudah diisi oleh user
function isSurveyCompleted($user_id, $survey_name, $conn) {
    $query = "SELECT s.survey_id FROM m_survey s
              JOIN t_responden_dosen r ON s.survey_id = r.survey_id
              WHERE s.survey_nama = '$survey_name' AND r.responden_nama = 
              (SELECT Nama FROM m_user WHERE user_id = '$user_id')";
    $result = $conn->query($query);
    return $result->num_rows > 0;
}

$survey_fasilitas_completed = isSurveyCompleted($user_id, 'Survey Fasilitas', $conn);
$survey_sistem_completed = isSurveyCompleted($user_id, 'Survey Sistem Informasi', $conn);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Survey Kepuasan - Politeknik Negeri Malang</title>
    <link rel="stylesheet" href="../../style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../../script.js"></script>
</head>

<body>
    <?php include '../TemplateUser/headerUser.php'; ?>
    <div class="containerMain">
        <?php include '../TemplateUser/sidebarUser.php'; ?>
        
            <div class="containerKanan_SP">
                <div class="blok_SP1">
                    <div class="blok_SP_HFD hasilSurvey-buttonD <?php echo $survey_fasilitas_completed ? 'completed' : ''; ?>" id="hasilSurveyFD_button">
                        <div class="content_SP">
                            <img src="../../gambar/icon_fasilitas.png" alt="icon_survey" class="icon_survey_SP">
                            <div class="text_SP">
                                <h2>Survey Fasilitas</h2>
                                <p>Berikan penilaian fasilitas yang diberikan POLINEMA</p>
                            </div>
                        </div>
                        <img src="../../gambar/icon_arrow.png" alt="arrow" class="icon_arrow_SP">
                    </div>
                    <div class="blok-strip_SP"></div>
                </div>
                <div class="blok_SP1">
                    <div class="blok_SP_HSD hasilSurvey-buttonD <?php echo $survey_sistem_completed ? 'completed' : ''; ?>" id="hasilSurveySD_button">
                        <div class="content_SP">
                            <img src="../../gambar/icon_sistem.png" alt="icon_survey" class="icon_survey_SP">
                            <div class="text_SP">
                                <h2>Survey Sistem</h2>
                                <p>Berikan penilaian SIAKAD yang dimiliki POLINEMA</p>
                            </div>
                        </div>
                        <img src="../../gambar/icon_arrow.png" alt="arrow" class="icon_arrow_SP">
                    </div>
                    <div class="blok-strip_SP"></div>
                </div>
            </div>

        
    </div>
</body>

</html>