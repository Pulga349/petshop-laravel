import './bootstrap';

import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';

window.Alpine = Alpine;
window.Chart = Chart;

// Toast Container
window.toastContainer = () => ({
    toasts: [],
    init() {
        this.loadFlashMessages();
        
        window.addEventListener('toast-show', (e) => {
            this.add(e.detail);
        });
    },
    loadFlashMessages() {
        const container = document.getElementById('flash-messages');
        if (!container) return;
        
        const messages = container.querySelectorAll('[data-type]');
        messages.forEach(el => {
            this.add({
                type: el.dataset.type,
                title: el.dataset.title || '',
                message: el.textContent.trim(),
                duration: el.dataset.duration ? parseInt(el.dataset.duration) : 5000
            });
        });
        container.remove();
    },
    add(toast) {
        const id = Date.now() + Math.random();
        this.toasts.push({ ...toast, id, visible: true });
        
        if (toast.duration > 0) {
            setTimeout(() => this.remove(id), toast.duration);
        }
    },
    remove(id) {
        const idx = this.toasts.findIndex(t => t.id === id);
        if (idx !== -1) {
            this.toasts[idx].visible = false;
            setTimeout(() => {
                this.toasts.splice(idx, 1);
            }, 300);
        }
    },
    getClasses(type) {
        const classes = {
            success: 'bg-emerald-500/10 border-emerald-500/30 border-l-emerald-500',
            error: 'bg-red-500/10 border-red-500/30 border-l-red-500',
            warning: 'bg-yellow-500/10 border-yellow-500/30 border-l-yellow-500',
            info: 'bg-blue-500/10 border-blue-500/30 border-l-blue-500',
        };
        return classes[type] || classes.info;
    },
    getIcon(type) {
        const icons = {
            success: 'bi-check-circle-fill text-emerald-500',
            error: 'bi-x-circle-fill text-red-500',
            warning: 'bi-exclamation-triangle-fill text-yellow-500',
            info: 'bi-info-circle-fill text-blue-500',
        };
        return icons[type] || icons.info;
    },
    show(type, message, title = '', duration = 5000) {
        this.add({ type, message, title, duration });
    }
});

// API global
window.showToast = (type, message, title = '', duration = 5000) => {
    window.dispatchEvent(new CustomEvent('toast-show', {
        detail: { type, message, title, duration }
    }));
};

// Sales Chart Component -lee los datos desde el DOM
window.salesChart = () => ({
    chart: null,
    init() {
        const ctx = this.$refs.salesChart.getContext('2d');
        
        // Get data from DOM attributes
        const labels = JSON.parse(this.$refs.salesChart.dataset.labels || '[]');
        const salesData = JSON.parse(this.$refs.salesChart.dataset.sales || '[]');
        const purchasesData = JSON.parse(this.$refs.salesChart.dataset.purchases || '[]');
        
        // Create gradients
        const salesGradient = ctx.createLinearGradient(0, 0, 0, 300);
        salesGradient.addColorStop(0, 'rgba(16, 185, 129, 0.3)');
        salesGradient.addColorStop(1, 'rgba(16, 185, 129, 0)');
        
        const purchasesGradient = ctx.createLinearGradient(0, 0, 0, 300);
        purchasesGradient.addColorStop(0, 'rgba(59, 130, 246, 0.3)');
        purchasesGradient.addColorStop(1, 'rgba(59, 130, 246, 0)');
        
        this.chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Ventas',
                        data: salesData,
                        borderColor: '#10b981',
                        backgroundColor: salesGradient,
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 0,
                        pointHoverRadius: 6,
                        pointHoverBackgroundColor: '#10b981',
                        pointHoverBorderColor: '#fff',
                        pointHoverBorderWidth: 2,
                    },
                    {
                        label: 'Compras',
                        data: purchasesData,
                        borderColor: '#3b82f6',
                        backgroundColor: purchasesGradient,
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 0,
                        pointHoverRadius: 6,
                        pointHoverBackgroundColor: '#3b82f6',
                        pointHoverBorderColor: '#fff',
                        pointHoverBorderWidth: 2,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(30, 30, 30, 0.9)',
                        titleColor: '#f5f6fa',
                        bodyColor: '#9ca3af',
                        borderColor: 'rgba(255, 255, 255, 0.1)',
                        borderWidth: 1,
                        padding: 12,
                        displayColors: true,
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': $' + context.parsed.y.toLocaleString();
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: 'rgba(255, 255, 255, 0.5)',
                            font: {
                                family: 'Inter'
                            }
                        }
                    },
                    y: {
                        grid: {
                            color: 'rgba(255, 255, 255, 0.05)'
                        },
                        ticks: {
                            color: 'rgba(255, 255, 255, 0.5)',
                            font: {
                                family: 'Inter'
                            },
                            callback: function(value) {
                                return '$' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    }
});

// Global Navigation Shortcuts
document.addEventListener('keydown', (e) => {
    if (e.ctrlKey || e.metaKey) {
        const key = e.key.toLowerCase();
        const target = document.querySelector(`[data-nav-key="${key}"]`);
        
        if (target) {
            e.preventDefault();
            if (target.tagName === 'A') {
                target.click();
            } else if (target.tagName === 'BUTTON') {
                target.click();
            }
        }
    }
});

Alpine.start();