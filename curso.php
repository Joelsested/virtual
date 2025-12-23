<?php
@session_start();
include('pgtos/cartao/ApiConfig.php');
?>



<body>

	<script src="https://sdk.mercadopago.com/js/v2"></script>
<script src="/sistema/painel-admin/js/sweetalert2.js"></script>
</body>

<script>

	const mp = new MercadoPago("<?php echo $public_key ?>");

</script>

<?php

require_once("sistema/conexao.php");

include('pgtos/cartao/ApiConfig.php');



$id_do_aluno = @$_SESSION['id'];




$query2 = $pdo->query("SELECT * FROM usuarios where id = '$id_do_aluno'");

$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);

if (@count($res2) > 0) {

	$id_pessoa = $res2[0]['id_pessoa'];

	$query3 = $pdo->query("SELECT * FROM alunos where id = '$id_pessoa'");

	$res3 = $query3->fetchAll(PDO::FETCH_ASSOC);

	if (@count($res3) > 0) {

		$quant_cartoes = $res3[0]['cartao'];

	}

}





$queryPix = $pdo->query("SELECT desconto_pix FROM config");

$resPix = $queryPix->fetchAll(PDO::FETCH_ASSOC);



$descontoPix = json_encode($resPix[0]['desconto_pix']);







$url = $_GET['url'];



$nivel = @$_SESSION['nivel'];

if ($nivel == "Aluno") {

	$modal = 'Pagamento';

} else if ($nivel == "Administrador" || $nivel == "Professor") {

	$modal = 'Matricular';

} else {

	$modal = 'Login';

}



$query = $pdo->query("SELECT * FROM cursos where nome_url = '$url'");

$res = $query->fetchAll(PDO::FETCH_ASSOC);

$total_reg = @count($res);

if ($total_reg > 0) {



	$palavras_chaves = $res[0]['palavras'];

	$nome_curso_titulo = $res[0]['nome'];



	$id = $res[0]['id'];

	$nome = $res[0]['nome'];

	$desc_rapida = $res[0]['desc_rapida'];

	$desc_longa = $res[0]['desc_longa'];

	$valor = $res[0]['valor'];

	$professor = $res[0]['professor'];

	$categoria = $res[0]['categoria'];

	$foto = $res[0]['imagem'];

	$status = $res[0]['status'];

	$carga = $res[0]['carga'];

	$mensagem = $res[0]['mensagem'];

	$arquivo = $res[0]['arquivo'];

	$ano = $res[0]['ano'];

	$palavras = $res[0]['palavras'];

	$grupo = $res[0]['grupo'];

	$nome_url = $res[0]['nome_url'];

	$pacote = $res[0]['pacote'];



	$link = $res[0]['link'];



	$promocao = $res[0]['promocao'];

	$matriculas = $res[0]['matriculas'];

	$valor_do_curso = $res[0]['valor'];



	$id_do_curso_pag = $res[0]['id'];

	$foto_do_curso = $res[0]['imagem'];

	$nome_do_curso_pag = $res[0]['nome'];



	if ($promocao > 0) {

		$valor_curso = $promocao;

	} else {

		$valor_curso = $valor;

	}



	$valor_real_curso = $valor_curso;



	$valor_pix = $valor_curso;



	if ($desconto_pix > 0) {

		$valor_pix = $valor_curso - ($valor_curso * ($desconto_pix / 100));

	}





	$query2 = $pdo->query("SELECT * FROM usuarios where id = '$professor'");

	$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);

	$nome_professor = $res2[0]['nome'];



	$query2 = $pdo->query("SELECT * FROM categorias where id = '$categoria'");

	$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);

	$nome_categoria = $res2[0]['nome'];



	$query2 = $pdo->query("SELECT * FROM grupos where id = '$grupo'");

	$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);

	$nome_grupo = $res2[0]['nome'];



	$query2 = $pdo->query("SELECT * FROM aulas where curso = '$id' order by id asc");

	$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);

	$aulas = @count($res2);

	$aula1 = @$res2[0]['link'];





	//buscar valor da matricula

	$query = $pdo->query("SELECT * FROM matriculas where id_curso = '$id_do_curso_pag' and aluno = '$id_do_aluno' ");

	$res = $query->fetchAll(PDO::FETCH_ASSOC);

	if (@count($res) > 0) {

		$valor_curso = $res[0]['subtotal'];

		$valor_pix = $valor_curso;

		$status_mat = $res[0]['status'];

		if ($desconto_pix > 0) {

			$valor_pix = $valor_curso - ($valor_curso * ($desconto_pix / 100));

		}

	}





	//FORMATAR VALORES

	$valorF = number_format($valor, 2, ',', '.');

	$desc_longa = str_replace('"', "**", $desc_longa);

	$promocaoF = number_format($promocao, 2, ',', '.');

	$valor_cursoF = number_format($valor_curso, 2, ',', '.');

	$valor_pixF = number_format($valor_pix, 2, ',', '.');

	$valor_real_cursoF = number_format($valor_real_curso, 2, ',', '.');













	$query2 = $pdo->query("SELECT * FROM matriculas where id_curso = '$id' and status != 'Aguardando'");

	$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);

	$total_alunos = @count($res2);

}



require_once("cabecalho.php");

?>



<hr>



