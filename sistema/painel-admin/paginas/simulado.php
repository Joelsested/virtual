<?php
require_once('../conexao.php');
require_once('verificar.php');
$pag = 'simulado';

@session_start();

$nivel = $_SESSION['nivel'] ?? '';
if (!in_array($nivel, ['Vendedor', 'Tutor', 'Parceiro', 'Secretario', 'Tesoureiro'], true)) {
    echo "<script>window.location='../index.php'</script>";
    exit();
}

$categorias = $pdo->query("SELECT id, nome FROM categorias ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);
$cursos = $pdo->query("SELECT id, nome, categoria, valor, promocao FROM cursos ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);
$stmt = $pdo->query("SELECT id, nome, valor, promocao FROM pacotes ORDER BY nome");
$pacotes = $stmt->fetchAll(PDO::FETCH_ASSOC);
$mapCursosPacotes = $pdo->query("SELECT id_pacote, id_curso FROM cursos_pacotes")->fetchAll(PDO::FETCH_ASSOC);
$pacoteCursosMap = [];
foreach ($mapCursosPacotes as $mapRow) {
    $pacoteId = $mapRow['id_pacote'] ?? null;
    $cursoId = $mapRow['id_curso'] ?? null;
    if ($pacoteId === null || $cursoId === null) {
        continue;
    }
    if (!isset($pacoteCursosMap[$pacoteId])) {
        $pacoteCursosMap[$pacoteId] = [];
    }
    $pacoteCursosMap[$pacoteId][] = (string) $cursoId;
}

?>

<div class="bs-example widget-shadow" style="padding:15px;">
    <h3 style="margin-top: 0;">Simulado de Pacotes</h3>
    <p class="text-muted" style="margin-bottom: 0;">Simule desconto e parcelamento ate 24x sem juros.</p>
</div>

<div class="bs-example widget-shadow" style="padding:15px; margin-bottom: 15px;">
    <div class="row">
        <div class="col-md-4 col-sm-6">
            <label>Categoria</label>
            <select class="form-control" id="categoria-simulado">
                <option value="">Selecione a categoria</option>
                <?php foreach ($categorias as $categoria) : ?>
                    <option value="<?php echo htmlspecialchars($categoria['id'] ?? ''); ?>">
                        <?php echo htmlspecialchars($categoria['nome'] ?? ''); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-5 col-sm-6">
            <label>Curso</label>
            <select class="form-control" id="curso-simulado" disabled>
                <option value="">Selecione o curso</option>
            </select>
        </div>
    </div>
</div>

<div class="bs-example widget-shadow" style="padding:15px;" id="simulado-conteudo" hidden>
    <?php if (count($pacotes) === 0) : ?>
        <div class="alert alert-info" style="margin: 0;">Nenhum pacote encontrado.</div>
    <?php else : ?>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Curso/Pacote</th>
                    <th>Preco</th>
                    <th>Desconto (R$)</th>
                    <th>Desconto (%)</th>
                    <th>Valor final</th>
                    <th>Parcelas</th>
                </tr>
            </thead>
            <tbody id="simulado-body"></tbody>
        </table>
    <?php endif; ?>
</div>

<script>
(function () {
    function toNumber(value) {
        if (typeof value !== 'string') {
            return 0;
        }
        var normalized = value.replace(/\./g, '').replace(',', '.');
        var parsed = parseFloat(normalized);
        return isNaN(parsed) ? 0 : parsed;
    }

    function formatBRL(value) {
        return value.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function atualizarLinha(row) {
        var base = parseFloat(row.getAttribute('data-base')) || 0;
        var inputValor = row.querySelector('.desconto-valor');
        var inputPercent = row.querySelector('.desconto-percent');
        var finalEl = row.querySelector('.valor-final');
        var parcelas = row.querySelector('.parcelas');

        var descontoValor = toNumber(inputValor.value);
        var descontoPercent = toNumber(inputPercent.value);
        if (descontoPercent < 0) {
            descontoPercent = 0;
        }
        if (descontoPercent > 100) {
            descontoPercent = 100;
        }

        var descontoCalc = (base * (descontoPercent / 100)) + descontoValor;
        var final = base - descontoCalc;
        if (final < 0) {
            final = 0;
        }

        finalEl.textContent = 'R$ ' + formatBRL(final);

        var selected = parseInt(parcelas.value, 10) || 1;
        parcelas.innerHTML = '';
        for (var i = 1; i <= 24; i++) {
            var opt = document.createElement('option');
            opt.value = String(i);
            opt.textContent = i + 'x de R$ ' + formatBRL(final / i);
            if (i === selected) {
                opt.selected = true;
            }
            parcelas.appendChild(opt);
        }
    }

    var cursos = <?php echo json_encode($cursos, JSON_UNESCAPED_UNICODE); ?>;
    var pacotes = <?php echo json_encode($pacotes, JSON_UNESCAPED_UNICODE); ?>;
    var pacoteCursosMap = <?php echo json_encode($pacoteCursosMap, JSON_UNESCAPED_UNICODE); ?>;
    var categoriaSelect = document.getElementById('categoria-simulado');
    var cursoSelect = document.getElementById('curso-simulado');
    var simuladoConteudo = document.getElementById('simulado-conteudo');
    var simuladoBody = document.getElementById('simulado-body');

    function resetCursoSelect() {
        cursoSelect.innerHTML = '<option value="">Selecione o curso</option>';
        cursoSelect.disabled = true;
    }

    function calcularBase(item) {
        var valor = parseFloat(item.valor || 0);
        var promocao = parseFloat(item.promocao || 0);
        return promocao > 0 ? promocao : valor;
    }

    function criarLinha(nome, base) {
        var tr = document.createElement('tr');
        tr.className = 'simulado-row';
        tr.setAttribute('data-base', base.toFixed(2));

        tr.innerHTML = '' +
            '<td>' + nome + '</td>' +
            '<td>R$ ' + formatBRL(base) + '</td>' +
            '<td style="max-width: 140px;">' +
                '<input type="text" class="form-control desconto-valor" placeholder="0,00" inputmode="decimal">' +
            '</td>' +
            '<td style="max-width: 120px;">' +
                '<input type="text" class="form-control desconto-percent" placeholder="0" inputmode="decimal">' +
            '</td>' +
            '<td><strong class="valor-final">R$ ' + formatBRL(base) + '</strong></td>' +
            '<td style="min-width: 170px;">' +
                '<select class="form-control parcelas"></select>' +
            '</td>';

        return tr;
    }

    function preencherParcelas(select, total) {
        select.innerHTML = '';
        for (var i = 1; i <= 24; i++) {
            var opt = document.createElement('option');
            opt.value = String(i);
            opt.textContent = i + 'x de R$ ' + formatBRL(total / i);
            select.appendChild(opt);
        }
    }

    function bindLinha(row) {
        var inputValor = row.querySelector('.desconto-valor');
        var inputPercent = row.querySelector('.desconto-percent');
        var parcelas = row.querySelector('.parcelas');
        inputValor.addEventListener('input', function () { atualizarLinha(row); });
        inputPercent.addEventListener('input', function () { atualizarLinha(row); });
        parcelas.addEventListener('change', function () { atualizarLinha(row); });
        atualizarLinha(row);
    }

    function atualizarCursos() {
        var categoriaId = categoriaSelect.value;
        resetCursoSelect();
        if (!categoriaId) {
            simuladoConteudo.hidden = true;
            simuladoBody.innerHTML = '';
            return;
        }
        var filtrados = cursos.filter(function (curso) {
            return String(curso.categoria) === String(categoriaId);
        });
        filtrados.forEach(function (curso) {
            var opt = document.createElement('option');
            opt.value = String(curso.id);
            opt.textContent = curso.nome;
            cursoSelect.appendChild(opt);
        });
        cursoSelect.disabled = filtrados.length === 0;
        simuladoConteudo.hidden = true;
        simuladoBody.innerHTML = '';
    }

    function filtrarPacotes() {
        var cursoId = cursoSelect.value;
        simuladoBody.innerHTML = '';
        if (!cursoId) {
            simuladoConteudo.hidden = true;
            return;
        }

        var cursoSelecionado = cursos.find(function (curso) {
            return String(curso.id) === String(cursoId);
        });

        if (cursoSelecionado) {
            var baseCurso = calcularBase(cursoSelecionado);
            var linhaCurso = criarLinha(cursoSelecionado.nome, baseCurso);
            simuladoBody.appendChild(linhaCurso);
            preencherParcelas(linhaCurso.querySelector('.parcelas'), baseCurso);
            bindLinha(linhaCurso);
        }

        var pacotesRelacionados = pacotes.filter(function (pacote) {
            var lista = pacoteCursosMap[pacote.id] || [];
            return lista.indexOf(String(cursoId)) !== -1;
        });

        pacotesRelacionados.forEach(function (pacote) {
            var basePacote = calcularBase(pacote);
            var linhaPacote = criarLinha(pacote.nome, basePacote);
            simuladoBody.appendChild(linhaPacote);
            preencherParcelas(linhaPacote.querySelector('.parcelas'), basePacote);
            bindLinha(linhaPacote);
        });

        simuladoConteudo.hidden = simuladoBody.children.length === 0;
    }

    categoriaSelect.addEventListener('change', atualizarCursos);
    cursoSelect.addEventListener('change', filtrarPacotes);

    resetCursoSelect();
})();
</script>
