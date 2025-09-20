<?php
header('Content-Type: application/json; charset=utf-8');
session_start();

// Exemple de preguntes internes
$all = [
  ['id'=>1, 'pregunta'=>'Quin color té el cel?', 'respostes'=>[['text'=>'Blau'],['text'=>'Verd'],['text'=>'Vermell']], 'correctIndex'=>0],
  ['id'=>2, 'pregunta'=>'2+2=?', 'respostes'=>[['text'=>'3'],['text'=>'4'],['text'=>'5']], 'correctIndex'=>1],
  ['id'=>3, 'pregunta'=>'Capital d\'Espanya?', 'respostes'=>[['text'=>'Madrid'],['text'=>'Barcelona'],['text'=>'València']], 'correctIndex'=>0]
];

// Llegir n i validar
$n = isset($_GET['n']) ? max(1, intval($_GET['n'])) : 1;
$n = min($n, count($all));

// Seleccionar aleatòries
shuffle($all);
$sel = array_slice($all, 0, $n);

// Guardar mapping id -> correctIndex per validar després
$_SESSION['answers'] = [];
foreach ($sel as $p) $_SESSION['answers'][$p['id']] = $p['correctIndex'];

// Retornar preguntes sense correctIndex
$public = array_map(function($p){
  return ['id'=>$p['id'],'pregunta'=>$p['pregunta'],'respostes'=>$p['respostes']];
}, $sel);

echo json_encode($public);
