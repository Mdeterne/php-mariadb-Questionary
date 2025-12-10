// Configuration des données simulées
const questionsData = [
    { id: 1, label: "Question 1 :", response: "Reponse 1" },
    { id: 2, label: "Question 2 :", response: "Reponse 1" },
    { id: 3, label: "Question 3 :", response: "Reponse 1" },
    { id: 4, label: "Question 4 :", response: "Reponse 1" },
    { id: 5, label: "Question 5 :", response: "Reponse 1" },
    { id: 6, label: "Question 6 :", response: "Reponse 1" }
];

const container = document.getElementById('cards-wrapper');

// 1. Génération du HTML pour chaque carte
questionsData.forEach(q => {
    const cardHTML = `
        <div class="card">
            <div class="input-group">${q.label}</div>
            <div class="response-box">Réponse retenue : ${q.response}</div>
            <div class="chart-container">
                <canvas id="chart-${q.id}"></canvas>
            </div>
        </div>
    `;
    container.innerHTML += cardHTML;
});

// 2. Initialisation des graphiques
questionsData.forEach(q => {
    const ctx = document.getElementById(`chart-${q.id}`).getContext('2d');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Reponse 1', 'Reponse 2', 'Reponse 3', 'Reponse 4'],
            datasets: [{
                label: 'Élèves',
                // Couleurs approximatives de l'image
                backgroundColor: [
                    'rgba(155, 89, 182, 0.8)', // Violet/Magenta
                    'rgba(41, 128, 185, 0.8)', // Bleu
                    'rgba(230, 126, 34, 0.8)', // Orange
                    'rgba(39, 174, 96, 0.8)'   // Vert
                ],
                // Données factices pour l'exemple
                data: [2, 1, 1, 0.8],
                barThickness: 20
            }]
        },
        options: {
            indexAxis: 'y', // Graphique horizontal
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                title: {
                    display: true,
                    text: 'Titre du graphique',
                    font: { size: 14, weight: 'normal' },
                    color: '#666'
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    max: 2.5,
                    ticks: { stepSize: 0.5 }
                },
                y: {
                    grid: { display: false }
                }
            }
        }
    });
});