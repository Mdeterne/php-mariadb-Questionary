// Configuration des données
const donneesQuestions = window.surveyData || [];
const conteneur = document.getElementById('cards-wrapper');

// Frequence de chaines pour texte court
function calculerFrequence(reponses) {
    const compteurs = {};
    reponses.forEach(r => {
        const val = r.trim();
        if (val) compteurs[val] = (compteurs[val] || 0) + 1;
    });
    // Convertir en tableau trié
    return Object.entries(compteurs)
        .map(([etiquette, compte]) => ({ etiquette, compte }))
        .sort((a, b) => b.compte - a.compte)
        .slice(0, 5); // Top 5
}

// Génération du HTML
let htmlAcc = '';
donneesQuestions.forEach(q => {
    let contenuHTML = '';

    const estEchelle = q.type === 'scale' || q.type === 'Jauge';
    const estChoix = !estEchelle && (['single_choice', 'multiple_choice'].includes(q.type) || ['Cases à cocher', 'Choix multiples'].includes(q.type) || (q.options && q.options.length > 0));
    const estTexteCourt = ['short_text', 'Réponse courte'].includes(q.type);
    const estTexteLong = ['paragraph', 'long_text', 'text', 'Paragraphe'].includes(q.type);

    if (estEchelle) {
        // Calcul de la moyenne
        let scoreTotal = 0;
        let votesTotaux = 0;
        if (q.stats) {
            q.stats.forEach(s => {
                const val = parseInt(s.label);
                if (!isNaN(val)) {
                    scoreTotal += val * s.count;
                    votesTotaux += s.count;
                }
            });
        }

        let affichageMoyenne = "N/A";
        if (votesTotaux > 0) {
            const moyenneBrute = scoreTotal / votesTotaux;
            // Arrondir au 0.5 le plus proche
            const arrondi = (Math.round(moyenneBrute * 2) / 2).toFixed(1).replace('.0', '');
            affichageMoyenne = `${arrondi} / 5`;
        }

        contenuHTML = `
            <div style="text-align:center; margin-bottom:20px;">
                <span style="font-size:32px; font-weight:800; color:#b52424;">${affichageMoyenne}</span>
                <div style="font-size:13px; color:#666; margin-top:5px; font-weight:500;">Note moyenne</div>
            </div>
            <div class="chart-container">
                <canvas id="chart-${q.id}"></canvas>
            </div>`;
    }
    else if (estChoix) {
        // Graphique
        contenuHTML = `
            <div class="chart-container">
                <canvas id="chart-${q.id}"></canvas>
            </div>`;
    }
    else if (estTexteCourt) {
        // Graphique + Liste
        contenuHTML = `
            <div class="chart-container" style="height:200px; margin-bottom:15px;">
                <canvas id="chart-text-${q.id}"></canvas>
            </div>
            <details>
                <summary style="cursor:pointer; color:#666; margin-bottom:10px;">Voir toutes les réponses</summary>
                <ul class="text-answers-list">
                    ${(q.text_answers || []).map(rep => `<li>${rep}</li>`).join('')}
                </ul>
            </details>`;
    }
    else if (estTexteLong) {
        // Nuage de mots + Liste
        contenuHTML = `
            <div class="chart-container" style="height:300px; margin-bottom:20px;">
                <canvas id="chart-wordcloud-${q.id}" width="800" height="400"></canvas>
            </div>
            <details>
                <summary style="cursor:pointer; color:#666; margin-bottom:10px;">Lire les réponses complètes</summary>
                <ul class="text-answers-list">
                    ${(q.text_answers || []).map(rep => `<li>${rep}</li>`).join('')}
                </ul>
            </details>`;
    }

    else {
        contenuHTML = `<p class="no-answer">Type de question non pris en charge.</p>`;
    }

    const carteHTML = `
        <div class="card">
            <div class="input-group">${q.label}</div>
            <div class="response-box-container">
                ${contenuHTML}
            </div>
        </div>
        `;
    htmlAcc += carteHTML;
});

