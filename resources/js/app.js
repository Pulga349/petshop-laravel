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
    getClasses() {
        // Flat monochrome panel per design contract; chromatic color only on the icon micro-indicator.
        return 'border border-line-focus bg-surface-raised';
    },
    getIcon(type) {
        const colors = {
            success: 'text-success',
            error: 'text-danger',
            warning: 'text-warning',
            info: 'text-accent',
        };
        return colors[type] || colors.info;
    },
    getGlyph(type) {
        const glyphs = {
            success: 'bi-check-lg',
            error: 'bi-x-lg',
            warning: 'bi-exclamation-triangle-fill',
            info: 'bi-info-lg',
        };
        return glyphs[type] || glyphs.info;
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
// Chart state lives in the factory CLOSURE, never on the Alpine data object:
// Alpine wraps component data in a deep reactive proxy, and Chart.js instances
// (or their gradients/options) break through it — update() then explodes with
// "Maximum call stack" / corrupted internals. Closures are not reactive.
window.salesChart = () => {
    let chart = null;
    let range = '12m';
    let requestId = 0;
    let chartUrl = '';

    return {
        init() {
            const ctx = this.$refs.salesChart.getContext('2d');

            // Get data from DOM attributes
            const labels = JSON.parse(this.$refs.salesChart.dataset.labels || '[]');
            const salesData = JSON.parse(this.$refs.salesChart.dataset.sales || '[]');
            const purchasesData = JSON.parse(this.$refs.salesChart.dataset.purchases || '[]');
            range = this.$refs.salesChart.dataset.range || '12m';
            // Capture the endpoint in init's root-component context: `this.$el`
            // inside event handlers resolves to the event target (the select),
            // not the x-data section, so reading dataset.url there yields undefined.
            chartUrl = this.$refs.salesChart.dataset.url || '';

            // Monochrome gradients — chromatic colors are reserved for micro-indicators
            const salesGradient = ctx.createLinearGradient(0, 0, 0, 300);
            salesGradient.addColorStop(0, 'rgba(255, 255, 255, 0.18)');
            salesGradient.addColorStop(1, 'rgba(255, 255, 255, 0)');

            const purchasesGradient = ctx.createLinearGradient(0, 0, 0, 300);
            purchasesGradient.addColorStop(0, 'rgba(141, 141, 141, 0.18)');
            purchasesGradient.addColorStop(1, 'rgba(141, 141, 141, 0)');

            chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Ventas',
                            data: salesData,
                            borderColor: '#ffffff',
                            backgroundColor: salesGradient,
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4,
                            pointRadius: 0,
                            pointHoverRadius: 6,
                            pointHoverBackgroundColor: '#ffffff',
                            pointHoverBorderColor: '#000000',
                            pointHoverBorderWidth: 2,
                        },
                        {
                            label: 'Compras',
                            data: purchasesData,
                            borderColor: '#8d8d8d',
                            backgroundColor: purchasesGradient,
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4,
                            pointRadius: 0,
                            pointHoverRadius: 6,
                            pointHoverBackgroundColor: '#8d8d8d',
                            pointHoverBorderColor: '#000000',
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
                            backgroundColor: '#161616',
                            titleColor: '#ffffff',
                            bodyColor: '#a3a3a3',
                            borderColor: '#262626',
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
                                color: '#a3a3a3',
                                font: {
                                    family: 'JetBrains Mono'
                                }
                            }
                        },
                        y: {
                            grid: {
                                color: '#262626'
                            },
                            ticks: {
                                color: '#a3a3a3',
                                font: {
                                    family: 'JetBrains Mono'
                                },
                                callback: function(value) {
                                    return '$' + value.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });
        },
        async setRange(nextRange) {
            if (nextRange === range) return;

            const currentRequestId = ++requestId;

            try {
                if (!chartUrl) throw new Error('chart data URL missing');
                const response = await fetch(chartUrl + '?range=' + encodeURIComponent(nextRange), {
                    headers: { 'Accept': 'application/json' },
                });
                if (!response.ok) throw new Error(`HTTP ${response.status}`);

                const payload = await response.json();
                // Ignore stale responses: a newer request superseded this one.
                if (currentRequestId !== requestId) return;

                chart.data.labels = payload.labels;
                chart.data.datasets[0].data = payload.sales;
                chart.data.datasets[1].data = payload.purchases;
                chart.update();

                this.$refs.rangeLabel.textContent = payload.rangeLabel;
                this.$refs.salesChart.setAttribute('aria-label', 'Gráfico de ventas y compras: ' + payload.rangeLabel);
                range = payload.range;

                // Preserve other query params in the URL without reloading.
                const url = new URL(window.location.href);
                url.searchParams.set('range', payload.range);
                history.replaceState({}, '', url.toString());
            } catch (error) {
                if (currentRequestId !== requestId) return;
                console.warn('Failed to update chart range:', error);
                // Revert the select to the range the chart still shows.
                if (this.$refs.rangeSelect) this.$refs.rangeSelect.value = range;
            }
        }
    };
};

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