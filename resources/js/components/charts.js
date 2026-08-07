import {
    Chart,
    ArcElement,
    BarController,
    BarElement,
    CategoryScale,
    DoughnutController,
    Filler,
    Legend,
    LineController,
    LineElement,
    LinearScale,
    PointElement,
    Tooltip,
} from 'chart.js';

Chart.register(
    ArcElement,
    BarController,
    BarElement,
    CategoryScale,
    DoughnutController,
    Filler,
    Legend,
    LineController,
    LineElement,
    LinearScale,
    PointElement,
    Tooltip,
);

/** Ordered so adjacent series stay distinguishable. */
const PALETTE = ['#4f46e5', '#ef8519', '#22c55e', '#0ea5e9', '#a855f7', '#ef4444'];

function themeColors() {
    const dark = document.documentElement.classList.contains('dark');

    return {
        grid: dark ? 'rgba(148,163,184,0.15)' : 'rgba(100,116,139,0.15)',
        text: dark ? '#94a3b8' : '#64748b',
        surface: dark ? '#0f172a' : '#ffffff',
    };
}

/**
 * Renders every `<canvas data-chart>` on the page. Config comes from
 * `data-chart` (type) and `data-chart-data` (JSON), so a Blade view never has
 * to write JavaScript.
 */
export function initCharts() {
    document.querySelectorAll('canvas[data-chart]').forEach((canvas) => {
        const type = canvas.dataset.chart;
        const colors = themeColors();

        let payload;

        try {
            payload = JSON.parse(canvas.dataset.chartData ?? '{}');
        } catch {
            return;
        }

        const labels = payload.labels ?? [];
        const values = payload.values ?? [];

        const baseScales = {
            x: {
                grid: { display: false },
                ticks: { color: colors.text, font: { size: 11 } },
                border: { display: false },
            },
            y: {
                beginAtZero: true,
                grid: { color: colors.grid },
                ticks: { color: colors.text, font: { size: 11 }, precision: 0 },
                border: { display: false },
            },
        };

        const common = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: type === 'doughnut', labels: { color: colors.text, usePointStyle: true, boxWidth: 8 } },
                tooltip: {
                    backgroundColor: colors.surface,
                    titleColor: colors.text,
                    bodyColor: colors.text,
                    borderColor: colors.grid,
                    borderWidth: 1,
                    padding: 10,
                    displayColors: type === 'doughnut',
                },
            },
        };

        if (type === 'line') {
            new Chart(canvas, {
                type: 'line',
                data: {
                    labels,
                    datasets: [
                        {
                            label: payload.label ?? '',
                            data: values,
                            borderColor: PALETTE[0],
                            backgroundColor: 'rgba(79,70,229,0.12)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.35,
                            pointRadius: 0,
                            pointHoverRadius: 4,
                        },
                    ],
                },
                options: { ...common, scales: baseScales },
            });

            return;
        }

        if (type === 'bar') {
            new Chart(canvas, {
                type: 'bar',
                data: {
                    labels,
                    datasets: [
                        {
                            label: payload.label ?? '',
                            data: values,
                            backgroundColor: PALETTE[1],
                            borderRadius: 6,
                            maxBarThickness: 40,
                        },
                    ],
                },
                options: { ...common, scales: baseScales },
            });

            return;
        }

        if (type === 'doughnut') {
            new Chart(canvas, {
                type: 'doughnut',
                data: {
                    labels,
                    datasets: [
                        {
                            data: values,
                            backgroundColor: PALETTE,
                            borderWidth: 0,
                            hoverOffset: 6,
                        },
                    ],
                },
                options: { ...common, cutout: '62%' },
            });
        }
    });
}
