<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
 
// Login
Route::get('/', function () {
    return view('/auth/login');
});

// Auth - Controller Autenticacao
Auth::routes();

// HomeController - Dashboard - Painel Inicial
Route::get('home', 'HomeController@index')->name('home');

// ContatoController
Route::resource('contato', 'ContatoController');
Route::post('contato/enviar', 'ContatoController@enviar');

// RoleController - Regras - Papeis
Route::resource('roles', 'RoleController');
Route::post('roles/busca', 'RoleController@busca');
Route::get('role/{id}/permissions', 'RoleController@permissions');
Route::post('role/permissionUpdate', 'RoleController@permissionUpdate');
Route::post('role/permissionDestroy', 'RoleController@permissionDestroy');

// PermissionController - Permissoes
Route::resource('permissions', 'PermissionController');
Route::post('permissions/busca', 'PermissionController@busca');
Route::get('permission/{id}/roles', 'PermissionController@roles');

// UserController - Permissoes
Route::resource('/users', 'UserController');
Route::post('users/busca', 'UserController@busca');
Route::post('users/updateActive', 'UserController@updateActive');
Route::get('user/{id}/roles', 'UserController@roles');
//Route::get('user/{id}/userroles', 'UserController@createUserRole');
Route::post('user/roleUpdate', 'UserController@roleUpdate');
Route::post('user/roleDestroy', 'UserController@roleDestroy');
//Setor User
Route::get('user/{id}/setors', 'UserController@setors');
Route::post('user/setorUpdate', 'UserController@setorUpdate');
Route::post('user/setorDestroy', 'UserController@setorDestroy');

//TEST
//Route::get('user/roleUpdateTest', 'UserController@roleUpdateTest');

Route::resource('equipamentos', 'EquipamentoController');
Route::post('equipamentos/busca', 'EquipamentoController@busca');


// TicketController
Route::resource('tickets', 'TicketController');
Route::post('tickets/busca', 'TicketController@busca');
Route::get('tickets/{id}/acao', 'TicketController@acao');
Route::post('tickets/storeAcao', 'TicketController@storeAcao');
Route::get('tickets/{id}/encerrar', 'TicketController@encerrar');
Route::post('tickets/storeEncerrar', 'TicketController@storeEncerrar');
Route::get('tickets/{status}/status', 'TicketController@status');

//Setor Ticket
Route::get('tickets/{id}/setors', 'TicketController@setors');
Route::post('tickets/setorUpdate', 'TicketController@setorUpdate');
Route::post('tickets/setorDestroy', 'TicketController@setorDestroy');


Route::resource('setors', 'SetorController');
Route::post('setors/busca', 'SetorController@busca');

// ClientController
Route::resource('clients', 'ClientController');
Route::post('clients/busca', 'ClientController@busca');
Route::get('clients/{id}/encerrar', 'ClientController@encerrar');
Route::post('clients/storeAcao', 'ClientController@storeAcao');
Route::post('clients/storeEncerrar', 'ClientController@storeEncerrar');
Route::get('clients/{status}/status', 'ClientController@status');
Route::get('clients/{id}/acao', 'ClientController@acao');

// TecnicoController
//Route::resource('tecnicos/', 'TecnicoController');
Route::get('tecnicos/{setor}/{id}/edit', 'TecnicoController@edit');
Route::get('tecnicos/{setor}/{id}/show', 'TecnicoController@show');
Route::get('tecnicos/{setor}/{id}/acao', 'TecnicoController@acao');

Route::post('tecnicos/{setor}/{id}/update', 'TecnicoController@update');

//Route::get('tecnicos/update/', 'TecnicoController@update');

Route::post('tecnicos/{setor}/busca', 'TecnicoController@busca');
Route::get('tecnicos/{setor}/tickets', 'TecnicoController@index');
Route::get('tecnicos/{setor}/tickets/{status}/status', 'TecnicoController@status');
Route::post('tecnicos/storeAcao', 'TecnicoController@storeAcao');
Route::get('tecnicos/{setor}/{id}/encerrar', 'TecnicoController@encerrar');
Route::post('tecnicos/storeEncerrar', 'TecnicoController@storeEncerrar');
//Route::get('tecnicos/{status}/status', 'TecnicoController@status');
//Route::get('tecnicos/{id}/acao', 'TecnicoController@acao');

//Setor Tecnico
Route::get('tecnicos/{setor}/{id}/setors', 'TecnicoController@setors');
Route::post('tecnicos/setorUpdate', 'TecnicoController@setorUpdate');
Route::post('tecnicos/setorDestroy', 'TecnicoController@setorDestroy');

//LIVROS Técnicos

Route::get('livros/{setor}/', 'LivroController@index');
Route::post('livros/{setor}/busca', 'LivroController@busca');
Route::get('livros/{setor}/{id}/show', 'LivroController@show');
Route::get('livros/{setor}/create', 'LivroController@create');


    

