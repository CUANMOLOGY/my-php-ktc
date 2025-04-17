/**
 * Script untuk menangani chart buku populer
 * File: assets/js/buku_populer.js
 */

document.addEventListener("DOMContentLoaded", function () {
  // Inisialisasi chart
  if (document.getElementById("popularityChart")) {
    initPopularityChart();
  }
});

function initPopularityChart() {
  const ctx = document.getElementById("popularityChart").getContext("2d");

  // Gunakan data yang sudah didefinisikan di halaman
  if (typeof chartData !== "undefined") {
    new Chart(ctx, {
      type: "bar",
      data: {
        labels: chartData.labels,
        datasets: [
          {
            label: "Jumlah Peminjaman",
            data: chartData.data,
            backgroundColor: "#3498db",
            borderColor: "#2980b9",
            borderWidth: 1,
          },
        ],
      },
      options: {
        responsive: true,
        scales: {
          y: {
            beginAtZero: true,
            title: {
              display: true,
              text: "Jumlah Dipinjam",
            },
          },
          x: {
            title: {
              display: true,
              text: "Judul Buku",
            },
          },
        },
      },
    });
  }
}
