let data = null;
let current = 0;

window.addEventListener('DOMContentLoaded', () => {
  const container = document.getElementById('questionari');
  const result = document.getElementById('contador'); // opcional: muestra número de pregunta
  
  const nEl = document.getElementById('num');
  const n = nEl ? Math.max(1, parseInt(nEl.value, 10) || 1) : 5;

  // 1) Cargar preguntas desde el servidor
  fetch(`./getPreguntes.php?n=${n}`)
    .then(r => {
      if (!r.ok) throw new Error('Error al cargar preguntas');
      return r.json();
    })
    .then(json => {
      // El PHP devuelve un ARRAY de preguntes. Normalizamos a data.preguntes
      data = { preguntes: Array.isArray(json) ? json : (json.preguntes || []) };

      if (!data.preguntes || data.preguntes.length === 0) {
        container.textContent = 'No hay preguntas.';
        return;
      }
      showQuestion(); // tu función que renderiza una pregunta
    })
    .catch(err => {
      container.textContent = 'Error cargando preguntas.';
      console.error(err);
    });

  // 2) Mostrar la pregunta actual
  function showQuestion() {
    const preguntas = data.preguntes;
    if (current < 0) current = 0;
    if (current >= preguntas.length) {
      container.innerHTML = '<h2>Fin del cuestionario</h2>';
      return;
    }

    const p = preguntas[current];
    // título
    let html = `<h2>${current + 1}. ${p.pregunta}</h2>`;

    // opciones (radio buttons para escoger una)
    p.respostes.forEach((opt, i) => {
      html += `
        <label style="display:block; margin:6px 0;">
          <input type="radio" name="q" value="${i}"> ${opt.text || opt}
        </label>
      `;
    });

    // controles sencillo: anterior / siguiente
    html += `
      <div style="margin-top:10px;">
        <button id="prev">Anterior</button>
        <button id="next">Siguiente</button>
      </div>
    `;

    container.innerHTML = html;
    if (result) result.textContent = `${current + 1} / ${preguntas.length}`;

    // 3) handlers de botones
    document.getElementById('prev').addEventListener('click', () => {
      if (current > 0) current--;
      showQuestion();
    });
    document.getElementById('next').addEventListener('click', () => {
      // aquí podrías leer la respuesta seleccionada antes de avanzar
      current++;
      showQuestion();
    });
  }
});
