/**
 * SIGEVEN - Sistema de Gestión de Eventos
 * Main JavaScript API Module
 * Connects frontend to PHP backend APIs
 */

// API Base URL
const API_BASE = 'php/';

// ==========================================
// API FETCH UTILITIES
// ==========================================

async function apiRequest(endpoint, options = {}) {
    const defaultOptions = {
        headers: {
            'Content-Type': 'application/json',
        },
    };

    const config = { ...defaultOptions, ...options };
    
    try {
        const response = await fetch(`${API_BASE}${endpoint}`, config);
        const data = await response.json();
        return data;
    } catch (error) {
        console.error('API Error:', error);
        showToast('Error de conexión con el servidor', 'error');
        return { success: false, message: error.message };
    }
}

// ==========================================
// USUARIOS API
// ==========================================

const UsuariosAPI = {
    async getAll(filters = {}) {
        const params = new URLSearchParams(filters).toString();
        return apiRequest(`api_usuarios.php?${params}`);
    },

    async getById(id) {
        return apiRequest(`api_usuarios.php?id=${id}`);
    },

    async create(userData) {
        return apiRequest('api_usuarios.php', {
            method: 'POST',
            body: JSON.stringify(userData)
        });
    },

    async update(id, userData) {
        return apiRequest('api_usuarios.php', {
            method: 'PUT',
            body: JSON.stringify({ id, ...userData })
        });
    },

    async delete(id) {
        return apiRequest('api_usuarios.php', {
            method: 'DELETE',
            body: JSON.stringify({ id })
        });
    },

    async aprobar(id) {
        return this.update(id, { estado: 'activo' });
    },

    async rechazar(id) {
        return this.update(id, { estado: 'inactivo' });
    }
};

// ==========================================
// EVENTOS API
// ==========================================

const EventosAPI = {
    async getAll(filters = {}) {
        const params = new URLSearchParams(filters).toString();
        return apiRequest(`api_eventos.php?${params}`);
    },

    async getById(id) {
        return apiRequest(`api_eventos.php?id=${id}`);
    },

    async create(eventoData) {
        return apiRequest('api_eventos.php', {
            method: 'POST',
            body: JSON.stringify(eventoData)
        });
    },

    async update(id, eventoData) {
        return apiRequest('api_eventos.php', {
            method: 'PUT',
            body: JSON.stringify({ id, ...eventoData })
        });
    },

    async delete(id) {
        return apiRequest('api_eventos.php', {
            method: 'DELETE',
            body: JSON.stringify({ id })
        });
    },

    async aprobar(id) {
        return this.update(id, { estado: 'aprobado' });
    },

    async rechazar(id, motivo) {
        return this.update(id, { estado: 'rechazado', motivo_rechazo: motivo });
    }
};

// ==========================================
// SOLICITUDES API
// ==========================================

const SolicitudesAPI = {
    async getAll(filters = {}) {
        const params = new URLSearchParams(filters).toString();
        return apiRequest(`api_solicitudes.php?${params}`);
    },

    async getById(id) {
        return apiRequest(`api_solicitudes.php?id=${id}`);
    },

    async create(solicitudData) {
        return apiRequest('api_solicitudes.php', {
            method: 'POST',
            body: JSON.stringify(solicitudData)
        });
    },

    async aprobar(id, adminId, respuesta = '') {
        return apiRequest('api_solicitudes.php', {
            method: 'PUT',
            body: JSON.stringify({
                id,
                estado: 'aprobada',
                admin_id: adminId,
                respuesta_admin: respuesta
            })
        });
    },

    async rechazar(id, adminId, respuesta) {
        return apiRequest('api_solicitudes.php', {
            method: 'PUT',
            body: JSON.stringify({
                id,
                estado: 'rechazada',
                admin_id: adminId,
                respuesta_admin: respuesta
            })
        });
    },

    async delete(id) {
        return apiRequest('api_solicitudes.php', {
            method: 'DELETE',
            body: JSON.stringify({ id })
        });
    }
};

