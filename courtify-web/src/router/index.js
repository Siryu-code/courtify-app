import { createRouter, createWebHistory } from 'vue-router'
import Home from '@/pages/Home.vue'
import PromoPage from '@/pages/PromoPage.vue'
import HistoryPage from '@/pages/HistoryPage.vue'
import ProfilePage from '@/pages/ProfilePage.vue'
import VenueDetail from '@/pages/booking/VenueDetail.vue'
import SetSchedule from '@/pages/booking/SetSchedule.vue'
import Checkout from '@/pages/booking/Checkout.vue'
import BookingSuccess from '@/pages/booking/BookingSuccess.vue'

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
router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('auth_token')
  
  if (to.meta.requiresAuth && !token) {
    // Belum login, redirect ke home atau show login modal
    next('/')
  } else {
    next()
  }
})