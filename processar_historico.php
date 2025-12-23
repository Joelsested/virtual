
<?php
// Arquivo: processar_historico.php (criar separadamente)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    if ($input['acao'] === 'gerar_pdf') {
        try {
            require_once('lib/tcpdf/tcpdf.php'); // Certifique-se de ter a biblioteca TCPDF

            $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

            // Configurações do PDF
            $pdf->SetCreator('Sistema de Histórico Escolar');
            $pdf->SetAuthor('Centro de Estudos Supletivos');
            $pdf->SetTitle('Histórico Escolar - ' . $input['dadosAluno']['nome']);
            $pdf->SetSubject('Histórico Escolar');

            // Remover header e footer padrão
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);

            // Adicionar página
            $pdf->AddPage();

            // Definir fonte
            $pdf->SetFont('helvetica', '', 12);

            // Cabeçalho
            $html = '
            <div style="text-align: center; margin-bottom: 20px;">
                <h2>HISTÓRICO ESCOLAR</h2>
                <h3>ENSINO MÉDIO</h3>
                <hr>
            </div>
            
            <div style="margin-bottom: 15px;">
                <h4>DADOS PESSOAIS</h4>
                <table border="1" cellpadding="5">
                    <tr>
                        <td><strong>Nome:</strong></td>
                        <td colspan="3">' . htmlspecialchars($input['dadosAluno']['nome']) . '</td>
                    </tr>
                    <tr>
                        <td><strong>Sexo:</strong></td>
                        <td>' . htmlspecialchars($input['dadosAluno']['sexo']) . '</td>
                        <td><strong>Data de Nascimento:</strong></td>
                        <td>' . date('d/m/Y', strtotime($input['dadosAluno']['dataNasc'])) . '</td>
                    </tr>
                    <tr>
                        <td><strong>Naturalidade:</strong></td>
                        <td>' . htmlspecialchars($input['dadosAluno']['naturalidade']) . '</td>
                        <td><strong>CPF:</strong></td>
                        <td>' . htmlspecialchars($input['dadosAluno']['cpf']) . '</td>
                    </tr>
                    <tr>
                        <td><strong>RG:</strong></td>
                        <td>' . htmlspecialchars($input['dadosAluno']['rg']) . '</td>
                        <td colspan="2"></td>
                    </tr>
                    <tr>
                        <td><strong>Nome do Pai:</strong></td>
                        <td colspan="3">' . htmlspecialchars($input['dadosAluno']['pai']) . '</td>
                    </tr>
                    <tr>
                        <td><strong>Nome da Mãe:</strong></td>
                        <td colspan="3">' . htmlspecialchars($input['dadosAluno']['mae']) . '</td>
                    </tr>
                </table>
            </div>
            
            <div style="margin-bottom: 15px;">
                <h4>DADOS DA INSTITUIÇÃO</h4>
                <table border="1" cellpadding="5">
                    <tr>
                        <td><strong>Instituição:</strong></td>
                        <td colspan="2">' . htmlspecialchars($input['dadosAdicionais']['escola']) . '</td>
                    </tr>
                    <tr>
                        <td><strong>Município/UF:</strong></td>
                        <td>' . htmlspecialchars($input['dadosAdicionais']['municipio']) . '</td>
                        <td><strong>Ano de Conclusão:</strong> ' . htmlspecialchars($input['dadosAdicionais']['anoConclusao']) . '</td>
                    </tr>
                </table>
            </div>
            
            <div>
                <h4>NOTAS E CONCEITOS</h4>
                <table border="1" cellpadding="3" style="font-size: 10px;">
                    <tr style="background-color: #f0f0f0;">
                        <th width="40%"><strong>COMPONENTE CURRICULAR</strong></th>
                        <th width="20%"><strong>1ª SÉRIE</strong></th>
                        <th width="20%"><strong>2ª SÉRIE</strong></th>
                        <th width="20%"><strong>3ª SÉRIE</strong></th>
                    </tr>';

            // Adicionar as matérias organizadas por área
            $areas = [
                'LINGUAGENS E TECNOLOGIAS' => ['Língua portuguesa', 'Arte', 'Língua inglesa', 'Língua espanhola', 'Educação física'],
                'MATEMÁTICA' => ['Matemática'],
                'CIÊNCIAS DA NATUREZA' => ['Química', 'Física', 'Biologia'],
                'CIÊNCIAS HUMANAS' => ['História', 'Geografia'],
                'PARTE DIVERSIFICADA' => ['Sociologia', 'Filosofia', 'História do Estado de Rondônia', 'Geografia do Estado de Rondônia']
            ];

            foreach ($areas as $nomeArea => $materiasArea) {
                $html .= '<tr style="background-color: #e0e0e0;"><td colspan="4"><strong>' . $nomeArea . '</strong></td></tr>';

                foreach ($materiasArea as $materia) {
                    $notas = $input['notas'][$materia];
                    $html .= '<tr>
                        <td>' . $materia . '</td>
                        <td style="text-align: center;">' . $notas['serie1'] . '</td>
                        <td style="text-align: center;">' . $notas['serie2'] . '</td>
                        <td style="text-align: center;">' . $notas['serie3'] . '</td>
                    </tr>';
                }
            }

            $html .= '</table>
            </div>
            
            <div style="margin-top: 20px;">
                <p><strong>Carga Horária Total:</strong> ' . $input['dadosAdicionais']['cargaHoraria'] . ' horas</p>
                <p><strong>Situação:</strong> ' . $input['dadosAdicionais']['situacao'] . '</p>
            </div>
            
            <div style="margin-top: 30px; text-align: center;">
                <p>____________________________</p>
                <p>Diretor(a) da Instituição</p>
                <br>
                <p>____________________________</p>
                <p>Secretário(a) Escolar</p>
            </div>';

            $pdf->writeHTML($html, true, false, true, false, '');

            // Salvar PDF
            $nomeArquivo = 'historico_' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $input['dadosAluno']['nome']) . '_' . date('YmdHis') . '.pdf';
            $caminhoCompleto = 'pdfs/' . $nomeArquivo;

            // Criar diretório se não existir
            if (!file_exists('pdfs')) {
                mkdir('pdfs', 0777, true);
            }

            $pdf->Output($caminhoCompleto, 'F');

            // Resposta de sucesso
            header('Content-Type: application/json');
            echo json_encode([
                'sucesso' => true,
                'arquivo_pdf' => $caminhoCompleto,
                'mensagem' => 'Histórico gerado com sucesso!'
            ]);
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Erro ao gerar PDF: ' . $e->getMessage()
            ]);
        }
    }
    exit;
}
?>