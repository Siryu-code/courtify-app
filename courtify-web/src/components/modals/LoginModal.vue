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
            <h5 class="modal-title fw-bold w-100 text-center">Masuk Courtify</h5>
            <p class="text-muted small w-100 text-center">Login ke akun Anda</p>
          </div>

          <!-- Form -->
          <form @submit.prevent="handleLogin" class="modal-body pt-2">
            <!-- Identifier -->
            <div class="mb-3">
              <div class="input-group">
                <span class="input-group-text bg-white">
                  <i class="bi bi-person"></i>
                </span>
                <input
                  v-model="identifier"
                  type="text"
                  class="form-control"
                  placeholder="Username atau Email"
                  :class="{ 'is-invalid': errors.identifier }"
                />
                <div v-if="errors.identifier" class="invalid-feedback">
                  {{ errors.identifier }}
                </div>
              </div>
            </div>

            <!-- Password -->
            <div class="mb-3">
              <div class="input-group">
                <span class="input-group-text bg-white">
                  <i class="bi bi-lock"></i>
                </span>
                <input
                  v-model="password"
                  :type="showPassword ? 'text' : 'password'"
                  class="form-control"
                  placeholder="Masukkan Password"
                  :class="{ 'is-invalid': errors.password }"
                />
                <button
                  type="button"
                  class="input-group-text bg-white"
                  @click="showPassword = !showPassword"
                >
                  <i :class="showPassword ? 'bi bi-eye-slash' : 'bi bi-eye'"></i>
                </button>
                <div v-if="errors.password" class="invalid-feedback">
                  {{ errors.password }}
                </div>
              </div>
            </div>

            <!-- Submit -->
            <button
              type="submit"
              class="btn btn-primary w-100 fw-bold"
              :disabled="loading"
            >
              <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
              Masuk
            </button>
          </form>

          <!-- Footer -->
          <div class="modal-footer border-0 pt-0 justify-content-center">
            <p class="mb-0 small">
              Belum punya akun?
              <a
                href="#"
                class="text-decoration-none fw-semibold"
                style="color: #0d6efd"
                @click.prevent="$emit('switch-to-register')"
              >
                Daftar di sini
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
  name: 'LoginModal',
  props: {
    isOpen: {
      type: Boolean,
      default: false
    }
  },
  emits: ['close', 'login', 'switch-to-register'],
  data() {
    return {
      identifier: '',
      password: '',
      showPassword: false,
      loading: false,
      errors: {}
    }
  },
  methods: {
    validate() {
      this.errors = {}
      if (!this.identifier.trim()) {
        this.errors.identifier = 'Username atau email wajib diisi'
      }
      if (!this.password) {
        this.errors.password = 'Password wajib diisi'
      }
      return Object.keys(this.errors).length === 0
    },
    async handleLogin() {
      if (!this.validate()) return
      this.loading = true
      // Simulasi async (bisa diganti dengan API call di parent)
      try {
        // Emit credentials ke parent
        this.$emit('login', {
          identifier: this.identifier.trim(),
          password: this.password
        })
        // Reset setelah sukses (atau biarkan parent yang close)
        this.resetForm()
      } finally {
        this.loading = false
      }
    },
    resetForm() {
      this.identifier = ''
      this.password = ''
      this.showPassword = false
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