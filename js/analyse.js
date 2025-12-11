// Custom tooltip positioner to follow mouse
Chart.Tooltip.positioners.cursor = function (elements, eventPosition) {
    return {
        x: eventPosition.x,
        y: eventPosition.y
    };
};

// Configuration des données
const questionsData = window.surveyData || [];
const container = document.getElementById('cards-wrapper');

// 1. Génération du HTML pour chaque carte
questionsData.forEach(q => {
    let contentHTML = '';

    // Determine type : Choice (Chart) or Text (List)
    const isChoice = ['single_choice', 'multiple_choice', 'scale'].includes(q.type) || ['Cases à cocher', 'Choix multiples', 'Jauge'].includes(q.type) || (q.options && q.options.length > 0);
    const isText = ['text', 'paragraph', 'short_text', 'long_text'].includes(q.type) || ['Réponse courte', 'Paragraphe'].includes(q.type);

    if (isChoice) {
        contentHTML = `
            <div class="chart-container">
                <canvas id="chart-${q.id}"></canvas>
            </div>`;
    } else if (isText) {
        if (q.text_answers && q.text_answers.length > 0) {
            contentHTML = `<ul class="text-answers-list">`;
            q.text_answers.forEach(ans => {
                contentHTML += `<li>${ans}</li>`;
            });
            contentHTML += `</ul>`;
        } else {
            contentHTML = `<p class="no-answer">Aucune réponse textuelle.</p>`;
        }
    } else {
        // Fallback/Jauge
        contentHTML = `<p class="no-answer">Type de question non pris en charge pour l'analyse graphique.</p>`;
    }

    const cardHTML = `
        <div class="card">
            <div class="input-group">${q.label}</div>
            <div class="response-box-container">
                ${contentHTML}
            </div>
        </div>
    `;
    container.innerHTML += cardHTML;
});

// 2. Initialisation des graphiques (Uniquement pour les questions à choix)
questionsData.forEach(q => {
    // Check if chart canvas exists
    const canvas = document.getElementById(`chart-${q.id}`);
    if (canvas) {
        const ctx = canvas.getContext('2d');

        // Prepare data from stats
        const labels = [];
        const dataValues = [];

        if (q.stats && q.stats.length > 0) {
            q.stats.forEach(stat => {
                labels.push(stat.label);
                dataValues.push(stat.count);
            });
        } else if (q.options && q.options.length > 0) {
            // Options exist but no answers yet
            q.options.forEach(opt => {
                labels.push(opt.label);
                dataValues.push(0);
            });
        }

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Réponses',
                    backgroundColor: [
                        'rgba(155, 89, 182, 0.8)',
                        'rgba(41, 128, 185, 0.8)',
                        'rgba(230, 126, 34, 0.8)',
                        'rgba(39, 174, 96, 0.8)',
                        'rgba(231, 76, 60, 0.8)',
                        'rgba(52, 73, 94, 0.8)'
                    ],
                    data: dataValues,
                    barThickness: 20
                }]
            },
            options: {
                indexAxis: 'y', // Graphique horizontal
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    title: { display: false },
                    tooltip: {
                        position: 'cursor',
                        backgroundColor: 'rgba(44, 62, 80, 0.9)', // Deep elegant blue
                        titleColor: '#ffffff',
                        bodyColor: '#ecf0f1',
                        titleFont: {
                            size: 14,
                            family: "'Helvetica Neue', 'Helvetica', 'Arial', sans-serif",
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 13,
                            family: "'Helvetica Neue', 'Helvetica', 'Arial', sans-serif"
                        },
                        padding: 12,
                        cornerRadius: 8,
                        displayColors: true,
                        boxPadding: 6, // Add space between color box and text
                        callbacks: {
                            label: function (context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.x !== null) {
                                    label += context.parsed.x + ' vote' + (context.parsed.x > 1 ? 's' : '');
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
                    },
                    y: {
                        grid: { display: false }
                    }
                }
            }
        });
    }
});