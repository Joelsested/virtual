<?php
require_once __DIR__ . '/../../config/session.php';
sested_session_start();
if (@$_SESSION['nivel'] != 'Aluno') {
    echo "<script>window.location='../index.php'</script>";
    exit();
}
?>
