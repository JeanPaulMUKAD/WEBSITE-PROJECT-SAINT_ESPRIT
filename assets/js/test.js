
    let a = 0;
    let masque = document.createElement('div');
    let logo = document.createElement('img');
    let cercle = document.createElement('div');

    let angle = 0;
    let scale = 1; 
    let opacityLogo = 1; 

    window.addEventListener('load', () => {
        a = 1;

        // Le cercle et le logo commencent à bouger immédiatement
        anime = setInterval(() => {
            angle += 10; // Vitesse de rotation du cercle
            cercle.style.transform = `translate(-50%, -50%) rotate(${angle}deg)`;

            // Zoom progressif du logo
            scale += 0.005; 
            opacityLogo -= 0.005; 

            logo.style.transform = `scale(${scale})`;
            logo.style.opacity = opacityLogo;

        }, 20);

        // Après 1 seconde, on arrête l'animation
        setTimeout(() => {
            clearInterval(anime);
            masque.style.opacity = '0';
        }, 1000);

        setTimeout(() => {
            masque.style.visibility = 'hidden';
        }, 1500);
    });

    // Création du masque
    masque.style.width = '100%';
    masque.style.height = '100vh';
    masque.style.zIndex = 100000;
    masque.style.background = '#ffffff';
    masque.style.position = 'fixed';
    masque.style.top = '0';
    masque.style.left = '0';
    masque.style.opacity = '1';
    masque.style.transition = '0.5s ease';
    masque.style.display = 'flex';
    masque.style.justifyContent = 'center';
    masque.style.alignItems = 'center';
    document.body.appendChild(masque);

    // Création du logo
    logo.setAttribute('src', 'assets/images/logo/Logo1.jpg');
    logo.style.width = '10vh';
    logo.style.height = '10vh';
    logo.style.position = 'relative';
    logo.style.zIndex = '2';
    logo.style.transition = '0.2s'; // Transition pour plus de fluidité
    masque.appendChild(logo);

    // Création du cercle autour du logo
    cercle.style.width = '15vh';
    cercle.style.height = '15vh';
    cercle.style.border = '3px solid red';
    cercle.style.borderTop = '3px solid #000000';
    cercle.style.borderRadius = '50%';
    cercle.style.position = 'absolute';
    cercle.style.top = '50%';
    cercle.style.left = '50%';
    cercle.style.transform = 'translate(-50%, -50%)'; 
    cercle.style.boxSizing = 'border-box';
    cercle.style.zIndex = '1';
    masque.appendChild(cercle);

    // Variables de l'animation
    let anime;

