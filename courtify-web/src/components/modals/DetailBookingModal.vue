<template>
  <Teleport to="body">
    <div v-if="isOpen" class="modal-mask" @click.self="$emit('close')">
      <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
          <!-- Close button -->
          <button
            type="button"
            class="btn-close position-absolute top-0 end-0 m-3"
            aria-label="Close"
            @click="$emit('close')"
          ></button>

          <div class="modal-body p-4">
            <!-- Invoice Header -->
            <div class="text-center mb-3">
              <h4 class="fw-bold text-uppercase mb-0">Bukti Pembayaran</h4>
              <p class="small text-muted mb-0">{{ invoiceNumber }}</p>
              <p class="small text-muted">{{ formattedCreatedAt }}</p>
              <hr class="my-2" />
            </div>

            <!-- Issued By -->
            <div class="mb-3">
              <h6 class="fw-bold text-uppercase small text-muted">Diterbitkan Oleh:</h6>
              <p class="mb-0">Courtify Arena</p>
              <p class="mb-0">123 Sports Way</p>
              <a href="mailto:support@courtify.com" class="text-decoration-none small">support@courtify.com</a>
              <hr class="my-2" />
            </div>

            <!-- Paid By -->
            <div class="mb-3">
              <h6 class="fw-bold text-uppercase small text-muted">Dibayarkan Oleh:</h6>
              <p class="mb-0">{{ booking.customer_name }}</p>
              <p class="mb-0">{{ booking.user?.email }}</p>
              <p class="mb-0">{{ booking.customer_phone }}</p>
              <hr class="my-2" />
            </div>

            <!-- Payment Method (static) -->
            <div class="mb-3">
              <p class="mb-0 small"><span class="fw-semibold">Metode:</span> Kartu Kredit (Visa berakhiran +4242)</p>
              <hr class="my-2" />
            </div>

            <!-- Booking Details -->
            <div class="mb-3">
              <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                  <h6 class="text-uppercase small fw-bold text-muted mb-1">Venue</h6>
                  <p class="fw-bold fs-5 mb-0">{{ booking.venue.name }}</p>
                </div>
              </div>
              <div class="row g-3">
                <div class="col-6">
                  <h6 class="text-uppercase small fw-bold text-muted mb-1">Date</h6>
                  <p class="mb-0">{{ formattedDate }}</p>
                </div>
                <div class="col-6">
                  <h6 class="text-uppercase small fw-bold text-muted mb-1">Time</h6>
                  <p class="mb-0">{{ formattedTime }}</p>
                </div>
              </div>
              <hr class="my-2" />
            </div>

            <!-- Price Breakdown -->
            <div class="mb-3">
              <div class="d-flex justify-content-between mb-1">
                <span>Subtotal</span>
                <span>Rp{{ formatPrice(subtotal) }}</span>
              </div>
              <div class="d-flex justify-content-between mb-1">
                <span v-if="booking.promo">{{ booking.promo.name }}</span>
                <span v-else>Promo</span>
                <span v-if="booking.promo" class="text-success">-{{ promoDisplay }}</span>
                <span v-else>-</span>
              </div>
              <hr class="my-2" />
              <div class="d-flex justify-content-between fw-bold">
                <span>Total Akhir</span>
                <span class="text-primary">Rp{{ formatPrice(booking.total_price) }}</span>
              </div>
            </div>

            <!-- Footer Note -->
            <div class="text-center mt-4">
              <p class="small text-muted mb-0">
                Terima kasih atas kepercayaan Anda. Jika ada pertanyaan, silakan hubungi
                <a href="mailto:support@courtify.com">support@courtify.com</a>.
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script>
export default {
  name: 'DetailBookingModal',
  props: {
    booking: {
      type: Object,
      required: true,
      validator: (v) =>
        v &&
        typeof v.id === 'number' &&
        v.customer_name &&
        v.start_time &&
        v.end_time &&
        v.venue &&
        v.venue.name
    },
    isOpen: Boolean
  },
  emits: ['close'],
  computed: {
    invoiceNumber() {
      const idStr = String(this.booking.id)
      const lastFour = idStr.slice(-4).padStart(4, '0')
      return `INV-${lastFour}`
    },
    formattedCreatedAt() {
      return this.formatDate(this.booking.created_at, 'long')
    },
    formattedDate() {
      return this.formatDate(this.booking.start_time, 'short')
    },
    formattedTime() {
      const start = new Date(this.booking.start_time)
      const end = new Date(this.booking.end_time)
      if (isNaN(start) || isNaN(end)) return '-'
      const options = { hour: '2-digit', minute: '2-digit', hour12: false }
      return `${start.toLocaleTimeString('id-ID', options)} - ${end.toLocaleTimeString('id-ID', options)}`
    },
    subtotal() {
      // Asumsi: subtotal = total_price + potongan promo (jika ada)
      let extra = 0
      if (this.booking.promo) {
        if (this.booking.promo.type === 'fixed') {
          extra = this.booking.promo.value
        } else if (this.booking.promo.type === 'percent') {
          // Hitung kasar: total_price adalah setelah diskon, maka subtotal = total_price / (1 - percent/100)
          extra = (this.booking.total_price / (1 - this.booking.promo.value / 100)) - this.booking.total_price
          // Agar sederhana: anggap total_price sudah final, tampilkan saja sebagai informasi
        }
      }
      return this.booking.total_price + extra
    },
    promoDisplay() {
      if (!this.booking.promo) return ''
      if (this.booking.promo.type === 'percent') return `-${this.booking.promo.value}%`
      return `-Rp${this.booking.promo.value.toLocaleString('id-ID')}`
    }
  },
  methods: {
    formatDate(dateStr, style = 'long') {
      if (!dateStr) return ''
      const d = new Date(dateStr)
      if (isNaN(d)) return dateStr
      if (style === 'long') {
        // "24 Okt 2026"
        const day = d.getDate()
        const month = d.toLocaleDateString('id-ID', { month: 'short' })
        const year = d.getFullYear()
        return `${day} ${month} ${year}`
      }
      // short: "14 Okt"
      const day = d.getDate()
      const month = d.toLocaleDateString('id-ID', { month: 'short' })
      return `${day} ${month}`
    },
    formatPrice(val) {
      return Number(val).toLocaleString('id-ID')
    }
  }
}
</script>

<style scoped>
.modal-mask {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1055;
  overflow-y: auto;
}
.modal-dialog {
  width: 95%;
  max-width: 480px;
  margin: 0;
  max-height: 90vh;
}
.modal-content {
  border-radius: 1rem;
  border: none;
  box-shadow: 0 0.5rem 1.5rem rgba(0,0,0,0.15);
}
hr {
  opacity: 0.4;
}
p {
  margin-bottom: 0.25rem;
}
</style>