<div class="container">



	<input type="hidden" name="" id="id_do_aluno" value="<?php echo $id_do_aluno ?>">



	<div class="row">

		<div class="col-md-9 col-sm-12">

			<a id="btn-pagamento" class="valor" title="Comprar o Curso - Liberação Imediata" href="#"
				onclick="pagamento('<?php echo $id ?>', '<?php echo $nome ?>', '<?php echo $valor_cursoF ?>', '<?php echo $modal ?>')">

				<p class="titulo-curso"><?php echo $nome ?> - <small><small><?php echo $desc_rapida ?></small></small>
				</p>

			</a>

		</div>



		<div class="col-md-3 col-sm-12 ocultar-mobile">

			<a href="#" onclick="enviarEmail('<?php echo $nome ?>')">

				<p><i class="fa fa-question-circle mr-1" style="margin-right:2px; color:#b00404"></i><span
						class="texto-15">Dúvidas? Contate-nos</span></p>

			</a>

		</div>

	</div>



	<div class="row">

		<div class="col-md-9 col-sm-12">

			<a class="valor" title="Comprar o Curso - Liberação Imediata" href="#"
				onclick="pagamento('<?php echo $id ?>', '<?php echo $nome ?>', '<?php echo $valor_cursoF ?>', '<?php echo $modal ?>')"><span
					class="valor"><i class="fa fa-shopping-cart mr-1 valor" title="Comprar o Curso - Pagamento Único"
						style="margin-right:3px"></i>Comprar R$ <?php echo $valor_real_cursoF; ?> <small><small><span
								class="inicie"><i class="fa fa-arrow-left mr-1 inicie"
									style="margin-right:3px"></i>Inicie Imediatamente</span></small></small> </span></a>



			<a href="https://www.youtube.com/watch?v=dHvkSxQcNkY" title="Dúvidas? Clique aqui para assitir o vídeo"
				target="_blank"><i style="margin-left:3px; color:#b00404"
					class="fa fa-question-circle text-danger ml-2 "></i></a>





		</div>



		<div class="col-md-3 col-sm-12 imagem-cartao margem-sup">

			<a title="Comprar o Curso - Liberação Imediata" href="#"
				onclick="pagamento('<?php echo $id ?>', '<?php echo $nome ?>', '<?php echo $valor_cursoF ?>', '<?php echo $modal ?>')">

				<small><span class="neutra"> EM ATÉ 12 VEZES</span></small><br>

				<img src="img/01mercado.png" width="100%">

			</a>

		</div>

	</div>



	<hr>







	<div class="row">



		<div class="col-md-9 col-sm-12" style="margin-bottom:10px">

			<div class="row">



				<div class="col-md-3 col-sm-12" style="margin-bottom:10px">

					<a href="#"
						onclick="pagamento('<?php echo $id ?>', '<?php echo $nome ?>', '<?php echo $valor_cursoF ?>', '<?php echo $modal ?>')"
						title="Iniciar Agora"><img src="sistema/painel-admin/img/cursos/<?php echo $foto ?>"
							width="100%"></a>

				</div>



				<div class="col-md-8 col-sm-12">

					<div class="neutra"
						style="margin-bottom:10px; overflow: scroll; max-height:200px; scrollbar-width: thin; padding-bottom:10px">
						<?php echo $desc_longa ?>
					</div>





					<div class="row">



						<div class="col-md-7 esquerda-mobile">

							<span class="text-muted itens texto-menor-mobile"><i class="fa fa-user mr-1 itens"
									style="margin-right: 2px"></i>Professor : <?php echo $nome_professor; ?></span>

						</div>







					</div>





					<div class="row mt-1">



						<div class="col-md-7 esquerda-mobile">

							<span class="text-muted itens texto-menor-mobile"><i style="margin-right: 2px"
									class="fa fa-list-alt mr-1 itens"></i>Categoria :
								<?php echo $nome_categoria; ?></span>

						</div>



						<div class="col-md-7 esquerda-mobile">

							<span class="text-muted itens texto-menor-mobile"><i style="margin-right: 2px"
									class="fa fa-certificate mr-1 itens"></i>Certificado : <?php echo $carga; ?>
								Horas</span>

						</div>



					</div>















				</div>

			</div>

		</div>



		<hr>



		<div class="row">

			<div class="col-md-4 col-sm-12" style="margin-bottom:20px">



				<div class="esquerda-mobile mb-2">

					<span class="text-muted topicos mr-3"><i class="fa fa-check-square mr-1 topicos"
							style="margin-right: 2px"></i>Disponibilização Imediata</span>

					<br>



					<span class="text-muted topicos mr-3"><i class="fa fa-check-square mr-1 topicos"
							style="margin-right: 2px"></i>Mensalidade que cabe no seu bolso</span>



					<br>



					<span class="text-muted topicos mr-3"><i class="fa fa-check-square mr-1 topicos"
							style="margin-right: 2px"></i>Certificado No final do Curso</span> <br>











				</div>

				<div class="direita-mobile mb-2" style="margin-bottom:20px">





					<span class="text-muted topicos mr-3"><i class="fa fa-check-square mr-1 topicos"
							style="margin-right: 2px"></i>Conteúdo Atualizado</span>







					<br>

					<span class="text-muted topicos mr-3"><i class="fa fa-check-square mr-1 topicos"
							style="margin-right: 2px"></i>Estude a Hora que quiser</span>



					<br>

					<span class="text-muted topicos mr-3"><i class="fa fa-check-square mr-1 topicos"
							style="margin-right: 2px"></i>Estude de onde estiver</span>





				</div>



			</div>



			<div class="col-md-8 col-sm-12">

				<iframe class="video-mobile" width="100%" height="300" src="<?php echo $aula1 ?>" frameborder="0"
					allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen
					id="target-video"></iframe>

			</div>

		</div>



		<div class="row">













			<div class="col-md-12" style="margin-bottom: 20px">



				<p class="titulo-curso"><small>Cursos Relacionados <small><small>(Semelhantes ou de mesmo
								pacote)</small></small></small> </p>



				<?php

				$query = $pdo->query("SELECT * FROM cursos where status = 'Aprovado' and sistema = 'Não' and grupo = '$grupo' and id != '$id' ORDER BY id desc limit $itens_rel");

				$res = $query->fetchAll(PDO::FETCH_ASSOC);

				$total_reg = @count($res);

				if ($total_reg > 0) {

					?>



					<section id="portfolio">



						<div class="row" style="margin-left:5px; margin-right:5px; margin-top:-40px;">



							<?php

							for ($i = 0; $i < $total_reg; $i++) {

								foreach ($res[$i] as $key => $value) {

								}

								$id = $res[$i]['id'];

								$nome = $res[$i]['nome'];

								$desc_rapida = $res[$i]['desc_rapida'];

								$valor = $res[$i]['valor'];

								$foto = $res[$i]['imagem'];

								$promocao = $res[$i]['promocao'];

								$url = $res[$i]['nome_url'];



								$valorF = number_format($valor, 2, ',', '.');

								$promocaoF = number_format($promocao, 2, ',', '.');



								$query2 = $pdo->query("SELECT * FROM aulas where curso = '$id' and num_aula = 1 order by id asc");

								$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);

								$total_reg2 = @count($res2);

								if ($total_reg2 > 0) {

									$primeira_aula = $res2[0]['link'];

								} else {

									$primeira_aula = "";

								}





								?>



								<a href="curso-de-<?php echo $url ?>" title="Detalhes do Curso">

									<div class="col-xs-6 col-sm-6 col-md-3 col-lg-3 portfolio-item">

										<div class="portfolio-one">

											<div class="portfolio-head" style="height:170px">

												<div class="portfolio-img"><img alt=""
														src="sistema/painel-admin/img/cursos/<?php echo $foto ?>"
														height="170px"></div>



											</div>

											<!-- End portfolio-head -->



											<div class="portfolio-content" style="text-align: center; ">

												<h6 class="title"><?php echo $nome ?></h6></small>

												<p style="margin-top:-10px;"><small><?php echo $desc_rapida ?></small></p>



												<?php if ($promocao > 0) { ?>

													<div class="product-bottom-details">

														<div class="product-price-menor2"><small><small>De
																	<?php echo $valorF ?></small></small> R$
															<?php echo $promocaoF ?>
														</div>

													</div>

												<?php } else { ?>

													<div class="product-bottom-details">

														<div class="product-price-menor">R$ <?php echo $valorF ?></div>

													</div>

												<?php } ?>

											</div>



											<!-- End portfolio-content -->

										</div>

										<!-- End portfolio-item -->

									</div>

								</a>





							<?php } ?>







						</div>

					</section>







				<?php } ?>





			</div>



			<div class="col-md-12" style="margin-bottom: 20px">



				<p class="titulo-curso"><small>Comentário dos Alunos</small></p>



				<?php

				$query = $pdo->query("SELECT * FROM avaliacoes where curso = '$id_do_curso_pag' and nota > 2 ORDER BY id desc");

				$res = $query->fetchAll(PDO::FETCH_ASSOC);

				$total_reg = @count($res);

				if ($total_reg > 0) {

					for ($i = 0; $i < $total_reg; $i++) {

						foreach ($res[$i] as $key => $value) {

						}



						$id_ava = $res[$i]['id'];

						$aluno_ava = $res[$i]['aluno'];

						$nota = $res[$i]['nota'];

						$comentario_ava = $res[$i]['comentario'];

						$data = $res[$i]['data'];

						$dataF = implode('/', array_reverse(explode('-', $data)));



						$query2 = $pdo->query("SELECT * FROM usuarios where id = '$aluno_ava' ORDER BY id desc");

						$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);

						$nome_aluno = $res2[0]['nome'];

						$foto_aluno = $res2[0]['foto'];









						if ($id_do_aluno == $aluno_ava or $nivel == 'Administrador') {

							$excluir = '';

						} else {

							$excluir = 'ocultar';

						}

						?>







						<div>

							<span class="nome-aluno"> <img src="sistema/painel-aluno/img/perfil/<?php echo $foto_aluno ?>"
									width="25" height="25" style="border-radius: 100%;"> <?php echo $nome_aluno ?>

							</span>

							<span class="neutra ocultar-mobile" style="margin-left: 10px"><?php echo $dataF ?></span>



							<?php if ($nota == 3) { ?>



								<span class="estrelas">

									<i class="estrela fa fa-star"></i>

									<i class="estrela fa fa-star"></i>

									<i class="estrela fa fa-star"></i>

								</span>



								<span class="ml-1 text-muted ocultar-mobile"><i class="neutra"> - Bom</i></span>



							<?php } ?>



							<?php if ($nota == 4) { ?>



								<span class="estrelas">

									<i class="estrela fa fa-star"></i>

									<i class="estrela fa fa-star"></i>

									<i class="estrela fa fa-star"></i>

									<i class="estrela fa fa-star"></i>

								</span>



								<span class="ml-1 text-muted ocultar-mobile"><i class="neutra"> - Muito Bom</i></span>



							<?php } ?>



							<?php if ($nota == 5) { ?>



								<span class="estrelas">

									<i class="estrela fa fa-star"></i>

									<i class="estrela fa fa-star"></i>

									<i class="estrela fa fa-star"></i>

									<i class="estrela fa fa-star"></i>

									<i class="estrela fa fa-star"></i>

								</span>



								<span class="ml-1 text-muted ocultar-mobile"><i class="neutra"> - Espetacular</i></span>



							<?php } ?>



							<li class="dropdown head-dpdn2 <?php echo $excluir ?>" style="display: inline-block;">

								<a title="Excluir Avaliação" href="#" class="dropdown-toggle <?php echo $excluir ?>"
									data-toggle="dropdown" aria-expanded="false"><big><i
											class="fa fa-trash-o text-danger"></i></big></a>



								<ul class="dropdown-menu" style="margin-left:-150px;">

									<li>

										<div class="notification_desc2">

											<p>Confirmar Exclusão? <a href="#"
													onclick="excluirAvaliacao('<?php echo $id_ava ?>')"><span
														class="text-danger">Sim</span></a></p>

										</div>

									</li>

								</ul>

							</li>







							<div class="comentario">

								<i class="neutra">"<?php echo $comentario_ava ?>"</i>

							</div>





						</div>



						<hr>





					<?php }

				} else {

					echo 'Este curso ainda não possui avaliações!';

				}

				?>







			</div>



		</div>



	</div>















	<div class="col-md-3 col-sm-12">

		<?php

		$query_m = $pdo->query("SELECT * FROM sessao where curso = '$id_do_curso_pag' ORDER BY id asc");

		$res_m = $query_m->fetchAll(PDO::FETCH_ASSOC);

		$total_reg_m = @count($res_m);

		if ($total_reg_m > 0) {

			$primeira_sessao = $res_m[0]['id'];

			for ($i_m = 0; $i_m < $total_reg_m; $i_m++) {

				foreach ($res_m[$i_m] as $key => $value) {

				}

				$sessao = $res_m[$i_m]['id'];

				$nome_sessao = $res_m[$i_m]['nome'];

				?>



				<p class="titulo-curso"><small><?php echo $nome_sessao ?></small></p>



				<?php



				$query = $pdo->query("SELECT * FROM aulas where curso = '$id_do_curso_pag' and sessao = '$sessao' ORDER BY num_aula asc");

				$res = $query->fetchAll(PDO::FETCH_ASSOC);

				$total_reg = @count($res);



				if ($total_reg > 0) {



					for ($i = 0; $i < $total_reg; $i++) {

						foreach ($res[$i] as $key => $value) {

						}

						$id_aula = $res[$i]['id'];

						$nome_aula = $res[$i]['nome'];

						$num_aula = $res[$i]['num_aula'];

						$sessao_aula = $res[$i]['sessao'];

						if ($num_aula <= $aulas_lib /*&& $primeira_sessao == $sessao_aula*/) {

							$link = $res[$i]['link'];

							?>

							<a href="#" onclick="abrirAula('<?php echo $link ?>', '<?php echo $num_aula ?>', '<?php echo $nome_aula ?>')"
								title="Ver Aula"><span class="neutra-forte">Aula <?php echo $num_aula ?> -
									<?php echo $nome_aula ?></span><br></a>

							<?php

						} else {

							$link = '';



							?>



							<a href="#"
								onclick="pagamento('<?php echo $id ?>', '<?php echo $nome ?>', '<?php echo $valor_cursoF ?>', '<?php echo $modal ?>')"
								title="Adquirir Curso"><span class="neutra-muted">Aula <?php echo $num_aula ?> -
									<?php echo $nome_aula ?></span><br></a>



							<?php

						}

					}

				} else {

					echo '<span class="neutra">Nenhuma aula Cadastrada</span>';

				}



				echo '<hr>';

			}

		} else {



			$query = $pdo->query("SELECT * FROM aulas where curso = '$id_do_curso_pag' ORDER BY num_aula asc");

			$res = $query->fetchAll(PDO::FETCH_ASSOC);

			$total_reg = @count($res);



			if ($total_reg > 0) {



				for ($i = 0; $i < $total_reg; $i++) {

					foreach ($res[$i] as $key => $value) {

					}

					$id_aula = $res[$i]['id'];

					$nome_aula = $res[$i]['nome'];

					$num_aula = $res[$i]['num_aula'];

					if ($num_aula <= $aulas_lib) {

						$link = $res[$i]['link'];

						?>

						<a href="#" onclick="abrirAula('<?php echo $link ?>', '<?php echo $num_aula ?>', '<?php echo $nome_aula ?>')"
							title="Ver Aula"><span class="neutra-forte">Aula <?php echo $num_aula ?> -
								<?php echo $nome_aula ?></span><br></a>

						<?php

					} else {

						$link = '';



						?>



						<a href="#"
							onclick="pagamento('<?php echo $id ?>', '<?php echo $nome ?>', '<?php echo $valor_cursoF ?>', '<?php echo $modal ?>')"
							title="Adquirir Curso"><span class="neutra-muted">Aula <?php echo $num_aula ?> -
								<?php echo $nome_aula ?></span><br></a>



						<?php

					}

				}

			} else {

				echo '<span class="neutra">Nenhuma aula Cadastrada</span>';

			}

		}



		?>





	</div>