// Injection unique dans le DOM pour éviter de casser les canvas
conteneur.innerHTML = htmlAcc;


// Initialisation des graphiques
function afficherGraphique(idCanvas, etiquettes, valeursDonnees, couleurBase, nomEtiquette) {
    const canvas = document.getElementById(idCanvas);
    if (!canvas) return;

    const ctx = canvas.getContext('2d');
    // Couleurs Université
    const paletteUniversite = [
        'rgba(181, 36, 36, 0.8)',
        'rgba(181, 36, 36, 0.65)',
        'rgba(181, 36, 36, 0.5)',
        'rgba(181, 36, 36, 0.35)',
        'rgba(181, 36, 36, 0.2)',
        'rgba(181, 36, 36, 0.9)',
        'rgba(181, 36, 36, 0.75)',
        'rgba(181, 36, 36, 0.6)',
        'rgba(181, 36, 36, 0.45)',
        'rgba(181, 36, 36, 0.3)',
        'rgba(140, 30, 30, 0.9)'
    ];

    // Si une couleur spécifique est passée, l'utiliser, sinon boucle sur la palette
    let couleursArrierePlan;
    if (couleurBase) {
        couleursArrierePlan = couleurBase;
    } else {
        couleursArrierePlan = valeursDonnees.map((_, i) => paletteUniversite[i % paletteUniversite.length]);
    }

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: etiquettes,
            datasets: [{
                label: nomEtiquette,
                backgroundColor: couleursArrierePlan,
                borderRadius: 4,
                data: valeursDonnees,
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
                    backgroundColor: '#252525',
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
                    ticks: { font: { family: "'Inter', sans-serif", weight: '500' }, color: '#70757a' }, // Atténué des variables
                    border: { display: false }
                }
            }
        }
    });
}

// Initialisation des graphiques (deuxieme étape) 
donneesQuestions.forEach(q => {
    // Graphiques de Choix Standard
    if (document.getElementById(`chart-${q.id}`)) {
        let etiquettes = [], valeurs = [];
        if (q.stats) {
            // Trier par nombre décroissant et prendre le Top 5
            const statsTriees = [...q.stats].sort((a, b) => b.count - a.count).slice(0, 5);
            statsTriees.forEach(s => { etiquettes.push(s.label); valeurs.push(s.count); });
        } else if (q.options) {
            q.options.forEach(o => { etiquettes.push(o.label); valeurs.push(0); });
        }
        afficherGraphique(`chart-${q.id}`, etiquettes, valeurs, null, 'Réponses');
    }

    // Fréquence Texte Court
    const idCanvasCourt = `chart-text-${q.id}`;
    if (document.getElementById(idCanvasCourt) && q.text_answers) {
        const freq = calculerFrequence(q.text_answers);
        const etiquettes = freq.map(f => f.etiquette);
        const valeurs = freq.map(f => f.compte);
        afficherGraphique(idCanvasCourt, etiquettes, valeurs, null, 'Occurrences');
    }

    // Nuage de Mots
    const idCanvasNuage = `chart-wordcloud-${q.id}`;
    const canvasNuage = document.getElementById(idCanvasNuage);

    if (q.text_answers && canvasNuage) {
        const topMots = calculerFrequenceMots(q.text_answers);
        const liste = topMots.map(m => [m.etiquette, m.compte * 10]);

        afficherNuageMots(canvasNuage, liste);
    }
});

