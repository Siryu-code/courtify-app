<template>
  <Teleport to="body">
    <div v-if="isOpen" class="modal-mask" @click.self="$emit('close')">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <!-- Close button -->
          <button
            type="button"
            class="btn-close position-absolute top-0 end-0 m-3"
            aria-label="Close"
            @click="$emit('close')"
          ></button>

          <!-- Header -->
          <div class="modal-header border-0 pb-0">
            <h5 class="modal-title fw-bold w-100 text-center">Gabung Courtify</h5>
            <p class="text-muted small w-100 text-center">Mulai booking lapangan favoritmu dengan mudah.</p>
          </div>

          <!-- Form -->
          <form @submit.prevent="handleRegister" class="modal-body pt-2">
            <!-- Full Name -->
            <div class="mb-3">
              <div class="input-group">
                <span class="input-group-text bg-white"><i class="bi bi-person-fill"></i></span>
                <input v-model="fullName" type="text" class="form-control" :class="{ 'is-invalid': errors.fullName }" placeholder="Nama Lengkap" />
                <div v-if="errors.fullName" class="invalid-feedback">{{ errors.fullName }}</div>
              </div>
            </div>

            <!-- Email -->
            <div class="mb-3">
              <div class="input-group">
                <span class="input-group-text bg-white"><i class="bi bi-envelope"></i></span>
                <input v-model="email" type="email" class="form-control" :class="{ 'is-invalid': errors.email }" placeholder="Email Address" />
                <div v-if="errors.email" class="invalid-feedback">{{ errors.email }}</div>
              </div>
            </div>

            <!-- Phone -->
            <div class="mb-3">
              <div class="input-group">
                <span class="input-group-text bg-white"><i class="bi bi-telephone"></i></span>
                <input v-model="phone" type="tel" class="form-control" :class="{ 'is-invalid': errors.phone }" placeholder="Nomor Telepon" />
                <div v-if="errors.phone" class="invalid-feedback">{{ errors.phone }}</div>
              </div>
            </div>

            <!-- Password -->
            <div class="mb-3">
              <div class="input-group">
                <span class="input-group-text bg-white"><i class="bi bi-lock"></i></span>
                <input v-model="password" :type="showPass ? 'text' : 'password'" class="form-control" :class="{ 'is-invalid': errors.password }" placeholder="Password" />
                <button type="button" class="input-group-text bg-white" @click="showPass = !showPass">
                  <i :class="showPass ? 'bi bi-eye-slash' : 'bi bi-eye'"></i>
                </button>
                <div v-if="errors.password" class="invalid-feedback">{{ errors.password }}</div>
              </div>
            </div>

            <!-- Confirm Password -->
            <div class="mb-3">
              <div class="input-group">
                <span class="input-group-text bg-white"><i class="bi bi-lock-fill"></i></span>
                <input v-model="confirmPassword" :type="showConfirm ? 'text' : 'password'" class="form-control" :class="{ 'is-invalid': errors.confirmPassword }" placeholder="Konfirmasi Password" />
                <button type="button" class="input-group-text bg-white" @click="showConfirm = !showConfirm">
                  <i :class="showConfirm ? 'bi bi-eye-slash' : 'bi bi-eye'"></i>
                </button>
                <div v-if="errors.confirmPassword" class="invalid-feedback">{{ errors.confirmPassword }}</div>
              </div>
            </div>

            <!-- Submit -->
            <button type="submit" class="btn btn-primary w-100 fw-bold" :disabled="loading">
              <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
              Daftar Sekarang
            </button>
          </form>

          <!-- Footer -->
          <div class="modal-footer border-0 pt-0 justify-content-center">
            <p class="mb-0 small">
              Sudah punya akun?
              <a href="#" class="text-decoration-none fw-semibold" style="color: #0d6efd" @click.prevent="$emit('switch-to-login')">
                Masuk di sini
              </a>
            </p>
          </div>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script>
export default {
  name: 'RegisterModal',
  props: {
    isOpen: Boolean
  },
  emits: ['close', 'register', 'switch-to-login'],
  data() {
    return {
      fullName: '',
      email: '',
      phone: '',
      password: '',
      confirmPassword: '',
      showPass: false,
      showConfirm: false,
      loading: false,
      errors: {}
    }
  },
  methods: {
    validate() {
      this.errors = {}
      if (!this.fullName.trim()) this.errors.fullName = 'Nama lengkap wajib diisi'
      if (!this.email.trim()) this.errors.email = 'Email wajib diisi'
      else if (!/^\S+@\S+\.\S+$/.test(this.email)) this.errors.email = 'Format email tidak valid'
      if (!this.phone.trim()) this.errors.phone = 'Nomor telepon wajib diisi'
      else if (this.phone.replace(/\D/g,'').length < 10) this.errors.phone = 'Nomor telepon minimal 10 digit'
      if (!this.password) this.errors.password = 'Password wajib diisi'
      else if (this.password.length < 6) this.errors.password = 'Password minimal 6 karakter'
      if (!this.confirmPassword) this.errors.confirmPassword = 'Konfirmasi password wajib diisi'
      else if (this.password !== this.confirmPassword) this.errors.confirmPassword = 'Password tidak sama'
      return Object.keys(this.errors).length === 0
    },
    async handleRegister() {
      if (!this.validate()) return
      this.loading = true
      try {
        this.$emit('register', {
          fullName: this.fullName.trim(),
          email: this.email.trim(),
          phoneNumber: this.phone.trim(),
          password: this.password,
          confirmPassword: this.confirmPassword
        })
        this.resetForm()
      } finally {
        this.loading = false
      }
    },
    resetForm() {
      this.fullName = ''
      this.email = ''
      this.phone = ''
      this.password = ''
      this.confirmPassword = ''
      this.showPass = false
      this.showConfirm = false
      this.errors = {}
    }
  },
  watch: {
    isOpen(val) {
      if (!val) this.resetForm()
    }
  }
}
</script>

<style scoped>
/* Sama seperti LoginModal, konsisten */
.modal-mask {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1055;
}
.modal-dialog {
  width: 90%;
  max-width: 400px;
  margin: 0;
}
.modal-content {
  border-radius: 1rem;
  border: none;
  box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);
}
.input-group-text {
  border-color: #e9ecef;
}
.form-control:focus {
  border-color: #0d6efd;
  box-shadow: 0 0 0 0.2rem rgba(13,110,253,0.15);
}
</style>