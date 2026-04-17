$(document).ready(function() {
    listar();
    // Garante inicializacao apos carregamento total dos scripts do template
    $(window).on('load', function () {
        inicializarBuscaFallback();
        inicializarTabelaComRetry();
    });
} );

function listar(){
    var queryParams = new URLSearchParams(window.location.search);
    var dadosFormulario = $('#form').serializeArray();
    if (queryParams.get('somente_meus') === '1') {
        dadosFormulario.push({ name: 'somente_meus', value: '1' });
    }
    $.ajax({
        url: 'paginas/' + pag + "/listar.php",
        method: 'POST',
        data: $.param(dadosFormulario),
        dataType: "html",

        success:function(result){
            $("#listar").html(result);
            $('#mensagem-excluir').text('');
            inicializarBuscaFallback();
            inicializarTabelaComRetry();
        }
    });
}

function inicializarTabelaComRetry(maxTentativas, intervaloMs) {
    var tentativas = typeof maxTentativas === 'number' ? maxTentativas : 20;
    var intervalo = typeof intervaloMs === 'number' ? intervaloMs : 150;

    (function tentar() {
        if (inicializarTabela()) {
            return;
        }
        tentativas -= 1;
        if (tentativas > 0) {
            setTimeout(tentar, intervalo);
        } else {
            inicializarBuscaFallback();
        }
    })();
}

function inicializarTabela() {
    if (!$.fn || !$.fn.DataTable) {
        return false;
    }
    var $tabela = $('#tabela');
    if ($tabela.length === 0) {
        return false;
    }
    if ($.fn.DataTable.isDataTable($tabela)) {
        return true;
    }
    $tabela.DataTable({
        "ordering": false,
        "stateSave": true,
    });
    $('#tabela_filter label input').focus();
    removerBuscaFallback();
    return true;
}

function inicializarBuscaFallback() {
    var $tabela = $('#tabela');
    if ($tabela.length === 0) {
        return;
    }
    if ($('#busca-fallback-wrap').length > 0) {
        return;
    }
    if ($('#tabela_filter').length > 0) {
        return;
    }

    var $wrap = $('<div id="busca-fallback-wrap" style="text-align:right; margin: 0 0 10px 0;"></div>');
    var $label = $('<label for="busca-fallback"><strong>Buscar:</strong></label>');
    var $input = $('<input id="busca-fallback" type="text" class="form-control" style="display:inline-block; width:220px; margin-left:8px;" />');
    $wrap.append($label).append($input);
    $tabela.before($wrap);

    $input.on('input', function () {
        var termo = ($(this).val() || '').toLowerCase();
        $tabela.find('tbody tr').each(function () {
            var texto = $(this).text().toLowerCase();
            $(this).toggle(texto.indexOf(termo) !== -1);
        });
    });
}

function removerBuscaFallback() {
    $('#busca-fallback-wrap').remove();
}

function inserir(){
    $('#mensagem').text('');
    $('#tituloModal').text('Inserir Registro');
    $('#modalForm').modal('show');
    limparCampos();
}



$("#form").submit(function () {	
	event.preventDefault();
	var formData = new FormData(this);

		$.ajax({
			url: 'paginas/' + pag + "/inserir.php",
			type: 'POST',
			data: formData,

			success: function (mensagem) {
				$('#mensagem').text('');
				$('#mensagem').removeClass()
				if (mensagem.trim() == "Salvo com Sucesso") {
					$('#btn-fechar').click();
					listar();
				} else {
					$('#mensagem').addClass('text-danger')
					$('#mensagem').text(mensagem)
				}

			},

            cache: false,
            contentType: false,
            processData: false,
            
        });

});





function excluir(id){
    var idNum = parseInt(id, 10);
    if (!idNum) {
        idNum = id;
    }
    $.ajax({
        url: 'paginas/' + pag + "/excluir.php?id=" + encodeURIComponent(idNum),
        method: 'POST',
        data: { id: idNum, id_matricula: idNum, csrf_token: (window.CSRF_TOKEN || '') },
        dataType: "text",

        success: function (mensagem) {
            var texto = (mensagem || '').toLowerCase();
            if (texto.indexOf('sucesso') !== -1) {
                listar();
            } else {
                $('#mensagem-excluir').addClass('text-danger')
                $('#mensagem-excluir').text(mensagem)
            }
        },

    });
}




function ativar(id, acao){
   
    $.ajax({
        url: 'paginas/' + pag + "/mudar-status.php",
        method: 'POST',
        data: {id, acao},
        dataType: "text",

        success: function (mensagem) {
            if (mensagem.trim() == "Alterado com Sucesso") {
                 listar();
            }else{
                $('#mensagem-excluir').addClass('text-danger')
                $('#mensagem-excluir').text(mensagem)
            }
        },

    });
}
