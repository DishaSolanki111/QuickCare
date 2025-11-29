// Animate elements on intersection (fade-in/slide-in)
document.addEventListener('DOMContentLoaded', () => {
  const observer = new IntersectionObserver((entries, obs) => {
    entries.forEach(e => {
      if (e.isIntersecting) {
        e.target.style.opacity = '1';
        e.target.style.transform = 'translate(0,0)';
        obs.unobserve(e.target);
      }
    });
  }, { threshold: 0.18 });

  document.querySelectorAll('.fade-in, .slide-in').forEach(el => observer.observe(el));

  // simple year filler
  document.getElementById('year').textContent = new Date().getFullYear();
});
