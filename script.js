// https://github.com/alvaroph/tr0_daw

let data = null; // Dades del quiz
let current = 0; // Índex de la pregunta actual
let answers = [];// Respostes escollides per l’usuari
let timer = null;// Referència al temporitzador

//Definim les funcions quan el DOM està carregat
window.addEventListener('DOMContentLoaded', () => {
  const container = document.getElementById('questionari'); // Contenidor de preguntes
  const preguntaCounter = document.getElementById('contador-pregunta'); // Contador de preguntes
  const tiempoCounter   = document.getElementById('contador-tiempo'); // Contador de temps

  // 1) Carregar preguntes del servidor
  fetch('./getPreguntes.php')
    .then(r => {
      if (!r.ok) throw new Error('Error al carregar preguntes');
      return r.json();
    })
    .then(json => {
      data = { preguntes: Array.isArray(json) ? json : [] };

      if (!data.preguntes.length) {
        container.textContent = 'No hi ha preguntes.';
        return;
      }

      // Barregem respostes dins de cada pregunta
      data.preguntes.forEach(p => {
        if (Array.isArray(p.respostes)) {
          p.respostes.sort(() => Math.random() - 0.5);
        }
      });

      startTimer();   // 30 segons
      showQuestion();
    })
    .catch(err => {
      container.textContent = 'Error carregant preguntes.';
      console.error(err);
    });

    
  // ----- Mostrar pregunta -----
  function showQuestion() {
    const preguntes = data.preguntes;

    if (current < 0) current = 0;
    if (current >= preguntes.length) {
      showEndScreen();
      return;
    }

    const p = preguntes[current]; // Pregunta actual
    let html = `<h2>${current + 1}. ${p.pregunta}</h2>`;  // Títol pregunta

    if (p.imatge) {
      html += `<img src="${p.imatge}" alt="imatge de la pregunta" style="max-width:200px; display:block; margin:10px auto;">`;
    }

    // Llistat d’opcions
    p.respostes.forEach((opt, i) => {
      html += `
        <button class="resposta" data-index="${i}" style="display:block; margin:10px auto; padding:10px; cursor:pointer;">
          ${opt.imatge ? `<img src="${opt.imatge}" alt="" style="max-width:100px; display:block; margin:auto;">` : ""}
          ${opt.text || ""}
        </button>
      `;
    });

    // Botons de navegació
    html += `
      <div style="margin-top:20px;">
        <button id="enrere" ${current === 0 ? "disabled" : ""}>Anterior</button>
        <button id="seguent">Següent</button>
      </div>
    `;

    container.innerHTML = html;
    if (preguntaCounter) preguntaCounter.textContent = `${current + 1} / ${preguntes.length}`;

    // Events respostes
    container.querySelectorAll(".resposta").forEach(btn => {
      btn.addEventListener("click", () => {
        answers[current] = parseInt(btn.dataset.index, 10);
        // marquem visualment
        container.querySelectorAll(".resposta").forEach(b => b.style.background = "");
        btn.style.background = "#d0f0d0";
      });
    });

    // Navegació
    document.getElementById('enrere').addEventListener('click', () => {
      if (current > 0) current--;
      showQuestion();
    });
    document.getElementById('seguent').addEventListener('click', () => {
      current++;
      showQuestion();
    });
  }

  // Iniciar temporitzador
  function startTimer() {
  let temps = 30; // segons

  if (!tiempoCounter) return; // Si no hi ha element, sortim

  tiempoCounter.textContent = formatTime(temps); // Mostrar temps inicial

  // Netejar qualsevol temporitzador anterior
  timer = setInterval(() => {
    temps--;
    if (temps >= 0) {
      tiempoCounter.textContent = `${temps}s`; // Actualitzar comptador
    } else {
      clearInterval(timer); // Aturar el temporitzador
      tiempoCounter.textContent = "TEMPS!"; // Indicar que s'ha acabat el temps
      showEndScreen(); // Mostrar pantalla final
    }
  }, 1000); // Actualitzar cada segon

  // Funció per formatar temps en mm:ss
  function formatTime(seconds) {
  const mins = Math.floor(seconds / 60);// minuts
  const secs = seconds % 60;// segons
  return `${String(mins).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;// format mm:ss
}
}

  //Mostrar pantalla final
  function showEndScreen() {
    clearInterval(timer);   // Aturar temporitzador

    container.innerHTML = `
      <h2>Fi del joc!</h2>
      <p>Has contestat ${answers.filter(a => a !== undefined).length} de ${data.preguntes.length} preguntes.</p>
      <p>(La correcció es farà al servidor quan enviïs les respostes)</p>
    `;
  }
});
