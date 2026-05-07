// Auto-dismiss alerts
document.querySelectorAll(".alert-auto-dismiss").forEach((el) => {
  setTimeout(() => {
    el.style.opacity = "0";
    setTimeout(() => el.remove(), 400);
  }, 4000);
  el.style.transition = "opacity .4s";
});
// Close sidebar on outside click (mobile)
document.addEventListener("click", function (e) {
  const sidebar = document.getElementById("sidebar");
  if (window.innerWidth < 768 && sidebar.classList.contains("show")) {
    if (!sidebar.contains(e.target)) sidebar.classList.remove("show");
  }
});