</div>





</div>



<br>

<br>



<br>

<br>



<br>

<br>





<hr>













<!-- Modal Contato -->

<div class="modal fade" id="modalContato" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
	aria-hidden="true">

	<div class="modal-dialog modal-lg" role="document">

		<div class="modal-content">

			<div class="modal-header">

				<h4 class="modal-title" id="exampleModalLabel">Faça sua Pergunta</h4>

				<button style="margin-top: -25px" type="button" class="close" data-dismiss="modal" aria-label="Close">

					<span class="neutra" aria-hidden="true">&times;</span>

				</button>

			</div>



			<div class="modal-body">





				<form id="form-contato" class="contact-form" name="contact-form">

					<div class="row">

						<div class="col-sm-5 col-sm-offset-1">

							<div class="form-group">

								<label>Nome *</label>

								<input type="text" name="nome" id="nome" class="form-control" required="required">

							</div>

							<div class="form-group">

								<label>Email *</label>

								<input type="email" name="email" id="email" class="form-control" required="required">

							</div>

							<div class="form-group">

								<label>WhatssApp</label>

								<input type="text" name="telefone" id="telefone" class="form-control">

							</div>



							<div class="form-check">

								<input type="checkbox" class="form-check-input" id="novidades" name="novidades"
									value="Sim" checked>

								<label class="form-check-label text-muted" for="exampleCheck1"><small>Receber nossas
										novidades por email?</small></label>

							</div>



						</div>

						<div class="col-sm-5">

							<div class="form-group">

								<label>Mensagem *</label>

								<textarea name="mensagem" id="mensagem" required="required" class="form-control"
									rows="8"></textarea>

							</div>

							<div class="form-group">

								<button id="btn-enviar-contato" type="submit" name="submit"
									class="btn btn-default submit-button">Enviar <i
										class="fa fa-caret-right"></i></button>

							</div>

						</div>

					</div>



					<input type="hidden" name="nome_curso" id="nome_curso">







				</form>











			</div>

			<div class="modal-footer">

				<small>

					<div align="center" id="msg"></div>

				</small>

			</div>



		</div>

	</div>

