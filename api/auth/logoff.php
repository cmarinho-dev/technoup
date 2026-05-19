<?php
include_once '../funcoes.php';

session_start();
session_unset();
session_destroy();

respostaJson('ok', 'Logout realizado com sucesso.');
