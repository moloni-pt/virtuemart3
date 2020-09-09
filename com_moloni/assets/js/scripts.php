<script>
    function showMoloniErrors() {
        var errorConsole = document.getElementsByClassName("msgAlertaForms3");
        if (errorConsole.length > 0) {
            Array.prototype.forEach.call(errorConsole, function (element) {
                element.style['display'] = element.style['display'] === 'none' ? 'block' : 'none';
            });
        }
    }
</script>