</div>

















<!-- Modal Login -->

<div class="modal fade" id="Login" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">

	<div class="modal-dialog modal-lg" role="document">

		<div class="modal-content">

			<div class="modal-header">

				<h4 class="modal-title" id="exampleModalLabel"><span class="neutra" id="nome_curso_Login"></span> -
					R$<span class="neutra" id="valor_curso_Login"></span></h4>

				<button id="btn-fechar-login" type="button" class="close" data-dismiss="modal" aria-label="Close"
					style="margin-top: -25px">

					<span class="neutra" aria-hidden="true">&times;</span>

				</button>

			</div>



			<div class="modal-body">





				<div class="container-fluid">

					<div class="row">

						<div class="col-sm-5">



							<h5 style="font-weight: 500" align="center"><span>FAÇA SEU LOGIN</span></h5>

							<hr>

							<form id="form-login" method="post">

								<div class="form-group">

									<label>Email ou CPF*</label>

									<input type="email" name="usuario" id="email_login" class="form-control"
										required="required" placeholder="Digite seu Email ou CPF">

								</div>

								<div class="form-group">

									<label>Senha *</label>

									<input type="password" name="senha" id="senha_login" class="form-control"
										required="" placeholder="Digite sua Senha">

								</div>

								<div class="form-group">

									<button type="submit" class="btn btn-default submit-button">Login <i
											class="fa fa-caret-right"></i></button>

								</div>



								<small>

									<div align="center" id="msg-login2"></div>

								</small>

							</form>



						</div>

						<div class="col-sm-1">

							<h6 style="font-weight: 500" align="center"><span class="ocultar-mobile">OU</span></h6>

							<hr>

						</div>

						<div class="col-sm-6">

							<h5 style="font-weight: 500" align="center"><span>CADASTRE-SE</span></h5>

							<hr>



							<form id="form-cadastro">



								<div class="form-group">

									<label>Nome *</label>

									<input type="text" name="nome" id="nome_cadastro" class="form-control"
										required="required" placeholder="Digite seu Nome">

								</div>



								<div class="form-group">

									<label>Email *</label>

									<input type="email" name="email" id="email_cadastro" class="form-control"
										required="required" placeholder="Digite seu Email">

								</div>

								<div class="row">

									<div class="col-sm-6">

										<div class="form-group">

											<label>Senha *</label>

											<input type="password" name="senha" id="senha_cadastro" class="form-control"
												required="" placeholder="Digite sua Senha">

										</div>

									</div>



									<div class="col-sm-6">

										<div class="form-group">

											<label>Cofirmar Senha *</label>

											<input type="password" name="conf_senha" id="conf_senha_cadastro"
												class="form-control" required="" placeholder="Digite sua Senha">

										</div>

									</div>



								</div>





								<div class="form-check">

									<input type="checkbox" class="form-check-input" id="termos" name="termos"
										value="Sim" required>

									<label class="form-check-label" for="exampleCheck1"><small>Aceitar <a href="termos"
												target="_blank">Termos e Condições</a> e <a href="politica"
												target="_blank">Politíca de Privacidade</a></small></label>

								</div>



								<div class="form-group">

									<button type="submit" class="btn btn-default submit-button">Cadastre-se <i
											class="fa fa-caret-right"></i></button>

								</div>



							</form>

						</div>

					</div>

				</div>







			</div>

			<div class="modal-footer">

				<small>

					<div align="center" id="msg-login"></div>

				</small>

			</div>



		</div>

	</div>

