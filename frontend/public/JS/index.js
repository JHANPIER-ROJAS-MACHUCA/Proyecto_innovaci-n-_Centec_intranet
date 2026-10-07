let btnmenu = document.getElementById('btnmenu');
let menu = document.getElementById('menu');
btnmenu.addEventListener('click',function(){
    'use strict';
    menu.classList.toggle('mostrar');
});


console.log(document);

function nombre(){
    document.getElementById("modal").classList.add("open");
}
function closemodal(){
    document.getElementById("modal").classList.remove("open");
}