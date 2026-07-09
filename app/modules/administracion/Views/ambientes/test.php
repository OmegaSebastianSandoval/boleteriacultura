<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Salón con Mesas Compuestas</title>
  <style>
    .salon {
      display: grid;
      grid-template-columns: repeat(10, 38px);
      grid-template-rows: repeat(10, 38px);
      gap: 2px;
      margin: 20px auto;
      width: max-content;
    }

    .celda {
      width: 38px;
      height: 38px;
      background-color: #fff;
      border: 1px solid #ccc;
      display: flex;
      justify-content: center;
      align-items: center;
      font-size: 10px;
      box-sizing: border-box;
      transition: background-color 0.2s;
    }

    .mesa {
      background-color: #66ccff;
      border: 1px solid #0077aa;
      cursor: pointer;
    }

    .mesa.seleccionada {
      background-color: #ffaa66 !important;
    }
  </style>
</head>
<body>

<div class="salon" id="salon"></div>

<script>
  const filas = 10;
  const columnas = 10;

  // Mesas con forma y posición (fila, col, ancho, alto)
const mesas = [
  { id: 1, fila: 1, col: 1, ancho: 2, alto: 2 }, // 2x2 = 4 personas
  { id: 2, fila: 3, col: 4, ancho: 3, alto: 2 }, // 3x2 = 6 personas
  { id: 3, fila: 6, col: 1, ancho: 2, alto: 2 }, // otra de 4
  { id: 4, fila: 7, col: 6, ancho: 3, alto: 2 }, // otra de 6
  { id: 5, fila: 0, col: 5, ancho: 4, alto: 2 }, // nueva mesa de 8
];

  // Mapa para saber qué celdas pertenecen a qué mesa
  const mapaMesas = Array.from({ length: filas }, () => Array(columnas).fill(null));

  // Asignar celdas a cada mesa
  mesas.forEach(mesa => {
    for (let i = 0; i < mesa.alto; i++) {
      for (let j = 0; j < mesa.ancho; j++) {
        const f = mesa.fila + i;
        const c = mesa.col + j;
        if (f < filas && c < columnas) {
          mapaMesas[f][c] = mesa.id;
        }
      }
    }
  });

  const salon = document.getElementById('salon');
  const celdasPorMesa = {}; // para almacenar las celdas DOM por mesa

  for (let i = 0; i < filas; i++) {
    for (let j = 0; j < columnas; j++) {
      const celda = document.createElement('div');
      celda.classList.add('celda');
      celda.dataset.fila = i;
      celda.dataset.columna = j;

      const mesaId = mapaMesas[i][j];
      if (mesaId) {
        celda.classList.add('mesa');
        celda.dataset.mesaId = mesaId;
        if (!celdasPorMesa[mesaId]) {
          celdasPorMesa[mesaId] = [];
        }
        celdasPorMesa[mesaId].push(celda);

        // Click en cualquier parte de la mesa cambia toda la mesa
        celda.addEventListener('click', () => {
          celdasPorMesa[mesaId].forEach(c => c.classList.toggle('seleccionada'));
        });
      }

      salon.appendChild(celda);
    }
  }
</script>

</body>
</html>