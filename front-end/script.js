let data = null;                  // { preguntes: [ {id, pregunta, imatge, respostes:[{id,text,imatge}]} ] }
let current = 0;                  // índice de la pregunta actual
let answers = [];                 // answers[indexPregunta] = idRespuesta
let timer = null;                 // temporizador
let sending = false;              // evitar envíos múltiples

const API = "/projtransv0-AdrianMaciel/back-end/src/getPreguntes.php"; // API preguntas

// Utilidad: escapar HTML (scope global)
function escapeHtml(str) {
  if (!str && str !== 0) return "";
  return String(str).replace(/[&<>"']/g, s =>
    ({ "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;" }[s])
  );
}

window.addEventListener("DOMContentLoaded", () => {
  const container = document.getElementById("questionari"); // contenedor principal
  const preguntaCounter = document.getElementById("contador-pregunta"); // contador preguntas
  const tiempoCounter = document.getElementById("contador-tiempo"); // contador tiempo

  // Verificar contenedor
  if (!container) {
    console.error("No se encontró #questionari");
    return;
  }

  // Mostrar nombre de usuario guardado
  const userNameEl = document.getElementById("user-name");
  const savedName = localStorage.getItem("username");
  if (savedName && userNameEl) userNameEl.textContent = savedName;

  // Intro
  const introDiv = document.createElement("div");
  introDiv.id = "intro-screen";
  introDiv.innerHTML = `
    <h2>Benvingut al joc!</h2>
    <p>Introdueix el teu nom per començar:</p>

    <div class="input-group">
      <input type="text" id="nom-usuari" class="text-input" placeholder="El teu nom…" autocomplete="name" />
      <button id="clear-name" class="btn-ghost" type="button" aria-label="Esborrar nom">✕</button>
      <button id="començar-joc" class="btn btn-primary" type="button">Començar</button>
    </div>
    <small class="meta">Pots canviar-lo o esborrar-lo abans de començar.</small>
  `;
  container.innerHTML = "";
  container.appendChild(introDiv);

  // referencias y precarga
  const inputNom = introDiv.querySelector("#nom-usuari");
  const btnClear = introDiv.querySelector("#clear-name");
  const btnStart = introDiv.querySelector("#començar-joc");

  // habilitar/deshabilitar y mostrar/ocultar “borrar”
  function syncNameUI(){
    const hasText = inputNom.value.trim().length > 0;
    btnClear.style.visibility = hasText ? "visible" : "hidden";
    btnStart.disabled = !hasText;
  }
  inputNom.addEventListener("input", syncNameUI);
  syncNameUI();

  // borrar nombre
  btnClear.addEventListener("click", () => {
    inputNom.value = "";
    localStorage.removeItem("username");
    if (userNameEl) userNameEl.textContent = "convidat";
    syncNameUI();
    inputNom.focus();
  });

  // comenzar
  btnStart.addEventListener("click", () => {
    const nom = inputNom.value.trim();
    if (!nom) return; // por si acaso

    const nomClean = nom.slice(0, 28);
    localStorage.setItem("username", nomClean);
    if (userNameEl) userNameEl.textContent = nomClean;

    container.innerHTML = `<p>Hola ${escapeHtml(nomClean)}! Preparant el qüestionari...</p>`;
    setTimeout(iniciarQuestionari, 800);
  });

  // Comenzar juego
  document.getElementById("començar-joc").addEventListener("click", () => {
    const nom = document.getElementById("nom-usuari").value.trim();
    if (!nom) {
      alert("Si us plau, introdueix el teu nom!");
      return;
    }

    // Guardar nombre (máx 28 chars)
    const nomClean = nom.slice(0, 28);
    localStorage.setItem("username", nomClean);
    if (userNameEl) userNameEl.textContent = nomClean;

    // Iniciar cuestionario
    container.innerHTML = `<p>Hola ${escapeHtml(nomClean)}! Preparant el qüestionari...</p>`;
    setTimeout(iniciarQuestionari, 800);
  });

  // Iniciar el cuestionario
  function iniciarQuestionari() {
    let marcadorDiv = document.getElementById("marcador");
    if (!marcadorDiv) {
      marcadorDiv = document.createElement("div");
      marcadorDiv.id = "marcador";
      if (container.parentNode)
        container.parentNode.insertBefore(marcadorDiv, container.nextSibling);
    }

    // Botón enviar resultados
    let btnEnviar = document.getElementById("enviar-resultats");
    if (!btnEnviar) {
      btnEnviar = document.createElement("button");
      btnEnviar.id = "enviar-resultats";
      btnEnviar.textContent = "Enviar Resultats";
      btnEnviar.classList.add("hidden");
      if (container.parentNode)
        container.parentNode.insertBefore(btnEnviar, marcadorDiv.nextSibling);
    }
    btnEnviar.addEventListener("click", enviarResultats);

    // Cargar preguntas
    fetch(API, { credentials: "include" })
      .then(r => { if (!r.ok) throw new Error(`HTTP ${r.status}`); return r.json(); })
      .then(json => {
        data = { preguntes: Array.isArray(json) ? json : (json?.preguntes || []) };
        if (!data.preguntes.length) {
          container.textContent = "No hi ha preguntes.";
          return;
        }

        // Mezclar opciones
        data.preguntes.forEach(p => {
          if (Array.isArray(p.respostes))
            p.respostes.sort(() => Math.random() - 0.5);
        });

        // Estado
        answers = new Array(data.preguntes.length).fill(null);
        startTimer();
        showQuestion();
        renderitzarMarcador();
      })
      .catch(err => {
        container.textContent = "Error carregant preguntes.";
        console.error("Error carregant preguntes:", err);
      });

      // Mostrar pregunta actual
    function showQuestion() {
      try {
        if (!data || !Array.isArray(data.preguntes)) return;
        const preguntes = data.preguntes;
        if (current < 0) current = 0;

        // Fin del cuestionario
        if (current >= preguntes.length) {
          container.innerHTML = `<h2>Has arribat al final.</h2>`;
          showEndScreen();
          renderitzarMarcador();
          if (preguntes.length === answers.filter(a => a !== null).length)
            btnEnviar.classList.remove("hidden");
          return;
        }

        // Renderizar pregunta
        const p = preguntes[current];
        const respostes = Array.isArray(p.respostes) ? p.respostes : [];

        let html = `<h2>${current + 1}. ${escapeHtml(p.pregunta)}</h2>`;
        if (p.imatge) {
          html += `<img src="${escapeHtml(p.imatge)}" alt="" style="max-width:200px; display:block; margin:10px auto;">`;
        }

        html += `<div class="resposta-container">`;
        respostes.forEach(opt => {
          const selected = answers[current] === opt.id ? " seleccionada" : "";
          html += `
            <button class="resposta${selected}" data-id="${opt.id}">
              ${opt.imatge ? `<img src="${escapeHtml(opt.imatge)}" alt="">` : ""} ${escapeHtml(opt.text || "")}
            </button>
          `;
        });
        html += `</div>`;

        html += `
          <div>
            <button class="botons-navegacio" id="enrere" ${current === 0 ? "disabled" : ""}>Anterior</button>
            <button class="botons-navegacio" id="seguent">Següent</button>
          </div>
          <h3>Pregunta ${current + 1} de ${preguntes.length}</h3>
        `;

        container.innerHTML = html;

        if (preguntaCounter)
          preguntaCounter.textContent = `${current + 1} / ${preguntes.length}`;

        // Eventos respuestas
        const respostaButtons = container.querySelectorAll(".resposta");
        respostaButtons.forEach(btn => {
          btn.addEventListener("click", () => {
            const id = parseInt(btn.dataset.id, 10);
            answers[current] = Number.isNaN(id) ? null : id;
            respostaButtons.forEach(b => b.classList.remove("seleccionada"));
            btn.classList.add("seleccionada");
            if (answers.filter(a => a !== null).length === data.preguntes.length) {
              btnEnviar.classList.remove("hidden");
            }
            renderitzarMarcador();
          });
        });

        // Navegación
        const enrereBtn = document.getElementById("enrere");
        if (enrereBtn) enrereBtn.addEventListener("click", () => { current--; showQuestion(); });

        const seguentBtn = document.getElementById("seguent");
        if (seguentBtn) seguentBtn.addEventListener("click", () => { current++; showQuestion(); });

        renderitzarMarcador();
        if (answers.filter(a => a !== null).length < data.preguntes.length)
          btnEnviar.classList.add("hidden");

      } catch (e) {
        console.error("Error en showQuestion:", e);
        container.innerHTML = `<p>Error renderitzant la pregunta.</p>`;
      }
    }

    // Renderizar marcador
    function renderitzarMarcador() {
      const total = data?.preguntes?.length || 0;
      const contestades = answers.filter(a => a !== null).length;
      const marcadorDiv = document.getElementById("marcador");
      if (marcadorDiv)
        marcadorDiv.textContent = `Preguntes respostes: ${contestades} de ${total}`;
    }

    // Temporizador
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
          document.querySelectorAll("button").forEach(b => (b.disabled = true));
          enviarResultats();
        }
      }, 1000);

      // Formatear tiempo mm:ss
      function formatTime(s) {
        const m = Math.floor(s / 60);
        const sec = s % 60;
        return `${m}:${sec.toString().padStart(2, "0")}`;
      }
    }

    // Pantalla final
    function showEndScreen() {
      clearInterval(timer);
      container.innerHTML = `
        <h2>Fi del qüestionari!</h2>
        <p>Pots enviar les respostes per veure el resultat.</p>
      `;
      document.getElementById("enviar-resultats")?.classList.remove("hidden");
    }

    // Enviar resultados
    function enviarResultats() {
      if (sending) return;
      sending = true;
      clearInterval(timer);

      // Preparar payload
      const payloadArray = data.preguntes.map((p, idx) => ({
        pregunta_id: p.id,
        resposta_id: answers[idx] ?? null,
      }));

      // Deshabilitar botones
      fetch(API, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        credentials: "include",
        body: JSON.stringify({ respostes: payloadArray }),
      })
        .then(r => { if (!r.ok) throw new Error(`HTTP ${r.status}`); return r.json(); })
        .then(result => {
          const correctes = Number(result.correctes) || 0;
          const totalRecibido = Number(result.total) || 0;

          container.innerHTML = `
            <h2>Fi del joc!</h2>
            <p>Has contestat ${totalRecibido} preguntes.</p>
            <p>Respostes correctes: ${correctes}</p>
            <button id="reiniciar" class="botons-navegacio">Reiniciar</button>
          `;

          // Reiniciar juego
          document.getElementById("reiniciar")?.addEventListener("click", () => {
            current = 0;
            answers = new Array(data.preguntes.length).fill(null);
            sending = false;
            document.querySelectorAll("button").forEach(b => (b.disabled = false));
            startTimer();
            showQuestion();
          });

          document.getElementById("enviar-resultats")?.classList.add("hidden");
        })
        .catch(err => {
          console.error("Error enviant resultats:", err);
          sending = false;
          alert("Error enviant resultats");
          document.querySelectorAll("button").forEach(b => (b.disabled = false));
        });
    }
  }
});
