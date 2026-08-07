<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">

    <style>
        @page {
            margin: 28px 34px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #1f2933;
            line-height: 1.35;
        }

        .header {
            border-bottom: 2px solid #1f2933;
            padding-bottom: 14px;
            margin-bottom: 18px;
        }

        .empresa-nome {
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 3px;
            color: #111827;
        }

        .empresa-subtitulo {
            font-size: 11px;
            color: #4b5563;
        }

        .orcamento-box {
            margin-top: 10px;
            padding: 10px;
            background: #f3f4f6;
            border-radius: 6px;
        }

        .orcamento-numero {
            font-size: 15px;
            font-weight: bold;
            color: #111827;
        }

        .section {
            margin-bottom: 16px;
        }

        .section-title {
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 7px;
            padding-bottom: 4px;
            border-bottom: 1px solid #d1d5db;
            color: #111827;
        }

        .grid {
            width: 100%;
            border-collapse: collapse;
        }

        .grid td {
            vertical-align: top;
            padding: 3px 0;
        }

        .label {
            font-size: 9px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: .03em;
        }

        .value {
            font-size: 11px;
            color: #111827;
        }

        table.itens {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
        }

        table.itens th {
            background: #1f2933;
            color: #ffffff;
            font-size: 9px;
            text-align: left;
            padding: 7px 6px;
            border: 1px solid #1f2933;
        }

        table.itens td {
            padding: 7px 6px;
            border: 1px solid #d1d5db;
            vertical-align: top;
        }

        table.itens tr:nth-child(even) td {
            background: #f9fafb;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .small {
            font-size: 9px;
            color: #6b7280;
        }

        .totais {
            width: 42%;
            margin-left: auto;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .totais td {
            padding: 6px;
            border-bottom: 1px solid #e5e7eb;
        }

        .totais .total-final td {
            font-size: 14px;
            font-weight: bold;
            background: #f3f4f6;
            border-top: 2px solid #1f2933;
            border-bottom: 2px solid #1f2933;
        }

        .observacoes {
            padding: 10px;
            border: 1px solid #d1d5db;
            background: #f9fafb;
            border-radius: 6px;
            min-height: 45px;
        }

        .assinatura-area {
            margin-top: 34px;
            width: 100%;
        }

        .assinatura {
            width: 46%;
            text-align: center;
            border-top: 1px solid #111827;
            padding-top: 6px;
            font-size: 10px;
        }

        .footer {
            position: fixed;
            bottom: -12px;
            left: 0;
            right: 0;
            font-size: 9px;
            color: #6b7280;
            text-align: center;
            border-top: 1px solid #e5e7eb;
            padding-top: 6px;
        }

        .status {
            display: inline-block;
            padding: 3px 7px;
            background: #e5e7eb;
            border-radius: 4px;
            font-size: 9px;
            font-weight: bold;
        }
    </style>
</head>

<body>

    <?php
        $statusLabels = [
            'novo' => 'Novo',
            'aguardando_medicao' => 'Aguardando medição',
            'em_elaboracao' => 'Em elaboração',
            'enviado' => 'Enviado',
            'em_negociacao' => 'Em negociação',
            'aprovado' => 'Aprovado',
            'recusado' => 'Recusado',
            'cancelado' => 'Cancelado'
        ];

        $unidades = [
            'm2' => 'm²',
            'metro_linear' => 'Metro linear',
            'unidade' => 'Unidade',
            'servico_fechado' => 'Serviço fechado'
        ];
    ?>

    <div class="header">
        <div class="empresa-nome">Lunna Vidraçaria</div>
        <div class="empresa-subtitulo">
            Soluções em vidros, esquadrias, PVC, box, espelhos, portas, janelas e guarda-corpo.
            <br>
            Rua Adão Vieira, nº 939, Jardim Regalito - São Francisco/MG
            <br>
            Tel.: (38) 3631-1901 | WhatsApp: (38) 99972-1903 | contato@lunnavidracaria.com.br
        </div>

        <div class="orcamento-box">
            <div class="orcamento-numero">
                Orçamento <?= esc($orcamento['numero']) ?>
            </div>
            <div>
                Status:
                <span class="status">
                    <?= esc($statusLabels[$orcamento['status']] ?? $orcamento['status']) ?>
                </span>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Dados do cliente</div>

        <table class="grid">
            <tr>
                <td style="width: 50%;">
                    <div class="label">Cliente</div>
                    <div class="value"><?= esc($orcamento['cliente_nome']) ?></div>
                </td>

                <td style="width: 25%;">
                    <div class="label">CPF/CNPJ</div>
                    <div class="value"><?= esc($orcamento['cpf_cnpj'] ?: '-') ?></div>
                </td>

                <td style="width: 25%;">
                    <div class="label">Contato</div>
                    <div class="value"><?= esc($orcamento['whatsapp'] ?: $orcamento['telefone'] ?: '-') ?></div>
                </td>
            </tr>

            <tr>
                <td>
                    <div class="label">E-mail</div>
                    <div class="value"><?= esc($orcamento['email'] ?: '-') ?></div>
                </td>

                <td colspan="2">
                    <div class="label">Endereço</div>
                    <div class="value">
                        <?= esc($orcamento['endereco'] ?: '-') ?>
                        <?= !empty($orcamento['cliente_numero']) ? ', ' . esc($orcamento['cliente_numero']) : '' ?>
                        <?= !empty($orcamento['complemento']) ? ' - ' . esc($orcamento['complemento']) : '' ?>
                        <br>
                        <?= esc($orcamento['bairro'] ?: '') ?>
                        <?= !empty($orcamento['cidade']) ? ' - ' . esc($orcamento['cidade']) : '' ?>/<?= esc($orcamento['estado'] ?: '') ?>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Dados da proposta</div>

        <table class="grid">
            <tr>
                <td style="width: 25%;">
                    <div class="label">Data do orçamento</div>
                    <div class="value"><?= date('d/m/Y', strtotime($orcamento['data_orcamento'])) ?></div>
                </td>

                <td style="width: 25%;">
                    <div class="label">Validade</div>
                    <div class="value">
                        <?= !empty($orcamento['validade']) ? date('d/m/Y', strtotime($orcamento['validade'])) : '-' ?>
                    </div>
                </td>

                <td style="width: 25%;">
                    <div class="label">Prazo estimado</div>
                    <div class="value"><?= esc($orcamento['prazo_entrega'] ?: '-') ?></div>
                </td>

                <td style="width: 25%;">
                    <div class="label">Forma de pagamento</div>
                    <div class="value"><?= esc($orcamento['forma_pagamento'] ?: '-') ?></div>
                </td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Itens do orçamento</div>

        <table class="itens">
            <thead>
                <tr>
                    <th style="width: 13%;">Ambiente</th>
                    <th style="width: 29%;">Descrição</th>
                    <th style="width: 18%;">Medidas</th>
                    <th style="width: 11%;">Unidade</th>
                    <th style="width: 8%;" class="text-center">Qtd.</th>
                    <th style="width: 10%;" class="text-right">Valor unit.</th>
                    <th style="width: 11%;" class="text-right">Total</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($itens as $item): ?>
                    <tr>
                        <td><?= esc($item['ambiente'] ?: '-') ?></td>

                        <td>
                            <strong><?= esc($item['descricao']) ?></strong>

                            <?php if (!empty($item['observacoes'])): ?>
                                <br>
                                <span class="small"><?= esc($item['observacoes']) ?></span>
                            <?php endif; ?>
                        </td>

                        <td>
                            <?php if ($item['unidade_calculo'] === 'm2'): ?>
                                <?= number_format($item['largura'], 2, ',', '.') ?> x
                                <?= number_format($item['altura'], 2, ',', '.') ?>
                                <br>
                                <span class="small">
                                    Área: <?= number_format($item['area_m2'], 3, ',', '.') ?> m²
                                </span>
                            <?php elseif ($item['unidade_calculo'] === 'metro_linear'): ?>
                                <?= number_format($item['largura'], 2, ',', '.') ?> m linear
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>

                        <td><?= esc($unidades[$item['unidade_calculo']] ?? $item['unidade_calculo']) ?></td>

                        <td class="text-center">
                            <?= number_format($item['quantidade'], 2, ',', '.') ?>
                        </td>

                        <td class="text-right">
                            R$ <?= number_format($item['valor_unitario'], 2, ',', '.') ?>
                        </td>

                        <td class="text-right">
                            <strong>R$ <?= number_format($item['valor_total'], 2, ',', '.') ?></strong>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <table class="totais">
            <tr>
                <td>Subtotal</td>
                <td class="text-right">
                    R$ <?= number_format($orcamento['subtotal'], 2, ',', '.') ?>
                </td>
            </tr>

            <tr>
                <td>Desconto</td>
                <td class="text-right">
                    R$ <?= number_format($orcamento['desconto'], 2, ',', '.') ?>
                </td>
            </tr>

            <tr class="total-final">
                <td>Total</td>
                <td class="text-right">
                    R$ <?= number_format($orcamento['total'], 2, ',', '.') ?>
                </td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Observações e condições</div>

        <div class="observacoes">
            <?= nl2br(esc($orcamento['observacoes_cliente'] ?: 'Valores sujeitos à conferência após medição técnica.')) ?>
        </div>
    </div>

    <table class="assinatura-area">
        <tr>
            <td style="width: 46%;" class="assinatura">
                Lunna Vidraçaria
            </td>

            <td style="width: 8%;"></td>

            <td style="width: 46%;" class="assinatura">
                Cliente / Responsável
            </td>
        </tr>
    </table>

    <div class="footer">
        Lunna Vidraçaria - Orçamento <?= esc($orcamento['numero']) ?> gerado em <?= date('d/m/Y H:i') ?>
    </div>

</body>
</html>