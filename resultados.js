// JavaScript para la página de resultados del administrador
class ResultsManager {
    constructor() {
        this.results = [];
        this.filteredResults = [];
        this.stats = {};
        this.currentFilter = 'all';
        this.init();
        this.setupFilterListeners(); // <-- conectar listeners de filtro solo una vez
    }
    
    async init() {
        await this.checkAdminAccess();
        await this.loadResults();
        this.setupEventListeners();
    }
    
    // Verificar acceso de administrador
    async checkAdminAccess() {
        try {
            const response = await fetch('get_user_status.php');
            const data = await response.json();
            
            if (!data.logged_in || data.user_role !== 'admin') {
                window.location.href = 'login.html';
                return;
            }
        } catch (error) {
            console.error('Error verificando acceso:', error);
            window.location.href = 'login.html';
        }
    }
    
    // Cargar resultados desde la base de datos
    async loadResults() {
        try {
            const response = await fetch('get_results.php');
            const data = await response.json();
            
            if (data.success) {
                this.results = data.results;
                this.filteredResults = data.results;
                this.stats = data.stats;
                this.updateStats();
                this.renderResults();
                // No llamar setupEventListeners aquí, solo tras render
            } else {
                this.showError('Error cargando resultados: ' + data.message);
            }
        } catch (error) {
            console.error('Error cargando resultados:', error);
            this.showError('Error de conexión al cargar resultados');
        }
    }
    
    // Actualizar estadísticas
    updateStats() {
        document.getElementById('totalTests').textContent = this.stats.totalTests || 0;
        document.getElementById('avgScore').textContent = this.stats.avgScore || 0;
        document.getElementById('highLevel').textContent = this.stats.highLevel || 0;
        document.getElementById('mediumLevel').textContent = this.stats.mediumLevel || 0;
    }
    
    // Renderizar tabla de resultados
    renderResults() {
        const container = document.getElementById('resultsContent');
        
        if (this.filteredResults.length === 0) {
            container.innerHTML = `
                <div class="no-results">
                    <i class="fas fa-inbox" style="font-size: 3em; color: #ccc; margin-bottom: 20px;"></i>
                    <h3>No hay resultados disponibles</h3>
                    <p>Aún no se han completado evaluaciones de habilidades blandas.</p>
                </div>
            `;
            return;
        }
        
        const tableHTML = `
            <table class="results-table">
                <thead>
                    <tr>
                        <th>Usuario</th>
                        <th>Email</th>
                        <th>Fecha</th>
                        <th>Puntaje</th>
                        <th>Resultado</th>
                        <th>Análisis</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    ${this.filteredResults.map(result => this.renderResultRow(result)).join('')}
                </tbody>
            </table>
        `;
        
        container.innerHTML = tableHTML;
        // No volver a llamar setupEventListeners aquí
    }
    
    // Renderizar fila de resultado
    renderResultRow(result) {
        // Determinar nivel basado en puntaje
        const puntaje = parseInt(result.puntaje_total) || 0;
        let nivel = 'Nivel medio';
        if (puntaje > 21) {
            nivel = 'Nivel alto';
        } else if (puntaje <= 12) {
            nivel = 'Nivel bajo';
        }
        
        const resultClass = this.getResultClass(nivel);
        const resultText = this.getResultText(nivel);
        
        // Truncar el análisis para mostrar en la tabla
        const analisisCompleto = result.resultado_final || result.analisis_preview || 'Sin análisis disponible';
        const analisisTruncado = this.truncateText(analisisCompleto, 100);
        
        return `
            <tr>
                <td>${result.nombre}</td>
                <td>${result.correo}</td>
                <td>${this.formatDate(result.fecha_registro)}</td>
                <td><strong>${result.puntaje_total}/30</strong></td>
                <td><span class="result-badge ${resultClass}">${resultText}</span></td>
                <td class="analysis-preview">${analisisTruncado}</td>
                <td>
                    <button class="view-details-btn" onclick="resultsManager.viewDetails(${result.Id_historial})">
                        <i class="fas fa-eye"></i> Ver Detalles
                    </button>
                </td>
            </tr>
        `;
    }
    
    // Truncar texto
    truncateText(text, maxLength) {
        if (!text || text.length <= maxLength) {
            return text || 'Sin análisis';
        }
        return text.substring(0, maxLength) + '...';
    }
    
    // Obtener clase CSS para el resultado
    getResultClass(resultado) {
        switch(resultado) {
            case 'Nivel alto': return 'result-alto';
            case 'Nivel medio': return 'result-medio';
            case 'Nivel bajo': return 'result-bajo';
            default: return 'result-medio';
        }
    }
    
    // Obtener texto del resultado
    getResultText(resultado) {
        switch(resultado) {
            case 'Nivel alto': return 'Alto';
            case 'Nivel medio': return 'Medio';
            case 'Nivel bajo': return 'Bajo';
            default: return resultado;
        }
    }
    
