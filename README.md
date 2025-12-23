# Sested Cursos Virtual

Aplicação PHP legacy que gerencia matrículas, boletos Gerencianet/EFI e split de comissões para o painel do aluno e administrativos. Reúne integrações com Gerencianet (PIX/boletos), registro de webhook e lógica própria de rateio (`split`).

## Pré‑requisitos

- PHP 8.0+ com cURL e mbstring habilitados  
- Composer (`composer install`)  
- MySQL (credenciais em `sistema/conexao.php`)  
- Certificados Gerencianet em `efi/` (não versionados)  
- Chaves `client_id`/`client_secret` em `efi/options.php`

## Instalação

```bash
cd c:/xampp/htdocs/virtual
composer install
# ajustar dados em efi/options.php e sistema/conexao.php
```

> Certifique-se de copiar os certificados `*.pem` fornecidos pelo banco para `efi/` e atualizar os paths em `efi/options.php`.

## Fluxo de boletos

1. O aluno acessa `sistema/painel-aluno/paginas/parcelas.php`.
2. Cada parcela tem um form próprio que envia `payload` (JSON) para `sistema/painel-aluno/paginas/gerar_boleto.php`.
3. Esse endpoint decodifica o payload, limpa `item_nome` (acentuação) e chama `efi/boleto_p.php` para consultar o token e criar a cobrança.
4. O PDF mostrado é o boleto retornado pela EFI e também atualiza `parcelas_geradas_por_boleto`.

## Repasses e comissões

- `efi/index.php` armazena o split configurado no curso (`split=1 padrão`, `2 sistema`, `3 vendedor`).  
- No modo padrão (`split=1`) o array inclui wallets que recebem `recebeSempre=1`, o vendedor e o responsável pelo cadastro do aluno.  
- `split=2` limpa o array (todo valor fica com a empresa).  
- `split=3` envia 100% para o vendedor responsável.
- Campo `split` é configurado em `sistema/painel-admin/paginas/cursos.php` durante o cadastro/edição.

## Webhooks

- `efi_webhook_boleto_parcelado.php` valida a notificação, consulta o hash via Gerencianet e registra `boleto_error` quando o hash venceu (retorna 400 em vez de 500).  
- É essencial ter `notification_url` apontando para esse endpoint ao criar o boleto (`efi/index.php` -> `$dadosBoleto['notification_url']`).  
- Logs ficam em `webhook_logs` (tabela) e `efi/error_log`.

## Testes & validação

- Execute `php -l` nos scripts modificados (`gerar_boleto.php`, `parcelas.php`) antes de commitar.  
- Gere boletos no painel e verifique no log (`sistema/painel-aluno/error_log`) se aparecem `PDO`/`payload`.  
- Dispare notificações inválidas (hash expirado) via Postman/cURL para validar o tratamento 400.

## Deploy

1. Copie o projeto para o servidor `public_html/`.
2. Instale dependências (`composer install --no-dev`).
3. Atualize `efi/options.php`, `sistema/conexao.php` e os certificados em `efi/`.
4. Configure cron/webhooks para usar `https://.../efi_webhook_boleto_parcelado.php`.
5. Use um serviço como GitHub Actions para automatizar `composer install`, lint e testes (pode ser um workflow simples com PHP 8).

## Contribuição

- Sempre rode `php -l` antes de commitar.  
- Documente alterações em `README` ou notas no GitHub.  
- Proteja branches (main) e exija revisão de pelo menos 1 colega antes do merge.
