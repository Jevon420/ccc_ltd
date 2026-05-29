import Alpine from 'alpinejs';
import { driver } from 'driver.js';

// =============================================================================
// Alpine.js — global reactive UI
// =============================================================================
window.Alpine = Alpine;
Alpine.start();

// =============================================================================
// CCC Ops — Guided Tour Engine (driver.js)
// =============================================================================

/**
 * Launch a guided tour by name.
 * Tour data is injected into the page as window.cccTours (JSON).
 *
 * Usage from Blade: onclick="window.cccOps.startTour('dashboard_intro')"
 */
window.cccOps = {
    startTour(tourName) {
        const tours = window.cccTours ?? [];
        const tour  = tours.find(t => t.name === tourName);

        if (!tour || !tour.steps?.length) {
            console.warn(`[CCC Ops] Tour "${tourName}" not found or has no steps.`);
            return;
        }

        const driverObj = driver({
            showProgress: true,
            animate: true,
            smoothScroll: true,
            allowClose: true,
            steps: tour.steps,
            onDestroyed() {
                // Mark tour as completed/skipped via AJAX in Phase 2
                // fetch('/app/tours/' + tour.id + '/progress', { method: 'POST', ... })
            },
        });

        driverObj.drive();
    },

    /**
     * Auto-start dashboard intro tour for first-time visitors.
     * Called on DOMContentLoaded if the tour hasn't been completed.
     */
    autoStartIfNew(tourName) {
        const key = `ccc_tour_seen_${tourName}`;
        if (!localStorage.getItem(key)) {
            setTimeout(() => {
                this.startTour(tourName);
                localStorage.setItem(key, '1');
            }, 1000);
        }
    },
};

// Auto-start the dashboard intro tour if on the dashboard page
document.addEventListener('DOMContentLoaded', () => {
    if (document.body.dataset.route === 'dashboard') {
        window.cccOps.autoStartIfNew('dashboard_intro');
    }
});
