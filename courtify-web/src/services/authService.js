import api from './api'

/**
 * Mendaftarkan user baru.
 * @param {Object} userData - { fullName, email, phoneNumber, password }
 * @returns {Promise<Object>} - { user, token }
 */
export async function register(userData) {
  try {
    const response = await api.post('/register', {
      username: userData.fullName,
      email: userData.email,
      no_hp: userData.phoneNumber,
      password: userData.password
    })
    const { token } = response.data
    if (token) {
      localStorage.setItem('auth_token', token)
    }
    return response.data // { user, token }
  } catch (error) {
    const message =
      error.response?.data?.message ||
      'Gagal mendaftar. Silakan coba lagi.'
    throw new Error(message)
  }
}

/**
 * Login user dengan username/email dan password.
 * @param {Object} credentials - { identifier, password }
 * @returns {Promise<Object>} - { user, token }
 */
export async function login(credentials) {
  try {
    const response = await api.post('/login', {
      identifier: credentials.identifier,
      password: credentials.password
    })
    const { token } = response.data
    if (token) {
      localStorage.setItem('auth_token', token)
    }
    return response.data
  } catch (error) {
    const message =
      error.response?.data?.message ||
      'Email atau password salah.'
    throw new Error(message)
  }
}

/**
 * Logout user, hapus token dari localStorage.
 * @returns {Promise<void>}
 */
export async function logout() {
  try {
    await api.post('/logout')
  } catch (error) {
    // Jika gagal logout (misal token expired), tetap hapus token lokal
    console.warn('Logout gagal, hapus token lokal.')
  } finally {
    localStorage.removeItem('auth_token')
  }
}