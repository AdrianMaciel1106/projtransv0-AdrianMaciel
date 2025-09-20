let data = null;
let current = 0;
const userAnswers = [];

window.addEventListener('DOMContentLoaded', () => {
  const container = document.getElementById('questionari');
  const result = document.getElementById('contador');

  fetch('getPreguntes.php')
    .then(r => {
      if (!r.ok) throw new Error('Error al cargar preguntas');
      return r.json();
    })
    .then(json => {
      data = { preguntes: Array.isArray(json) ? json : (json.preguntes || []) };
      if (!data.preguntes || data.preguntes.length === 0) {
        container.textContent = 'No hay preguntas.';
        return;
      }
      showQuestion();
    })
    .catch(err => {
      container.textContent = 'Error cargando preguntas.';
      console.error(err);
    });

  function showQuestion() {
    const preguntas = data.preguntes;
    if (current >= preguntas.length) {
      submitAnswers();
      return;
    }

    const p = preguntas[current];
    let html = `<h2>${current + 1}. ${p.pregunta}</h2>`;

    p.respostes.forEach((opt, i) => {
      const checked = (userAnswers[current] === i) ? 'checked' : '';
      html += `
        <label style="display:block; margin:6px 0;">
          <input type="radio" name="q" value="${i}" ${checked}> ${opt.text}
        </label>
      `;
    });

    html += `
      <div style="margin-top:10px;">
        <button id="prev">Anterior</button>
        <button id="next">Siguiente</button>
      </div>
    `;

    container.innerHTML = html;
    result.textContent = `${current + 1} / ${preguntas.length}`;

    document.getElementById('prev').addEventListener('click', () => {
      saveCurrentAnswer();
      if (current > 0) current--;
      showQuestion();
    });
    document.getElementById('next').addEventListener('click', () => {
      saveCurrentAnswer();
      current++;
      showQuestion();
    });
  }

  function saveCurrentAnswer() {
    const sel = document.querySelector('input[name="q"]:checked');
    userAnswers[current] = sel ? parseInt(sel.value, 10) : -1;
  }

  function submitAnswers() {
    const body = {
      answers: data.preguntes.map((p, idx) => ({
        id: p.id,
        chosen: userAnswers[idx] ?? -1
      }))
    };

    fetch('finalitza.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(body)
    })
      .then(r => r.json())
      .then(res => {
        container.innerHTML = `<h2>Resultats</h2>
                               <p><strong>Total:</strong> ${res.total} — <strong>Correctes:</strong> ${res.correctes}</p>`;
        result.textContent = '';
      });
  }
});

