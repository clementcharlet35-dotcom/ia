document.addEventListener('DOMContentLoaded', () => {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach((alert) => {
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transition = 'opacity .5s ease';
        }, 3500);
    });

    const translations = {
        en: {
            'coupe du monde 2026': 'world cup 2026',
            'Accueil': 'Home',
            'Pronostics': 'Predictions',
            'Classement': 'Leaderboard',
            'Admin': 'Admin',
            'Connexion': 'Login',
            'Inscription': 'Sign up',
            'Déconnexion': 'Logout',
            'Choisir une langue': 'Choose a language',
            'Français': 'French',
            'Anglais': 'English',
            'Espagnol': 'Spanish',
            'Mentions légales': 'Legal notice',
            '© 2026 Pronostics': '© 2026 Predictions',
            "Bienvenue sur l'application de pronostics": 'Welcome to the predictions app',
            'Inscris-toi, connecte-toi, pronostique les matchs et grimpe dans le classement.': 'Sign up, log in, predict matches and climb the leaderboard.',
            "Le système attribue 1 point par bon pronostic après validation du résultat par l'administrateur.": 'The system awards 1 point for each correct prediction after the administrator validates the result.',
            'Faire mes pronostics': 'Make my predictions',
            'Voir le classement': 'View leaderboard',
            'Compte connecté': 'Connected account',
            "Tu n'es pas encore connecté.": 'You are not logged in yet.',
            'Se connecter': 'Log in',
            'Utilisateur :': 'User:',
            'Rôle :': 'Role:',
            'Prochains matchs': 'Upcoming matches',
            'Match': 'Match',
            'Date': 'Date',
            'Description': 'Description',
            'Aucun match planifié.': 'No match scheduled.',
            'Email': 'Email',
            'Mot de passe': 'Password',
            'Créer mon compte': 'Create my account',
            "Nom d'utilisateur": 'Username',
            'Confirmation du mot de passe': 'Confirm password',
            'Faire un pronostic': 'Make a prediction',
            'Choisir un match': 'Choose a match',
            'Résultat pronostiqué': 'Predicted result',
            'Choisir': 'Choose',
            '1 = victoire équipe 1': '1 = team 1 wins',
            'N = match nul': 'N = draw',
            '2 = victoire équipe 2': '2 = team 2 wins',
            'Enregistrer': 'Save',
            'Mes pronostics': 'My predictions',
            'Aucun pronostic enregistré.': 'No prediction saved.',
            'Pronostic': 'Prediction',
            'Validé': 'Validated',
            'Points': 'Points',
            'Oui': 'Yes',
            'Non': 'No',
            '#': '#',
            'Utilisateur': 'User',
            'Rôle': 'Role',
            'Panel Admin': 'Admin Panel',
            'Résultat': 'Result',
            'Match nul': 'Draw',
            'Victoire équipe 1': 'Team 1 wins',
            'Victoire équipe 2': 'Team 2 wins',
            'Valider': 'Validate',
            'Ajouter un nouveau match': 'Add a new match',
            'Équipe 1': 'Team 1',
            'Équipe 2': 'Team 2',
            'Date et Heure': 'Date and Time',
            'Ajouter': 'Add',
            'Matchs terminés': 'Finished matches',
            'Action': 'Action',
            'Supprimer': 'Delete',
            'Coupe du monde 2026': 'World Cup 2026',
            "Informations relatives au projet, à son éditeur, à l'hébergement et à l'utilisation des données.": 'Information about the project, its publisher, hosting and data usage.',
            'Présentation du site': 'Site presentation',
            "Le présent site est un projet réalisé dans le cadre d'un exercice scolaire. Il a pour objectif de permettre aux utilisateurs de réaliser des pronostics autour de la Coupe du Monde 2026.": 'This website is a school exercise project. Its goal is to let users make predictions for the 2026 World Cup.',
            'Éditeur du site': 'Site publisher',
            'Nom : Charlet': 'Last name: Charlet',
            'Prénom : Clément': 'First name: Clément',
            'Projet : Site de pronostics sportif coupe du monde 2026': 'Project: 2026 World Cup sports prediction website',
            'Hébergement': 'Hosting',
            "Le site est hébergé sur Planethoster dans le cadre d'un projet scolaire.": 'The site is hosted on Planethoster as part of a school project.',
            'Données personnelles': 'Personal data',
            "Les données saisies (nom, email, mot de passe) sont uniquement utilisées dans le cadre du fonctionnement du site. Aucune donnée n'est vendue ou partagée avec des tiers.": 'The data entered (name, email, password) is only used for the website to work. No data is sold or shared with third parties.',
            'Cookies': 'Cookies',
            'Ce site ne dépose pas de cookies à des fins commerciales.': 'This site does not use cookies for commercial purposes.',
            'Propriété intellectuelle': 'Intellectual property',
            "L'ensemble du contenu du site (textes, design, images) est utilisé uniquement dans un cadre pédagogique.": 'All site content (text, design, images) is used only for educational purposes.',
            'Responsabilité': 'Liability',
            'Ce site est un projet fictif. Les informations affichées (équipes, scores, classements) ne sont pas réelles.': 'This site is a fictional project. The displayed information (teams, scores, rankings) is not real.',
            'Contact': 'Contact',
            'Pour toute question, vous pouvez contacter : clement.charlet35@gmail.com': 'For any question, you can contact: clement.charlet35@gmail.com',
            'Description du match': 'Match description'
        },
        es: {
            'coupe du monde 2026': 'copa del mundo 2026',
            'Accueil': 'Inicio',
            'Pronostics': 'Pronósticos',
            'Classement': 'Clasificación',
            'Admin': 'Admin',
            'Connexion': 'Conexión',
            'Inscription': 'Registro',
            'Déconnexion': 'Desconexión',
            'Choisir une langue': 'Elegir un idioma',
            'Français': 'Francés',
            'Anglais': 'Inglés',
            'Espagnol': 'Español',
            'Mentions légales': 'Aviso legal',
            '© 2026 Pronostics': '© 2026 Pronósticos',
            "Bienvenue sur l'application de pronostics": 'Bienvenido a la aplicación de pronósticos',
            'Inscris-toi, connecte-toi, pronostique les matchs et grimpe dans le classement.': 'Regístrate, inicia sesión, pronostica los partidos y sube en la clasificación.',
            "Le système attribue 1 point par bon pronostic après validation du résultat par l'administrateur.": 'El sistema otorga 1 punto por cada pronóstico correcto después de que el administrador valide el resultado.',
            'Faire mes pronostics': 'Hacer mis pronósticos',
            'Voir le classement': 'Ver la clasificación',
            'Compte connecté': 'Cuenta conectada',
            "Tu n'es pas encore connecté.": 'Todavía no has iniciado sesión.',
            'Se connecter': 'Iniciar sesión',
            'Utilisateur :': 'Usuario:',
            'Rôle :': 'Rol:',
            'Prochains matchs': 'Próximos partidos',
            'Match': 'Partido',
            'Date': 'Fecha',
            'Description': 'Descripción',
            'Aucun match planifié.': 'No hay partidos programados.',
            'Email': 'Correo electrónico',
            'Mot de passe': 'Contraseña',
            'Créer mon compte': 'Crear mi cuenta',
            "Nom d'utilisateur": 'Nombre de usuario',
            'Confirmation du mot de passe': 'Confirmación de contraseña',
            'Faire un pronostic': 'Hacer un pronóstico',
            'Choisir un match': 'Elegir un partido',
            'Résultat pronostiqué': 'Resultado pronosticado',
            'Choisir': 'Elegir',
            '1 = victoire équipe 1': '1 = gana equipo 1',
            'N = match nul': 'N = empate',
            '2 = victoire équipe 2': '2 = gana equipo 2',
            'Enregistrer': 'Guardar',
            'Mes pronostics': 'Mis pronósticos',
            'Aucun pronostic enregistré.': 'No hay pronósticos guardados.',
            'Pronostic': 'Pronóstico',
            'Validé': 'Validado',
            'Points': 'Puntos',
            'Oui': 'Sí',
            'Non': 'No',
            '#': '#',
            'Utilisateur': 'Usuario',
            'Rôle': 'Rol',
            'Panel Admin': 'Panel Admin',
            'Résultat': 'Resultado',
            'Match nul': 'Empate',
            'Victoire équipe 1': 'Victoria equipo 1',
            'Victoire équipe 2': 'Victoria equipo 2',
            'Valider': 'Validar',
            'Ajouter un nouveau match': 'Agregar un nuevo partido',
            'Équipe 1': 'Equipo 1',
            'Équipe 2': 'Equipo 2',
            'Date et Heure': 'Fecha y hora',
            'Ajouter': 'Agregar',
            'Matchs terminés': 'Partidos terminados',
            'Action': 'Acción',
            'Supprimer': 'Eliminar',
            'Coupe du monde 2026': 'Copa del mundo 2026',
            "Informations relatives au projet, à son éditeur, à l'hébergement et à l'utilisation des données.": 'Información sobre el proyecto, su editor, el alojamiento y el uso de datos.',
            'Présentation du site': 'Presentación del sitio',
            "Le présent site est un projet réalisé dans le cadre d'un exercice scolaire. Il a pour objectif de permettre aux utilisateurs de réaliser des pronostics autour de la Coupe du Monde 2026.": 'Este sitio es un proyecto realizado como ejercicio escolar. Su objetivo es permitir a los usuarios hacer pronósticos sobre la Copa del Mundo 2026.',
            'Éditeur du site': 'Editor del sitio',
            'Nom : Charlet': 'Apellido: Charlet',
            'Prénom : Clément': 'Nombre: Clément',
            'Projet : Site de pronostics sportif coupe du monde 2026': 'Proyecto: sitio de pronósticos deportivos copa del mundo 2026',
            'Hébergement': 'Alojamiento',
            "Le site est hébergé sur Planethoster dans le cadre d'un projet scolaire.": 'El sitio está alojado en Planethoster como parte de un proyecto escolar.',
            'Données personnelles': 'Datos personales',
            "Les données saisies (nom, email, mot de passe) sont uniquement utilisées dans le cadre du fonctionnement du site. Aucune donnée n'est vendue ou partagée avec des tiers.": 'Los datos introducidos (nombre, email, contraseña) solo se utilizan para el funcionamiento del sitio. Ningún dato se vende ni se comparte con terceros.',
            'Cookies': 'Cookies',
            'Ce site ne dépose pas de cookies à des fins commerciales.': 'Este sitio no utiliza cookies con fines comerciales.',
            'Propriété intellectuelle': 'Propiedad intelectual',
            "L'ensemble du contenu du site (textes, design, images) est utilisé uniquement dans un cadre pédagogique.": 'Todo el contenido del sitio (textos, diseño, imágenes) se utiliza únicamente con fines educativos.',
            'Responsabilité': 'Responsabilidad',
            'Ce site est un projet fictif. Les informations affichées (équipes, scores, classements) ne sont pas réelles.': 'Este sitio es un proyecto ficticio. La información mostrada (equipos, marcadores, clasificaciones) no es real.',
            'Contact': 'Contacto',
            'Pour toute question, vous pouvez contacter : clement.charlet35@gmail.com': 'Para cualquier pregunta, puedes contactar: clement.charlet35@gmail.com',
            'Description du match': 'Descripción del partido'
        }
    };

    const normalize = (value) => value.replace(/\s+/g, ' ').trim();
    const normalizeCountry = (value) => value
        .normalize('NFKD')
        .replace(/[\u0300-\u036f]/g, '')
        .toLowerCase()
        .replace(/\.png$/g, '')
        .replace(/[^a-z0-9]+/g, ' ')
        .replace(/\s+/g, ' ')
        .trim();

    const countryNames = {
        algeria: { fr: 'Algérie', en: 'Algeria', es: 'Argelia' },
        allemagne: { fr: 'Allemagne', en: 'Germany', es: 'Alemania' },
        angleterre: { fr: 'Angleterre', en: 'England', es: 'Inglaterra' },
        argentina: { fr: 'Argentine', en: 'Argentina', es: 'Argentina' },
        argentine: { fr: 'Argentine', en: 'Argentina', es: 'Argentina' },
        australia: { fr: 'Australie', en: 'Australia', es: 'Australia' },
        australie: { fr: 'Australie', en: 'Australia', es: 'Australia' },
        austria: { fr: 'Autriche', en: 'Austria', es: 'Austria' },
        belgium: { fr: 'Belgique', en: 'Belgium', es: 'Bélgica' },
        belgique: { fr: 'Belgique', en: 'Belgium', es: 'Bélgica' },
        bosnia: { fr: 'Bosnie-Herzégovine', en: 'Bosnia and Herzegovina', es: 'Bosnia y Herzegovina' },
        'bosnia and herzegovina': { fr: 'Bosnie-Herzégovine', en: 'Bosnia and Herzegovina', es: 'Bosnia y Herzegovina' },
        'bosnia herzegovina': { fr: 'Bosnie-Herzégovine', en: 'Bosnia and Herzegovina', es: 'Bosnia y Herzegovina' },
        brazil: { fr: 'Brésil', en: 'Brazil', es: 'Brasil' },
        bresil: { fr: 'Brésil', en: 'Brazil', es: 'Brasil' },
        'cabo verde': { fr: 'Cap-Vert', en: 'Cabo Verde', es: 'Cabo Verde' },
        cameroon: { fr: 'Cameroun', en: 'Cameroon', es: 'Camerún' },
        cameroun: { fr: 'Cameroun', en: 'Cameroon', es: 'Camerún' },
        canada: { fr: 'Canada', en: 'Canada', es: 'Canadá' },
        colombia: { fr: 'Colombie', en: 'Colombia', es: 'Colombia' },
        colombie: { fr: 'Colombie', en: 'Colombia', es: 'Colombia' },
        'coree du sud': { fr: 'Corée du Sud', en: 'South Korea', es: 'Corea del Sur' },
        croatia: { fr: 'Croatie', en: 'Croatia', es: 'Croacia' },
        croatie: { fr: 'Croatie', en: 'Croatia', es: 'Croacia' },
        curacao: { fr: 'Curaçao', en: 'Curaçao', es: 'Curazao' },
        czechia: { fr: 'Tchéquie', en: 'Czechia', es: 'Chequia' },
        denmark: { fr: 'Danemark', en: 'Denmark', es: 'Dinamarca' },
        danemark: { fr: 'Danemark', en: 'Denmark', es: 'Dinamarca' },
        'dr congo': { fr: 'RD Congo', en: 'DR Congo', es: 'RD Congo' },
        ecuador: { fr: 'Équateur', en: 'Ecuador', es: 'Ecuador' },
        egypt: { fr: 'Égypte', en: 'Egypt', es: 'Egipto' },
        england: { fr: 'Angleterre', en: 'England', es: 'Inglaterra' },
        espagne: { fr: 'Espagne', en: 'Spain', es: 'España' },
        'etats unis': { fr: 'États-Unis', en: 'United States', es: 'Estados Unidos' },
        france: { fr: 'France', en: 'France', es: 'Francia' },
        germany: { fr: 'Allemagne', en: 'Germany', es: 'Alemania' },
        ghana: { fr: 'Ghana', en: 'Ghana', es: 'Ghana' },
        haiti: { fr: 'Haïti', en: 'Haiti', es: 'Haití' },
        iran: { fr: 'Iran', en: 'Iran', es: 'Irán' },
        iraq: { fr: 'Irak', en: 'Iraq', es: 'Irak' },
        italy: { fr: 'Italie', en: 'Italy', es: 'Italia' },
        italie: { fr: 'Italie', en: 'Italy', es: 'Italia' },
        'ivory coast': { fr: 'Côte d’Ivoire', en: 'Ivory Coast', es: 'Costa de Marfil' },
        japan: { fr: 'Japon', en: 'Japan', es: 'Japón' },
        japon: { fr: 'Japon', en: 'Japan', es: 'Japón' },
        jordan: { fr: 'Jordanie', en: 'Jordan', es: 'Jordania' },
        'korea republic': { fr: 'Corée du Sud', en: 'South Korea', es: 'Corea del Sur' },
        maroc: { fr: 'Maroc', en: 'Morocco', es: 'Marruecos' },
        mexico: { fr: 'Mexique', en: 'Mexico', es: 'México' },
        mexique: { fr: 'Mexique', en: 'Mexico', es: 'México' },
        morocco: { fr: 'Maroc', en: 'Morocco', es: 'Marruecos' },
        netherlands: { fr: 'Pays-Bas', en: 'Netherlands', es: 'Países Bajos' },
        'new zealand': { fr: 'Nouvelle-Zélande', en: 'New Zealand', es: 'Nueva Zelanda' },
        nigeria: { fr: 'Nigeria', en: 'Nigeria', es: 'Nigeria' },
        norway: { fr: 'Norvège', en: 'Norway', es: 'Noruega' },
        panama: { fr: 'Panama', en: 'Panama', es: 'Panamá' },
        paraguay: { fr: 'Paraguay', en: 'Paraguay', es: 'Paraguay' },
        'pays bas': { fr: 'Pays-Bas', en: 'Netherlands', es: 'Países Bajos' },
        poland: { fr: 'Pologne', en: 'Poland', es: 'Polonia' },
        pologne: { fr: 'Pologne', en: 'Poland', es: 'Polonia' },
        portugal: { fr: 'Portugal', en: 'Portugal', es: 'Portugal' },
        qatar: { fr: 'Qatar', en: 'Qatar', es: 'Catar' },
        'saudi arabia': { fr: 'Arabie saoudite', en: 'Saudi Arabia', es: 'Arabia Saudita' },
        scotland: { fr: 'Écosse', en: 'Scotland', es: 'Escocia' },
        senegal: { fr: 'Sénégal', en: 'Senegal', es: 'Senegal' },
        serbia: { fr: 'Serbie', en: 'Serbia', es: 'Serbia' },
        serbie: { fr: 'Serbie', en: 'Serbia', es: 'Serbia' },
        'south africa': { fr: 'Afrique du Sud', en: 'South Africa', es: 'Sudáfrica' },
        'south korea': { fr: 'Corée du Sud', en: 'South Korea', es: 'Corea del Sur' },
        spain: { fr: 'Espagne', en: 'Spain', es: 'España' },
        suisse: { fr: 'Suisse', en: 'Switzerland', es: 'Suiza' },
        sweden: { fr: 'Suède', en: 'Sweden', es: 'Suecia' },
        switzerland: { fr: 'Suisse', en: 'Switzerland', es: 'Suiza' },
        tunisia: { fr: 'Tunisie', en: 'Tunisia', es: 'Túnez' },
        tunisie: { fr: 'Tunisie', en: 'Tunisia', es: 'Túnez' },
        turkiye: { fr: 'Turquie', en: 'Turkiye', es: 'Turquía' },
        turkey: { fr: 'Turquie', en: 'Turkiye', es: 'Turquía' },
        turquie: { fr: 'Turquie', en: 'Turkiye', es: 'Turquía' },
        ukraine: { fr: 'Ukraine', en: 'Ukraine', es: 'Ucrania' },
        'united states': { fr: 'États-Unis', en: 'United States', es: 'Estados Unidos' },
        usa: { fr: 'États-Unis', en: 'United States', es: 'Estados Unidos' },
        uruguay: { fr: 'Uruguay', en: 'Uruguay', es: 'Uruguay' },
        uzbekistan: { fr: 'Ouzbékistan', en: 'Uzbekistan', es: 'Uzbekistán' }
    };

    const translateTextNodes = (lang) => {
        const walker = document.createTreeWalker(document.body, NodeFilter.SHOW_TEXT, {
            acceptNode(node) {
                const parent = node.parentElement;
                if (!parent || ['SCRIPT', 'STYLE', 'NOSCRIPT'].includes(parent.tagName)) {
                    return NodeFilter.FILTER_REJECT;
                }

                return normalize(node.nodeValue) ? NodeFilter.FILTER_ACCEPT : NodeFilter.FILTER_REJECT;
            }
        });

        const nodes = [];
        while (walker.nextNode()) {
            nodes.push(walker.currentNode);
        }

        nodes.forEach((node) => {
            if (!node.__originalText) {
                node.__originalText = node.nodeValue;
            }

            const original = normalize(node.__originalText);
            const translated = translations[lang]?.[original] ?? original;
            const leading = node.__originalText.match(/^\s*/)?.[0] ?? '';
            const trailing = node.__originalText.match(/\s*$/)?.[0] ?? '';
            node.nodeValue = lang === 'fr' ? node.__originalText : `${leading}${translated}${trailing}`;
        });
    };

    const translateAttributes = (lang) => {
        document.querySelectorAll('[placeholder], [aria-label], [title]').forEach((element) => {
            ['placeholder', 'aria-label', 'title'].forEach((attribute) => {
                if (!element.hasAttribute(attribute)) {
                    return;
                }

                const originalAttribute = `data-original-${attribute}`;
                if (!element.hasAttribute(originalAttribute)) {
                    element.setAttribute(originalAttribute, element.getAttribute(attribute));
                }

                const original = element.getAttribute(originalAttribute);
                element.setAttribute(attribute, lang === 'fr' ? original : translations[lang]?.[normalize(original)] ?? original);
            });
        });
    };

    const translateCountryNames = (lang) => {
        document.querySelectorAll('[data-country-name]').forEach((element) => {
            if (!element.dataset.originalCountryName) {
                element.dataset.originalCountryName = element.dataset.countryName || element.textContent;
            }

            const countryKey = normalizeCountry(element.dataset.countryKey || '');
            const nameKey = normalizeCountry(element.dataset.originalCountryName);
            const translated = countryNames[countryKey]?.[lang]
                ?? countryNames[nameKey]?.[lang]
                ?? element.dataset.originalCountryName;
            element.textContent = translated;
        });

        document.querySelectorAll('[data-match-option]').forEach((option) => {
            const firstKey = normalizeCountry(option.dataset.countryKey1 || '');
            const secondKey = normalizeCountry(option.dataset.countryKey2 || '');
            const firstNameKey = normalizeCountry(option.dataset.countryName1 || '');
            const secondNameKey = normalizeCountry(option.dataset.countryName2 || '');
            const firstName = countryNames[firstKey]?.[lang]
                ?? countryNames[firstNameKey]?.[lang]
                ?? option.dataset.countryName1
                ?? '';
            const secondName = countryNames[secondKey]?.[lang]
                ?? countryNames[secondNameKey]?.[lang]
                ?? option.dataset.countryName2
                ?? '';
            const matchDate = option.dataset.matchDate ? ` - ${option.dataset.matchDate}` : '';

            option.textContent = `${firstName} vs ${secondName}${matchDate}`;
        });
    };

    const applyLanguage = (lang) => {
        const safeLang = ['fr', 'en', 'es'].includes(lang) ? lang : 'fr';
        document.documentElement.lang = safeLang;
        localStorage.setItem('siteLanguage', safeLang);
        translateTextNodes(safeLang);
        translateAttributes(safeLang);
        translateCountryNames(safeLang);
    };

    const languageMenu = document.querySelector('.language-menu');
    const languageToggle = document.querySelector('.language-toggle');

    document.querySelectorAll('[data-lang]').forEach((button) => {
        button.addEventListener('click', (event) => {
            event.preventDefault();
            event.stopPropagation();
            applyLanguage(button.dataset.lang);
            languageMenu?.removeAttribute('open');
        });
    });

    applyLanguage(localStorage.getItem('siteLanguage') || 'fr');

    if (languageMenu && languageToggle) {
        languageToggle.addEventListener('click', (event) => {
            event.stopPropagation();
        });

        document.addEventListener('click', () => {
            languageMenu.classList.remove('is-open');
            languageMenu.removeAttribute('open');
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                languageMenu.classList.remove('is-open');
                languageMenu.removeAttribute('open');
            }
        });
    }
});
