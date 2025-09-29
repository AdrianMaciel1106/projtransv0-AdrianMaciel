let data = null; // { preguntes: [ {id, pregunta, imatge, respostes:[{id,text,imatge}]} ] }
let current = 0; // index de la pregunta actual
let answers = []; // answers[indexPregunta] = idRespuesta
let timer = null; // temporizador
let sending = false; // para evitar envíos múltiples

// Esperar a que el DOM esté cargado
window.addEventListener('DOMContentLoaded', () => {
  const container = document.getElementById('questionari');
  const preguntaCounter = document.getElementById('contador-pregunta');
  const tiempoCounter = document.getElementById('contador-tiempo');

  // Crear marcador y botón de enviar si no existen
  let marcadorDiv = document.getElementById('marcador');
  if (!marcadorDiv) {
    marcadorDiv = document.createElement('div');
    marcadorDiv.id = 'marcador';
    container.parentNode.insertBefore(marcadorDiv, container.nextSibling);
  }

  // Botón de enviar resultados
  let btnEnviar = document.getElementById('enviar-resultats');
  if (!btnEnviar) {
    btnEnviar = document.createElement('button');
    btnEnviar.id = 'enviar-resultats';
    btnEnviar.textContent = 'Enviar Resultats';
    btnEnviar.classList.add('hidden');
    container.parentNode.insertBefore(btnEnviar, marcadorDiv.nextSibling);
  }
  btnEnviar.addEventListener('click', enviarResultats);

  // Cargar preguntas desde el servidor
  fetch('getPreguntes.php?num=10')
    .then(r => r.json())
    .then(json => {
      data = { preguntes: Array.isArray(json) ? json : [] };
      if (!data.preguntes.length) { container.textContent = 'No hi ha preguntes.'; return; }

      // Mezclar respuestas
      data.preguntes.forEach(p => { if (Array.isArray(p.respostes)) p.respostes.sort(() => Math.random()-0.5); });

      // Inicializar estado
      answers = new Array(data.preguntes.length).fill(null);
      startTimer();
      showQuestion();
      renderitzarMarcador();
    })
    .catch(err => { container.textContent = 'Error carregant preguntes.'; console.error(err); });

    // Mostrar la pregunta actual
  function showQuestion() {
    if (!data) return;
    const preguntes = data.preguntes;
    if (current < 0) current = 0;
    if (current >= preguntes.length) {
      container.innerHTML = `<h2>Has arribat al final.</h2>`;
      showEndScreen();
      renderitzarMarcador();
      if (preguntes.length === answers.filter(a=>a!==null).length) btnEnviar.classList.remove('hidden');
      return;
    }

    // Renderizar pregunta y respuestas
    const p = preguntes[current];
    let html = `<h2>${current+1}. ${escapeHtml(p.pregunta)}</h2>`;
    if (p.imatge) html += `<img src="${escapeHtml(p.imatge)}" alt="" style="max-width:200px; display:block; margin:10px auto;">`;

    html += `<div class="resposta-container">`;
    p.respostes.forEach(opt => {
      const selected = answers[current] === opt.id ? ' seleccionada' : '';
      html += `
        <button class="resposta${selected}" data-id="${opt.id}">
          ${opt.imatge ? `<img src="${escapeHtml(opt.imatge)}" alt="">` : ""} ${escapeHtml(opt.text || "")}
        </button>
      `;
    });
    html += `</div>`;

    html += `
      <div>
        <button class="botons-navegacio" id="enrere" ${current===0?"disabled":""}>Anterior</button>
        <button class="botons-navegacio" id="seguent">Següent</button>
      </div>
    `;
    html += `<h3>Pregunta ${current+1} de ${preguntes.length}</h3>`;

    container.innerHTML = html;
    if (preguntaCounter) preguntaCounter.textContent = `${current+1} / ${preguntes.length}`;

    // Añadir eventos a botones de respuesta
    const respostaButtons = container.querySelectorAll('.resposta');
    respostaButtons.forEach(btn => {
      btn.addEventListener('click', () => {
        const id = parseInt(btn.dataset.id, 10);
        answers[current] = id;

        respostaButtons.forEach(b => b.classList.remove('seleccionada'));
        btn.classList.add('seleccionada');

        if (answers.filter(a=>a!==null).length === data.preguntes.length) {
          btnEnviar.classList.remove('hidden');
        }
        renderitzarMarcador();
      });
    });

    const enrereBtn = document.getElementById('enrere');
    if (enrereBtn) enrereBtn.addEventListener('click', () => { current--; showQuestion(); });

    const seguentBtn = document.getElementById('seguent');
    if (seguentBtn) seguentBtn.addEventListener('click', () => { current++; showQuestion(); });

    
    renderitzarMarcador();
    if (answers.filter(a=>a!==null).length < data.preguntes.length) btnEnviar.classList.add('hidden');
  }

  // Renderizar marcador de preguntas contestadas
  function renderitzarMarcador() {
    const total = data?.preguntes?.length || 0;
    const contestades = answers.filter(a=>a!==null).length;
    marcadorDiv.textContent = `Preguntes respostes: ${contestades} de ${total}`;
  }

  // Iniciar temporizador de 30 segundos
  function startTimer() {
    let temps = 30;
    if (!tiempoCounter) return;
    tiempoCounter.textContent = formatTime(temps);
    clearInterval(timer);
    timer = setInterval(() => {
      temps--;
      if (temps >= 0) tiempoCounter.textContent = formatTime(temps);
      if (temps < 0) {
        clearInterval(timer);
        tiempoCounter.textContent = "TEMPS!";
        // Directamente enviar resultados al agotarse el tiempo
        // Deshabilitar botones para evitar interacciones mientras se envía
        document.querySelectorAll('button').forEach(b => b.disabled = true);
        enviarResultats();
      }
    }, 1000);

    function formatTime(s){ const m=Math.floor(s/60); const sec=s%60; return `${m}:${sec.toString().padStart(2,'0')}`; }
  }

  // Mostrar pantalla final con resultados
  function showEndScreen() {
    clearInterval(timer); 

  // Enviar resultados al servidor
  function enviarResultats() {
    if (sending) return;
    sending = true;
    clearInterval(timer);

    const payloadArray = data.preguntes.map((p, idx) => ({ pregunta_id: p.id, resposta_id: answers[idx] ?? null }))
      .filter(r => r.resposta_id !== null);

      // Deshabilitar botones para evitar interacciones mientras se envía
    fetch('finalitza.php', {
      method: 'POST',
      headers: {'Content-Type':'application/json'},
      body: JSON.stringify({ respostes: payloadArray })
    })
    .then(r => r.json())
    .then(result => {
      const totalRecibido = Number(result.total) || 0;
      const correctes = Number(result.correctes) || 0;
      container.innerHTML = `
        <h2>Fi del joc!</h2>
        <p>Has contestat ${totalRecibido} preguntes.</p>
        <p>Respostes correctes: ${correctes}</p>
        <button id="reiniciar" class="botons-navegacio">Reiniciar</button>
      `;
      document.getElementById('reiniciar').addEventListener('click', () => {
        current = 0; answers = new Array(data.preguntes.length).fill(null); sending = false;
        startTimer(); showQuestion();
      });
      btnEnviar.classList.add('hidden');
    })
    .catch(err => {
      console.error(err);
      sending = false;
      alert('Error enviant resultats');
    });
  }

  // Escapar HTML para evitar XSS
  function escapeHtml(str) {
    if (!str && str !== 0) return '';
    return String(str).replace(/[&<>"']/g, s => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'})[s]);
  }
  }
});
