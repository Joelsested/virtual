 <?php 
 //includes para o mercado pago
include_once("../../pagamentos/mercadopago/lib/mercadopago.php");
include_once("../../pagamentos/mercadopago/PagamentoMP.php");
$pagar = new PagamentoMP;


require_once('../../sistema/conexao.php');

$id_aluno = $_POST['aluno'];
$id_do_curso_pag = $_POST['id'];
$nome = $_POST['nome'];
$pacote = $_POST['pacote'];

$query = $pdo->query("SELECT * FROM matriculas where id_curso = '$id_do_curso_pag' and aluno = $id_aluno ");
$res = $query->fetchAll(PDO::FETCH_ASSOC);

if(@count($res) > 0){
	$id_matricula = $res[0]['id'];
	$valor_curso = $res[0]['subtotal'];

	if($valor_curso == 0){
		$valor_curso = 1;
	}

                           //botao do mercado pago                       
 $btn = $pagar->PagarMP($id_do_curso_pag, $nome, (float)$valor_curso, $url_sistema);
         echo $btn;

 echo '<div align="center"><i class="neutra"><small>(Parcele em até 12 Vezes) <span class="neutra ocultar-mobile">Pagamento no Cartão ou Saldo</span></small></i></div>';
}

?>
                        