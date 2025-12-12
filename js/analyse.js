// Custom tooltip positioner - DISABLED to prevent crash with Chart.js v4+
// if (Chart && Chart.Tooltip) {
//    Chart.Tooltip.positioners.cursor = function (elements, eventPosition) {
//        return {
//            x: eventPosition.x,
//            y: eventPosition.y
//        };
//    };
// }


// Configuration des données
const questionsData = window.surveyData || [];
const container = document.getElementById('cards-wrapper');

// ---------------------------------------------------------
// Helper: Compute Frequency of exact strings (for Short Text)
// ---------------------------------------------------------
function computeFrequency(answers) {
    const counts = {};
    answers.forEach(a => {
        const val = a.trim();
        if (val) counts[val] = (counts[val] || 0) + 1;
    });
    // Convert to sorted array
    return Object.entries(counts)
        .map(([label, count]) => ({ label, count }))
        .sort((a, b) => b.count - a.count)
        .slice(0, 5); // Top 5
}

// ---------------------------------------------------------
// Helper: Compute Word Frequency (for Long Text)
// ---------------------------------------------------------
/* computeWordFrequency moved below */

// ---------------------------------------------------------
// 1. Génération du HTML
// ---------------------------------------------------------
questionsData.forEach(q => {
    let contentHTML = '';

    const isScale = q.type === 'scale' || q.type === 'Jauge';
    const isChoice = !isScale && (['single_choice', 'multiple_choice'].includes(q.type) || ['Cases à cocher', 'Choix multiples'].includes(q.type) || (q.options && q.options.length > 0));
    const isShortText = ['short_text', 'Réponse courte'].includes(q.type);
    const isLongText = ['paragraph', 'long_text', 'text', 'Paragraphe'].includes(q.type);

    if (isScale) {
        // Calculate Average
        let totalScore = 0;
        let totalVotes = 0;
        if (q.stats) {
            q.stats.forEach(s => {
                const val = parseInt(s.label);
                if (!isNaN(val)) {
                    totalScore += val * s.count;
                    totalVotes += s.count;
                }
            });
        }

        let avgDisplay = "N/A";
        if (totalVotes > 0) {
            const rawAvg = totalScore / totalVotes;
            // Round to nearest 0.5
            const rounded = (Math.round(rawAvg * 2) / 2).toFixed(1).replace('.0', '');
            avgDisplay = `${rounded} / 5`;
        }

        contentHTML = `
            <div style="text-align:center; margin-bottom:20px;">
                <span style="font-size:32px; font-weight:800; color:#b52424;">${avgDisplay}</span>
                <div style="font-size:13px; color:#666; margin-top:5px; font-weight:500;">Note moyenne</div>
            </div>
            <div class="chart-container">
                <canvas id="chart-${q.id}"></canvas>
            </div>`;
    }
    else if (isChoice) {
        contentHTML = `
            <div class="chart-container">
                <canvas id="chart-${q.id}"></canvas>
            </div>`;
    }
    else if (isShortText) {
        // Hybrid: Chart + List
        contentHTML = `
            <div class="chart-container" style="height:200px; margin-bottom:15px;">
                <canvas id="chart-text-${q.id}"></canvas>
            </div>
            <details>
                <summary style="cursor:pointer; color:#666; margin-bottom:10px;">Voir toutes les réponses</summary>
                <ul class="text-answers-list">
                    ${(q.text_answers || []).map(ans => `<li>${ans}</li>`).join('')}
                </ul>
            </details>`;
    }
    else if (isLongText) {
        // Sentiment Only + List
        contentHTML = `
            <div class="chart-container" style="height:300px; margin-bottom:20px;">
                <canvas id="chart-sentiment-${q.id}"></canvas>
            </div>
            <details>
                <summary style="cursor:pointer; color:#666; margin-bottom:10px;">Lire les réponses complètes</summary>
                <ul class="text-answers-list">
                    ${(q.text_answers || []).map(ans => `<li>${ans}</li>`).join('')}
                </ul>
            </details>`;
    }

    else {
        contentHTML = `<p class="no-answer">Type de question non pris en charge.</p>`;
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


// ---------------------------------------------------------
// 2. Initialisation des graphiques
// ---------------------------------------------------------
function renderChart(canvasId, labels, dataValues, colorBase, labelName) {
    const canvas = document.getElementById(canvasId);
    if (!canvas) return;

    const ctx = canvas.getContext('2d');
    // Brand colors (Université de Limoges Red: #b52424)
    // Monochromatic palette based on #b52424
    const brandPalette = [
        'rgba(181, 36, 36, 0.8)',   // Base Red
        'rgba(181, 36, 36, 0.65)',  // Lighter
        'rgba(181, 36, 36, 0.5)',   // Soft
        'rgba(181, 36, 36, 0.35)',  // Very Soft
        'rgba(181, 36, 36, 0.2)',   // Pale
        'rgba(181, 36, 36, 0.9)',   // Base Red
        'rgba(181, 36, 36, 0.75)',
        'rgba(181, 36, 36, 0.6)',
        'rgba(181, 36, 36, 0.45)',
        'rgba(181, 36, 36, 0.3)',
        'rgba(140, 30, 30, 0.9)'    // Darker accent
    ];

    // If a specific color is passed, use it (as single array), else use palette loop
    let backgroundColors;
    if (colorBase) {
        backgroundColors = colorBase;
    } else {
        backgroundColors = dataValues.map((_, i) => brandPalette[i % brandPalette.length]);
    }

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: labelName,
                backgroundColor: backgroundColors,
                borderRadius: 4,
                data: dataValues,
                barThickness: 30,
                maxBarThickness: 50
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            layout: {
                padding: 0
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#252525', // Dark Grey from variables
                    padding: 12,
                    cornerRadius: 8,
                    titleFont: { family: "'Inter', sans-serif", size: 13 },
                    bodyFont: { family: "'Inter', sans-serif", size: 13 }
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: { stepSize: 1, font: { family: "'Inter', sans-serif" } },
                    grid: { color: '#f1f5f9' },
                    border: { display: false }
                },
                y: {
                    grid: { display: false },
                    ticks: { font: { family: "'Inter', sans-serif", weight: '500' }, color: '#70757a' }, // Muted from variables
                    border: { display: false }
                }
            }
        }
    });
}

