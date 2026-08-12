<template>
  <div class="promo-page">
    <TopBar type="hamburger" @toggle-menu="$emit('toggle-menu')" />

    <!-- Title -->
    <div class="text-center py-4">
      <h4 class="fw-bold">Promosi & Penawaran</h4>
      <p class="text-muted small">Jangan lewatkan diskon menarik kami</p>
    </div>

    <!-- Promo Slider -->
    <div class="container mb-4">
      <swiper
        :modules="[Pagination, Autoplay]"
        :slides-per-view="1"
        :loop="true"
        :autoplay="{ delay: 4000 }"
        :pagination="{ clickable: true }"
        class="rounded-3 overflow-hidden promo-swiper"
      >
        <swiper-slide v-for="img in sliderImages" :key="img">
          <img :src="img" class="w-100" style="height: 200px; object-fit: cover;" alt="promo" />
        </swiper-slide>
      </swiper>
    </div>

    <!-- Promo Card List -->
    <div class="container pb-4">
      <div v-if="promos.length" class="d-flex flex-column gap-3">
        <PromoCard v-for="promo in promos" :key="promo.id" :promo="promo" />
      </div>
      <p v-else class="text-muted text-center">Belum ada promo</p>
    </div>

    <BottomNavbar activeRoute="deals" @navigate="$emit('navigate', $event)" />
  </div>
</template>

<script>
import TopBar from '@/components/layout/TopBar.vue'
import BottomNavbar from '@/components/layout/BottomNavBar.vue'
import PromoCard from '@/components/promo/PromoCard.vue'
import { Swiper, SwiperSlide } from 'swiper/vue'
import { Pagination, Autoplay } from 'swiper/modules'
import 'swiper/css'
import 'swiper/css/pagination'

export default {
  name: 'PromoPage',
  components: { TopBar, BottomNavbar, PromoCard, Swiper, SwiperSlide },
  setup() { return { Pagination, Autoplay } },
  emits: ['toggle-menu', 'navigate'],
  data() {
    return {
      promos: [],
      sliderImages: [
        'https://placehold.co/800x400/0d6efd/white?text=Promo+Spesial',
        'https://placehold.co/800x400/28a745/white?text=Diskon+50%25'
      ]
    }
  },
  mounted() {
    this.fetchPromos()
  },
  methods: {
    async fetchPromos() {
      // Gunakan service: const res = await axios.get('/api/promos');
      // Simulasi
      this.promos = [
        { id: 1, name: 'Diskon Awal', description: 'Potongan 20% untuk member baru', type: 'percent', value: 20, start_date: '2025-01-01', end_date: '2025-12-31' },
        { id: 2, name: 'Paket Hemat', description: 'Booking 2 jam gratis 1 jam', type: 'fixed', value: 50000, start_date: '2025-03-01', end_date: '2025-06-30' }
      ]
    }
  }
}
</script>

<style scoped>
.promo-swiper {
  --swiper-pagination-color: #0d6efd;
}
</style>