</div>














<!-- Modal Pagamento Novo -->

<div class="modal fade scrollbar-mobile" id="Pagamento" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
	aria-hidden="true" data-backdrop="static" style="overflow: scroll; height:100%; scrollbar-width: thin;">

	<div class="modal-dialog" role="document">

		<div class="modal-content">

			<div class="modal-header">

				<h4 class="modal-title" id="exampleModalLabel"><span class="neutra ocultar-mobile">Liberação Automática
						-</span> <span class="neutra" id="nome_curso_Pagamento"></span> - R$<span class="neutra"
						id="valor_curso_Pagamento"></span></h4>

				<button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top: -25px">
					<span class="neutra" aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<?php if (@$valor_do_curso == "0") {

					echo 'Este curso é Gratuito, <a href="sistema/painel-aluno" target="_blank"><i>clique aqui</i></a> clique aqui para ir para seu painel e acompanhar o curso!';

				} else {

					?>

					<?php if (@$status_mat != "Matriculado" and @$status_mat != "Finalizado") {

						?>

						<div class="row">

							<div class="<?php echo $classe_col ?> col-sm-12" style="margin-bottom: 10px">

								<div class="row botoes-mobile" style="margin-top: 25px" align="center">

									<form id="cupom-desconto">

										<div class="col-sm-9 esquerda-mobile-input-botao">

											<div class="form-group">

												<input type="text" name="cupom" id="cupom" class="form-control" required
													placeholder="Código do Cupom">



											</div>

										</div>

										<div class="col-sm-3 direita-mobile-input-botao" style="margin-left:-20px">

											<button id="btn-cupom" type="submit" name="submit"
												class="btn btn-success botao-laranja submit-button">Aplicar </button>
										</div>

										<input type="hidden" name="id_curso_cupom" value="<?php echo $id_do_curso_pag ?>">

									</form>

								</div>
								<small>

									<div id="msg-cupom" align="center"></div>

								</small>
							</div>

							<div align="left" class="<?php echo $classe_col ?> col-sm-12 " style="margin-top: 15px">




								<button type="submit" onclick="realizarMatricula();" class="btn btn-primary submit-button"
									style="margin-top: 24px; width: 100%;">

									<i class="fa fa-cart-plus" aria-hidden="true"></i> Realizar Matricula e prosseguir para
									pagamento

								</button>

							</div>
						</div>
						<br>

					<?php } else {

						echo 'Você já possui este curso!';

					} ?>

				</div>

				<div class="modal-footer">

					<div align="center">

						Se já efetuou o pagamento, <a href="sistema/painel-aluno" target="_blank"><i>clique aqui</i></a>
						para ir para o painel do aluno!
						<?php if (@$quant_cartoes >= $cartoes_fidelidade and $valor_real_curso <= $valor_max_cartao) { ?>

							<div></div>

						<?php } else { ?>

							<i class="fa fa-whatsapp neutra-escura" style="color:#FFF; margin-left:15px"></i><a
								href="http://api.whatsapp.com/send?1=pt_BR&phone=<?php echo $tel_whatsapp ?>"
								target="_blank"><?php echo $tel_sistema ?></a>

						<?php } ?>



					</div>

				</div>
			<?php } ?>
		</div>

	</div>

