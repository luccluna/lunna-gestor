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

    if (! function_exists('siteIcon')) {
        function siteIcon(string $name): string
        {
            $icons = [
                'services' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7h16"/><path d="M7 4v6"/><path d="M17 4v6"/><path d="M6 14h12"/><path d="M8 18h8"/></svg>',
                'company' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 21V7l8-4 8 4v14"/><path d="M9 21v-6h6v6"/><path d="M9 9h.01"/><path d="M15 9h.01"/></svg>',
                'contact' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M21 15a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4z"/></svg>',
                'whatsapp' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 19.5 6.2 16A7.5 7.5 0 1 1 9 18.8z"/><path d="M9.5 8.8c.2 2.9 2 4.8 4.9 5.4l1.2-1.2 2 1.1c-.4 1.6-1.4 2.5-3 2.5-4.4-.1-7.1-2.8-7.4-7.2 0-1.6.8-2.6 2.3-3l1.2 2z"/></svg>',
                'budget' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9 3h6l1 2h3v16H5V5h3z"/><path d="M9 10h6"/><path d="M9 14h6"/><path d="M9 18h3"/></svg>',
                'phone' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.7 19.7 0 0 1-8.6-3.1 19.3 19.3 0 0 1-6-6A19.7 19.7 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1.9.3 1.8.7 2.6a2 2 0 0 1-.5 2.1L8 9.7a16 16 0 0 0 6.3 6.3l1.3-1.3a2 2 0 0 1 2.1-.5c.8.3 1.7.6 2.6.7a2 2 0 0 1 1.7 2z"/></svg>',
                'email' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 4h16v16H4z"/><path d="m4 7 8 6 8-6"/></svg>',
                'instagram' => '<svg viewBox="0 0 24 24" aria-hidden="true"><rect x="4" y="4" width="16" height="16" rx="5"/><circle cx="12" cy="12" r="3"/><path d="M17 7h.01"/></svg>',
            ];

            $icon = $icons[$name] ?? '';

            return str_replace(
                '<svg viewBox',
                '<svg class="site-btn-icon" xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" focusable="false" viewBox',
                $icon
            );
        }
    }
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
    <link href="<?= base_url('assets/css/style.css?v=20260811-2') ?>" rel="stylesheet">
</head>

<body class="site-page">
    <header class="site-header">
        <a class="site-logo" href="<?= base_url('/') ?>" aria-label="Lunna Vidraçaria">
            <img src="<?= $logo ?>" alt="Lunna Vidraçaria">
        </a>

        <nav class="site-nav" aria-label="Navegação principal">
            <a class="site-icon-btn" href="#servicos" aria-label="Serviços" title="Serviços">
                <?= siteIcon('services') ?>
                <span>Serviços</span>
            </a>
            <a class="site-icon-btn" href="#sobre" aria-label="A empresa" title="A empresa">
                <?= siteIcon('company') ?>
                <span>A empresa</span>
            </a>
            <a class="site-icon-btn" href="#contato" aria-label="Contato" title="Contato">
                <?= siteIcon('contact') ?>
                <span>Contato</span>
            </a>
            <a class="site-icon-btn site-nav-whatsapp" href="<?= esc($whatsappUrl) ?>" target="_blank" rel="noopener" aria-label="WhatsApp" title="WhatsApp">
                <?= siteIcon('whatsapp') ?>
                <span>WhatsApp</span>
            </a>
        </nav>
    </header>

    <main>
        <section class="site-hero" style="--site-hero-image: url('<?= $hero ?>');">
            <div class="site-hero-content">
                <span>São Francisco-MG</span>
                <h1>Lunna Vidraçaria</h1>
                <p>Vidros, esquadrias, box, espelhos e acabamentos para obras residenciais, comerciais e reformas.</p>

                <div class="site-hero-actions">
                    <a class="site-action-btn site-btn site-btn-primary" href="#contato" aria-label="Solicitar orçamento" title="Solicitar orçamento">
                        <?= siteIcon('budget') ?>
                        <span>Solicitar orçamento</span>
                    </a>
                    <a class="site-action-btn site-btn site-btn-whatsapp" href="<?= esc($whatsappUrl) ?>" target="_blank" rel="noopener" aria-label="Chamar no WhatsApp" title="Chamar no WhatsApp">
                        <?= siteIcon('whatsapp') ?>
                        <span>Chamar no WhatsApp</span>
                    </a>
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
                <a class="site-icon-btn site-btn site-btn-primary" href="<?= esc($whatsappUrl) ?>" target="_blank" rel="noopener" aria-label="Chamar no WhatsApp" title="Chamar no WhatsApp">
                    <?= siteIcon('whatsapp') ?>
                    <span>WhatsApp</span>
                </a>
                <a class="site-icon-btn site-btn site-btn-outline" href="tel:+553836311901" aria-label="Ligar para a Lunna" title="Ligar">
                    <?= siteIcon('phone') ?>
                    <span>Telefone</span>
                </a>
                <a class="site-icon-btn site-btn site-btn-outline" href="mailto:contato@lunnavidracaria.com.br" aria-label="Enviar e-mail" title="E-mail">
                    <?= siteIcon('email') ?>
                    <span>E-mail</span>
                </a>
                <a class="site-icon-btn site-btn site-btn-outline" href="https://www.instagram.com/lunnavidracariasf/" target="_blank" rel="noopener" aria-label="Abrir Instagram" title="Instagram">
                    <?= siteIcon('instagram') ?>
                    <span>Instagram</span>
                </a>
            </div>
        </section>
    </main>

    <footer class="site-footer">
        <span>Lunna Vidraçaria © <?= date('Y') ?></span>
        <span>Vidros, alumínio, box e acabamentos em São Francisco-MG</span>
    </footer>
</body>
</html>
