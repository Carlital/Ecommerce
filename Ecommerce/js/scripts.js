function actualizarFiltros() {
    const categoria = document.querySelector('input[name="categoria"]').value;
    const genero = document.getElementById('genero').value;
    const tipoCuelloDiv = document.getElementById('tipo_cuello_div');

    if (categoria === 'camisetas') {
        tipoCuelloDiv.style.display = 'block';
    } else {
        tipoCuelloDiv.style.display = 'none';
    }

    $.ajax({
        url: '../model/get_filters.php',
        type: 'GET',
        data: {
            categoria: categoria,
            genero: genero
        },
        success: function(data) {
            const filters = JSON.parse(data);
            actualizarSelect('marca', filters.marcas);
            actualizarSelect('color', filters.colores);
            actualizarSelect('talla', filters.tallas);
            actualizarSelect('tipo', filters.tipos);
            actualizarSelect('tipo_cuello', filters.tipos_cuello);
        }
    });
}

function actualizarSelect(id, opciones) {
    const select = document.getElementById(id);
    select.innerHTML = '<option value="">Todos</option>';
    opciones.forEach(opcion => {
        const option = document.createElement('option');
        option.value = opcion;
        option.textContent = opcion;
        select.appendChild(option);
    });
}

function updatePriceValues() {
    const minInput = document.getElementById('precioMin');
    const maxInput = document.getElementById('precioMax');
    const minValueDisplay = document.getElementById('precioMin-value');
    const maxValueDisplay = document.getElementById('precioMax-value');

    if (parseInt(minInput.value) > parseInt(maxInput.value)) {
        const tempValue = minInput.value;
        minInput.value = maxInput.value;
        maxInput.value = tempValue;
    }

    minValueDisplay.textContent = '$' + minInput.value;
    maxValueDisplay.textContent = '$' + maxInput.value;

    const min = parseInt(minInput.min);
    const max = parseInt(maxInput.max);
    const rangeMin = parseInt(minInput.value);
    const rangeMax = parseInt(maxInput.value);

    const trackActive = document.querySelector('.slider-track-active');
    const track = document.querySelector('.slider-track');

    const trackWidth = track.offsetWidth;
    const leftPercent = ((rangeMin - min) / (max - min)) * 100;
    const rightPercent = ((rangeMax - min) / (max - min)) * 100;

    trackActive.style.left = leftPercent + '%';
    trackActive.style.width = (rightPercent - leftPercent) + '%';
}

window.addEventListener('DOMContentLoaded', () => {
    actualizarFiltros();
    updatePriceValues();

    document.getElementById('filterForm').addEventListener('change', actualizarFiltros);
});
