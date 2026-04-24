<?php
session_start();
session_unset();
session_destroy();

header('Content-Type: application/json; charset=utf-8');
echo json_encode(['status' => 'ok', 'mensagem' => 'Logout realizado com sucesso.', 'data' => []]);
