<template>
  <div class="home-page">
    <!-- TopBar -->
    <TopBar type="hamburger" @toggle-menu="$emit('toggle-menu')" />

    <!-- Hero Section -->
    <section class="hero-section d-flex align-items-center justify-content-center text-white text-center position-relative">
      <div class="hero-overlay position-absolute top-0 start-0 w-100 h-100"></div>
      <div class="container position-relative py-5">
        <h1 class="display-4 fw-bold">Book Your Court Instantly</h1>
        <p class="lead mb-4">Cari dan pesan lapangan terdekat dengan jadwal real-time dan fasilitas premium</p>
        <!-- Search Bar -->
        <div class="input-group mx-auto" style="max-width: 500px;">
          <span class="input-group-text bg-white border-0"><i class="bi bi-search"></i></span>
          <input
            v-model="heroSearch"
            type="text"
            class="form-control border-0"
            placeholder="Search venues, locations..."
            @keyup.enter="emitSearch"
          />
          <button class="btn btn-primary" @click="emitSearch">Search</button>
        </div>
      </div>
    </section>

    <!-- Promo Section -->
    <section class="py-4 bg-white">
      <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h5 class="fw-bold mb-0">Promo & Penawaran</h5>
          <router-link to="/deals" class="text-decoration-none small">Lihat Semua</router-link>
        </div>
        <swiper
          v-if="promos.length"
          :modules="[Pagination, Autoplay]"
          :slides-per-view="1.2"
          :space-between="12"
          :loop="true"
          :autoplay="{ delay: 3500 }"
          :pagination="{ clickable: true }"
          :breakpoints="{
            768: { slidesPerView: 2.2, spaceBetween: 16 },
            992: { slidesPerView: 3, spaceBetween: 20 }
          }"
          class="pb-4"
        >
          <swiper-slide v-for="promo in promos" :key="promo.id">
            <PromoCard :promo="promo" />
          </swiper-slide>
        </swiper>
        <p v-else class="text-muted">Tidak ada promo saat ini.</p>
      </div>
    </section>

    <!-- Venue Section -->
    <section class="py-4">
      <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h5 class="fw-bold mb-0">Lapangan Futsal Tersedia</h5>
          <select v-model="sortBy" class="form-select form-select-sm w-auto" @change="emitSortChange">
            <option value="az">A - Z</option>
            <option value="za">Z - A</option>
            <option value="price_asc">Harga Terendah</option>
            <option value="price_desc">Harga Tertinggi</option>
          </select>
        </div>
        <input
          v-model="venueSearch"
          type="text"
          class="form-control mb-3"
          placeholder="Cari nama lapangan..."
          @input="emitSearchVenue"
        />
        <div class="d-flex overflow-auto gap-3 pb-2 venue-scroll">
          <div v-for="venue in venues" :key="venue.id" style="min-width: 280px; max-width: 320px;">
            <VenueCard
              :venue="venue"
              @view-detail="onViewDetail(venue.id)"
              @book-now="onBookNow(venue.id)"
            />
          </div>
        </div>
        <p v-if="!venues.length" class="text-muted">Tidak ada venue tersedia.</p>
      </div>
    </section>

    <!-- FAQ Section -->
    <section class="py-4 bg-white">
      <div class="container">
        <h5 class="fw-bold mb-3">Frequently Asked Questions</h5>
        <div class="accordion" id="faqAccordion">
          <div class="accordion-item border-0 border-bottom" v-for="(faq, index) in faqs" :key="index">
            <h2 class="accordion-header">
              <button
                class="accordion-button collapsed bg-white shadow-none"
                type="button"
                data-bs-toggle="collapse"
                :data-bs-target="'#faq'+index"
                aria-expanded="false"
                :aria-controls="'faq'+index"
              >
                {{ faq.q }}
              </button>
            </h2>
            <div :id="'faq'+index" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
              <div class="accordion-body text-muted">
                {{ faq.a }}
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- BottomNavbar -->
    <BottomNavbar activeRoute="home" @navigate="onNavigate" />
  </div>
