const state = {
  menu: [],
  reviews: []
};

const defaultMenuImage =
  'https://images.unsplash.com/photo-1548365328-8b849e4f8c57?auto=format&fit=crop&w=900&q=80';

const createStatus = (type, message) => `<div class="status-box ${type}">${message}</div>`;

const setLoading = (targetId, message = 'Loading...') => {
  const target = document.getElementById(targetId);
  if (target) target.innerHTML = createStatus('loading', message);
};

const setError = (targetId, message = 'Something went wrong.') => {
  const target = document.getElementById(targetId);
  if (target) target.innerHTML = createStatus('error', message);
};

const renderMenu = (menuItems) => {
  const container = document.getElementById('menu-list');
  if (!container) return;

  if (!Array.isArray(menuItems) || menuItems.length === 0) {
    container.innerHTML = createStatus('error', 'No menu items are available right now.');
    return;
  }

  container.innerHTML = menuItems
    .map((item) => {
      const image = item.image || item.imageUrl || defaultMenuImage;
      const name = item.name || 'Dish';
      const description = item.description || 'Freshly prepared with premium ingredients.';
      const rawPrice = item.price;
      const priceValue =
        typeof rawPrice === 'number'
          ? `$${rawPrice.toFixed(2)}`
          : typeof rawPrice === 'string'
            ? rawPrice
            : '--';

      return `
        <div class="col-sm-6 col-lg-4 reveal-item">
          <article class="card feature-card h-100">
            <img src="${image}" alt="${name}" loading="lazy" />
            <div class="card-body">
              <h5 class="card-title mb-2">${name}</h5>
              <p class="card-text text-secondary mb-3">${description}</p>
              <div class="d-flex justify-content-between align-items-center">
                <span class="fw-semibold text-dark">${priceValue}</span>
                <button class="btn btn-sm btn-fire rounded-pill px-3" type="button">Order</button>
              </div>
            </div>
          </article>
        </div>`;
    })
    .join('');

  initRevealAnimations();
};

const renderReviews = (reviews) => {
  const container = document.getElementById('reviews-list');
  if (!container) return;

  if (!Array.isArray(reviews) || reviews.length === 0) {
    container.innerHTML = createStatus('error', 'No reviews yet. Be the first to leave one.');
    return;
  }

  container.innerHTML = reviews
    .map((review) => {
      const name = review.name || 'Anonymous Guest';
      const comment = review.comment || 'Great food and warm atmosphere.';
      const rating = Number(review.rating) || 5;
      const stars = '*'.repeat(Math.max(1, Math.min(5, rating)));

      return `
        <div class="col-md-6 col-lg-4 reveal-item">
          <article class="card review-card h-100">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="mb-0">${name}</h6>
                <span class="badge rounded-pill badge-rating">${stars}</span>
              </div>
              <p class="mb-0 text-secondary">"${comment}"</p>
            </div>
          </article>
        </div>`;
    })
    .join('');

  initRevealAnimations();
};

const loadMenu = async () => {
  setLoading('menu-list', 'Loading menu...');

  try {
    const response = await fetch('/api/menu');
    if (!response.ok) throw new Error('Menu request failed.');

    const data = await response.json();
    const menuItems = Array.isArray(data) ? data : data.items || [];
    state.menu = menuItems;
    renderMenu(menuItems);
  } catch (error) {
    console.error(error);
    setError('menu-list', 'Unable to load menu right now. Please try again later.');
  }
};

const loadReviews = async () => {
  setLoading('reviews-list', 'Loading reviews...');

  try {
    const response = await fetch('/api/reviews');
    if (!response.ok) throw new Error('Reviews request failed.');

    const data = await response.json();
    const reviewItems = Array.isArray(data) ? data : data.reviews || [];
    state.reviews = reviewItems;
    renderReviews(reviewItems);
  } catch (error) {
    console.error(error);
    setError('reviews-list', 'Unable to load reviews right now. Please try again later.');
  }
};