// ---------------------------------------------------------
// 2. Initialisation des graphiques (Second pass after HTML is in DOM)
// ---------------------------------------------------------
questionsData.forEach(q => {
    // A. Standard Choice Charts
    if (document.getElementById(`chart-${q.id}`)) {
        let labels = [], values = [];
        if (q.stats) {
            // Sort by count descending and take Top 5 (user request)
            const sortedStats = [...q.stats].sort((a, b) => b.count - a.count).slice(0, 5);
            sortedStats.forEach(s => { labels.push(s.label); values.push(s.count); });
        } else if (q.options) {
            q.options.forEach(o => { labels.push(o.label); values.push(0); });
        }
        renderChart(`chart-${q.id}`, labels, values, null, 'Réponses');
    }

    // B. Short Text Frequency
    const shortCanvasId = `chart-text-${q.id}`;
    if (document.getElementById(shortCanvasId) && q.text_answers) {
        const freq = computeFrequency(q.text_answers);
        const labels = freq.map(f => f.label);
        const values = freq.map(f => f.count);
        renderChart(shortCanvasId, labels, values, null, 'Occurrences');
    }

    // C. Sentiment Only (Long Text)
    const sentimentCanvasId = `chart-sentiment-${q.id}`;

    if (q.text_answers && document.getElementById(sentimentCanvasId)) {
        const sentiment = computeSentiment(q.text_answers);
        renderPieChart(sentimentCanvasId, sentiment);
    }
});

