const navbar = document.querySelector('.navbar-custom');

if (navbar) {
	const updateNavbar = () => {
		navbar.classList.toggle('is-scrolled', window.scrollY > 0);
	};

	window.addEventListener('scroll', updateNavbar, { passive: true });
	updateNavbar();
}

document.addEventListener('click', function (e) {
	const link = e.target.closest('a[href^="copy:"]');
	if (!link) return;
	e.preventDefault();

	const value = link.getAttribute('href').replace('copy:', '');
	const original = link.textContent;

	const copied = () => {
		link.textContent = 'copied!';
		link.style.color = 'var(--color-c)';
		setTimeout(() => {
			link.textContent = original;
			link.style.color = '';
		}, 1500);
	};

	if (navigator.clipboard && navigator.clipboard.writeText) {
		navigator.clipboard.writeText(value).then(copied).catch(() => {
			window.prompt('Copy email:', value);
		});
	} else {
		window.prompt('Copy email:', value);
	}
});

function setupProjectsGrid() {
	const grid = document.getElementById('projects-grid');
	if (!grid) return;

	const cards = grid.querySelectorAll('.project-col');
	if (!cards.length) return;

	function applyLayout() {
		const width = window.innerWidth;
		const screenWidth = window.screen.availWidth || window.screen.width || width;
		const ratio = width / screenWidth;

		let cols = 3;

		// 1) Mobile (< 768px): 1 card per row
		// 2) Half-screen on ANY screen (FullHD, 2K, 3K, 4K) or viewport < 1200px: 2 cards per row
		// 3) Full screen desktop (>= 1200px and ratio > 0.68): 3 cards per row
		if (width < 768) {
			cols = 1;
		} else if (ratio <= 0.68 || width < 1200) {
			cols = 2;
		} else {
			cols = 3;
		}

		grid.classList.remove('grid-1-col', 'grid-2-col', 'grid-3-col');
		grid.classList.add(`grid-${cols}-col`);

		cards.forEach((card, index) => {
			let delay = 0;
			let offset = 20;

			if (cols === 1) {
				// Mobile: immediate smooth animation on scroll, no long delay
				delay = 0;
				offset = 10;
			} else if (cols === 2) {
				// 2 cards per row (half-screen):
				// First 2 cards appear on load at the end of the intro
				if (index < 2) {
					delay = 650 + (index * 150);
					offset = 0;
				} else {
					// Subsequent rows: stagger pair within the same row (0ms left, 100ms right)
					delay = (index % 2) * 100;
					offset = 20;
				}
			} else {
				// 3 cards per row (full-screen):
				// First 3 cards appear on load at the end of the intro
				if (index < 3) {
					delay = 650 + (index * 150);
					offset = 0;
				} else {
					// Subsequent rows: stagger trio (0ms, 100ms, 200ms)
					delay = (index % 3) * 100;
					offset = 30;
				}
			}

			card.setAttribute('data-aos-delay', String(delay));
			card.setAttribute('data-aos-offset', String(offset));
			if (!card.classList.contains('aos-animate')) {
				card.style.transitionDelay = `${delay}ms`;
			}
		});

		if (window.AOS) {
			window.AOS.refresh();
		}
	}

	window.__applyProjectsLayout = applyLayout;
	applyLayout();

	let resizeTimer;
	window.addEventListener('resize', function () {
		if (resizeTimer) cancelAnimationFrame(resizeTimer);
		resizeTimer = requestAnimationFrame(applyLayout);
	}, { passive: true });
}

if (document.readyState === 'loading') {
	document.addEventListener('DOMContentLoaded', setupProjectsGrid);
} else {
	setupProjectsGrid();
}