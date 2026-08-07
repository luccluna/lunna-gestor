document.addEventListener('DOMContentLoaded', function () {
    iniciarMascarasMoeda();
    iniciarOrcamento();
});

function iniciarMascarasMoeda() {
    const moneyInputs = document.querySelectorAll('.money');

    moneyInputs.forEach(function (input) {
        input.addEventListener('input', function () {
            input.value = formatarMoedaInput(input.value);
        });
    });
}

function iniciarOrcamento() {
    const formOrcamento = document.getElementById('form-orcamento');

    if (!formOrcamento) {
        return;
    }

    const btnAdicionar = document.getElementById('btn-adicionar-item');
    const container = document.getElementById('itens-container');
    const template = document.getElementById('template-item');

    btnAdicionar.addEventListener('click', function () {
        adicionarItemOrcamento(container, template);
    });

    adicionarItemOrcamento(container, template);

    const desconto = document.getElementById('desconto');

    if (desconto) {
        desconto.addEventListener('input', atualizarTotalGeral);
    }

    formOrcamento.addEventListener('submit', function () {
        prepararNamesItens();
    });
}

function adicionarItemOrcamento(container, template) {
    const clone = template.content.cloneNode(true);
    const item = clone.querySelector('.orcamento-item');

    container.appendChild(item);

    atualizarTitulosItens();
    configurarEventosItem(item);
    atualizarCalculoItem(item);
}

function configurarEventosItem(item) {
    const produtoSelect = item.querySelector('.produto-select');
    const btnRemover = item.querySelector('.btn-remover-item');

    produtoSelect.addEventListener('change', function () {
        const option = produtoSelect.options[produtoSelect.selectedIndex];

        const nome = option.dataset.nome || '';
        const unidade = option.dataset.unidade || 'm2';
        const valor = option.dataset.valor || '0';

        const descricao = item.querySelector('.descricao-item');
        const unidadeSelect = item.querySelector('.unidade-calculo');
        const valorUnitario = item.querySelector('.valor-unitario');

        if (nome) {
            descricao.value = nome;
        }

        unidadeSelect.value = unidade;
        valorUnitario.value = decimalParaMoeda(valor);

        atualizarCalculoItem(item);
    });

    const inputsCalculo = item.querySelectorAll('.calc-input');

    inputsCalculo.forEach(function (input) {
        input.addEventListener('input', function () {
            if (input.classList.contains('money')) {
                input.value = formatarMoedaInput(input.value);
            }

            atualizarCalculoItem(item);
        });

        input.addEventListener('change', function () {
            atualizarCalculoItem(item);
        });
    });

    btnRemover.addEventListener('click', function () {
        item.remove();
        atualizarTitulosItens();
        atualizarTotalGeral();
    });
}

function atualizarCalculoItem(item) {
    const largura = textoParaNumero(item.querySelector('.largura').value);
    const altura = textoParaNumero(item.querySelector('.altura').value);
    const quantidade = textoParaNumero(item.querySelector('.quantidade').value || '1');
    const unidade = item.querySelector('.unidade-calculo').value;
    const valorUnitario = moedaParaNumero(item.querySelector('.valor-unitario').value);

    let baseCalculo = 1;
    let areaM2 = 0;

    if (unidade === 'm2') {
        areaM2 = largura * altura;
        baseCalculo = areaM2;
    } else if (unidade === 'metro_linear') {
        baseCalculo = largura;
        areaM2 = 0;
    } else if (unidade === 'unidade') {
        baseCalculo = 1;
        areaM2 = 0;
    } else if (unidade === 'servico_fechado') {
        baseCalculo = 1;
        areaM2 = 0;
    }

    const totalItem = baseCalculo * quantidade * valorUnitario;

    item.querySelector('.area-m2').value = numeroParaDecimalBR(areaM2, 3);
    item.querySelector('.valor-total-item').value = numeroParaMoedaBR(totalItem);

    atualizarTotalGeral();
}

function atualizarTotalGeral() {
    const totais = document.querySelectorAll('.valor-total-item');

    let subtotal = 0;

    totais.forEach(function (input) {
        subtotal += moedaParaNumero(input.value);
    });

    const descontoInput = document.getElementById('desconto');
    const desconto = descontoInput ? moedaParaNumero(descontoInput.value) : 0;

    const total = Math.max(subtotal - desconto, 0);

    const subtotalInput = document.getElementById('subtotal');
    const totalGeralInput = document.getElementById('total_geral');

    if (subtotalInput) {
        subtotalInput.value = numeroParaMoedaBR(subtotal);
    }

    if (totalGeralInput) {
        totalGeralInput.value = numeroParaMoedaBR(total);
    }
}

function prepararNamesItens() {
    const itens = document.querySelectorAll('.orcamento-item');

    itens.forEach(function (item, index) {
        const campos = item.querySelectorAll('[data-name]');

        campos.forEach(function (campo) {
            const nomeCampo = campo.dataset.name;
            campo.setAttribute('name', `itens[${index}][${nomeCampo}]`);
        });
    });
}

function atualizarTitulosItens() {
    const itens = document.querySelectorAll('.orcamento-item');

    itens.forEach(function (item, index) {
        const titulo = item.querySelector('.item-titulo');

        if (titulo) {
            titulo.textContent = `Item ${index + 1}`;
        }
    });
}

function formatarMoedaInput(value) {
    value = value.replace(/\D/g, '');

    if (value === '') {
        return '';
    }

    value = (parseInt(value, 10) / 100).toFixed(2);
    value = value.replace('.', ',');
    value = value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');

    return value;
}

function moedaParaNumero(value) {
    if (!value) {
        return 0;
    }

    value = String(value).replace('R$', '').trim();
    value = value.replace(/\./g, '');
    value = value.replace(',', '.');

    const numero = parseFloat(value);

    return isNaN(numero) ? 0 : numero;
}

function textoParaNumero(value) {
    if (!value) {
        return 0;
    }

    value = String(value).replace(',', '.');

    const numero = parseFloat(value);

    return isNaN(numero) ? 0 : numero;
}

function numeroParaMoedaBR(value) {
    return Number(value || 0).toLocaleString('pt-BR', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

function numeroParaDecimalBR(value, casas = 2) {
    return Number(value || 0).toLocaleString('pt-BR', {
        minimumFractionDigits: casas,
        maximumFractionDigits: casas
    });
}

function decimalParaMoeda(value) {
    return numeroParaMoedaBR(parseFloat(value || 0));
}