<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'Site::index');
$routes->get('/apresentacao', 'Site::index');

$routes->get('/login', 'Auth::login');
$routes->post('/login', 'Auth::autenticar');
$routes->get('/logout', 'Auth::logout');

$routes->get('/dashboard', 'Dashboard::index');
$routes->get('/primeiros-passos', 'PrimeirosPassos::index');

$routes->get('/clientes', 'Clientes::index');
$routes->get('/clientes/novo', 'Clientes::novo');
$routes->post('/clientes/salvar', 'Clientes::salvar');
$routes->get('/clientes/editar/(:num)', 'Clientes::editar/$1');
$routes->post('/clientes/atualizar/(:num)', 'Clientes::atualizar/$1');
$routes->post('/clientes/excluir/(:num)', 'Clientes::excluir/$1');

$routes->get('/produtos', 'Produtos::index');
$routes->get('/produtos/categorias', 'Produtos::categorias');
$routes->get('/produtos/categorias/nova', 'Produtos::categoriaNova');
$routes->post('/produtos/categorias/salvar', 'Produtos::categoriaSalvar');
$routes->get('/produtos/categorias/editar/(:num)', 'Produtos::categoriaEditar/$1');
$routes->post('/produtos/categorias/atualizar/(:num)', 'Produtos::categoriaAtualizar/$1');
$routes->post('/produtos/categorias/excluir/(:num)', 'Produtos::categoriaExcluir/$1');
$routes->get('/produtos/novo', 'Produtos::novo');
$routes->post('/produtos/salvar', 'Produtos::salvar');
$routes->get('/produtos/editar/(:num)', 'Produtos::editar/$1');
$routes->post('/produtos/atualizar/(:num)', 'Produtos::atualizar/$1');
$routes->post('/produtos/excluir/(:num)', 'Produtos::excluir/$1');

$routes->get('/estoque', 'Estoque::index');
$routes->get('/estoque/novo', 'Estoque::novo');
$routes->post('/estoque/salvar', 'Estoque::salvar');
$routes->get('/estoque/editar/(:num)', 'Estoque::editar/$1');
$routes->post('/estoque/atualizar/(:num)', 'Estoque::atualizar/$1');
$routes->get('/estoque/movimentar/(:num)', 'Estoque::movimentar/$1');
$routes->post('/estoque/registrar-movimentacao/(:num)', 'Estoque::registrarMovimentacao/$1');
$routes->post('/estoque/excluir/(:num)', 'Estoque::excluir/$1');

$routes->get('/orcamentos', 'Orcamentos::index');
$routes->get('/orcamentos/novo', 'Orcamentos::novo');
$routes->post('/orcamentos/salvar', 'Orcamentos::salvar');
$routes->get('/orcamentos/ver/(:num)', 'Orcamentos::ver/$1');
$routes->get('/orcamentos/pdf/(:num)', 'Orcamentos::pdf/$1');
$routes->post('/orcamentos/aprovar/(:num)', 'Orcamentos::aprovar/$1');
$routes->post('/orcamentos/excluir/(:num)', 'Orcamentos::excluir/$1');

$routes->get('/pedidos', 'Pedidos::index');
$routes->get('/pedidos/ver/(:num)', 'Pedidos::ver/$1');
$routes->post('/pedidos/status/(:num)', 'Pedidos::atualizarStatus/$1');
$routes->post('/pedidos/excluir/(:num)', 'Pedidos::excluir/$1');

$routes->get('/agenda', 'Agenda::index');
$routes->get('/agenda/novo', 'Agenda::novo');
$routes->post('/agenda/salvar', 'Agenda::salvar');
$routes->get('/agenda/editar/(:num)', 'Agenda::editar/$1');
$routes->post('/agenda/atualizar/(:num)', 'Agenda::atualizar/$1');
$routes->post('/agenda/concluir/(:num)', 'Agenda::concluir/$1');
$routes->post('/agenda/excluir/(:num)', 'Agenda::excluir/$1');

$routes->get('/pagamentos', 'Pagamentos::index');
$routes->get('/pagamentos/novo', 'Pagamentos::novo');
$routes->post('/pagamentos/salvar', 'Pagamentos::salvar');
$routes->get('/pagamentos/editar/(:num)', 'Pagamentos::editar/$1');
$routes->post('/pagamentos/atualizar/(:num)', 'Pagamentos::atualizar/$1');
$routes->post('/pagamentos/marcar-pago/(:num)', 'Pagamentos::marcarPago/$1');
$routes->post('/pagamentos/excluir/(:num)', 'Pagamentos::excluir/$1');

$routes->get('/usuarios', 'Usuarios::index');
$routes->get('/usuarios/novo', 'Usuarios::novo');
$routes->post('/usuarios/salvar', 'Usuarios::salvar');
$routes->get('/usuarios/editar/(:num)', 'Usuarios::editar/$1');
$routes->post('/usuarios/atualizar/(:num)', 'Usuarios::atualizar/$1');
$routes->post('/usuarios/excluir/(:num)', 'Usuarios::excluir/$1');
