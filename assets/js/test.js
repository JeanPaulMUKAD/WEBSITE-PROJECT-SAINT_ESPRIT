let a = 0;
window.addEventListener('load',()=>{
    a = 1;
    setTimeout(() => {
        clearInterval(anime);
        masque.style.opacity = 0;
    }, 1000);
    setTimeout(() => {
        masque.style.visibility = 'hidden';
    }, 1500);
});
let masque = document.createElement('div');
masque.style.width = 100 +'%';
masque.style.height = 100 +'vh';
masque.style.zIndex = 100000;
masque.style.background = '#360c0c';
masque.style.position = 'fixed';
masque.style.top = 0;
masque.style.left = 0;
masque.style.opacity = 100+'%';
masque.style.transition = 0.5+'s';
masque.style.display = 'flex';
masque.style.justifyContent = 'center';
masque.style.alignItems = 'center';
document.body.appendChild(masque);

let cube = document.createElement('img');
cube.setAttribute('src','assets/images/logo/i.png')
cube.style.width = 20 +'vh';
cube.style.height = 20 +'vh';
//cube.style.background = '#00c8be';
//cube.style.position = 'absolute';
//cube.style.left = '50%';
//cube.style.top = '30vh';
cube.style.transition = 0.5+'s';
cube.style.display = 'flex';
let x = 0;
let anime = setInterval(() => {
    cube.style.transform = `rotate(${x}deg)`;
    x = x - 10;
}, 20);

masque.appendChild(cube);