// ---------------------------------------------------------
// Helper: Compute Word Frequency (for Long Text)
// ---------------------------------------------------------
function computeWordFrequency(answers) {
    const stopWords = [
        'le', 'la', 'les', 'de', 'du', 'des', 'un', 'une', 'et', 'à', 'en', 'ce', 'pour', 'que', 'qui',
        'dans', 'sur', 'par', 'a', 'plus', 'est', 'sont', 'c\'est', 'j\'ai', 'je', 'mon',
        'ma', 'mes', 'au', 'aux', 'ne', 'se', 'ce', 'ces', 'son', 'sa', 'ses', 'vos', 'votre', 'nous',
        'vous', 'il', 'elle', 'ils', 'elles', 'on', 'mais', 'ou', 'où', 'donc', 'or', 'ni', 'car',
        'tout', 'tous', 'toute', 'toutes', 'cela', 'ça', 'comme',
        'si', 'y', 'sans', 'sous', 'vers', 'avec', 'rien', 'aucun', 'aucune',
        // Helpers re-added to stopWords so they don't appear alone, 
        // but they act as connectors in the logic below
        'très', 'trop', 'peu', 'pas', 'assez', 'bien', 'mal'
    ];

    // Words that trigger a "combo" lookahead
    const intensifiers = ['très', 'trop', 'peu', 'pas', 'assez', 'super', 'hyper', 'vraiment', 'bien', 'mal'];

    const counts = {};

    answers.forEach(text => {
        // Clean and tokenize
        const words = text.toLowerCase()
            .replace(/[.,/#!$%^&*;:{}=\-_`~()]/g, "")
            .split(/\s+/)
            .filter(w => w.length > 0);

        for (let i = 0; i < words.length; i++) {
            const w = words[i];

            // Check for Bigram (Intensifier + NextWord)
            // e.g. "très" + "bien" -> "très bien"
            if (intensifiers.includes(w) && i + 1 < words.length) {
                const nextW = words[i + 1];
                // Ensure next word is significant (longer than 2 chars)
                if (nextW.length > 2) {
                    const bigram = `${w} ${nextW}`;
                    counts[bigram] = (counts[bigram] || 0) + 1;
                    i++; // Skip next word as it's consumed in the bigram
                    continue;
                }
            }

            // Regular Single Word handling
            if (w.length > 2 && !stopWords.includes(w)) {
                counts[w] = (counts[w] || 0) + 1;
            }
        }
    });

    return Object.entries(counts)
        .map(([label, count]) => ({ label, count }))
        .sort((a, b) => b.count - a.count)
        .slice(0, 5); // Top 5
}

// ---------------------------------------------------------
// Helper: Compute Sentiment Analysis
// ---------------------------------------------------------
function computeSentiment(answers) {
    const positiveWords = ['super', 'excellent', 'adoré', 'aime', 'aimé', 'bien', 'bon', 'top', 'génial', 'parfait', 'efficace', 'rapide', 'merci', 'bravo', 'satisfait', 'cool', 'meilleur', 'facile', 'rapide', 'pro'];
    const negativeWords = ['déçu', 'nul', 'mauvais', 'horrible', 'lent', 'bug', 'problème', 'erreur', 'catastrophe', 'pire', 'jamais', 'bof', 'difficile', 'compliqué', 'cher', 'pas', 'peu'];

    let counts = { 'Positif': 0, 'Neutre': 0, 'Négatif': 0 };

    answers.forEach(text => {
        let score = 0;
        const words = text.toLowerCase()
            .replace(/[.,/#!$%^&*;:{}=\-_`~()]/g, "")
            .split(/\s+/);

        words.forEach(w => {
            if (positiveWords.includes(w)) score++;
            if (negativeWords.includes(w)) score--;
        });

        if (score > 0) counts['Positif']++;
        else if (score < 0) counts['Négatif']++;
        else counts['Neutre']++;
    });

    return counts;
}

// ---------------------------------------------------------
// Helper: Render Pie Chart
// ---------------------------------------------------------
function renderPieChart(canvasId, dataMap) {
    const canvas = document.getElementById(canvasId);
    if (!canvas) return;
    const ctx = canvas.getContext('2d');

    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: Object.keys(dataMap),
            datasets: [{
                data: Object.values(dataMap),
                backgroundColor: [
                    '#48bb78', // Green (Positif)
                    '#a0aec0', // Grey (Neutre)
                    '#f56565'  // Red (Négatif)
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'right', labels: { font: { family: "'Inter', sans-serif" } } },
                title: { display: true, text: 'Analyse de Sentiment (Bêta)', font: { size: 14 } }
            },
            layout: { padding: 20 }
        }
    });
}