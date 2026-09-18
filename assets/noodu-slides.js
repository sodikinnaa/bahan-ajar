/* Menyusun header dan footer setiap slide secara otomatis.
   Penulis deck cukup menulis <section class="slide" data-label="..."> beserta isinya. */

(function () {
  const root = document.body.dataset;
  const meta = root.meta || '';
  const brand = root.brand || 'Noodu Academy';
  const base = root.assets || '../assets';
  const slides = [...document.querySelectorAll('.slide')];

  slides.forEach((slide, i) => {
    const num = i + 1;

    const top = document.createElement('header');
    top.className = 'topbar';
    top.innerHTML = `
      <img class="topbar__logo" src="${base}/noodu-logo.png" alt="noodu">
      <span class="topbar__rule"></span>
      <span class="topbar__meta">${meta}</span>
      <span class="topbar__label">${slide.dataset.label || ''}</span>`;

    const foot = document.createElement('footer');
    foot.className = 'footer';
    foot.innerHTML = `
      <span>${brand}</span>
      <span class="footer__track"><span class="footer__bar"
        style="width:${(num / slides.length) * 100}%"></span></span>
      <span class="footer__num">${String(num).padStart(2, '0')} / ${slides.length}</span>`;

    slide.prepend(top);
    slide.append(foot);
  });
})();
