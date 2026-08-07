# Backup e restauracao

## Objetivo

Padronizar backup e restauracao do banco `lunna_gestor` antes de mudancas estruturais, limpezas de dados ou preparacao para producao.

Nenhum backup ou restore foi executado durante esta documentacao.

## Quando fazer backup

Fazer backup antes de:

- criar ou alterar tabelas;
- executar migrations;
- apagar ou limpar massa de teste;
- atualizar relacionamentos;
- importar dumps;
- preparar ambiente para producao;
- qualquer operacao em massa.

## Padrao de nome

Use um nome com data, hora e contexto:

```text
lunna_gestor_backup_YYYYMMDD_HHMM_contexto.sql
```

Exemplos:

```text
lunna_gestor_backup_20260704_0930_antes_migrations.sql
lunna_gestor_backup_20260704_1015_antes_limpeza_testes.sql
```

Guardar backups fora da pasta publica do projeto.

## Backup via phpMyAdmin

1. Abrir o XAMPP Control Panel.
2. Iniciar Apache e MySQL.
3. Acessar `http://localhost/phpmyadmin`.
4. Selecionar o banco `lunna_gestor`.
5. Abrir a aba Exportar.
6. Usar modo Personalizado quando precisar conferir tabelas.
7. Selecionar formato SQL.
8. Manter estrutura e dados.
9. Exportar e salvar com o padrao de nome definido.
10. Conferir se o arquivo foi criado e tem tamanho coerente.

## Backup via mysqldump

O `mysqldump` do XAMPP foi localizado em:

```text
C:\xampp\mysql\bin\mysqldump.exe
```

Exemplo:

```powershell
C:\xampp\mysql\bin\mysqldump.exe -u root lunna_gestor > C:\backups\lunna_gestor_backup_YYYYMMDD_HHMM_contexto.sql
```

Se houver senha no MySQL:

```powershell
C:\xampp\mysql\bin\mysqldump.exe -u root -p lunna_gestor > C:\backups\lunna_gestor_backup_YYYYMMDD_HHMM_contexto.sql
```

Nao salvar senhas em scripts versionados.

## Restauracao em banco vazio via phpMyAdmin

1. Fazer backup do estado atual, se existir.
2. Criar um banco vazio com o nome desejado.
3. Selecionar o banco no phpMyAdmin.
4. Abrir a aba Importar.
5. Selecionar o arquivo `.sql`.
6. Executar importacao.
7. Conferir tabelas, registros e FKs.
8. Testar login e principais modulos no navegador.

## Restauracao em banco vazio via mysql

O cliente MySQL do XAMPP foi localizado em:

```text
C:\xampp\mysql\bin\mysql.exe
```

Exemplo:

```powershell
C:\xampp\mysql\bin\mysql.exe -u root -e "CREATE DATABASE lunna_gestor CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;"
C:\xampp\mysql\bin\mysql.exe -u root lunna_gestor < C:\backups\lunna_gestor_backup_YYYYMMDD_HHMM_contexto.sql
```

Com senha:

```powershell
C:\xampp\mysql\bin\mysql.exe -u root -p lunna_gestor < C:\backups\lunna_gestor_backup_YYYYMMDD_HHMM_contexto.sql
```

## Checklist antes de mudancas estruturais

- [ ] Confirmar que esta no ambiente correto.
- [ ] Confirmar nome do banco.
- [ ] Fazer backup.
- [ ] Conferir tamanho e data do arquivo gerado.
- [ ] Guardar copia fora da pasta publica.
- [ ] Conferir se `.env` nao sera versionado.
- [ ] Ter SQL de reversao ou caminho de restore.
- [ ] Executar primeiro em banco de teste quando possivel.
- [ ] Nao mexer em arquivos internos do MySQL/InnoDB.

## Cuidados por historico de corrupcao InnoDB

O ambiente ja teve problema de corrupcao MySQL/InnoDB.

Por isso:

- nao apagar arquivos da pasta `mysql\data` manualmente;
- nao alterar tablespaces;
- nao copiar tabelas InnoDB por arquivo;
- preferir exportacao/importacao SQL;
- manter backup antes de qualquer mudanca de estrutura;
- testar restore periodicamente em banco vazio.
