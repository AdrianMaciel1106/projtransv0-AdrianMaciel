# Projecte Transversal 0 – Quiz Educatiu (ODS 4)

**Autor:** Adrian Maciel  
**Contacte:** [a22adrmacfir@inspedralbes.cat](mailto:a22adrmacfir@inspedralbes.cat)  
**Assignatura:** Desenvolupament Client (DIGISOS)  
**Curs:** 2024 – 2025  

---

## Descripció del Projecte

Aquest projecte és una aplicació **web interactiva** tipus *quiz* desenvolupada amb **JavaScript, PHP i MySQL**, que permet als usuaris respondre preguntes de manera dinàmica i veure el resultat final en temps real.  
L'objectiu principal és promoure l’aprenentatge actiu i l’autoavaluació mitjançant un entorn senzill i accessible des del navegador.

L’aplicació està pensada per servir com a base d’un sistema educatiu gamificat, fomentant la **participació i la motivació dels estudiants**.

---

## Objectiu de Desenvolupament Sostenible (ODS)

###ODS 4 – Educació de Qualitat
Aquest projecte contribueix a l’**ODS 4: Educació de Qualitat**, ja que utilitza la tecnologia per:
- Fomentar l’aprenentatge digital interactiu.
- Donar eines accessibles per a l’avaluació i la formació contínua.
- Integrar la ludificació (*gamificació*) dins l’aprenentatge per augmentar la implicació de l’alumnat.

> **Meta 4.4**: Incrementar el nombre de joves i adults amb competències tècniques i professionals rellevants per a l’ocupació i el treball digne.

---

## Tecnologies Utilitzades

| Àrea | Tecnologia |
|------|-------------|
| Front-end | HTML5, CSS3, JavaScript (ES6 Vanilla) |
| Back-end | PHP (PDO + MySQL) |
| Base de dades | MySQL |
| Control de versions | Git + GitHub |
| Qualitat de codi | SonarCloud |
| Desplegament | Vercel / Netlify (Front-end) |

---

## Estructura del Projecte

projtransv0-AdrianMaciel/
│
├── front-end/
│ ├── index.html
│ ├── script.js
│ ├── style.css
│ ├── admin.html / admin.js / admin.css
│ └── logos/ (imatges)
│
├── back-end/
│ ├── admin/
│ │ └── admin.php
│ └── src/
│ ├── db.php
│ ├── getPreguntes.php ← endpoint GET/POST del qüestionari
│ ├── enviarResultats.php
│ ├── finalitza.php
│ └── README.md
│
├── Activitat2/ i Activitat3/ ← versions de proves anteriors
│
└── README.md ← aquest document


---

## Instal·lació i Execució Local

### Requisits previs
- PHP ≥ 8.0  
- MySQL o MariaDB  
- Navegador modern (Chrome, Firefox, Edge, etc.)

### Configuració de la base de dades
1. Crear una base de dades (per exemple, `projtransv0`).
2. Importar el script SQL amb les taules `preguntes` i `respostes`.
3. Configurar les credencials a `back-end/src/db.php`:
   ```php
   $host = 'localhost';
   $dbname = 'projtransv0';
   $user = 'root';
   $pass = '';