// ==========================================
// ESPACIOS API
// ==========================================

const EspaciosAPI = {
    async getAll(filters = {}) {
        const params = new URLSearchParams(filters).toString();
        return apiRequest(`api_espacios.php?${params}`);
    },

    async getById(id) {
        return apiRequest(`api_espacios.php?id=${id}`);
    },

    async checkDisponibilidad(fecha, horaInicio, horaFin) {
        return apiRequest(`api_espacios.php?disponibilidad=1&fecha=${fecha}&hora_inicio=${horaInicio}&hora_fin=${horaFin}`);
    },

    async create(espacioData) {
        return apiRequest('api_espacios.php', {
            method: 'POST',
            body: JSON.stringify(espacioData)
        });
    },

    async update(id, espacioData) {
        return apiRequest('api_espacios.php', {
            method: 'PUT',
            body: JSON.stringify({ id, ...espacioData })
        });
    },

    async delete(id) {
        return apiRequest('api_espacios.php', {
            method: 'DELETE',
            body: JSON.stringify({ id })
        });
    }
};

// ==========================================
// CALENDARIO API
// ==========================================

const CalendarioAPI = {
    async getEventos(mes, anio, options = {}) {
        const params = new URLSearchParams({
            mes,
            anio,
            ...options
        }).toString();
        return apiRequest(`api_calendario.php?${params}`);
    }
};

// ==========================================
// EXPORT FUNCTIONS
// ==========================================

const ExportAPI = {
    exportToExcel(tipo, fechaInicio = null, fechaFin = null) {
        let url = `${API_BASE}api_exportar.php?tipo=${tipo}&formato=excel`;
        if (fechaInicio) url += `&fecha_inicio=${fechaInicio}`;
        if (fechaFin) url += `&fecha_fin=${fechaFin}`;
        window.location.href = url;
    },

    exportToPDF(tipo, fechaInicio = null, fechaFin = null) {
        let url = `${API_BASE}api_exportar.php?tipo=${tipo}&formato=pdf`;
        if (fechaInicio) url += `&fecha_inicio=${fechaInicio}`;
        if (fechaFin) url += `&fecha_fin=${fechaFin}`;
        window.open(url, '_blank');
    },

    async preview(tipo) {
        return apiRequest(`api_exportar.php?tipo=${tipo}&formato=json`);
    }
};

// ==========================================
// TOAST NOTIFICATIONS
// ==========================================

