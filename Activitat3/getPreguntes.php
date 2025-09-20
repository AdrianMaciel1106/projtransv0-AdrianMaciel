<?php
header('Content-Type: application/json; charset=utf-8');
session_start();

// Banco de preguntas
$all = [
  ['id'=>1, 'pregunta'=>'Quin color té el cel?', 'respostes'=>[['text'=>'Blau'],['text'=>'Verd'],['text'=>'Vermell']], 'correctIndex'=>0],
  ['id'=>2, 'pregunta'=>'2+2=?', 'respostes'=>[['text'=>'3'],['text'=>'4'],['text'=>'5']], 'correctIndex'=>1],
  ['id'=>3, 'pregunta'=>'Capital d\'Espanya?', 'respostes'=>[['text'=>'Madrid'],['text'=>'Barcelona'],['text'=>'València']], 'correctIndex'=>0],
  ['id'=>4, 'pregunta'=>'Quin és l\'oceà més gran del món?', 'respostes'=>[['text'=>'Atlàntic'],['text'=>'Pacífic'],['text'=>'Índic']], 'correctIndex'=>1],
  ['id'=>5, 'pregunta'=>'Quin planeta és conegut com el planeta vermell?', 'respostes'=>[['text'=>'Mart'],['text'=>'Júpiter'],['text'=>'Venus']], 'correctIndex'=>0],
  ['id'=>6, 'pregunta'=>'Quin animal és el més ràpid a terra?', 'respostes'=>[['text'=>'Guepard'],['text'=>'Cavall'],['text'=>'Lleó']], 'correctIndex'=>0],
  ['id'=>7, 'pregunta'=>'Quin és l\'element químic amb símbol O?', 'respostes'=>[['text'=>'Or'],['text'=>'Oxigen'],['text'=>'Osmir']], 'correctIndex'=>1],
  ['id'=>8, 'pregunta'=>'Quin és el riu més llarg del món?', 'respostes'=>[['text'=>'Nil'],['text'=>'Amazones'],['text'=>'Yangtsé']], 'correctIndex'=>1],
  ['id'=>9, 'pregunta'=>'Quin és el país amb més habitants?', 'respostes'=>[['text'=>'Índia'],['text'=>'Xina'],['text'=>'Estats Units']], 'correctIndex'=>1],
  ['id'=>10, 'pregunta'=>'Quin instrument té sis cordes habitualment?', 'respostes'=>[['text'=>'Violí'],['text'=>'Guitarra'],['text'=>'Piano']], 'correctIndex'=>1],
  ['id'=>11, 'pregunta'=>'Quin és el metall més utilitzat en la fabricació d\'acer?', 'respostes'=>[['text'=>'Ferro'],['text'=>'Alumini'],['text'=>'Coure']], 'correctIndex'=>0],
  ['id'=>12, 'pregunta'=>'Quin és el continent on es troba Egipte?', 'respostes'=>[['text'=>'Àfrica'],['text'=>'Àsia'],['text'=>'Europa']], 'correctIndex'=>0],
  ['id'=>13, 'pregunta'=>'Quin gas respiren principalment els humans?', 'respostes'=>[['text'=>'Diòxid de carboni'],['text'=>'Oxigen'],['text'=>'Nitrogen']], 'correctIndex'=>1],
];

$n = min(10, count($all)); // punto y coma añadido

shuffle($all);
$sel = array_slice($all, 0, $n);

$_SESSION['answers'] = [];
foreach ($sel as $p) {
    $_SESSION['answers'][$p['id']] = $p['correctIndex'];
}

$public = array_map(function($p){
  return ['id'=>$p['id'],'pregunta'=>$p['pregunta'],'respostes'=>$p['respostes']];
}, $sel);

echo json_encode($public);