    // Formatear fecha
    formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('es-ES', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }
    
    // Ver detalles de una evaluación
    async viewDetails(historialId) {
        try {
            const response = await fetch('get_result_details.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ historialId: historialId })
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.showDetailsModal(data.details, data.responses);
            } else {
                this.showError('Error cargando detalles: ' + data.message);
            }
        } catch (error) {
            console.error('Error cargando detalles:', error);
            this.showError('Error de conexión al cargar detalles');
        }
    }
    
    // Mostrar modal con detalles
    showDetailsModal(details, responses) {
        const modal = document.getElementById('detailsModal');
        const modalContent = document.getElementById('modalContent');
        
        // Determinar nivel basado en puntaje
        const puntaje = parseInt(details.puntaje_total) || 0;
        let nivel = 'Nivel medio';
        if (puntaje > 21) {
            nivel = 'Nivel alto';
        } else if (puntaje <= 12) {
            nivel = 'Nivel bajo';
        }
        
        // Obtener análisis completo
        const analisisCompleto = details.analisis_completo.trim() || details.resultado_final.trim() || 'Sin análisis disponible';
        
        const detailsHTML = `
            <div style="margin-bottom: 20px;">
                <h3>Información General</h3>
                <p><strong>Usuario:</strong> ${details.nombre}</p>
                <p><strong>Email:</strong> ${details.correo}</p>
                <p><strong>Fecha:</strong> ${this.formatDate(details.fecha_registro)}</p>
                <p><strong>Puntaje Total:</strong> ${details.puntaje_total}/30</p>
                <p><strong>Resultado:</strong> <span class="result-badge ${this.getResultClass(nivel)}">${nivel}</span></p>
            </div>
            
            <div style="margin-bottom: 20px; padding: 20px; background: #f8f9fa; border-radius: 10px; border-left: 4px solid #667eea;">
                <h3 style="margin-top: 0; color: #667eea;">
                    <i class="fas fa-brain"></i> Análisis de IA Completo
                </h3>
                <div style="line-height: 1.8; color: #555;">
                    ${analisisCompleto.replace(/\n/g, '<br>')}
                </div>
            </div>
            
            <h3>Respuestas Detalladas</h3>
            <table class="details-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Pregunta</th>
                        <th>Respuesta</th>
                        <th>Puntaje</th>
                    </tr>
                </thead>
                <tbody>
                    ${responses.map((response, index) => `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${response.pregunta}</td>
                            <td>${response.respuesta}</td>
                            <td><strong>${response.puntaje}</strong></td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        `;
        
        modalContent.innerHTML = detailsHTML;
        modal.style.display = 'block';
    }
    
    // Configurar event listeners
    setupEventListeners() {
        // Cerrar modal
        const modal = document.getElementById('detailsModal');
        const closeBtn = document.querySelector('.close');
        
        closeBtn.onclick = () => {
            modal.style.display = 'none';
        };
        
        window.onclick = (event) => {
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        };
    }

    setupFilterListeners() {
        // Filtros de nivel
        const filterButtons = document.querySelectorAll('.filter-btn');
        filterButtons.forEach(btn => {
            btn.onclick = (e) => {
                const filter = btn.getAttribute('data-filter');
                this.currentFilter = filter;
                if (filter === 'all') {
                    this.filteredResults = this.results;
                } else {
                    // Filtrar basado en puntaje, no en texto
                    this.filteredResults = this.results.filter(r => {
                        const puntaje = parseInt(r.puntaje_total) || 0;
                        if (filter === 'Nivel alto') {
                            return puntaje > 21;
                        } else if (filter === 'Nivel medio') {
                            return puntaje > 12 && puntaje <= 21;
                        } else if (filter === 'Nivel bajo') {
                            return puntaje <= 12;
                        }
                        return true;
                    });
                }
                this.renderResults();
                // Marcar botón activo
                filterButtons.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
            };
        });
    }
    
    // Mostrar error
    showError(message) {
        const container = document.getElementById('resultsContent');
        container.innerHTML = `
            <div class="no-results">
                <i class="fas fa-exclamation-triangle" style="font-size: 3em; color: #dc3545; margin-bottom: 20px;"></i>
                <h3>Error</h3>
                <p>${message}</p>
                <button onclick="location.reload()" style="margin-top: 20px; padding: 10px 20px; background: #667eea; color: white; border: none; border-radius: 5px; cursor: pointer;">
                    <i class="fas fa-redo"></i> Reintentar
                </button>
            </div>
        `;
    }
}

// Inicializar cuando el DOM esté listo
let resultsManager;
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        resultsManager = new ResultsManager();
    });
} else {
    resultsManager = new ResultsManager();
} 