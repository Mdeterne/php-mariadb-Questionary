document.addEventListener('DOMContentLoaded', () => {

    // Configuration des données
    const donneesQuestions = window.surveyData || [];


    const conteneur = document.getElementById('cards-wrapper');
    if (!conteneur) console.error("ERREUR CRITIQUE: Element 'cards-wrapper' non trouvé !");

    // Logique de gestion de la modale de réponse
    const modalOverlay = document.getElementById('text-answers-modal');
    const modalTitle = document.getElementById('modal-title-text');
    const modalList = document.getElementById('modal-text-list');
    const modalCloseBtn = document.querySelector('.modal-close-btn');

    // Exposition de la fonction pour l'accès global via HTML
    window.openTextModal = function (questionIndex) {
        const q = donneesQuestions[questionIndex];
        if (!q || !q.text_answers) {
            console.warn("Pas de données textuelles pour cette question");
            return;
        }

        if (modalTitle) modalTitle.innerText = q.label;

        const validAnswers = q.text_answers.filter(a => a && a.trim() !== "");

        if (modalList) {
            if (validAnswers.length === 0) {
                modalList.innerHTML = '<li style="text-align:center; font-style:italic;">Aucune réponse textuelle.</li>';
            } else {
                modalList.innerHTML = validAnswers.map(ans => `<li>${ans}</li>`).join('');
            }
        }

        if (modalOverlay) {
            modalOverlay.style.display = 'flex';
            // Activation de l'animation CSS via un léger délai
            setTimeout(() => {
                modalOverlay.classList.add('active');
            }, 10);
        }
    }

    function closeModal() {
        if (!modalOverlay) return;
        modalOverlay.classList.remove('active');
        setTimeout(() => {
            modalOverlay.style.display = 'none';
            if (modalList) modalList.innerHTML = '';
        }, 200);
    }

    if (modalCloseBtn) {
        modalCloseBtn.addEventListener('click', closeModal);
    }
    if (modalOverlay) {
        modalOverlay.addEventListener('click', (e) => {
            if (e.target === modalOverlay) closeModal();
        });
    }
    // Gestion de la touche Échap pour fermer la modale
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && modalOverlay && modalOverlay.classList.contains('active')) {
            closeModal();
        }
    });


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
    donneesQuestions.forEach((q, index) => {
        try {
            let contenuHTML = '';

            const estEchelle = q.type === 'scale' || q.type === 'Jauge';
            const estChoix = !estEchelle && (['single_choice', 'multiple_choice'].includes(q.type) || ['Cases à cocher', 'Choix multiples'].includes(q.type) || (q.options && q.options.length > 0));
            const estTexteCourt = ['short_text', 'Réponse courte'].includes(q.type);
            const estTexteLong = ['paragraph', 'long_text', 'text', 'Paragraphe'].includes(q.type);

            if (estEchelle) {
                // Calcul des statistiques pour les questions de type Échelle
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
                contenuHTML = `
                    <div class="chart-container">
                        <canvas id="chart-${q.id}"></canvas>
                    </div>`;
            }
            else if (estTexteCourt) {
                // Affichage du graphique et du bouton pour les réponses courtes
                const nbRep = q.text_answers ? q.text_answers.length : 0;
                contenuHTML = `
                    <div class="chart-container" style="height:200px; margin-bottom:15px;">
                        <canvas id="chart-text-${q.id}"></canvas>
                    </div>
                    <button class="btn-view-answers" onclick="openTextModal(${index})">
                        Voir toutes les réponses (${nbRep})
                    </button>`;
            }
            else if (estTexteLong) {
                // Affichage du nuage de mots et du bouton pour les réponses longues
                const nbRep = q.text_answers ? q.text_answers.length : 0;
                contenuHTML = `
                    <div class="chart-container" style="height:300px; margin-bottom:20px;">
                        <canvas id="chart-wordcloud-${q.id}" width="800" height="400"></canvas>
                    </div>
                    <button class="btn-view-answers" onclick="openTextModal(${index})">
                        Lire les réponses complètes (${nbRep})
                    </button>`;
            }
            else {
                console.warn(`Type inconnu/non géré: ${q.type}`);
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
        } catch (err) {
            console.error(`Erreur génération HTML pour question ID ${q.id}:`, err);
        }
    });

    // Injection
    if (conteneur && donneesQuestions.length > 0) {
        conteneur.innerHTML = htmlAcc;
    } else {
        console.warn("Aucune question à afficher, on laisse le message par défaut (PHP).");
    }


    // Initialisation et configuration des graphiques (Chart.js)
    function afficherGraphique(idCanvas, etiquettes, valeursDonnees, couleurBase, nomEtiquette) {
        const canvas = document.getElementById(idCanvas);
        if (!canvas) {
            console.warn(`Canvas introuvable pour afficherGraphique: ${idCanvas}`);
            return;
        }

        try {
            const ctx = canvas.getContext('2d');
            const paletteUniversite = [
                'rgba(181, 36, 36, 0.8)', 'rgba(181, 36, 36, 0.65)', 'rgba(181, 36, 36, 0.5)',
                'rgba(181, 36, 36, 0.35)', 'rgba(181, 36, 36, 0.2)'
            ];
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
                    layout: { padding: 0 },
                    plugins: {
                        legend: { display: false },
                        tooltip: { backgroundColor: '#252525', padding: 12, cornerRadius: 8 }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            grid: { color: '#f1f5f9' },
                            border: { display: false }
                        },
                        y: {
                            grid: { display: false },
                            border: { display: false }
                        }
                    }
                }
            });
        } catch (e) {
            console.error(`Erreur Chart.js sur ${idCanvas}:`, e);
        }
    }

    // Initialisation Loop
    donneesQuestions.forEach(q => {
        try {
            if (document.getElementById(`chart-${q.id}`)) {
                let etiquettes = [], valeurs = [];
                if (q.stats) {
                    const statsTriees = [...q.stats].sort((a, b) => b.count - a.count).slice(0, 5);
                    statsTriees.forEach(s => { etiquettes.push(s.label); valeurs.push(s.count); });
                } else if (q.options) {
                    q.options.forEach(o => { etiquettes.push(o.label); valeurs.push(0); });
                }
                afficherGraphique(`chart-${q.id}`, etiquettes, valeurs, null, 'Réponses');
            }

            const idCanvasCourt = `chart-text-${q.id}`;
            if (document.getElementById(idCanvasCourt) && q.text_answers) {
                const freq = calculerFrequence(q.text_answers);
                const etiquettes = freq.map(f => f.etiquette);
                const valeurs = freq.map(f => f.compte);
                afficherGraphique(idCanvasCourt, etiquettes, valeurs, null, 'Occurrences');
            }

            const idCanvasNuage = `chart-wordcloud-${q.id}`;
            const canvasNuage = document.getElementById(idCanvasNuage);
            if (q.text_answers && canvasNuage) {
                const topMots = calculerFrequenceMots(q.text_answers);
                const liste = topMots.map(m => [m.etiquette, m.compte * 10]);
                afficherNuageMots(canvasNuage, liste);
            }
        } catch (loopErr) {
            console.error(`Erreur boucle init graph pour QID ${q.id}:`, loopErr);
        }
    });

    // Utilitaires pour le calcul de fréquence et le nuage de mots
    function calculerFrequenceMots(reponses) {
        const motsVides = ['le', 'la', 'les', 'de', 'du', 'des', 'un', 'une', 'et', 'à', 'en', 'ce', 'pour', 'que', 'qui', 'dans', 'sur', 'par', 'a', 'plus', 'est', 'sont', 'c\'est', 'j\'ai', 'je', 'mon', 'ma', 'mes', 'au', 'aux', 'ne', 'se', 'ce', 'ces', 'son', 'sa', 'ses', 'vos', 'votre', 'nous', 'vous', 'il', 'elle', 'ils', 'elles', 'on', 'mais', 'ou', 'où', 'donc', 'or', 'ni', 'car', 'tout', 'tous', 'toute', 'toutes', 'cela', 'ça', 'comme', 'si', 'y', 'sans', 'sous', 'vers', 'avec', 'rien', 'aucun', 'aucune', 'très', 'trop', 'peu', 'pas', 'assez', 'bien', 'mal'];
        const compteurs = {};
        reponses.forEach(texte => {
            if (!texte) return;
            const mots = texte.toLowerCase().replace(/[.,/#!$%^&*;:{}=\-_`~()]/g, "").split(/\s+/).filter(m => m.length > 0);
            for (let i = 0; i < mots.length; i++) {
                const m = mots[i];
                if (m.length > 2 && !motsVides.includes(m)) {
                    compteurs[m] = (compteurs[m] || 0) + 1;
                }
            }
        });
        return Object.entries(compteurs).map(([etiquette, compte]) => ({ etiquette, compte })).sort((a, b) => b.compte - a.compte).slice(0, 30);
    }

    function afficherNuageMots(canvas, liste) {
        if (!liste || liste.length === 0) return;
        try {
            canvas.width = canvas.parentElement.offsetWidth || 600;
            canvas.height = 300;
            if (typeof WordCloud !== 'undefined') {
                WordCloud(canvas, {
                    list: liste,
                    gridSize: 8,
                    weightFactor: function (taille) {
                        const max = liste[0][1];
                        return (taille / max) * 60;
                    },
                    fontFamily: "'Inter', sans-serif",
                    color: function (mot, poids) {
                        const couleurs = ['#b52424', '#c93434', '#d64d4d', '#8c1e1e', '#a32020', '#555', '#666', '#777'];
                        return couleurs[Math.floor(Math.random() * couleurs.length)];
                    },
                    rotateRatio: 0,
                    backgroundColor: '#ffffff'
                });
            } else {
                console.error("WordCloud library not defined");
            }
        } catch (wcErr) {
            console.error("Erreur Nuage de mots:", wcErr);
        }
    }
});