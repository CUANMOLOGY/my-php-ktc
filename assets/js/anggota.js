document.addEventListener("DOMContentLoaded", function () {
  // Konfirmasi hapus anggota
  document.querySelectorAll(".delete-btn").forEach((button) => {
    button.addEventListener("click", function (e) {
      e.preventDefault();
      const id = this.closest("tr").dataset.id;

      if (confirm("Apakah Anda yakin ingin menghapus anggota ini?")) {
        window.location.href = `hapus.php?id=${id}`;
      }
    });
  });

  // Auto-hide alerts after 5 seconds
  const alerts = document.querySelectorAll(".alert");
  alerts.forEach((alert) => {
    setTimeout(() => {
      alert.style.transition = "opacity 0.5s ease";
      alert.style.opacity = "0";
      setTimeout(() => alert.remove(), 500);
    }, 5000);
  });

  // Form validation
  const forms = document.querySelectorAll("form");
  forms.forEach((form) => {
    form.addEventListener("submit", function (e) {
      const requiredFields = this.querySelectorAll("[required]");
      let isValid = true;

      requiredFields.forEach((field) => {
        if (!field.value.trim()) {
          field.style.borderColor = "var(--danger)";
          isValid = false;
        }
      });

      if (!isValid) {
        e.preventDefault();
        alert("Harap isi semua field yang wajib diisi!");
      }
    });
  });
});