</div>









<!-- Modal Matricular -->

<div class="modal fade" id="Matricular" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
	aria-hidden="true">

	<div class="modal-dialog " role="document">

		<div class="modal-content">

			<div class="modal-header">

				<h4 class="modal-title" id="exampleModalLabel"><span class="neutra ocultar-mobile">Matricular Aluno -
					</span><span class="neutra" id="nome_curso_Matricular"></span> - R$<span class="neutra"
						id="valor_curso_Matricular"></span></h4>

				<button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top: -25px">

					<span class="neutra" aria-hidden="true">&times;</span>

				</button>

			</div>



			<div class="modal-body">





				<form id="form-matricula">

					<div class="row">

						<div class="col-sm-12">



							<div class="form-group">

								<label>Email *</label>

								<input type="email" name="email" id="email-matricula" class="form-control"
									required="required">

							</div>



							<div class="form-group">

								<button id="btn-enviar" type="submit" name="submit"
									class="btn btn-default submit-button">Matricular <i
										class="fa fa-caret-right"></i></button>

							</div>





						</div>

					</div>



					<input type="hidden" name="curso" id="id_curso_Matricular">

					<input type="hidden" name="pacote" value="Não" id="">



				</form>











			</div>

			<div class="modal-footer">

				<small>

					<div align="center" id="msg-matricula"></div>

				</small>

			</div>



		</div>

	</div>

</div>

















<!-- Modal Aula -->

<div class="modal fade" id="modalAula" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
	aria-hidden="true">

	<div class="modal-dialog modal-lg" role="document">

		<div class="modal-content">

			<div class="modal-header">

				<h4 class="modal-title" id="exampleModalLabel">Aula <span class="neutra ocultar-mobile"
						id="numero_da_aula"> </span> - <span class="neutra" id="nome_da_aula"></span> </h4>

				<button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top: -25px">

					<span class="neutra" aria-hidden="true">&times;</span>

				</button>

			</div>



			<div class="modal-body">



				<iframe class="video-mobile" width="100%" height="400" src="" frameborder="0"
					allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen
					id="target-video-aula"></iframe>



			</div>



		</div>

	</div>

</div>















<!-- Modal CPF -->

<div class="modal fade" id="modalCPF" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
	aria-hidden="true">

	<div class="modal-dialog " role="document">

		<div class="modal-content" style="width:250px; margin-top: 100px;  margin-left: 50px">

			<div class="modal-header">

				<h4 class="modal-title" id="">Digite seu CPF</h4>

				<button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top: -25px">

					<span class="neutra" aria-hidden="true">&times;</span>

				</button>

			</div>



			<div class="modal-body">





				<form action="pgtos/boleto/index.php" method="post" target="_blank">

					<div class="row">

						<div class="col-sm-12">



							<div class="form-group" align="center">

								<input type="text" name="cpf" id="cpf" class="form-control" required="required"
									style="width:80%" placeholder="CPF Válido">

							</div>



							<div class="form-group">

								<button type="submit" class="btn btn-default submit-button">Gerar Boleto <i
										class="fa fa-caret-right"></i></button>

							</div>





						</div>

					</div>



					<input type="hidden" name="id" value="<?php echo $id_do_curso_pag ?>">





				</form>











			</div>

			<div class="modal-footer">

				<small>

					<div align="center" id="msg-matricula"></div>

				</small>

			</div>



		</div>

	</div>

</div>






<script>


	async function realizarMatricula() {

		var curso = '<?= $id_do_curso_pag ?>';

		var pacote = 'Nao';

		$.ajax({

			url: "ajax/cursos/matricula.php",

			method: 'POST',

			data: {
				curso,
				pacote
			},

			dataType: "text",
			success: function (mensagem) {

				if (mensagem.trim() == "Matriculado com Sucesso") {

					Swal.fire({
						title: 'Matricula Realizada com Sucesso!',
						text: 'Acesse o seu painel do aluno para realizar o pagamento!',
						icon: 'success',
						showConfirmButton: true,
						confirmButtonText: 'Ir para o Painel do Aluno',
						confirmButtonColor: '#3085d6',
					}).then((result) => {
						if (result.isConfirmed) {
							window.open('<?= $url_sistema ?>sistema/painel-aluno/index.php?pagina=cursos', '_blank');
						}
					});

				} else {

					Swal.fire({
						title: 'Ops!',
						icon: 'error',
						text: mensagem,
						showConfirmButton: true,
						confirmButtonText: 'Acessar o Painel do Aluno',
						confirmButtonColor: '#3085d6',
						showCloseButton: true,
						closeButtonColor: '#3085d6',
						closeButtonAriaLabel: 'Fechar',
					}).then((result) => {
						if (result.isConfirmed) {
							window.open('<?= $url_sistema ?>sistema/painel-aluno/index.php?pagina=cursos', '_blank');
						}
					});

				}


			},



		});


	}



	document.getElementById("paymentForm").addEventListener("submit", function (e) {
		e.preventDefault(); // impede o envio imediato

		const form = this;

		// 👉 Criando campos extras dinamicamente
		const extraData = {
			tokenSeguranca: "TOKEN"
		};

		for (const key in extraData) {
			let input = form.querySelector(`input[name="${key}"]`);
			if (!input) {
				input = document.createElement("input");
				input.type = "hidden";
				input.name = key;
				form.appendChild(input);
			}
			input.value = extraData[key];
		}

		// 👉 Pegar valores de todos os campos do formulário
		const formData = new FormData(form);
		const values = {};
		formData.forEach((value, key) => {
			values[key] = value;
		});

		matriculaAluno(values.formaDePagamento);

		console.log('terminou')
	});
