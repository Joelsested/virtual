# Sested Cursos Virtual

Aplicação PHP legacy que orquestra matrículas, boletos Gerencianet/EFI e rateio de comissões (`split`) para o painel do aluno e o administrativo. Inclui geração de PDFs referentes às parcelas e recepção de webhooks (boletos parcelados e PIX).

## Pré-requisitos

- PHP 8.0+ com cURL, mbstring e JSON habilitados  
- Composer (`composer install`)  
- MySQL com as credenciais em `sistema/conexao.php`  
- Certificados `.pem` oficiais dentro de `efi/` (fora do versionamento)  
- Chaves `client_id`/`client_secret` configuradas em `efi/options.php`

## Instalação e deploy

```bash
cd c:/xampp/htdocs/virtual
composer install
# atualize efi/options.php (client_id/client_secret/certificados) e sistema/conexao.php (DB)
# copie os arquivos .pem do Gerencianet para efi/
```

1. Ajuste `efi/options.php` para apontar para os certificados de produção e configure `sandbox` como `false`.
2. Garanta que `sistema/conexao.php` apunta para o banco real e que tabelas como `parcelas_geradas_por_boleto` existem.
3. Configure o webhook Gerencianet para `https://<domínio>/efi_webhook_boleto_parcelado.php`.
4. No painel administrativo (`sistema/painel-admin/paginas/cursos.php`), escolha o `split` correto e atualize o curso; cada valor (1,2,3) aciona fluxos diferentes na criação do boleto (`efi/index.php`: padrão, sistema, vendedor).
5. Em produção, use `composer install --no-dev` e mantenha o diretório `efi/` com os certificados protegidos (não versionados).

### Branch protection & CI

- Crie um workflow GitHub Actions que execute `composer install` e `php -l sistema/painel-aluno/paginas/gerar_boleto.php`/`php -l sistema/painel-aluno/paginas/parcelas.php`.  
- Proteja a `main` exigindo revisão de pelo menos 1 revisor e builds verdes.  
- Habilite alertas de segurança/Dependabot para monitorar dependências como Guzzle e Dompdf.

### Git LFS e arquivos grandes

- Os PDFs em `sistema/painel-admin/img/arquivos/` têm >50 MB; use Git LFS para rastrear esses binários:

  ```bash
  git lfs install
  git lfs track "sistema/painel-admin/img/arquivos/*.pdf"
  git lfs track "sistema/painel-admin/img/arquivos-longo/*.pdf"
  git add .gitattributes
  ```

- Depois disso, reenvie os PDFs (ou mova-os para um servidor de arquivos externo) para evitar exceder o limite do GitHub.

## Fluxo de boletos

1. O aluno abre `sistema/painel-aluno/paginas/parcelas.php`; cada parcela renderiza um `<form>` com o `payload` JSON armazenado em `parcelas_geradas_por_boleto.payload`.
2. Ao clicar em “Gerar boleto”, o form envia esse JSON para `sistema/painel-aluno/paginas/gerar_boleto.php`.
3. O script decodifica o payload, limpa acentuação (`decodeEscapedUnicode`) em `item_nome` e chama `efi/boleto_p.php` para:
   - obter token acessando `https://api.gerencianet.com.br/v1/authorize`
   - criar cobrança (`/v1/charge`)
   - pagar via boleto (`/v1/charge/{id}/pay`)
4. Os dados retornados atualizam a tabela `parcelas_geradas_por_boleto` com `charge_id`, `id_asaas`, `transaction_receipt_url`.

## Repasses e comissões

- `efi/index.php` monta o array `$fixos_wallet_ids` baseado em:
  - perfis com `recebeSempre=1` (comissões obrigatórias)  
  - vendedor do curso  
  - responsável pelo cadastro do aluno (vendedor ou tutor)
- `split=1` (padrão) envia o valor proporcional para cada wallet. `split=2` (“Comissão Sistema”) limpa o array e a empresa recebe tudo. `split=3` (“Comissão Vendedor”) envia 100 % para o vendedor responsável.  
- A escolha do `split` é feita no cadastro/edição do curso (`sistema/painel-admin/paginas/cursos.php`).

## Webhooks

- `efi_webhook_boleto_parcelado.php` valida notificações, extrai o `notification hash`, consulta Gerencianet e grava logs na tabela `webhook_logs`.  
- Notificações inválidas (hash expirado/código 3500010) retornam 400 com `{"error":"Notificação inválida ou expirada."}` em vez do 500 antigo.  
- Quando o status for `paid`, o webhook atualiza `parcelas_geradas_por_boleto.transaction_receipt_url` e marca a matrícula, liberando cursos de pacotes automaticamente.

## Testes & validação

- Rode `php -l` nos scripts alterados antes de commitar (`gerar_boleto.php`, `parcelas.php`, `efi_webhook_boleto_parcelado.php`).  
- Gere boletos no painel e observe os logs (`sistema/painel-aluno/error_log`, `efi/error_log` e a tabela `webhook_logs`).  
- Simule notificações inválidas (hash expirado) com `curl -d "notification=<hash>" https://<domínio>/efi_webhook_boleto_parcelado.php` para garantir o novo tratamento 400.

## Deploy & manutenção

1. Suba o código para o servidor (ex.: `public_html/virtual`).  
2. Instale dependências: `composer install --no-dev`.  
3. Ajuste `efi/options.php`, `sistema/conexao.php` e coloque os certificados `.pem` reais em `efi/`.  
4. Ative GitHub Actions/CI para rodar lint/testes e monitorar `composer audit`.  
5. Configure branch protection em `main` (revisão obrigatória + status “CI OK”) e habilite alertas Dependabot.

## Contribuição

- Sempre execute `php -l` antes de commitar.  
- Documente mudanças no `README.md` e, se necessário, crie uma issue descrevendo o impacto na geração de boletos/comissões.  
- Use PRs revisados; não force push em `main`.  