function showToast(message, type = 'info', duration = 4000) {
    // Remove existing toast container if any
    let container = document.getElementById('toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toast-container';
        container.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 10000;
            display: flex;
            flex-direction: column;
            gap: 10px;
        `;
        document.body.appendChild(container);
    }

    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    
    const icons = {
        success: '✅',
        error: '❌',
        warning: '⚠️',
        info: 'ℹ️'
    };

    const colors = {
        success: '#28a745',
        error: '#dc3545',
        warning: '#ffc107',
        info: '#17a2b8'
    };

    toast.style.cssText = `
        background: white;
        padding: 16px 20px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 300px;
        max-width: 400px;
        border-left: 4px solid ${colors[type]};
        animation: slideInRight 0.3s ease, fadeOut 0.3s ease ${duration - 300}ms forwards;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    `;

    toast.innerHTML = `
        <span style="font-size: 20px;">${icons[type]}</span>
        <span style="flex: 1; color: #333;">${message}</span>
        <button onclick="this.parentElement.remove()" style="background: none; border: none; cursor: pointer; color: #999; font-size: 18px;">&times;</button>
    `;

    container.appendChild(toast);

    // Auto remove
    setTimeout(() => {
        if (toast.parentElement) {
            toast.remove();
        }
    }, duration);
}

// Add CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes fadeOut {
        from {
            opacity: 1;
        }
        to {
            opacity: 0;
            transform: translateX(100%);
        }
    }

    /* Card animations */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes pulse {
        0%, 100% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.05);
        }
    }

    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
    }

    @keyframes bounce {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
    }

    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    /* Animation classes */
    .animate-fadeInUp {
        animation: fadeInUp 0.5s ease forwards;
    }

    .animate-pulse {
        animation: pulse 0.3s ease;
    }

    .animate-shake {
        animation: shake 0.3s ease;
    }

    .animate-bounce {
        animation: bounce 0.5s ease;
    }

    .animate-spin {
        animation: spin 1s linear infinite;
    }

    /* Staggered animation for lists */
    .stagger-item {
        opacity: 0;
        animation: fadeInUp 0.5s ease forwards;
    }

    .stagger-item:nth-child(1) { animation-delay: 0.1s; }
    .stagger-item:nth-child(2) { animation-delay: 0.2s; }
    .stagger-item:nth-child(3) { animation-delay: 0.3s; }
    .stagger-item:nth-child(4) { animation-delay: 0.4s; }
    .stagger-item:nth-child(5) { animation-delay: 0.5s; }
    .stagger-item:nth-child(6) { animation-delay: 0.6s; }

    /* Button hover animations */
    .btn-animated {
        transition: all 0.3s ease;
    }

    .btn-animated:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    }

    .btn-animated:active {
        transform: translateY(0);
    }

    /* Loading spinner */
    .loading-spinner {
        display: inline-block;
        width: 20px;
        height: 20px;
        border: 2px solid #f3f3f3;
        border-top: 2px solid #163E8C;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    /* Card hover effects */
    .card-hover {
        transition: all 0.3s ease;
    }

    .card-hover:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }

    /* Table row animations */
    .table-row-animated {
        transition: all 0.2s ease;
    }

    .table-row-animated:hover {
        background-color: rgba(22, 62, 140, 0.05);
    }

    /* Modal animations */
    .modal-animated {
        animation: fadeInUp 0.3s ease;
    }

    .modal-backdrop-animated {
        animation: fadeIn 0.3s ease;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    /* Status badge animations */
    .status-pulse {
        animation: pulse 2s infinite;
    }
`;
document.head.appendChild(style);

// ==========================================
// UI HELPER FUNCTIONS
// ==========================================

function showLoading(element, text = 'Cargando...') {
    const originalContent = element.innerHTML;
    element.dataset.originalContent = originalContent;
    element.innerHTML = `<span class="loading-spinner"></span> ${text}`;
    element.disabled = true;
}

function hideLoading(element) {
    if (element.dataset.originalContent) {
        element.innerHTML = element.dataset.originalContent;
        delete element.dataset.originalContent;
    }
    element.disabled = false;
}

function confirmAction(message) {
    return new Promise((resolve) => {
        if (confirm(message)) {
            resolve(true);
        } else {
            resolve(false);
        }
    });
}

// ==========================================
// ADMIN FUNCTIONS
// ==========================================

// Approve user
async function aprobarUsuario(id, button) {
    const confirmed = await confirmAction('¿Está seguro de aprobar este usuario?');
    if (!confirmed) return;

    showLoading(button, 'Aprobando...');
    
    const result = await UsuariosAPI.aprobar(id);
    
    hideLoading(button);
    
    if (result.success) {
        showToast('Usuario aprobado exitosamente', 'success');
        // Refresh the table or update UI
        if (typeof loadUsuarios === 'function') loadUsuarios();
        // Or update the row directly
        const row = button.closest('tr');
        if (row) {
            const statusBadge = row.querySelector('.status-badge');
            if (statusBadge) {
                statusBadge.textContent = 'Activo';
                statusBadge.className = 'status-badge status-active';
            }
            row.classList.add('animate-pulse');
        }
    } else {
        showToast(result.message || 'Error al aprobar usuario', 'error');
    }
}

// Delete user
async function eliminarUsuario(id, button) {
    const confirmed = await confirmAction('¿Está seguro de eliminar este usuario? Esta acción no se puede deshacer.');
    if (!confirmed) return;

    showLoading(button, 'Eliminando...');
    
    const result = await UsuariosAPI.delete(id);
    
    hideLoading(button);
    
    if (result.success) {
        showToast('Usuario eliminado exitosamente', 'success');
        const row = button.closest('tr');
        if (row) {
            row.style.animation = 'fadeOut 0.3s ease forwards';
            setTimeout(() => row.remove(), 300);
        }
    } else {
        showToast(result.message || 'Error al eliminar usuario', 'error');
    }
}

// Approve event
async function aprobarEvento(id, button) {
    const confirmed = await confirmAction('¿Está seguro de aprobar este evento?');
    if (!confirmed) return;

    showLoading(button, 'Aprobando...');
    
    const result = await EventosAPI.aprobar(id);
    
    hideLoading(button);
    
    if (result.success) {
        showToast('Evento aprobado exitosamente', 'success');
        const card = button.closest('.event-card');
        if (card) {
            card.classList.remove('pending');
            card.classList.add('approved', 'animate-pulse');
            const badge = card.querySelector('.event-status-badge');
            if (badge) {
                badge.textContent = '✅ Aprobado';
                badge.className = 'event-status-badge approved';
            }
        }
    } else {
        showToast(result.message || 'Error al aprobar evento', 'error');
    }
}

// Reject event
async function rechazarEvento(id, button) {
    const motivo = prompt('Por favor, ingrese el motivo del rechazo:');
    if (!motivo) {
        showToast('Debe ingresar un motivo para rechazar', 'warning');
        return;
    }

    showLoading(button, 'Rechazando...');
    
    const result = await EventosAPI.rechazar(id, motivo);
    
    hideLoading(button);
    
    if (result.success) {
        showToast('Evento rechazado', 'info');
        const card = button.closest('.event-card');
        if (card) {
            card.style.animation = 'fadeOut 0.5s ease forwards';
            setTimeout(() => card.remove(), 500);
        }
    } else {
        showToast(result.message || 'Error al rechazar evento', 'error');
    }
}

// Export to Excel
function exportarExcel(tipo) {
    showToast('Generando archivo Excel...', 'info');
    ExportAPI.exportToExcel(tipo);
}

// Export to PDF
function exportarPDF(tipo) {
    showToast('Generando documento PDF...', 'info');
    ExportAPI.exportToPDF(tipo);
}

// ==========================================
// INITIALIZE ON PAGE LOAD
// ==========================================

document.addEventListener('DOMContentLoaded', function() {
    // Add stagger animation to list items
    document.querySelectorAll('.event-card, .user-card, .stat-card').forEach((card, index) => {
        card.classList.add('stagger-item');
        card.style.animationDelay = `${index * 0.1}s`;
    });

    // Add hover effects to buttons
    document.querySelectorAll('.btn').forEach(btn => {
        btn.classList.add('btn-animated');
    });

    // Add hover effects to cards
    document.querySelectorAll('.stat-card, .chart-card, .table-card').forEach(card => {
        card.classList.add('card-hover');
    });

    // Add row animations to tables
    document.querySelectorAll('tbody tr').forEach(row => {
        row.classList.add('table-row-animated');
    });

    console.log('SIGEVEN API Module loaded successfully');
});

// Export for global access
window.SIGEVEN = {
    UsuariosAPI,
    EventosAPI,
    SolicitudesAPI,
    EspaciosAPI,
    CalendarioAPI,
    ExportAPI,
    showToast,
    showLoading,
    hideLoading,
    aprobarUsuario,
    eliminarUsuario,
    aprobarEvento,
    rechazarEvento,
    exportarExcel,
    exportarPDF
};
