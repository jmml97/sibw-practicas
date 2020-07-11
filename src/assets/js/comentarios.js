function abrePanel() {
  document.getElementById("comentarios-panel-lateral").style.width = "400px";
}

function cierraPanel() {
  document.getElementById("comentarios-panel-lateral").style.width = "0";
}


// Obtenemos las palabras prohibidas de la lista oculta
var prohibidas_ul = document.getElementById("prohibidas").childNodes;
var prohibidas = [];

for(var i = 0; i < prohibidas_ul.length; i++) {
    var palabra = prohibidas_ul[i].innerHTML;
    prohibidas.push(palabra);
}

function censurar_comentario() {
  var comentario = document.getElementById('comentario-comentario').value;

  var comentario_censurado = censurar(comentario, prohibidas);

  document.getElementById('comentario-comentario').value = comentario_censurado;
  
  return true;
};

// http://jsfiddle.net/raam86/QZW7K/2/
function censurar(string, filters) {
    // "i" is to ignore case and "g" for global
    var regex = new RegExp(filters.join("|"), "gi");
    console.log(regex);
    return string.replace(regex, function (match) {
        //replace each letter with a star
        var stars = '';
        for (var i = 0; i < match.length; i++) {
            stars += '*';
        }
        return stars;
    });

}