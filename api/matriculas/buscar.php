<?php 

include_once('../conexao.php');

$postjson = json_decode(file_get_contents('php://input'), true);

$buscar = '%' .@$_GET['buscar']. '%';

$query = $pdo->query("SELECT m.id, m.id_curso, m.aluno, m.aulas_concluidas, m.subtotal, m.data, m.pacote, m.obs, u.nome, u.usuario from matriculas as m INNER JOIN usuarios as u ON m.aluno = u.id where m.status = 'Aguardando' and (u.nome LIKE '$buscar' or u.usuario LIKE '$buscar') order by m.id desc LIMIT 30");

$res = $query->fetchAll(PDO::FETCH_ASSOC);


for ($i=0; $i < count($res); $i++) { 

$curso = $res[$i]['id_curso'];  
$valor = $res[$i]['subtotal'];  
$data = $res[$i]['data'];   
$pacote = $res[$i]['pacote'];
$aluno = $res[$i]['aluno'];
$obs = $res[$i]['obs'];


if($pacote == 'Sim'){
        $tab = 'pacotes';
        $item_curso = ' (Pacote)';
        $classe_curso = 'text-primary';     
    }else{
        $tab = 'cursos';
        $item_curso = '';
        $classe_curso = '';     
    }
    

    $query2 = $pdo->query("SELECT * FROM $tab where id = '$curso'");
    $res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
    if(@count($res2) > 0){
        $nome_curso = $res2[0]['nome'];     
        $id_do_curso = $res2[0]['id'];
        $foto = $res2[0]['imagem'];

    }else{
        $nome_curso = "";
    }

    $query2 = $pdo->query("SELECT * FROM usuarios where id = '$aluno'");
    $res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
    if(@count($res2) > 0){
        $nome_aluno = $res2[0]['nome'];
        $email_aluno = $res2[0]['usuario'];
        
    }
            
    //FORMATAR VALORES
    $valorF = number_format($valor, 2, ',', '.');
    $dataF = implode('/', array_reverse(explode('-', $data)));

   

      $dados[] = array(
        'id' => $res[$i]['id'],
        'aluno' => $nome_aluno,        
        'email' => $email_aluno,
        'curso' => $nome_curso,
        'valor' => $valor,
        'data' => $dataF,  
        'obs' => $obs,     
        'foto' => $foto,  
        'tab' => $tab, 
        'valorF' => $valorF,        
    );

}


if(count($res) > 0){
    $result = json_encode(array('success'=>true, 'resultado'=>$dados));
}else{
    $result = json_encode(array('success'=>false, 'resultado'=>'0'));
}

echo $result;

?>