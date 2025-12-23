<?php 
require_once("../conexao.php");

$nome = $_POST['nome_usu'];
$cpf = $_POST['cpf_usu'];
$email = $_POST['email_usu'];
$senha = $_POST['senha_usu'];
$senha_crip = md5($senha);
$id = $_POST['id_usu'];
$foto = $_POST['foto_usu'];

$rg = $_POST['rg_usu'];
$expedicao = $_POST['expedicao_usu'];
$nascimento = $_POST['nascimento_usu'];
$telefone = $_POST['telefone_usu'];
$cep = $_POST['cep_usu'];
$sexo = $_POST['sexo_usu'];
$endereco = $_POST['endereco_usu'];
$numero = $_POST['numero_usu'];
$bairro = $_POST['bairro_usu'];
$cidade = $_POST['cidade_usu'];
$estado = $_POST['estado_usu'];
$mae = $_POST['mae_usu'];
$pai = $_POST['pai_usu'];
$naturalidade = $_POST['naturalidade_usu'];



$query = $pdo->query("SELECT * FROM usuarios where id = '$id'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$id_pessoa = $res[0]['id_pessoa'];

//validar email duplicado
$query = $pdo->query("SELECT * FROM usuarios where usuario = '$email'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);
if($total_reg > 0 and $res[0]['id'] != $id){
	echo 'Email já Cadastrado, escolha Outro!';
	exit();
}


//validar cpf duplicado
$query = $pdo->query("SELECT * FROM usuarios where cpf = '$cpf'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);
if($total_reg > 0 and $res[0]['id'] != $id){
	echo 'CPF já Cadastrado, escolha Outro!';
	exit();
}





//SCRIPT PARA SUBIR FOTO NO SERVIDOR
$nome_img = date('d-m-Y H:i:s') .'-'.@$_FILES['foto']['name'];
$nome_img = preg_replace('/[ :]+/' , '-' , $nome_img);

$caminho = 'img/perfil/' .$nome_img;

$imagem_temp = @$_FILES['foto']['tmp_name']; 

if(@$_FILES['foto']['name'] != ""){
	$ext = pathinfo($nome_img, PATHINFO_EXTENSION);   
	if($ext == 'png' or $ext == 'jpg' or $ext == 'jpeg' or $ext == 'gif'){ 
	
			//EXCLUO A FOTO ANTERIOR
			if($foto != "sem-perfil.jpg"){
				@unlink('img/perfil/'.$foto);
			}

			$foto = $nome_img;
		
		move_uploaded_file($imagem_temp, $caminho);
	}else{
		echo 'Extensão de Imagem não permitida!';
		exit();
	}
}




$query = $pdo->query("SELECT * FROM alunos where id = '$id'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);
if($total_reg > 0){
	$foto2 = $res[0]['arquivo'];
}else{
	$foto2 = '';
}





//SCRIPT PARA SUBIR arquivo NO SERVIDOR
$nome_img = date('d-m-Y H:i:s') .'-'.@$_FILES['arquivo_2']['name'];
$nome_img = preg_replace('/[ :]+/' , '-' , $nome_img);

$caminho = 'img/arquivos/' .$nome_img;

$imagem_temp = @$_FILES['arquivo_2']['tmp_name']; 

if(@$_FILES['arquivo_2']['name'] != ""){
	$ext = pathinfo($nome_img, PATHINFO_EXTENSION);   
	if($ext == 'zip' or  $ext == 'pdf' or $ext == 'PDF' or $ext == 'rar'){ 
						
			//EXCLUO A FOTO ANTERIOR
			if($foto2 != "sem-arquivo"){
				@unlink('img/arquivos/'.$foto);
			}

			$foto2 = $nome_img;
		
		move_uploaded_file($imagem_temp, $caminho);
	}else{
		echo 'Extensão de Imagem não permitida!';
		exit();
	}
}







//atualizar os dados do usuário
$query = $pdo->prepare("UPDATE usuarios SET nome = :nome, cpf = :cpf, usuario = :usuario, senha = :senha, senha_crip = '$senha_crip', foto = '$foto' where id = '$id'");

$query->bindValue(":nome", "$nome");
$query->bindValue(":usuario", "$email");
$query->bindValue(":cpf", "$cpf");
$query->bindValue(":senha", "$senha");
$query->execute();


$query = $pdo->prepare("UPDATE alunos SET nome = :nome, cpf = :cpf, email = :email, telefone = :telefone, rg = :rg, expedicao = :expedicao,  nascimento = :nascimento, cep = :cep, sexo = :sexo, endereco = :endereco, numero = :numero, bairro = :bairro, cidade = :cidade, estado = :estado, mae = :mae, pai = :pai, naturalidade = :naturalidade where id = '$id_pessoa'");

$query->bindValue(":nome", "$nome");
$query->bindValue(":cpf", "$cpf");
$query->bindValue(":email", "$email");
$query->bindValue(":telefone", "$telefone");
$query->bindValue(":rg", "$rg");
$query->bindValue(":expedicao", "$expedicao");
$query->bindValue(":nascimento", "$nascimento");
$query->bindValue(":cep", "$cep");
$query->bindValue(":sexo", "$sexo");
$query->bindValue(":endereco", "$endereco");
$query->bindValue(":numero", "$numero");
$query->bindValue(":bairro", "$bairro");
$query->bindValue(":cidade", "$cidade");
$query->bindValue(":estado", "$estado");
$query->bindValue(":mae", "$mae");
$query->bindValue(":pai", "$pai");
$query->bindValue(":naturalidade", "$naturalidade");
$query->execute();

echo 'Editado com Sucesso';

 ?>