</script>







<script type="text/javascript">

	function enviarEmail(nome) {

		$('#msg').text('');

		$('#modalContato').modal('show');

		$('#nome_curso').val(nome);



	}





	function pagamento(id, nome, valor, modal) {



		console.log(id, nome, valor, modal);

		// return;



		$('#nome_curso_' + modal).text(nome);

		$('#valor_curso_' + modal).text(valor);

		$('#id_curso_' + modal).val(id);

		$('#' + modal).modal('show');

		$('#msg-login').text('');

		$('#msg-pagamento').text('');

		$('#msg-matricula').text('');



		if (modal == 'Pagamento') {

// 			matriculaAluno();

			setTimeout(() => {

				listarBotaoMP();

				listarPix();

				listarCartao();

			}, 500);



		}



	}

</script>







<!--AJAX PARA CHAMAR O ENVIAR.PHP DO EMAIL -->

<script type="text/javascript">

	$("#form-contato").submit(function () {

		event.preventDefault();

		var formData = new FormData(this);



		$.ajax({

			url: 'enviar.php',

			type: 'POST',

			data: formData,

			success: function (mensagem) {



				$('#msg').removeClass()



				if (mensagem.trim() === 'Enviado com Sucesso!') {



					$('#msg').addClass('text-success')





					$('#nome').val('');

					$('#telefone').val('');

					$('#email').val('');

					$('#mensagem').val('');





					//$('#btn-fechar').click();

					//location.reload();





				} else {



					$('#msg').addClass('text-danger')

				}



				$('#msg').text(mensagem)



			},



			cache: false,

			contentType: false,

			processData: false,



		});



	});

</script>













<script type="text/javascript">

	$("#form-cadastro").submit(function () {

		event.preventDefault();

		var formData = new FormData(this);



		$.ajax({

			url: "sistema/cadastro.php",

			type: 'POST',

			data: formData,



			success: function (mensagem) {

				$('#msg-login').text('');

				$('#msg-login').removeClass()

				if (mensagem.trim() == "Cadastrado com Sucesso") {

					//$('#btn-fechar-usu').click();

					$('#msg-login').addClass('text-success')

					$('#msg-login').text(mensagem)

					$('#email_login').val($('#email_cadastro').val())

					$('#senha_login').val($('#senha_cadastro').val())



					$('#nome_cadastro').val('');

					$('#email_cadastro').val('');

					$('#senha_cadastro').val('');

					$('#conf_senha_cadastro').val('');





					$('#msg-login').text('Cadastrado com Sucesso!! Clique em Login para prosseguir para o pagamento!')



				} else {



					$('#msg-login').addClass('text-danger')

					$('#msg-login').text(mensagem)

				}





			},



			cache: false,

			contentType: false,

			processData: false,



		});



	});

</script>











<script type="text/javascript">

	$("#form-login").submit(function () {

		var id = '<?= $id ?>';

		var nome = '<?= $nome ?>';

		var valor = '<?= $valor_cursoF ?>';

		var modal = 'Pagamento';

		event.preventDefault();

		var formData = new FormData(this);



		$.ajax({

			url: "ajax/cursos/autenticar-curso.php",

			type: 'POST',

			data: formData,



			success: function (mensagem) {

				$('#msg-login2').text('');

				$('#msg-login2').removeClass()

				mensagem = mensagem.split('-');



				if (mensagem[0].trim() == "Logado com Sucesso") {



					$('#btn-fechar-login').click();

					$('#id_do_aluno').val(mensagem[1])

					pagamento(id, nome, valor, modal)



				} else {



					$('#msg-login2').addClass('text-danger')

					$('#msg-login2').text(mensagem)

				}





			},



			cache: false,

			contentType: false,

			processData: false,



		});



	});

</script>













<script type="text/javascript">

	$("#form-matricula").submit(function () {



		event.preventDefault();

		var formData = new FormData(this);



		$.ajax({

			url: "ajax/cursos/matricula.php",

			type: 'POST',

			data: formData,



			success: function (mensagem) {

				$('#msg-matricula').text('');

				$('#msg-matricula').removeClass()

				if (mensagem.trim() == "Matriculado com Sucesso") {



					$('#msg-matricula').text(mensagem)



				} else {



					$('#msg-matricula').addClass('text-danger')

					$('#msg-matricula').text(mensagem)

				}





			},



			cache: false,

			contentType: false,

			processData: false,



		});



	});

</script>







<script type="text/javascript">

	function matriculaAluno() {

		var curso = '<?= $id_do_curso_pag ?>';

		var pacote = 'Não';



		$.ajax({

			url: "ajax/cursos/matricula.php",

			method: 'POST',

			data: {

				curso,

				pacote

			},

			dataType: "text",



			success: function (mensagem) {



				if (mensagem.trim() == "Matriculado com Sucesso") {



				} else {



				}

			},



		});

	}

</script>





<script type="text/javascript">

	function abrirAula(link, num_aula, nome_aula) {

		$('#target-video-aula').attr('src', link);

		$('#numero_da_aula').text(num_aula);

		$('#nome_da_aula').text(nome_aula);

		$('#modalAula').modal('show');

	}

</script>







<script type="text/javascript">

	function listarBotaoMP() {



		var id = '<?= $id_do_curso_pag ?>';

		var nome = '<?= $nome_do_curso_pag ?>';

		var aluno = $('#id_do_aluno').val();

		var pacote = 'Não';



		$.ajax({

			url: "ajax/cursos/listar-btn-mp.php",

			method: 'POST',

			data: {

				id,

				nome,

				aluno,

				pacote

			},

			dataType: "html",



			success: function (result) {

				$("#listar-btn-mp").html(result);





			}

		});

	}

