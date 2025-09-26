<?php
header('Content-Type: application/json; charset=utf-8'); // Indiquem que retornem JSON
session_start();// Iniciem sessió per guardar respostes correctes

// Banc preguntes
/*$all = [
  [
    'id' => 1,
    'pregunta' => "Quin d’aquests és el logotip correcte de BMW?",
    'respostes' => [
      [ 'id' => 1, 'text' => "", 'correcta' => true, 'imatge' => "logos/bmw.png" ],
      [ 'id' => 2, 'text' => "", 'correcta' => false, 'imatge' => "https://static.vecteezy.com/system/resources/previews/019/766/236/non_2x/audi-logo-audi-icon-transparent-free-png.png" ],
      [ 'id' => 3, 'text' => "", 'correcta' => false, 'imatge' => "https://upload.wikimedia.org/wikipedia/commons/9/90/Mercedes-Logo.svg" ],
      [ 'id' => 4, 'text' => "", 'correcta' => false, 'imatge' => "logos/lexus.png" ]
    ]
  ],
  [
    'id' => 2,
    'pregunta' => "Quin és el logotip d’Apple?",
    'respostes' => [
      [ 'id' => 1, 'text' => "", 'correcta' => true, 'imatge' => "https://api.triviacreator.com/v1/imgs/quiz/8a7240d179-450-ceca425e-64e9-4495-94ac-53985e96b9d3.png" ],
      [ 'id' => 2, 'text' => "", 'correcta' => false, 'imatge' => "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTh9axExasKJoaXSnBnr8vc25f-zDUuHSvh4A&s" ],
      [ 'id' => 3, 'text' => "", 'correcta' => false, 'imatge' => "logos/sony.png" ],
      [ 'id' => 4, 'text' => "", 'correcta' => false, 'imatge' => "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSH9wxO5Dk0EyiRvjbFNx5yUo-DqP7kQ9zzqQ&s" ]
    ]
  ],
  [
    'id' => 3,
    'pregunta' => "Quin logotip pertany a Coca-Cola?",
    'respostes' => [
      [ 'id' => 1, 'text' => "", 'correcta' => true, 'imatge' => "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSdHM3pgI5wMfDnAqgDX6ld2ALPRRijJ00s4w&s" ],
      [ 'id' => 2, 'text' => "", 'correcta' => false, 'imatge' => "https://i.pinimg.com/474x/d1/23/a6/d123a699d889bf2ea3543a56f2ce18cf.jpg" ],
      [ 'id' => 3, 'text' => "", 'correcta' => false, 'imatge' => "https://pbs.twimg.com/media/E7efFYfXMAY66w9.png" ],
      [ 'id' => 4, 'text' => "", 'correcta' => false, 'imatge' => "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRObb3MpBSl4erlkFnBwTz_h6irckSpBSSQnQ&s" ]
    ]
  ],
  [
    'id' => 4,
    'pregunta' => "Quin és el logotip de Nike?",
    'respostes' => [
      [ 'id' => 1, 'text' => "", 'correcta' => true, 'imatge' => "https://upload.wikimedia.org/wikipedia/commons/a/a6/Logo_NIKE.svg" ],
      [ 'id' => 2, 'text' => "", 'correcta' => false, 'imatge' => "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTOXBUSqR9QfqToTSuR2VKYa4UOm4ojiJdn8A&s" ],
      [ 'id' => 3, 'text' => "", 'correcta' => false, 'imatge' => "https://i.pinimg.com/originals/ca/b4/1b/cab41b8b072053584102f8567f6ecce5.jpg" ],
      [ 'id' => 4, 'text' => "", 'correcta' => false, 'imatge' => "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSmWl9lUsbbzP5vM52R00xIgoVorht_UYOR4w&s" ]
    ]
  ],
  [
    'id' => 5,
    'pregunta' => "Quin logotip representa Starbucks?",
    'respostes' => [
      [ 'id' => 1, 'text' => "", 'correcta' => true, 'imatge' => "https://upload.wikimedia.org/wikipedia/en/d/d3/Starbucks_Corporation_Logo_2011.svg" ],
      [ 'id' => 2, 'text' => "", 'correcta' => false, 'imatge' => "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRsaVtFtTGmzHiAGBh4S2jt4kjzgweab_ytsA&s" ],
      [ 'id' => 3, 'text' => "", 'correcta' => false, 'imatge' => "https://i.pinimg.com/736x/a4/28/5f/a4285fd266a5e705678fcda933519df7.jpg" ],
      [ 'id' => 4, 'text' => "", 'correcta' => false, 'imatge' => "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQZz5kgElf_8HVk5EJnsD7k64j01_RMZzQofQ&s" ]
    ]
  ],
  [
    'id' => 6,
    'pregunta' => "Quin logotip és de PlayStation?",
    'respostes' => [
      [ 'id' => 1, 'text' => "", 'correcta' => true, 'imatge' => "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSH8wt-79v8mHxlRJEoxLqTnHhMsbgJVgGCTw&s" ],
      [ 'id' => 2, 'text' => "", 'correcta' => false, 'imatge' => "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRiEM_izokj80kOWvN9B8eDQfGy6MLZU0icWw&s" ],
      [ 'id' => 3, 'text' => "", 'correcta' => false, 'imatge' => "https://media.baamboozle.com/uploads/images/410104/1640753043_9758.jpeg" ],
      [ 'id' => 4, 'text' => "", 'correcta' => false, 'imatge' => "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQG1lqrBAYLmYdkGcOjuJr7Ix-BEwSsRTr9og&s" ]
    ]
  ],
  [
    'id' => 7,
    'pregunta' => "Quin logotip pertany a Google?",
    'respostes' => [
      [ 'id' => 1, 'text' => "", 'correcta' => true, 'imatge' => "https://quizizz.com/media/resource/gs/quizizz-media/quizzes/940f318e-d7e6-4277-ae39-0723d02373c7" ],
      [ 'id' => 2, 'text' => "", 'correcta' => false, 'imatge' => "https://www.jetpunk.com/img/user-img/b2/b25be27e03-450.webp" ],
      [ 'id' => 3, 'text' => "", 'correcta' => false, 'imatge' => "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQSLFS48ZDFfFjDnzM4zU9BEbrU-KUvK3I0LQ&s" ],
      [ 'id' => 4, 'text' => "", 'correcta' => false, 'imatge' => "https://logos-world.net/wp-content/uploads/2022/04/DuckDuckGo-Emblem.png" ]
    ]
  ],
  [
    'id' => 8,
    'pregunta' => "Quin és el logotip correcte d’Audi?",
    'respostes' => [
      [ 'id' => 1, 'text' => "", 'correcta' => true, 'imatge' => "https://i.pinimg.com/474x/4c/34/ee/4c34eefba221546293d1032ae967eddc.jpg" ],
      [ 'id' => 2, 'text' => "", 'correcta' => false, 'imatge' => "https://1000logos.net/wp-content/uploads/2019/12/Volkswagen-Logo-1948.png" ],
      [ 'id' => 3, 'text' => "", 'correcta' => false, 'imatge' => "https://ahaslides.com/wp-content/uploads/2023/11/Skoda-Logo-2011-1024x576.png" ],
      [ 'id' => 4, 'text' => "", 'correcta' => false, 'imatge' => "https://www.carlogos.org/car-logos/seat-logo.png" ]
    ]
  ],
  [
    'id' => 9,
    'pregunta' => "Quin d’aquests logos és Burberry?",
    'respostes' => [
      [ 'id' => 1, 'text' => "", 'correcta' => true, 'imatge' => "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS8ZnuazW1wyHTHsBOEGj-DX-7GOrrhowEhLQ&s" ],
      [ 'id' => 2, 'text' => "", 'correcta' => false, 'imatge' => "https://www.infoplease.com/sites/infoplease.com/files/h5p/content/186/images/file-5e8c1a8d54f92.jpg" ],
      [ 'id' => 3, 'text' => "", 'correcta' => false, 'imatge' => "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTzcBWNy_OArmyhL2Cm44b-wn7g14dwtzgtXQ&s" ],
      [ 'id' => 4, 'text' => "", 'correcta' => false, 'imatge' => "https://img.buzzfeed.com/store-an-image-prod-us-east-1/cE4eBFtsB.png?downsize=625%3A*&output-format=jpg&output-quality=auto" ]
    ]
  ],
  [
    'id' => 10,
    'pregunta' => "Quin logotip és de Samsung?",
    'respostes' => [
      [ 'id' => 1, 'text' => "", 'correcta' => true, 'imatge' => "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTh9axExasKJoaXSnBnr8vc25f-zDUuHSvh4A&s" ],
      [ 'id' => 2, 'text' => "", 'correcta' => false, 'imatge' => "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSH9wxO5Dk0EyiRvjbFNx5yUo-DqP7kQ9zzqQ&s" ],
      [ 'id' => 3, 'text' => "", 'correcta' => false, 'imatge' => "https://api.triviacreator.com/v1/imgs/quiz/8a7240d179-450-ceca425e-64e9-4495-94ac-53985e96b9d3.png" ],
      [ 'id' => 4, 'text' => "", 'correcta' => false, 'imatge' => "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSLDnKyNDYRIaBX-ykpe42hTUOALPuiFAxZQg&s" ]
    ]
  ],
  [
    'id' => 11,
    'pregunta' => "Quin logotip representa Red Bull?",
    'respostes' => [
      [ 'id' => 1, 'text' => "", 'correcta' => true, 'imatge' => "https://quiz.com/image-cache/uploads/0bb99152-2dc4-4603-9777-36fcfc942d98/a935a0468fb6dbf77a028409d9359dd9aef8e686.jpg.jpg?width=332&height=249&x=0.000&y=0.000&z=1.000" ],
      [ 'id' => 2, 'text' => "", 'correcta' => false, 'imatge' => "https://media.baamboozle.com/uploads/images/670774/1680501528_144598.png" ],
      [ 'id' => 3, 'text' => "", 'correcta' => false, 'imatge' => "logos/rockstar.png" ],
      [ 'id' => 4, 'text' => "", 'correcta' => false, 'imatge' => "https://1000marcas.net/wp-content/uploads/2021/04/Burn-Logo.png" ]
    ]
  ],
  [
    'id' => 12,
    'pregunta' => "Quin és el logotip de Rolex?",
    'respostes' => [
      [ 'id' => 1, 'text' => "", 'correcta' => true, 'imatge' => "logos/rolex.png" ],
      [ 'id' => 2, 'text' => "", 'correcta' => false, 'imatge' => "https://cdn.quizly.co/wp-content/uploads/2025/02/14143050/Omega-logo.webp" ],
      [ 'id' => 3, 'text' => "", 'correcta' => false, 'imatge' => "https://cdn.virily.com/wp-content/uploads/2017/07/image-6.jpg" ],
      [ 'id' => 4, 'text' => "", 'correcta' => false, 'imatge' => "https://watchesulike.com/68424-large_default/patek-philippe-logo-to-stick-tm.jpg" ]
    ]
  ],
  [
    'id' => 13,
    'pregunta' => "Quin d’aquests és Calvin Klein?",
    'respostes' => [
      [ 'id' => 1, 'text' => "", 'correcta' => true, 'imatge' => "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSpSM0ElKkWO3n6m7oQfWzJKOrcMriQWwV-bw&s" ],
      [ 'id' => 2, 'text' => "", 'correcta' => false, 'imatge' => "https://i.pinimg.com/736x/bd/03/30/bd0330279e1a0c076b5fd3cefd25cba9.jpg" ],
      [ 'id' => 3, 'text' => "", 'correcta' => false, 'imatge' => "https://1000logos.net/wp-content/uploads/2021/11/Hugo-Boss-Logo-before-2021.png" ],
      [ 'id' => 4, 'text' => "", 'correcta' => false, 'imatge' => "https://logosenvector.com/logo/img/guess-40.jpg" ]
    ]
  ],
  [
    'id' => 14,
    'pregunta' => "Quin logotip és d’Hermès?",
    'respostes' => [
      [ 'id' => 1, 'text' => "", 'correcta' => true, 'imatge' => "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTdx5ssZYG5aXJfPveEZe6_awpqxHjcFYPWSA&s" ],
      [ 'id' => 2, 'text' => "", 'correcta' => false, 'imatge' => "https://1000logos.net/wp-content/uploads/2021/11/Hugo-Boss-Logo-before-2021.png" ],
      [ 'id' => 3, 'text' => "", 'correcta' => false, 'imatge' => "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTbd8_DZe3Tue4JXVj6aigAMkcrQq1xFGBaLQ&s" ],
      [ 'id' => 4, 'text' => "", 'correcta' => false, 'imatge' => "https://1000logos.net/wp-content/uploads/2018/04/Hyundai-Logo-1990.png" ]
    ]
  ],
  [
    'id' => 15,
    'pregunta' => "Quin logotip és Toblerone?",
    'respostes' => [
      [ 'id' => 1, 'text' => "", 'correcta' => true, 'imatge' => "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRqEkRfLFKUNO8tkJ0rAd2LjCdGg75BuHSXrA&s" ],
      [ 'id' => 2, 'text' => "", 'correcta' => false, 'imatge' => "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQExYrHGgccN_k1ZBnc2HU8XMsuSytp93gcug&s" ],
      [ 'id' => 3, 'text' => "", 'correcta' => false, 'imatge' => "https://logosquiz.net/data/logosquiz-aticod/images/milka.png" ],
      [ 'id' => 4, 'text' => "", 'correcta' => false, 'imatge' => "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQbDdQe80zu7YB-Y8ukXwJz-HwKJmp5-DLf9g&s" ]
    ]
  ],
  [
    'id' => 16,
    'pregunta' => "Quin logotip és de L’Oréal Paris?",
    'respostes' => [
      [ 'id' => 1, 'text' => "", 'correcta' => true, 'imatge' => "https://logosquiz.net/data/logoquiz-mangoogames/images/logo_loreal.png" ],
      [ 'id' => 2, 'text' => "", 'correcta' => false, 'imatge' => "https://logosquiz.net/data/logoquiz/images/maybelline_3.png" ],
      [ 'id' => 3, 'text' => "", 'correcta' => false, 'imatge' => "https://1000marcas.net/wp-content/uploads/2020/11/Revlon-Logo-tumb-150x150.jpg" ],
      [ 'id' => 4, 'text' => "", 'correcta' => false, 'imatge' => "https://1000logos.net/wp-content/uploads/2020/04/Emblem-Clinique.jpeg" ]
    ]
  ],
  [
    'id' => 17,
    'pregunta' => "Quin logotip és d’Adidas?",
    'respostes' => [
      [ 'id' => 1, 'text' => "", 'correcta' => true, 'imatge' => "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTOXBUSqR9QfqToTSuR2VKYa4UOm4ojiJdn8A&s" ],
      [ 'id' => 2, 'text' => "", 'correcta' => false, 'imatge' => "https://upload.wikimedia.org/wikipedia/commons/a/a6/Logo_NIKE.svg" ],
      [ 'id' => 3, 'text' => "", 'correcta' => false, 'imatge' => "https://www.jetpunk.com/img/user-img/fb/fbc1c8f69a-450.webp" ],
      [ 'id' => 4, 'text' => "", 'correcta' => false, 'imatge' => "https://static.stacker.com/s3fs-public/styles/1280x720/s3/asics.png" ]
    ]
  ],
  [
    'id' => 18,
    'pregunta' => "Quin logotip és Louis Vuitton?",
    'respostes' => [
      [ 'id' => 1, 'text' => "", 'correcta' => true, 'imatge' => "https://img.buzzfeed.com/store-an-image-prod-us-east-1/cE4eBFtsB.png?downsize=625%3A*&output-format=jpg&output-quality=auto" ],
      [ 'id' => 2, 'text' => "", 'correcta' => false, 'imatge' => "https://www.jetpunk.com/img/user-img/f4/f49fba335f-450.webp" ],
      [ 'id' => 3, 'text' => "", 'correcta' => false, 'imatge' => "https://img.buzzfeed.com/buzzfeed-static/static/2017-11/17/18/enhanced/buzzfeed-prod-fastlane-03/enhanced-4372-1510962054-1.jpg?output-format=jpg&output-quality=auto" ],
      [ 'id' => 4, 'text' => "", 'correcta' => false, 'imatge' => "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTziJH4cDUw97k_lWt1pcYZHsau5F0PtwQdrQ&s" ]
    ]
  ],
  [
    'id' => 19,
    'pregunta' => "Quin logotip és Corona (cervesa)?",
    'respostes' => [
      [ 'id' => 1, 'text' => "", 'correcta' => true, 'imatge' => "logos/corona.png" ],
      [ 'id' => 2, 'text' => "", 'correcta' => false, 'imatge' => "logos/heineken.png" ],
      [ 'id' => 3, 'text' => "", 'correcta' => false, 'imatge' => "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQehXJ5KOIQXbeHar4hnd1XGxc-2LGy5pXZjA&s" ],
      [ 'id' => 4, 'text' => "", 'correcta' => false, 'imatge' => "https://www.thesun.co.uk/wp-content/uploads/2024/10/stella-artois-beer-logo-938755734.jpg?strip=all&w=625" ]
    ]
  ],
  [
    'id' => 20,
    'pregunta' => "Quin logotip és Mitsubishi?",
    'respostes' => [
      [ 'id' => 1, 'text' => "", 'correcta' => true, 'imatge' => "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSiz-n2bMuac0KJsrUyCqyPAoAOUo6YCR0LqQ&s" ],
      [ 'id' => 2, 'text' => "", 'correcta' => false, 'imatge' => "https://i.pinimg.com/736x/eb/b0/3e/ebb03eba30cd4880617d37b28215a31f.jpg" ],
      [ 'id' => 3, 'text' => "", 'correcta' => false, 'imatge' => "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTAe4NjDBNV6qTtkZsinVtjo6cIbTvklPnWHQ&s" ],
      [ 'id' => 4, 'text' => "", 'correcta' => false, 'imatge' => "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSTIvWSbqiT-nX_x2aASbFnxSpKmn6-HQQHZQ&s" ]
    ]
  ]
];*/

// shuffle($all); // Si quieres que salgan en orden aleatorio
// $sel = $all; // Como siempre serán 20, no hace falta array_slice

$_SESSION['answers'] = []; // Inicializamos array de respuestas correctas
foreach ($sel as $p) {
    // Guardamos el índice de la respuesta correcta
    $_SESSION['answers'][$p['id']] =
        array_search(true, array_column($p['respostes'], 'correcta'));
}

// Quitamos el campo "correcta" antes de enviar al cliente

$public = array_map(function($p){
  $respostes = array_map(function($r){
    unset($r['correcta']);
    return $r;
  }, $p['respostes']);
  $correctaIndex = array_search(true, array_column($p['respostes'], 'correcta'));
  return [
    'id'            => $p['id'],
    'pregunta'      => $p['pregunta'],
    'respostes'     => $respostes,
    'correctaIndex' => $correctaIndex
  ];
}, $sel);

echo json_encode($public, JSON_UNESCAPED_UNICODE); // Enviem dades al client