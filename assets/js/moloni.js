/**
 *  Script para alterar o estilo de mostrar um elemento. Nesta caso específico , este script foi criado para quando
 *  clicar num link mostrar uma <div> que esta inicialmente escondida, ou seja, alterar o  valor da div com
 *  id="showMoloniConsoleLogError" para "block" quando inicial mente o valor do display é none.
 *
 */
function showMoloniErrors() {
    let x = document.getElementById("showMoloniConsoleLogError");
    if (x.style.display === "none") {
        x.style.display = "block";
    } else {
        x.style.display = "none";
    }
}