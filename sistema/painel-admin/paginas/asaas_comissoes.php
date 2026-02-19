<?php
require_once('../conexao.php');
require_once('verificar.php');

if (@$_SESSION['nivel'] != 'Administrador' and @$_SESSION['nivel'] != 'Tesoureiro') {
    echo "<script>window.location='../index.php'</script>";
    exit();
}

$consulta_niveis = $pdo->query("SELECT nivel FROM usuarios ORDER BY id DESC");
$resposta_consulta_niveis = $consulta_niveis->fetchAll(PDO::FETCH_COLUMN, 0);
$niveis = array_values(array_unique($resposta_consulta_niveis));

if (($key = array_search('Aluno', $niveis)) !== false) {
    unset($niveis[$key]);
}
$niveis = array_values($niveis);

$consulta_comissoes = $pdo->query("SELECT * FROM comissoes ORDER BY id DESC");
$comissoes = $consulta_comissoes->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="bs-example widget-shadow" style="padding:15px;">
    <div class="modal-header">
        <h4 class="modal-title" id="tituloModal">Adicionar comissao fixa</h4>
    </div>
    <form method="POST" id="form" action="paginas/asaas_comissoes/inserir.php">
        <div class="modal-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="nivel">Nivel</label>
                        <select class="form-control" name="nivel" id="nivel" required>
                            <?php foreach ($niveis as $nivel) { ?>
                                <option value="<?= htmlspecialchars($nivel) ?>"><?= htmlspecialchars($nivel) ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="porcentagem">Porcentagem (%)</label>
                        <input type="number" class="form-control" name="porcentagem" id="porcentagem" min="0" step="0.01" required>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="recebeSempre">Recebe pagamentos fixos por todas as vendas?</label>
                        <select class="form-control" name="recebeSempre" id="recebeSempre" required>
                            <option value="1">Sim</option>
                            <option value="0">Nao</option>
                        </select>
                    </div>
                </div>
            </div>
            <br>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Salvar</button>
        </div>
    </form>
</div>

<div class="bs-example widget-shadow" style="padding:15px;">
    <div id="listar">
        <form method="POST" id="form_alterar_registros" action="paginas/asaas_comissoes/alterar_registros.php">
            <table class="table table-hover" id="tabela">
                <thead>
                <tr>
                    <th class="esc">Nivel</th>
                    <th class="esc">Pagamento fixo</th>
                    <th class="esc">Porcentagem</th>
                    <th>Acoes</th>
                </tr>
                </thead>
                <tbody>
                <input name="acao" id="acao" value="editar" type="hidden"/>
                <input type="hidden" id="id_exclusao" name="id_exclusao" value="">
                <?php foreach ($comissoes as $comissao) : ?>
                    <tr id="<?= $comissao['id'] ?>">
                        <input type="hidden" name="registros[<?= $comissao['id'] ?>][id]" value="<?= $comissao['id'] ?>">
                        <input type="hidden" name="registros[<?= $comissao['id'] ?>][recebeSempre]" value="<?= $comissao['recebeSempre'] ?>">

                        <td class="esc"><?= $comissao['nivel'] ?></td>
                        <td class="esc"><?= $comissao['recebeSempre'] ? 'Sim' : 'Nao' ?></td>
                        <td class="esc">
                            <input class="form-control" type="number" min="0" step="0.01" value="<?= $comissao['porcentagem'] ?>" name="registros[<?= $comissao['id'] ?>][porcentagem]">
                        </td>
                        <td>
                            <button type="submit" class="btn btn-danger" onclick="definirTipoAcao('<?= $comissao['id'] ?>')">Excluir</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>

            <button type="submit" class="btn btn-primary">Salvar alteracoes</button>
        </form>
    </div>
</div>

<script>
function definirTipoAcao(id) {
    document.getElementById('acao').value = 'excluir';
    document.getElementById('id_exclusao').value = id;
}
</script>
