<?php
    move_uploaded_file($_FILES['photo']['tmp_name'], '../../sistema/painel-admin/img/contas/' . $_FILES['photo']['name']);
?>