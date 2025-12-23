<?php
require_once("../../../conexao.php");
$tabela = 'matriculas';
@session_start();

$id_usuario = $_SESSION['id'];
$id_curso = $_POST['id_curso'] ?? null;
$forma_pgto = $_POST['forma_pgto'] ?? null;
$id_matricula = $_POST['id'] ?? null;

// Se você já tiver essas infos no POST ou sessão, pode pegar de lá
$nome_do_curso = $_POST['nome_do_curso'] ?? 'Pagamento Curso';
$pacote        = $_POST['pacote'] ?? 'Não';
$quantidadeParcelas = $_POST['quantidadeParcelas'] ?? 1; // valor padrão


header('Content-Type: application/json; charset=utf-8');


// echo '<pre>';
//         echo json_encode($_POST, JSON_PRETTY_PRINT);
//         echo '</pre>';
//         return;


try {
    if (!$id_usuario || !$id_curso || !$forma_pgto || !$id_matricula) {
        throw new Exception("Dados incompletos");
    }


    $query = $pdo->prepare("UPDATE $tabela SET forma_pgto = :forma_pgto where aluno = '$id_usuario' and id = '$id_matricula' ");
    $query->bindValue(":forma_pgto", "$forma_pgto");
    $query->execute();

    // Definir página de redirecionamento com base na forma
    $redirectUrl = null;
    switch ($forma_pgto) {
        case 'BOLETO':
              // monta URL com os parâmetros exigidos pelo /efi/index.php
        $redirectUrl = "/efi/index.php?" . http_build_query([
            "formaDePagamento"   => $forma_pgto,
            "billingType"        => strtoupper($forma_pgto),
            "quantidadeParcelas" => $quantidadeParcelas,
            "id_do_curso"        => $id_curso,
            "nome_do_curso"      => $nome_do_curso,
            "pacote"             => $pacote
        ]);
            break;
        case 'BOLETO_PARCELADO':
            // monta URL com os parâmetros exigidos pelo /efi/index.php
            $redirectUrl = "/efi/index.php?" . http_build_query([
                "formaDePagamento" => $forma_pgto,
                "billingType" => strtoupper($forma_pgto),
                "quantidadeParcelas" => $quantidadeParcelas,
                "id_do_curso" => $id_curso,
                "nome_do_curso" => $nome_do_curso,
                "pacote" => $pacote
            ]);
            break;
        case 'CARTAO_DE_CREDITO':
            $redirectUrl = "/sistema/painel-aluno/index.php?pagina=parcelas_cartao";
            break;
        default:
            $redirectUrl = null;
    }

    echo json_encode([
        "status" => "success",
        "message" => "Forma de pagamento salva com sucesso!",
        "redirect" => $redirectUrl
    ]);
} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}
