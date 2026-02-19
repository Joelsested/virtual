<?php 

include('../conexao.php');

@session_start();

if (!isset($_SESSION) || ($_SESSION['nivel'] !== 'Administrador' && $_SESSION['nivel'] !== 'Secretario')) {
    $json = json_encode(['error' => 'Você não está autorizado a realizar essa operação!'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    echo '' . highlight_string("" . $json, true) . '';
    return;
}


$id = $_POST['id'] ?? $_GET['id'] ?? '';
$id_mat = $_POST['id_mat'] ?? $_GET['id_mat'] ?? '';

// $data_certificado = $_GET['data'];
$data_certificado = $_POST['data'] ?? $_GET['data'] ?? null;

$ano_certificado = $_POST['ano'] ?? $_GET['ano'] ?? null;

if ($id === '' && $id_mat !== '') {
    $stmtMatricula = $pdo->prepare("SELECT aluno FROM matriculas WHERE id = :id LIMIT 1");
    $stmtMatricula->bindValue(':id', $id_mat);
    $stmtMatricula->execute();
    $matricula = $stmtMatricula->fetch(PDO::FETCH_ASSOC);
    if ($matricula && !empty($matricula['aluno'])) {
        $id = $matricula['aluno'];
    }
}

if ($id === '') {
    $json = json_encode(['error' => 'Aluno n\u00e3o informado!'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    echo '' . highlight_string("" . $json, true) . '';
    return;
}

//CARREGAR DOMPDF
require_once '../dompdf/autoload.inc.php';
use Dompdf\Dompdf;
use Dompdf\Options; ? header("Content-Transfer-Encoding: binary"); ? header("Content-Type: image/png");

//INICIALIZAR A CLASSE DO DOMPDF
$options = new Options();
$options->set('isRemoteEnabled', true);
$pdf = new DOMPDF($options);



//ALIMENTAR OS DADOS NO RELATÓRIO
// $html = utf8_encode(file_get_contents($url_sistema."sistema/rel/certificado.phpid=".$id));
$html = utf8_encode(file_get_contents($url_sistema . "sistema/rel/certificado.phpid=" . $id . "&data=" . urlencode($data_certificado) . "&ano=" . urlencode($ano_certificado) . "&id_mat=" . urlencode($id_mat)));



//Definir o tamanho do papel e orientação da página
$pdf->set_paper('A4', 'landscape');

//CARREGAR O CONTEÚDO HTML
$pdf->load_html(utf8_decode($html));

//RENDERIZAR O PDF
$pdf->render();

//NOMEAR O PDF GERADO
$pdf->stream( ?? 'certificado.pdf',
array("Attachment" => false)
);




?>