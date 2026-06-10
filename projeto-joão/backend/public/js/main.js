/* ====================================================================
   CheckInd — JavaScript do Painel Web (TechForge Industrial)
   ==================================================================== */

document.addEventListener('DOMContentLoaded', () => {
    
    // --- 1. Toggle Sidebar ---
    const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    
    if (sidebar && sidebarToggle) {
        // Recupera o estado anterior do sidebar do localStorage
        const isCollapsed = localStorage.getItem('sidebar-collapsed') === 'true';
        if (isCollapsed) {
            sidebar.classList.add('collapsed');
        }

        sidebarToggle.addEventListener('click', (e) => {
            e.preventDefault();
            sidebar.classList.toggle('collapsed');
            
            // Salva o estado atual
            const currentlyCollapsed = sidebar.classList.contains('collapsed');
            localStorage.setItem('sidebar-collapsed', currentlyCollapsed);
        });
    }

    // --- 2. Relógio Digital do Topbar ---
    const clockTime = document.getElementById('clockTime');
    if (clockTime) {
        const updateClock = () => {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            
            clockTime.textContent = `${hours}:${minutes}:${seconds}`;
        };
        
        // Executa imediatamente e depois a cada 1 segundo
        updateClock();
        setInterval(updateClock, 1000);
    }

    // --- 3. Fechamento Automático de Mensagens Flash ---
    const flashAlerts = document.querySelectorAll('.flash-alert');
    flashAlerts.forEach((alert) => {
        setTimeout(() => {
            // Usa o helper do Bootstrap se disponível
            if (typeof bootstrap !== 'undefined' && bootstrap.Alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            } else {
                // Fallback vanilla js
                alert.classList.remove('show');
                setTimeout(() => alert.remove(), 150);
            }
        }, 5000); // 5 segundos
    });

    // --- 4. Tooltips e Popovers do Bootstrap ---
    if (typeof bootstrap !== 'undefined') {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map((tooltipTriggerEl) => {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
});
