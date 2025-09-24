// https://github.com/alvaroph/tr0_daw


let data = null; // Dades del quiz
let current = 0; // Índex de la pregunta actual
let answers = [];// Respostes escollides per l’usuari
let timer = null;// Referència al temporitzador

// Objecte estat de la partida
let estatDeLaPartida = {
  contadorPreguntes: 0,
  respostesUsuari: [] // Índexs de respostes escollides
};

//Definim les funcions quan el DOM està carregat

window.addEventListener('DOMContentLoaded', () => {
  const container = document.getElementById('questionari'); // Contenidor de preguntes
  const preguntaCounter = document.getElementById('contador-pregunta'); // Contador de preguntes
  const tiempoCounter   = document.getElementById('contador-tiempo'); // Contador de temps
  let marcadorDiv = document.getElementById('marcador'); // Div marcador
  
  // Si no existeix el div marcador, el creem
  if (!marcadorDiv) {
    marcadorDiv = document.createElement('div');
    marcadorDiv.id = 'marcador';
    container.parentNode.insertBefore(marcadorDiv, container.nextSibling);
  }

  // Afegim el botó "Enviar Resultats" ocult
  let btnEnviar = document.getElementById('enviar-resultats');
  if (!btnEnviar) {
    btnEnviar = document.createElement('button');
    btnEnviar.id = 'enviar-resultats';
    btnEnviar.textContent = 'Enviar Resultats';
    btnEnviar.classList.add('hidden');
    container.parentNode.insertBefore(btnEnviar, marcadorDiv.nextSibling);
  }

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
      showQuestion(); // Mostrar primera pregunta
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

    // Llistat d’opcions dentro de un contenedor flex
    html += `<div class="resposta-container">`;
    p.respostes.forEach((opt, i) => {
      html += `
        <button class="resposta" data-index="${i}">
          ${opt.imatge ? `<img src="${opt.imatge}" alt="">` : ""}
          ${opt.text || ""}
        </button>
      `;
    });
    html += `</div>`;

    // Botons de navegació
    html += `
      <div>
        <button class="botons-navegacio" id="enrere" ${current === 0 ? "disabled" : ""}>Anterior</button>
        <button class="botons-navegacio" id="seguent">Següent</button>
      </div>
    `;

    html += `<h3>Pregunta ${current + 1} de ${preguntes.length}</h3>`;

    container.innerHTML = html;
    if (preguntaCounter) preguntaCounter.textContent = `${current + 1} / ${preguntes.length}`;

    // Events respostes
    const respostaButtons = container.querySelectorAll(".resposta");
    respostaButtons.forEach((btn, i) => {
      btn.addEventListener("click", () => {
        answers[current] = i;
        estatDeLaPartida.respostesUsuari[current] = i;
        estatDeLaPartida.contadorPreguntes = estatDeLaPartida.respostesUsuari.filter(a => a !== undefined).length;
        respostaButtons.forEach(b => b.classList.remove("seleccionada"));
        btn.classList.add("seleccionada");
        renderitzarMarcador();
        // Mostrar botó "Enviar Resultats" si totes les preguntes estan respostes
        if (estatDeLaPartida.contadorPreguntes === preguntes.length) {
          btnEnviar.classList.remove('hidden');
        }
      });
      // Marcar la opción seleccionada si existe
      if (answers[current] === i) {
        btn.classList.add("seleccionada");
      }
    });

    // Navegació
    document.getElementById('enrere').addEventListener('click', () => {
      if (current > 0) current--;
      showQuestion();
      renderitzarMarcador();
    });
    document.getElementById('seguent').addEventListener('click', () => {
      current++;
      showQuestion();
      renderitzarMarcador();
    });

    renderitzarMarcador();
    // Ocultem el botó "Enviar Resultats" si no s'han respost totes
    if (estatDeLaPartida.contadorPreguntes < preguntes.length) {
      btnEnviar.classList.add('hidden'); // Ocult
    }
  }

  // Funció per renderitzar el marcador
  function renderitzarMarcador() {
    if (!marcadorDiv) return;
    const total = data && data.preguntes ? data.preguntes.length : 0;
    const contestades = estatDeLaPartida.respostesUsuari.filter(a => a !== undefined).length;
    marcadorDiv.textContent = `Preguntes respostes: ${contestades} de ${total}`;
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
      tiempoCounter.textContent = formatTime(temps); // Actualitzar comptador en formato mm:ss
    } else {
      clearInterval(timer); // Aturar el temporitzador
      tiempoCounter.textContent = "TEMPS!"; // Indicar que s'ha acabat el temps
      showEndScreen(); // Mostrar pantalla final
    }
  }, 1000); // Actualitzar cada segon

  // Funció per formatar temps en m:ss
  function formatTime(seconds) {
    const mins = Math.floor(seconds / 60);// minuts
    const secs = seconds % 60;// segons
    return `${mins}:${secs.toString().padStart(2, '0')}`;// format m:ss
  }
}

  //Mostrar pantalla final
  function showEndScreen() {
    clearInterval(timer);   // Aturar temporitzador

    // Calcular respostes correctes
    let correctes = 0;
    data.preguntes.forEach((pregunta, idx) => {
      const respostaUsuari = estatDeLaPartida.respostesUsuari[idx];
      if (respostaUsuari !== undefined) {
        // Trobar la resposta correcta (la propietat correcta=true)
        const respostaCorrectaIndex = pregunta.respostes.findIndex(r => r.correcta === true);
        if (respostaUsuari === respostaCorrectaIndex) correctes++;
      }
    });
    const total = data.preguntes.length;
    const nota = total > 0 ? ((correctes / total) * 10).toFixed(2) : '0.00';

    container.innerHTML = `
      <h2>Fi del joc!</h2>
      <p>Has contestat ${estatDeLaPartida.respostesUsuari.filter(a => a !== undefined).length} de ${total} preguntes.</p>
      <p>Respostes correctes: <strong>${correctes}</strong> de <strong>${total}</strong></p>
      <p>Nota final: <strong>${nota}</strong> / 10</p>
      <button id="reiniciar" class="botons-navegacio">Reiniciar</button>
    `;
    renderitzarMarcador();
    btnEnviar.classList.add('hidden');
    document.getElementById('reiniciar').addEventListener('click', () => {
      current = 0;
      estatDeLaPartida = { contadorPreguntes: 0, respostesUsuari: [] };
      answers = [];
      showQuestion();
      renderitzarMarcador();
    });
  }
});
