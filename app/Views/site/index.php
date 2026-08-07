<?php
    $logo = base_url('assets/img/lunna/logo-lunna.png');
    $hero = base_url('assets/img/lunna/hero-portas-de-vidro.jpg');
    $whatsappUrl = whatsappUrl('5538999990000', 'Olá! Gostaria de solicitar um orçamento para vidraçaria.');

    $servicos = [
        [
            'titulo' => 'Box Blindex',
            'texto' => 'Soluções sob medida para banheiros residenciais e comerciais.',
            'imagem' => base_url('assets/img/lunna/box-banheiro.jpg'),
        ],
        [
            'titulo' => 'Esquadrias de alumínio',
            'texto' => 'Portas, janelas e fechamentos com acabamento moderno.',
            'imagem' => base_url('assets/img/lunna/esquadrias-de-aluminio.jpg'),
        ],
        [
            'titulo' => 'Guarda-corpo',
            'texto' => 'Proteção e transparência para sacadas, escadas e áreas elevadas.',
            'imagem' => base_url('assets/img/lunna/guarda-corpo.jpg'),
        ],
        [
            'titulo' => 'Espelhos',
            'texto' => 'Peças lapidadas para banheiros, salas, lojas e projetos especiais.',
            'imagem' => base_url('assets/img/lunna/espelho.jpg'),
        ],
        [
            'titulo' => 'Forro PVC',
            'texto' => 'Instalação prática para obras, reformas e ambientes comerciais.',
            'imagem' => base_url('assets/img/lunna/forro-pvc.jpg'),
        ],
        [
            'titulo' => 'Portas de vidro',
            'texto' => 'Ambientes mais iluminados, bonitos e funcionais.',
            'imagem' => base_url('assets/img/lunna/porta-vidro.jpg'),
        ],
    ];
?>

<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title><?= esc($title ?? 'Lunna Vidraçaria') ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Vidraçaria em São Francisco-MG especializada em box, esquadrias de alumínio, espelhos, portas de vidro, guarda-corpo e forro PVC.">
    <meta name="theme-color" content="#006634">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= base_url('assets/css/style.css') ?>" rel="stylesheet">
</head>

<body class="site-page">
    <header class="site-header">
        <a class="site-logo" href="<?= base_url('/') ?>" aria-label="Lunna Vidraçaria">
            <img src="<?= $logo ?>" alt="Lunna Vidraçaria">
        </a>

        <nav class="site-nav" aria-label="Navegação principal">
            <a href="#servicos">Serviços</a>
            <a href="#sobre">A empresa</a>
            <a href="#contato">Contato</a>
            <a class="site-nav-whatsapp" href="<?= esc($whatsappUrl) ?>" target="_blank" rel="noopener">WhatsApp</a>
        </nav>
    </header>

    <main>
        <section class="site-hero" style="--site-hero-image: url('<?= $hero ?>');">
            <div class="site-hero-content">
                <span>São Francisco-MG</span>
                <h1>Lunna Vidraçaria</h1>
                <p>Vidros, esquadrias, box, espelhos e acabamentos para obras residenciais, comerciais e reformas.</p>

                <div class="site-hero-actions">
                    <a class="site-btn site-btn-primary" href="#contato">Solicitar orçamento</a>
                    <a class="site-btn site-btn-whatsapp" href="<?= esc($whatsappUrl) ?>" target="_blank" rel="noopener">Chamar no WhatsApp</a>
                </div>
            </div>
        </section>

        <section class="site-proof" aria-label="Diferenciais">
            <div>
                <strong>+12 anos</strong>
                <span>de mercado</span>
            </div>
            <div>
                <strong>Equipe técnica</strong>
                <span>medição e instalação</span>
            </div>
            <div>
                <strong>Projetos sob medida</strong>
                <span>vidros, alumínio e PVC</span>
            </div>
        </section>

        <section id="servicos" class="site-section">
            <div class="site-section-heading">
                <span>Produtos e serviços</span>
                <h2>Soluções para construir, reformar e valorizar ambientes</h2>
            </div>

            <div class="site-service-grid">
                <?php foreach ($servicos as $servico): ?>
                    <article class="site-service-card">
                        <img src="<?= esc($servico['imagem']) ?>" alt="<?= esc($servico['titulo']) ?>">
                        <div>
                            <h3><?= esc($servico['titulo']) ?></h3>
                            <p><?= esc($servico['texto']) ?></p>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>

        <section id="sobre" class="site-about">
            <div class="site-about-copy">
                <span>A empresa</span>
                <h2>Atendimento próximo, medição cuidadosa e acabamento profissional.</h2>
                <p>
                    A Lunna Vidraçaria atende São Francisco-MG e região com soluções em vidro, alumínio e PVC para clientes que estão construindo, reformando ou modernizando seus espaços.
                </p>
            </div>

            <div class="site-about-list">
                <div>
                    <strong>Orçamento orientado</strong>
                    <span>Levantamento das necessidades antes da execução.</span>
                </div>
                <div>
                    <strong>Serviço técnico</strong>
                    <span>Medição, produção e instalação acompanhadas pela equipe.</span>
                </div>
                <div>
                    <strong>Acabamento sob medida</strong>
                    <span>Soluções em vidro, alumínio e PVC pensadas para cada ambiente.</span>
                </div>
            </div>
        </section>

        <section id="contato" class="site-contact">
            <div>
                <span>Fale com a Lunna</span>
                <h2>Vamos transformar sua ideia em um orçamento.</h2>
                <p>Rua Adão Vieira, nº 939 · São Francisco-MG</p>
            </div>

            <div class="site-contact-actions">
                <a class="site-btn site-btn-primary" href="<?= esc($whatsappUrl) ?>" target="_blank" rel="noopener">Chamar no WhatsApp</a>
                <a class="site-btn site-btn-outline" href="tel:+553836311901">(38) 3631-1901</a>
                <a class="site-btn site-btn-outline" href="mailto:contato@lunnavidracaria.com.br">Enviar e-mail</a>
                <a class="site-btn site-btn-outline" href="https://www.instagram.com/lunnavidracariasf/" target="_blank" rel="noopener">Instagram</a>
            </div>
        </section>
    </main>

    <footer class="site-footer">
        <span>Lunna Vidraçaria © <?= date('Y') ?></span>
        <span>Vidros, alumínio, box e acabamentos em São Francisco-MG</span>
    </footer>
</body>
</html>
