<?php
session_start();

include '../../Koneksi/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $action = $_POST['action'];

    if ($action === 'accept') {
        $sql = "SELECT * FROM t_pending_dosen WHERE pending_user_id = $id";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $username = $row['username'];
            $password = $row['password'];
            $nama = $row['nama'];
            $user_status = $row['user_status'];
            $responden_nip = $row['responden_nip'];
            $responden_unit = $row['responden_unit'];

            $sql1 = "INSERT INTO m_user (username, password, Nama, user_status) 
                     VALUES ('$username', '$password', '$nama', '$user_status')";

            if ($conn->query($sql1) === TRUE) {
                $success = true;
                for ($i = 0; $i < 2; $i++) {
                    $sql2 = "INSERT INTO t_responden_dosen (responden_nama, responden_nip, responden_unit) 
                VALUES ('$nama', '$responden_nip', '$responden_unit')";
                    if ($conn->query($sql2) !== TRUE) {
                        $success = false;
                        break;

                    }
                }
                if ($success) {
                    $conn->query("DELETE FROM t_pending_dosen WHERE pending_user_id = $id");
                    echo "Accepted";
                } else {
                    echo "Error: " . $conn->error;
                }
            }
        } 
    } else if ($action === 'reject') {
        $conn->query("DELETE FROM t_pending_dosen WHERE pending_user_id = $id");
        echo "Rejected";
    }
}

$conn->close();
?>