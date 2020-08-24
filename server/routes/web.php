<?php

Route::get('/', function () {
    return view('welcome');
});



Route::get('/contract/{id}', function ($id) {
    return "Ops... Ainda não posso exibir o Contrato no. {$id}, estamos trabalhando nesta funcionalidade.";
})->name('contract.view');
