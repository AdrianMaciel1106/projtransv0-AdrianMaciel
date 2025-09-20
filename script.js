let data = null; //Definim la variable global per a les dades
let current = 0; //Definim la variable global per a la pregunta actual
let answers = []; //Definim la variable global per a les respostes

// Definim la funció principal que s'executa quan el document (DOM) està carregat
window.addEventListener('DOMContentLoaded', () => {

  const container = document.getElementById('questionari'); //Contenidor de les preguntes
  const result = document.getElementById('contador'); //Contenidor del resultat

  // 1) Carregar preguntes des del servidor
  fetch(`getPreguntes.php?n=${n}`)
    // Si no s'ha pogut carregar, llencem un error
    .then(r => {
      if (!r.ok) throw new Error('Error al carregar preguntes');
      return r.json();
    })

    // Si s'ha carregat correctament, processem les dades
    .then(json => {
      data = { preguntes: Array.isArray(json) ? json : (json.preguntes || []) };

      // Comprovem si hi ha preguntes
      if (!data.preguntes || data.preguntes.length === 0) {
        container.textContent = 'No hi ha preguntes.';
        return;
      }

      // Barregem les preguntes
      data.preguntes.forEach(p => {
        if (Array.isArray(p.respostes)) {
          p.respostes.sort(() => Math.random() - 0.5);
        }
      });

      // Iniciar temporizador de 30 segundos
      startTimer();

      // Mostrar la primera pregunta
      showQuestion();
    })

    // Si hi ha un error, mostrem un missatge
    .catch(err => {
      container.textContent = 'Error carregant preguntes.';
      console.error(err);
    });


  // 2) Funció per mostrar pregunta actual
  function showQuestion() {
    const preguntes = data.preguntes; // Array de preguntes

    // Comprovem els limits de l'index actual dins de l'array
    if (current < 0) current = 0;
    if (current >= preguntes.length) {
      showEndScreen();
      return;
    }

    const p = preguntes[current]; //Definim variable per a la pregunta actual
    let html = `<h2>${current + 1}. ${p.pregunta}</h2>`; //Mostrem la pregunta actual

    // Imatge principal de la pregunta (si existeix)
    if (p.imatge) {
      html += `<img src="${p.imatge}" alt="imatge de la pregunta" style="max-width:200px; display:block; margin:10px auto;">`;
    }

    // Opciones de respuesta
    p.respostes.forEach((opt, i) => {
      html += `
        <button class="resposta" data-index="${i}" style="display:block; margin:10px auto; padding:10px; cursor:pointer;">
          ${opt.imatge ? `<img src="${opt.imatge}" alt="${opt.resposta}" style="max-width:100px; display:block; margin:auto;">` : ""}
          <span>${opt.resposta}</span>
        </button>
      `;
    });

    // Controls: anterior / següent
    html += `
      <div style="margin-top:20px;">
        <button id="prev" ${current === 0 ? "disabled" : ""}>Anterior</button>
        <button id="next">Següent</button>
      </div>
    `;

    container.innerHTML = html; //Actualitzem el contenidor amb la pregunta i respostes
    if (result) result.textContent = `${current + 1} / ${preguntes.length}`; //Actualitzem el comptador de preguntes si existeix

    // Handlers de botons de resposta
    container.querySelectorAll(".resposta").forEach(btn => {
      btn.addEventListener("click", () => {
        const idx = parseInt(btn.dataset.index, 10);
        marcarResposta(idx);
      });
    });

    // Handlers de navegació quan l'usuari fa clic als botons d'anterior i següent
    document.getElementById('prev').addEventListener('click', () => {
      if (current > 0) current--;
      showQuestion();
    });
    document.getElementById('next').addEventListener('click', () => {
      current++;
      showQuestion();
    });
  }

  // 3) Funció per marcar resposta
  function marcarResposta(index) {
    const pregunta = data.preguntes[current];
    const resposta = pregunta.respostes[index];

    // Comprovem si la resposta és correcta
    if (resposta.correcta) {
      alert("Correcte!");
      answers[current] = true;
    } else {
      alert("Incorrecte!");
      answers[current] = false;
    }
  }

  // 4) Temporitzador de 30 segons
  function startTimer() {
    let temps = 30; // Segons
    const display = document.getElementById('contador'); // Contenidor del temporitzador

    // Actualitzem el temporitzador cada segon
    const interval = setInterval(() => {
      display.textContent = `${temps}s`;
      temps--;

      // Quan el temps s'acaba, mostrem la pantalla final
      if (temps < 0) {
        clearInterval(interval);
        display.textContent = "TEMPS!";
        showEndScreen();
      }
    }, 1000);
  }

  // 5) Pantalla final
  function showEndScreen() {
    const correctes = answers.filter(x => x === true).length; // Comptem les respostes correctes
    container.innerHTML = `
      <h2>Fi del joc!</h2>
      <p>Has encertat ${correctes} de ${data.preguntes.length} preguntes.</p>
    `;
  }
});