const setFormStatus = (elementId, type, message) => {
  const el = document.getElementById(elementId);
  if (!el) return;

  if (!message) {
    el.innerHTML = '';
    return;
  }

  let className = 'loading';
  if (type === 'error') className = 'error';
  if (type === 'success') className = 'success';
  el.innerHTML = createStatus(className, message);
};

const handleReservationSubmit = () => {
  const form = document.getElementById('reservation-form');
  if (!form) return;

  form.addEventListener('submit', async (event) => {
    event.preventDefault();

    const submitBtn = form.querySelector('button[type="submit"]');
    if (submitBtn) submitBtn.disabled = true;
    setFormStatus('reservation-status', 'loading', 'Sending reservation...');

    const payload = {
      name: form.name.value.trim(),
      email: form.email.value.trim(),
      phone: form.phone.value.trim(),
      date: form.date.value,
      time: form.time.value,
      guests: form.guests.value
    };

    try {
      const response = await fetch('/api/reservation', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      });

      if (!response.ok) throw new Error('Reservation submission failed.');

      form.reset();
      setFormStatus('reservation-status', 'success', 'Reservation submitted successfully. We will contact you shortly.');
    } catch (error) {
      console.error(error);
      setFormStatus('reservation-status', 'error', 'Could not submit reservation. Please try again.');
    } finally {
      if (submitBtn) submitBtn.disabled = false;
    }
  });
};

const handleReviewSubmit = () => {
  const form = document.getElementById('review-form');
  if (!form) return;

  form.addEventListener('submit', async (event) => {
    event.preventDefault();

    const submitBtn = form.querySelector('button[type="submit"]');
    if (submitBtn) submitBtn.disabled = true;
    setFormStatus('review-status', 'loading', 'Submitting review...');

    const payload = {
      name: form.reviewName.value.trim(),
      rating: Number(form.reviewRating.value),
      comment: form.reviewComment.value.trim()
    };

    try {
      const response = await fetch('/api/reviews', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      });

      if (!response.ok) throw new Error('Review submission failed.');

      form.reset();
      setFormStatus('review-status', 'success', 'Thanks for sharing your feedback.');
      await loadReviews();
    } catch (error) {
      console.error(error);
      setFormStatus('review-status', 'error', 'Could not submit review. Please try again.');
    } finally {
      if (submitBtn) submitBtn.disabled = false;
    }
  });
};

const initRevealAnimations = () => {
  const items = document.querySelectorAll('.reveal-item:not(.revealed)');
  if (!items.length) return;

  const observer = new IntersectionObserver(
    (entries, obs) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add('revealed');
          obs.unobserve(entry.target);
        }
      });
    },
    { threshold: 0.12 }
  );

  items.forEach((item) => observer.observe(item));
};

const initActiveNav = () => {
  const links = Array.from(document.querySelectorAll('.navbar .nav-link[data-section]'));
  const sections = links
    .map((link) => document.getElementById(link.dataset.section))
    .filter(Boolean);

  if (!links.length || !sections.length) return;

  const activate = (id) => {
    links.forEach((link) => {
      link.classList.toggle('active', link.dataset.section === id);
    });
  };

  const observer = new IntersectionObserver(
    (entries) => {
      const visible = entries
        .filter((entry) => entry.isIntersecting)
        .sort((a, b) => b.intersectionRatio - a.intersectionRatio)[0];

      if (visible?.target?.id) activate(visible.target.id);
    },
    { threshold: [0.25, 0.45, 0.65], rootMargin: '-90px 0px -40% 0px' }
  );

  sections.forEach((section) => observer.observe(section));

  links.forEach((link) => {
    link.addEventListener('click', () => {
      const navCollapse = document.getElementById('mainNavbar');
      if (navCollapse?.classList.contains('show') && window.bootstrap) {
        window.bootstrap.Collapse.getOrCreateInstance(navCollapse).hide();
      }
    });
  });
};

const initApp = () => {
  initRevealAnimations();
  initActiveNav();
  handleReservationSubmit();
  handleReviewSubmit();
  loadMenu();
  loadReviews();
};

document.addEventListener('components:loaded', initApp);
document.addEventListener('DOMContentLoaded', () => {
  if (document.querySelector('#siteNavbar')) initApp();
});