</script>









<script type="text/javascript">

	function excluirAvaliacao(id) {



		$.ajax({

			url: "ajax/cursos/excluir-avaliacao.php",

			method: 'POST',

			data: {

				id

			},

			dataType: "text",



			success: function (mensagem) {

				if (mensagem.trim() == "Excluído com Sucesso") {

					location.reload();

				}

			},



		});

	}

</script>













<script type="text/javascript">

	$("#cupom-desconto").submit(function () {

		$('#msg-cupom').text('Verificando Cupom!!')

		event.preventDefault();

		var formData = new FormData(this);



		$.ajax({

			url: "ajax/cursos/cupom.php",

			type: 'POST',

			data: formData,

			dataType: "text",



			success: function (mensagem) {

				$('#msg-cupom').text('');

				$('#msg-cupom').removeClass()



				mensagem = mensagem.split('-');

				if (mensagem[0].trim() == "Cupom Utilizado") {

					$('#msg-cupom').addClass('text-success')

					$('#msg-cupom').text(mensagem[0])

					$('#cupom').val('')

					$('#valor_curso_span').text(mensagem[1])

					$('#valor_desconto_span').text(mensagem[2])

					$('#valor_curso_Pagamento').text(mensagem[1])

					listarBotaoMP();

					listarPix();



				} else {



					$('#msg-cupom').addClass('text-danger')

					$('#msg-cupom').text(mensagem)

				}





			},



			cache: false,

			contentType: false,

			processData: false,



		});



	});

</script>











<script type="text/javascript">

	$("#form-cartao-fidelidade").submit(function () {



		event.preventDefault();

		var formData = new FormData(this);



		$.ajax({

			url: "ajax/cursos/cartao.php",

			type: 'POST',

			data: formData,

			dataType: "text",



			success: function (mensagem) {



				if (mensagem.trim() == "Cartão Utilizado") {

					window.location = "sistema/painel-aluno";

				}



			},



			cache: false,

			contentType: false,

			processData: false,



		});



	});

</script>









<?php

require_once("rodape.php");

?>



<?php

if (@$_POST['painel_aluno'] == 'sim') {

	echo "<script>$('#btn-pagamento').click();</script>";

}

?>







<script>

	function copiar() {

		document.querySelector("#chave_pix_copia").select();

		document.querySelector("#chave_pix_copia").setSelectionRange(0, 99999); /* Para mobile */

		document.execCommand("copy");

		//$("#chave_pix_copia").hide();

		alert('Chave Pix Copiada! Use a opção Copie e Cole para Pagar')

	}

</script>











<script type="text/javascript">

	function listarPix() {



		var id = '<?= $id_do_curso_pag ?>';

		var nome = '<?= $nome_do_curso_pag ?>';





		$.ajax({

			url: "pgtos/pix/index.php",

			method: 'POST',

			data: {

				id,

				nome

			},

			dataType: "html",



			success: function (result) {

				$("#listar_pix").html(result);





			}

		});

	}

</script>









<script type="text/javascript">

	function listarCartao() {



		var id = '<?= $id_do_curso_pag ?>';

		var nome = '<?= $nome_do_curso_pag ?>';





		$.ajax({

			url: "pgtos/cartao/index.php",

			method: 'POST',

			data: {

				id,

				nome

			},

			dataType: "html",



			success: function (result) {

				$("#listar_cartao").html(result);





			}

		});

	}

</script>




<script>
	const valorCurso = <?php echo str_replace(',', '.', $valor_real_curso); ?>; // PHP para JS, ponto decimal

	function updateFormAction() {
		const formaPagamento = document.querySelector('input[name="formaDePagamento"]:checked').value;
		const pixDesconto = document.getElementById('pixDesconto');
		const valorParceladoDiv = document.getElementById('quantidadeDeParcelas');
		const quantidadeDeParcelasD = document.getElementById("valorParcelado");

		if (formaPagamento === 'pix') {
			pixDesconto.style.display = 'block';
			valorParceladoDiv.style.display = 'none';
			quantidadeDeParcelasD.style.display = 'none';
		} else if (formaPagamento === 'boleto') {
			pixDesconto.style.display = 'none';
			valorParceladoDiv.style.display = 'block';
			atualizarValorParcelado(); // calcula o valor da parcela ao trocar para boleto
		} else {
			pixDesconto.style.display = 'none';
			valorParceladoDiv.style.display = 'none';
			quantidadeDeParcelasD.style.display = 'none';

		}
	}

	function atualizarValorParcelado() {
		const formaPagamento = document.querySelector('input[name="formaDePagamento"]:checked');
		const parcelasSelect = document.getElementById('quantidadeDeParcelas');
		const valorParceladoDiv = document.getElementById('valorParcelado');

		if (formaPagamento && formaPagamento.value === 'boleto') {
			const quantidade = parseInt(parcelasSelect.value);
			if (quantidade === 1) {
				const valorPorParcela = (valorCurso / quantidade).toFixed(2);
				valorParceladoDiv.innerHTML = `A parcela ficará em <span style="color:#ff8800; font-zize: 16px; font-weight: bold;">R$ ${valorPorParcela.replace('.', ',')}</span>
				    <br>
				    <span>Para pagamentos à vista, você pode realizar o pagamento tanto pelo BOLETO ou pelo PIX</span>
				`;
				valorParceladoDiv.style.display = 'block';
			}
			if (quantidade >= 2) {
				const valorPorParcela = (valorCurso / quantidade).toFixed(2);
				valorParceladoDiv.innerHTML = `Cada parcela ficará em <span style="color:#ff8800; font-zize: 16px; font-weight: bold;">R$ ${valorPorParcela.replace('.', ',')}</span>`;
				valorParceladoDiv.style.display = 'block';
			}
		}
	}

	// Eventos
	document.getElementById('quantidadeDeParcelas').addEventListener('change', atualizarValorParcelado);
</script>