</template>

<script>
import TopBar from '@/components/layout/TopBar.vue'
import BottomNavbar from '@/components/layout/BottomNavBar.vue'
import VenueCard from '@/components/venue/VenueCard.vue'
import PromoCard from '@/components/promo/PromoCard.vue'
import { Swiper, SwiperSlide } from 'swiper/vue'
import { Pagination, Autoplay } from 'swiper/modules'
import 'swiper/css'
import 'swiper/css/pagination'

export default {
  name: 'HomePage',
  components: {
    TopBar,
    BottomNavbar,
    VenueCard,
    PromoCard,
    Swiper,
    SwiperSlide
  },
  setup() {
    return { Pagination, Autoplay }
  },
  emits: ['toggle-menu', 'navigate', 'search', 'search-venue', 'sort-change'],
  data() {
    return {
      heroSearch: '',
      venueSearch: '',
      sortBy: 'az',
      promos: [],   // akan diisi dari store/API
      venues: [],   // akan diisi dari store/API
      faqs: [
        { q: 'Bagaimana cara booking lapangan?', a: 'Pilih venue, tentukan jadwal, lalu lakukan pembayaran. Mudah!' },
        { q: 'Apa kebijakan pembatalan?', a: 'Pembatalan dapat dilakukan maksimal 2 jam sebelum jadwal. Dana akan dikembalikan penuh.' },
        { q: 'Apakah ada penyediaan peralatan?', a: 'Kami menyediakan bola dan rompi. Sepatu futsal harap bawa sendiri.' },
        { q: 'Apakah ada ruang locker dan shower?', a: 'Ya, setiap venue dilengkapi locker dan shower.' }
      ]
    }
  },
  mounted() {
    this.fetchData()
  },
  methods: {
    async fetchData() {
      // Gantikan dengan service API dari src/services/
      try {
        // const promosRes = await axios.get('/api/promos');
        // this.promos = promosRes.data.data;
        // const venuesRes = await axios.get('/api/venues');
        // this.venues = venuesRes.data.data;
        // Simulasi data sementara
        this.promos = [
          { id: 1, name: 'Diskon Awal', description: 'Potongan 20% untuk member baru', type: 'percent', value: 20, start_date: '2025-01-01', end_date: '2025-12-31' },
          { id: 2, name: 'Paket Hemat', description: 'Booking 2 jam gratis 1 jam', type: 'fixed', value: 50000, start_date: '2025-03-01', end_date: '2025-06-30' }
        ]
        this.venues = [
          { id: 1, name: 'Futsal Champion', location: 'Jakarta Selatan', rating: 4.8, type: 'indoor', price_per_hour: 150000, image: 'https://placehold.co/600x400?text=Venue', status: 'available' },
          { id: 2, name: 'Arena Star', location: 'Tangerang', rating: 4.5, type: 'outdoor', price_per_hour: 120000, image: 'https://placehold.co/600x400?text=Venue', status: 'available' }
        ]
      } catch (error) {
        console.error('Gagal mengambil data:', error)
      }
    },
    emitSearch() {
      this.$emit('search', this.heroSearch)
    },
    emitSearchVenue() {
      this.$emit('search-venue', this.venueSearch)
    },
    emitSortChange() {
      this.$emit('sort-change', this.sortBy)
    },
    onViewDetail(id) {
      // Handle view detail
    },
    onBookNow(id) {
      // Handle booking
    },
    onNavigate(route) {
      this.$emit('navigate', route)
    }
  }
}
</script>

<style scoped>
.hero-section {
  background: url('@/assets/images/hero-placeholder.jpg') center/cover no-repeat;
  min-height: 280px;
}
.hero-overlay {
  background: rgba(0,0,0,0.5);
}
@media (min-width: 768px) {
  .hero-section {
    min-height: 360px;
  }
}
.venue-scroll::-webkit-scrollbar {
  height: 4px;
}
.venue-scroll::-webkit-scrollbar-thumb {
  background: #ccc;
  border-radius: 2px;
}
</style>