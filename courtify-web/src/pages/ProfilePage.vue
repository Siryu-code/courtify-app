<template>
  <div class="profile-page">
    <TopBar type="hamburger" @toggle-menu="$emit('toggle-menu')" />

    <!-- Profile Header -->
    <div class="text-center py-4 bg-white">
      <label class="avatar-wrapper d-inline-block position-relative">
        <img
          :src="user.photo || defaultAvatar"
          alt="Profile"
          class="rounded-circle border border-3 border-white shadow"
          width="120"
          height="120"
          style="object-fit: cover; cursor: pointer;"
          @click="triggerFileInput"
        />
        <input
          type="file"
          accept="image/*"
          ref="fileInput"
          class="d-none"
          @change="uploadPhoto"
        />
      </label>
      <p class="text-muted mt-2 mb-0">{{ user.email }}</p>
    </div>

    <!-- User Info (inline edit) -->
    <div class="container px-3 mb-3">
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <!-- Nama -->
          <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
            <span class="fw-semibold">Nama</span>
            <div v-if="editingField === 'name'" class="d-flex gap-2">
              <input v-model="editName" type="text" class="form-control form-control-sm" />
              <button class="btn btn-sm btn-primary" @click="saveField('name')">Simpan</button>
              <button class="btn btn-sm btn-outline-secondary" @click="cancelEdit">Batal</button>
            </div>
            <span v-else class="text-muted" @click="startEdit('name', user.name)">
              {{ user.name }}
              <i class="bi bi-pencil ms-1 small"></i>
            </span>
          </div>
          <!-- Nomor HP -->
          <div class="d-flex justify-content-between align-items-center py-2">
            <span class="fw-semibold">Nomor HP</span>
            <div v-if="editingField === 'phone'" class="d-flex gap-2">
              <input v-model="editPhone" type="text" class="form-control form-control-sm" />
              <button class="btn btn-sm btn-primary" @click="saveField('phone')">Simpan</button>
              <button class="btn btn-sm btn-outline-secondary" @click="cancelEdit">Batal</button>
            </div>
            <span v-else class="text-muted" @click="startEdit('phone', user.phone)">
              {{ user.phone || 'Belum diisi' }}
              <i class="bi bi-pencil ms-1 small"></i>
            </span>
          </div>
        </div>
      </div>
    </div>

    <!-- Stats -->
    <div class="container px-3 mb-4">
      <div class="card border-0 shadow-sm text-center p-3">
        <h2 class="fw-bold text-primary mb-0">{{ totalBookings }}</h2>
        <small class="text-muted">Total Bookings</small>
      </div>
    </div>

    <!-- Menu Items -->
    <div class="container px-3 mb-4">
      <div class="list-group shadow-sm">
        <button class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" @click="togglePaymentMethods">
          <span><i class="bi bi-credit-card me-2"></i> Metode Pembayaran</span>
          <i class="bi bi-chevron-right"></i>
        </button>
        <button class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" @click="$emit('navigate', 'history')">
          <span><i class="bi bi-clock-history me-2"></i> Riwayat Booking</span>
          <i class="bi bi-chevron-right"></i>
        </button>
      </div>
    </div>

    <!-- Payment Methods (collapsible) -->
    <div v-if="showPayment" class="container px-3 mb-4">
      <div class="card border-0 shadow-sm p-3">
        <h6 class="fw-bold">Metode Pembayaran</h6>
        <div v-for="card in paymentMethods" :key="card.id" class="d-flex justify-content-between align-items-center py-2 border-bottom">
          <div>
            <i :class="card.icon" class="me-2"></i>
            {{ card.label }}
          </div>
          <span v-if="card.default" class="badge bg-primary">Default</span>
        </div>
      </div>
    </div>

    <!-- Logout -->
    <div class="container px-3 mb-5">
      <button class="btn btn-outline-danger w-100 py-2 fw-bold" @click="logout">LOG OUT</button>
    </div>

    <BottomNavbar activeRoute="profile" @navigate="$emit('navigate', $event)" />
  </div>
</template>

<script>
import TopBar from '@/components/layout/TopBar.vue'
import BottomNavbar from '@/components/layout/BottomNavBar.vue'

export default {
  name: 'ProfilePage',
  components: { TopBar, BottomNavbar },
  emits: ['toggle-menu', 'navigate', 'logout'],
  data() {
    return {
      user: {
        name: 'Ahmad Fauzi',
        email: 'ahmad@example.com',
        phone: '08123456789',
        photo: null
      },
      defaultAvatar: 'https://placehold.co/120x120/e9ecef/6c757d?text=User',
      editingField: null,
      editName: '',
      editPhone: '',
      totalBookings: 12, // dari store
      showPayment: false,
      paymentMethods: [
        { id: 1, label: 'Visa berakhiran +4242', icon: 'bi bi-credit-card', default: true },
        { id: 2, label: 'MasterCard berakhiran +5555', icon: 'bi bi-credit-card', default: false },
        { id: 3, label: 'Dana (e-wallet)', icon: 'bi bi-wallet2', default: false }
      ]
    }
  },
  methods: {
    triggerFileInput() {
      this.$refs.fileInput.click()
    },
    async uploadPhoto(e) {
      const file = e.target.files[0]
      if (!file) return
      // const formData = new FormData(); formData.append('photo', file);
      // await axios.post('/api/user/photo', formData);
      alert('Foto akan diupload (simulasi)')
    },
    startEdit(field, value) {
      this.editingField = field
      if (field === 'name') this.editName = value
      if (field === 'phone') this.editPhone = value
    },
    cancelEdit() {
      this.editingField = null
    },
    async saveField(field) {
      const newValue = field === 'name' ? this.editName : this.editPhone
      // await axios.patch('/api/user', { [field === 'name' ? 'name' : 'phone']: newValue })
      this.user[field === 'name' ? 'name' : 'phone'] = newValue
      this.editingField = null
      alert(`${field} disimpan!`)
    },
    togglePaymentMethods() {
      this.showPayment = !this.showPayment
    },
    logout() {
      // dispatch store action logout
      this.$emit('logout')
    }
  }
}
</script>

<style scoped>
.avatar-wrapper:hover img {
  opacity: 0.8;
}
</style>