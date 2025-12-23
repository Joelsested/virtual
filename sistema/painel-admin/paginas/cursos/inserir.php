<?php  
require_once("../../../conexao.php"); 
$tabela = 'cursos';  
$ano_atual = date('Y');  

@session_start(); 
$id_usuario = $_SESSION['id'];  

$nome        = $_POST['nome']; 
$desc_rapida = $_POST['desc_rapida']; 
$categoria   = $_POST['categoria']; 
$grupo       = $_POST['grupo']; 
$valor       = str_replace(',', '.', $_POST['valor']); 
$promocao    = str_replace(',', '.', $_POST['promocao']); 
$carga       = $_POST['carga']; 
$palavras    = $_POST['palavras']; 
$pacote      = $_POST['pacote'] ?? '';
$tecnologias = $_POST['tecnologias'] ?? '';
$sistema     = isset($_POST['sistema']) && $_POST['sistema'] === 'on' ? 1 : 0; 
$arquivo    = $_POST['arquivo'] ?? '';
$link       = $_POST['link'] ?? '';
$desc_longa  = $_POST['desc_longa']; 
$comissao   = $_POST['comissao'] ?? 0; 
$split = isset($_POST['split']) ? (int) $_POST['split'] : 1;
$is_matricula = isset($_POST['is_matricula']) ? 1 : 0;  

// Sanitizações básicas
$desc_longa   = str_replace(["'", '"', "\n", "\r"], ' ', $desc_longa); 
$nome         = str_replace(["'", '"'], ' ', $nome); 
$desc_rapida  = str_replace(["'", '"'], ' ', $desc_rapida); 

// Criar URL amigável
$nome_novo = strtolower( preg_replace("[^a-zA-Z0-9-]", "-", 
    strtr(utf8_decode(trim($nome)), 
    utf8_decode("áàãâéêíóôõúüñçÁÀÃÂÉÊÍÓÔÕÚÜÑÇ"), 
    "aaaaeeiooouuncAAAAEEIOOOUUNC-")) ); 

$url = preg_replace('/[ -]+/' , '-' , $nome_novo); 

$id = $_POST['id'];  

// validar nome curso duplicado
$query = $pdo->query("SELECT * FROM $tabela WHERE nome = '$nome'"); 
$res = $query->fetchAll(PDO::FETCH_ASSOC); 
if(count($res) > 0 && $res[0]['id'] != $id){ 
    echo 'Curso já Cadastrado com este nome, escolha Outro!'; 
    exit(); 
}  

// buscar foto atual
$query = $pdo->query("SELECT * FROM $tabela WHERE id = '$id'"); 
$res = $query->fetchAll(PDO::FETCH_ASSOC); 
$foto = (count($res) > 0) ? $res[0]['imagem'] : 'sem-foto.png';  

// upload da imagem
$nome_img_original = $_FILES['foto']['name'] ?? '';
$imagem_temp = $_FILES['foto']['tmp_name'] ?? '';
if (!empty($nome_img_original)) {
    $ext = strtolower(pathinfo($nome_img_original, PATHINFO_EXTENSION));
    if (in_array($ext, ['png', 'jpg', 'jpeg', 'gif'])) {
        if ($foto != "sem-foto.png") {
            @unlink('../../img/cursos/' . $foto);
        }
        $nome_img = 'curso_' . preg_replace('/[^a-zA-Z0-9]/', '', uniqid('', true)) . mt_rand(1000, 9999) . '.' . $ext;
        $caminho = '../../img/cursos/' . $nome_img;
        $foto = $nome_img;
        move_uploaded_file($imagem_temp, $caminho);
    } else {
        echo 'Extens�o de Imagem n�o permitida!';
        exit();
    }
}

try {
    if($id == ""){  
        $query = $pdo->prepare("INSERT INTO $tabela 
            SET nome = :nome, 
                desc_rapida = :desc_rapida, 
                desc_longa = :desc_longa, 
                valor = :valor, 
                professor = :professor, 
                categoria = :categoria, 
                imagem = :foto, 
                status = 'Aguardando', 
                carga = :carga, 
                arquivo = :arquivo, 
                ano = :ano, 
                palavras = :palavras, 
                grupo = :grupo, 
                nome_url = :url, 
                pacote = :pacote, 
                sistema = :sistema, 
                link = :link, 
                tecnologias = :tecnologias, 
                promocao = :promocao, 
                comissao = :comissao, 
                split = :split,
                is_matricula = :is_matricula"); 
    } else {  
        $query = $pdo->prepare("UPDATE $tabela SET 
                nome = :nome, 
                desc_rapida = :desc_rapida, 
                desc_longa = :desc_longa, 
                valor = :valor, 
                professor = :professor, 
                categoria = :categoria, 
                imagem = :foto, 
                carga = :carga, 
                arquivo = :arquivo,  
                ano = :ano,
                palavras = :palavras, 
                grupo = :grupo, 
                nome_url = :url, 
                pacote = :pacote, 
                sistema = :sistema, 
                link = :link, 
                tecnologias = :tecnologias, 
                promocao = :promocao, 
                comissao = :comissao, 
                split = :split,
                is_matricula = :is_matricula 
            WHERE id = :id"); 
        $query->bindValue(":id", $id);
    }

    // bind values
    $query->bindValue(":nome", $nome); 
    $query->bindValue(":desc_rapida", $desc_rapida); 
    $query->bindValue(":desc_longa", $desc_longa); 
    $query->bindValue(":valor", $valor); 
    $query->bindValue(":professor", $id_usuario); 
    $query->bindValue(":categoria", $categoria); 
    $query->bindValue(":foto", $foto); 
    $query->bindValue(":carga", $carga); 
    $query->bindValue(":arquivo", $arquivo); 
    $query->bindValue(":ano", $ano_atual); 
    $query->bindValue(":palavras", $palavras); 
    $query->bindValue(":grupo", $grupo); 
    $query->bindValue(":url", $url); 
    $query->bindValue(":pacote", $pacote); 
    $query->bindValue(":sistema", $sistema); 
    $query->bindValue(":link", $link); 
    $query->bindValue(":tecnologias", $tecnologias); 
    $query->bindValue(":promocao", $promocao); 
    $query->bindValue(":comissao", $comissao); 
    $query->bindValue(":split", "$split");
    $query->bindValue(":is_matricula", $is_matricula); 

    $query->execute(); 

    echo 'Salvo com Sucesso';  

} catch (PDOException $e) {
    echo "Erro ao salvar no banco: " . $e->getMessage();
}
?>