// Calculer la fréquence des mots (texte long)
function calculerFrequenceMots(reponses) {
    const motsVides = [
        'le', 'la', 'les', 'de', 'du', 'des', 'un', 'une', 'et', 'à', 'en', 'ce', 'pour', 'que', 'qui',
        'dans', 'sur', 'par', 'a', 'plus', 'est', 'sont', 'c\'est', 'j\'ai', 'je', 'mon',
        'ma', 'mes', 'au', 'aux', 'ne', 'se', 'ce', 'ces', 'son', 'sa', 'ses', 'vos', 'votre', 'nous',
        'vous', 'il', 'elle', 'ils', 'elles', 'on', 'mais', 'ou', 'où', 'donc', 'or', 'ni', 'car',
        'tout', 'tous', 'toute', 'toutes', 'cela', 'ça', 'comme',
        'si', 'y', 'sans', 'sous', 'vers', 'avec', 'rien', 'aucun', 'aucune',
        'très', 'trop', 'peu', 'pas', 'assez', 'bien', 'mal'
    ];

    // Mots pour intensifier
    const intensificateurs = ['très', 'trop', 'peu', 'pas', 'assez', 'super', 'hyper', 'vraiment', 'bien', 'mal'];

    const compteurs = {};

    reponses.forEach(texte => {
        // Nettoyer et tokeniser
        const mots = texte.toLowerCase()
            .replace(/[.,/#!$%^&*;:{}=\-_`~()]/g, "")
            .split(/\s+/)
            .filter(m => m.length > 0);

        for (let i = 0; i < mots.length; i++) {
            const m = mots[i];

            // Vérifier Intensificateur + MotSuivant
            if (intensificateurs.includes(m) && i + 1 < mots.length) {
                const motSuivant = mots[i + 1];
                if (motSuivant.length > 2) {
                    const bigramme = `${m} ${motSuivant}`;
                    compteurs[bigramme] = (compteurs[bigramme] || 0) + 1;
                    i++;
                    continue;
                }
            }

            // Gestion mot classique
            if (m.length > 2 && !motsVides.includes(m)) {
                compteurs[m] = (compteurs[m] || 0) + 1;
            }
        }
    });

    // On prend le top 30 pour le nuage de mots
    return Object.entries(compteurs)
        .map(([etiquette, compte]) => ({ etiquette, compte }))
        .sort((a, b) => b.compte - a.compte)
        .slice(0, 30);
}

// Afficher le Nuage de Mots
function afficherNuageMots(canvas, liste) {
    if (!liste || liste.length === 0) return;

    // Taille du canvas
    canvas.width = canvas.parentElement.offsetWidth || 600;
    canvas.height = 300;

    WordCloud(canvas, {
        list: liste,
        gridSize: 8,
        weightFactor: function (taille) {
            // Mise à l'échelle basée sur la taille de la liste
            const max = liste[0][1];
            return (taille / max) * 60;
        },
        fontFamily: "'Inter', sans-serif",
        color: function (mot, poids) {
            // Couleurs aléatoires dans les tons de l'app
            const couleurs = [
                '#b52424', '#c93434', '#d64d4d', '#8c1e1e', '#a32020',
                '#555', '#666', '#777'
            ];
            return couleurs[Math.floor(Math.random() * couleurs.length)];
        },
        rotateRatio: 0,
        backgroundColor: '#ffffff'
    });
}

// PDF Export Logic
const btnExportPdf = document.getElementById('btn-export-pdf');
if (btnExportPdf) {
    btnExportPdf.addEventListener('click', () => {
        const element = document.querySelector('main');
        const originalBtnText = btnExportPdf.innerHTML;

        // Hide buttons during export
        const controls = document.querySelector('.top-controls');
        if (controls) controls.style.display = 'none';

        btnExportPdf.innerHTML = 'Génération...';
        btnExportPdf.disabled = true;

        const opt = {
            margin: [10, 10, 10, 10], // top, left, bottom, right
            filename: 'analyse-questionnaire.pdf',
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2, useCORS: true },
            jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
        };

        html2pdf().set(opt).from(element).save().then(() => {
            // Restore UI
            if (controls) controls.style.display = '';
            btnExportPdf.innerHTML = originalBtnText;
            btnExportPdf.disabled = false;
        }).catch(err => {
            console.error(err);
            alert("Erreur lors de la génération du PDF");
            if (controls) controls.style.display = '';
            btnExportPdf.innerHTML = originalBtnText;
            btnExportPdf.disabled = false;
        });
    });
}