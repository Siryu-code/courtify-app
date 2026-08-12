<template>
  <div class="venue-slider">
    <swiper
      :modules="[Pagination, Autoplay]"
      :slides-per-view="1"
      :space-between="0"
      :loop="true"
      :autoplay="{ delay: 4000, disableOnInteraction: false }"
      :pagination="{ clickable: true }"
      @swiper="onSwiper"
      class="swiper-container"
    >
      <swiper-slide v-for="(img, index) in images" :key="index">
        <img
          :src="img"
          :alt="`Venue image ${index + 1}`"
          class="slider-image"
          @error="onImageError(index)"
        />
      </swiper-slide>
    </swiper>
  </div>
</template>

<script>
import { Swiper, SwiperSlide } from 'swiper/vue'
import { Pagination, Autoplay } from 'swiper/modules'
import 'swiper/css'
import 'swiper/css/pagination'

export default {
  name: 'VenueImageSlider',
  components: { Swiper, SwiperSlide },
  setup() {
    return { Pagination, Autoplay }
  },
  props: {
    images: {
      type: Array,
      required: true,
      default: () => []
    }
  },
  methods: {
    onSwiper(swiper) {
      // swiper instance jika diperlukan nanti
    },
    onImageError(index) {
      // fallback gambar placeholder jika gagal load
      this.images[index] = 'https://placehold.co/600x400/e9ecef/6c757d?text=No+Image'
    }
  }
}
</script>

<style scoped>
.venue-slider {
  border-radius: 0.5rem;
  overflow: hidden;
  background-color: #e9ecef;
}

.slider-image {
  width: 100%;
  aspect-ratio: 16 / 9;
  object-fit: cover;
}

/* Custom dots */
:deep(.swiper-pagination-bullet) {
  background-color: #6c757d;
  opacity: 0.5;
}

:deep(.swiper-pagination-bullet-active) {
  background-color: #0d6efd;
  opacity: 1;
}

/* Responsive padding jika perlu */
@media (max-width: 767.98px) {
  .venue-slider {
    border-radius: 0.25rem;
  }
}
</style>