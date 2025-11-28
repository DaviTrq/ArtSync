document.addEventListener('DOMContentLoaded', () => {
    const streamsChartCanvas = document.getElementById('streamsChart');
    if (streamsChartCanvas) {
        const ctx = streamsChartCanvas.getContext('2d');
        const weekLabel = window.dashboardTranslations?.week || 'Week';
        const labels = [weekLabel + ' 1', weekLabel + ' 2', weekLabel + ' 3', weekLabel + ' 4'];
        const spotifyData = [1200, 1900, 3000, 5000];
        const appleMusicData = [800, 1200, 2500, 3200];
        const deezerData = [400, 500, 900, 1100];
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Spotify',
                        data: spotifyData,
                        borderColor: '#1DB954',
                        backgroundColor: 'rgba(29, 185, 84, 0.15)',
                        borderWidth: 2.5,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 5,
                        pointHoverRadius: 7,
                        pointBackgroundColor: '#1DB954',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2
                    },
                    {
                        label: 'Apple Music',
                        data: appleMusicData,
                        borderColor: '#FC3C44',
                        backgroundColor: 'rgba(252, 60, 68, 0.15)',
                        borderWidth: 2.5,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 5,
                        pointHoverRadius: 7,
                        pointBackgroundColor: '#FC3C44',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2
                    },
                    {
                        label: 'Deezer',
                        data: deezerData,
                        borderColor: '#A238FF',
                        backgroundColor: 'rgba(162, 56, 255, 0.15)',
                        borderWidth: 2.5,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 5,
                        pointHoverRadius: 7,
                        pointBackgroundColor: '#A238FF',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        align: 'end',
                        labels: {
                            color: getComputedStyle(document.documentElement).getPropertyValue('--primary-text-color'),
                            font: {
                                family: 'Poppins',
                                size: 13,
                                weight: '500'
                            },
                            padding: 15,
                            usePointStyle: true,
                            pointStyle: 'circle',
                            boxWidth: 8,
                            boxHeight: 8
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleFont: {
                            family: 'Poppins',
                            size: 13,
                            weight: '600'
                        },
                        bodyFont: {
                            family: 'Poppins',
                            size: 12
                        },
                        padding: 12,
                        cornerRadius: 8,
                        displayColors: true,
                        boxPadding: 6
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: getComputedStyle(document.documentElement).getPropertyValue('--secondary-text-color'),
                            font: {family: 'Poppins', size: 11},
                            padding: 8
                        },
                        grid: {
                            color: getComputedStyle(document.documentElement).getPropertyValue('--border-color'),
                            drawBorder: false
                        },
                        border: {display: false}
                    },
                    x: {
                        ticks: {
                            color: getComputedStyle(document.documentElement).getPropertyValue('--secondary-text-color'),
                            font: {family: 'Poppins', size: 11, weight: '500'},
                            padding: 8
                        },
                        grid: {display: false},
                        border: {display: false}
                    }
                }
            }
        });
    }
});
