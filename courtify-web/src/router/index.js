import { createRouter, createWebHistory } from 'vue-router'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    { path: '/', component: Home },
    { path: '/promos', component: PromoPage },
    { path: '/history', component: HistoryPage, meta: { requiresAuth: true } },
    { path: '/profile', component: ProfilePage, meta: { requiresAuth: true } },

    // Booking flow
    { path: '/venues/:id', component: VenueDetail },
    { path: '/venues/:id/schedule', component: SetSchedule, meta: { requiresAuth: true } },
    { path: '/venues/:id/checkout', component: Checkout, meta: { requiresAuth: true } },
    { path: '/booking/success', component: BookingSuccess, meta: { requiresAuth: true } },
  ],
})

export default router
