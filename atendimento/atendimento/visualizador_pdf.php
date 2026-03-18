<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>Visualizador de PDF</title>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
	<style>
		* {
			margin: 0;
			padding: 0;
			box-sizing: border-box;
		}
		
		body {
			font-family: Arial, sans-serif;
			background: #f0f0f0;
		}
		
		.toolbar {
			background: #333;
			color: white;
			padding: 10px;
			display: flex;
			align-items: center;
			gap: 10px;
			flex-wrap: wrap;
		}
		
		.toolbar button {
			background: #0066cc;
			color: white;
			border: none;
			padding: 8px 15px;
			border-radius: 4px;
			cursor: pointer;
			font-size: 14px;
		}
		
		.toolbar button:hover {
			background: #0052a3;
		}
		
		.toolbar input {
			width: 60px;
			padding: 6px;
			border-radius: 4px;
			border: 1px solid #ddd;
		}
		
		.toolbar span {
			margin: 0 10px;
		}
		
		#pdf-title {
			flex: 1;
			text-align: left;
			font-weight: bold;
		}
		
		.container {
			display: flex;
			justify-content: center;
			padding: 20px;
			min-height: calc(100vh - 60px);
		}
		
		#pdf-canvas {
			max-width: 100%;
			border: 1px solid #ddd;
			box-shadow: 0 0 10px rgba(0,0,0,0.1);
			background: white;
		}
		
		.loading {
			text-align: center;
			padding: 20px;
			color: #666;
		}
		
		.error {
			background: #fee;
			color: #c00;
			padding: 20px;
			border-radius: 4px;
			margin: 20px;
		}
	</style>
</head>
<body>
	<div class="toolbar">
		<span id="pdf-title">Visualizador de PDF</span>
		<button onclick="previousPage()">← Anterior</button>
		<input type="number" id="page-num" value="1" min="1" onchange="goToPage()">
		<span id="page-count">de 1</span>
		<button onclick="nextPage()">Próximo →</button>
		<button onclick="zoomIn()">Aumentar 🔍</button>
		<button onclick="zoomOut()">Diminuir 🔍</button>
		<button onclick="downloadPDF()">Download ⬇️</button>
	</div>
	
	<div class="container">
		<canvas id="pdf-canvas"></canvas>
	</div>
	
	<div id="loading" class="loading" style="display:none;">Carregando PDF...</div>
	<div id="error" class="error" style="display:none;"></div>

	<script>
		// Configurar PDF.js
		pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
		
		let pdfData = null;
		let pdfDoc = null;
		let scale = 1.5;
		let currentPage = 1;
		
		// Função para carregar PDF do Base64
		async function loadPDF() {
			try {
				// Pega o base64 da URL
				const params = new URLSearchParams(window.location.search);
				const base64Data = params.get('data');
				const filename = decodeURIComponent(params.get('filename') || 'documento.pdf');
				
				document.getElementById('pdf-title').textContent = filename;
				
				if (!base64Data) {
					throw new Error('Nenhum PDF foi fornecido');
				}
				
				// Remove o prefixo data: se existir
				let cleanBase64 = base64Data;
				if (cleanBase64.indexOf('data:') === 0) {
					cleanBase64 = cleanBase64.split(',')[1];
				}
				
				// Decodifica o Base64
				const binaryString = atob(cleanBase64);
				const bytes = new Uint8Array(binaryString.length);
				for (let i = 0; i < binaryString.length; i++) {
					bytes[i] = binaryString.charCodeAt(i);
				}
				
				// Carrega o PDF
				pdfDoc = await pdfjsLib.getDocument({data: bytes}).promise;
				
				// Atualiza página
				document.getElementById('page-count').textContent = 'de ' + pdfDoc.numPages;
				document.getElementById('page-num').max = pdfDoc.numPages;
				
				// Renderiza primeira página
				renderPage(1);
				
				document.getElementById('loading').style.display = 'none';
				
			} catch (error) {
				console.error('Erro ao carregar PDF:', error);
				document.getElementById('loading').style.display = 'none';
				document.getElementById('error').style.display = 'block';
				document.getElementById('error').textContent = '❌ Erro ao carregar PDF: ' + error.message;
			}
		}
		
		// Renderizar página
		async function renderPage(num) {
			if (!pdfDoc || num < 1 || num > pdfDoc.numPages) return;
			
			currentPage = num;
			document.getElementById('page-num').value = currentPage;
			
			try {
				const page = await pdfDoc.getPage(num);
				const canvas = document.getElementById('pdf-canvas');
				const ctx = canvas.getContext('2d');
				
				const viewport = page.getViewport({scale: scale});
				canvas.width = viewport.width;
				canvas.height = viewport.height;
				
				await page.render({
					canvasContext: ctx,
					viewport: viewport
				}).promise;
				
			} catch (error) {
				console.error('Erro ao renderizar página:', error);
			}
		}
		
		// Controles de navegação
		function previousPage() {
			if (currentPage > 1) renderPage(currentPage - 1);
		}
		
		function nextPage() {
			if (pdfDoc && currentPage < pdfDoc.numPages) renderPage(currentPage + 1);
		}
		
		function goToPage() {
			const pageNum = parseInt(document.getElementById('page-num').value) || 1;
			renderPage(pageNum);
		}
		
		function zoomIn() {
			scale += 0.2;
			renderPage(currentPage);
		}
		
		function zoomOut() {
			if (scale > 0.5) {
				scale -= 0.2;
				renderPage(currentPage);
			}
		}
		
		function downloadPDF() {
			const params = new URLSearchParams(window.location.search);
			const base64Data = params.get('data');
			const filename = decodeURIComponent(params.get('filename') || 'documento.pdf');
			
			if (!base64Data) return;
			
			const link = document.createElement('a');
			link.href = base64Data.indexOf('data:') === 0 ? base64Data : 'data:application/pdf;base64,' + base64Data;
			link.download = filename;
			document.body.appendChild(link);
			link.click();
			document.body.removeChild(link);
		}
		
		// Carregar PDF ao iniciar
		document.getElementById('loading').style.display = 'block';
		loadPDF();
	</script>
</body>
</html>
