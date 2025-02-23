import axios from 'axios'

const axiosInstance = axios.create({
  baseURL: 'http://localhost:8000/api',
  headers: {
    'Content-Type': 'application/json',
  },
})

// Hàm cập nhật timestamp
const updateLastActivity = () => {
  localStorage.setItem('lastActivity', Date.now())
}

// Kiểm tra timeout
const checkInactivity = () => {
  const lastActivity = localStorage.getItem('lastActivity')
  const currentTime = Date.now()

  // Nếu thời gian không hoạt động vượt quá 15 phút (900000ms), đăng xuất
  if (lastActivity && currentTime - lastActivity > 900000) {
    console.warn('Session expired due to inactivity')
    localStorage.removeItem('token')
    localStorage.removeItem('tokenExpiry')
    localStorage.removeItem('lastActivity')
    window.location.href = '/login'
  }
}

// Interceptor request
axiosInstance.interceptors.request.use(
  async (config) => {
    const token = localStorage.getItem('token')
    if (token) {
      const tokenExpiry = localStorage.getItem('tokenExpiry')
      const currentTime = Date.now()

      // Cập nhật timestamp mỗi khi gửi request
      updateLastActivity()

      // Làm mới token nếu cần
      if (tokenExpiry && currentTime > tokenExpiry - 600000) {
        try {
          const refreshResponse = await axios.post(
            'http://localhost:8000/api/refresh-token',
            {},
            {
              headers: {
                Authorization: `Bearer ${token}`,
              },
            },
          )

          const newToken = refreshResponse.data.token
          const expiresIn = refreshResponse.data.expires_in * 1000

          localStorage.setItem('token', newToken)
          localStorage.setItem('tokenExpiry', Date.now() + expiresIn)

          config.headers['Authorization'] = `Bearer ${newToken}`
        } catch (error) {
          console.error('Unable to refresh token', error)
          localStorage.clear()
          window.location.href = '/login'
        }
      } else {
        config.headers['Authorization'] = `Bearer ${token}`
      }
    }
    return config
  },
  (error) => Promise.reject(error),
)

// Interceptor response
axiosInstance.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response && error.response.status === 401) {
      localStorage.clear()

      window.location.href = '/login'
    }
    return Promise.reject(error)
  },
)

// Kiểm tra timeout định kỳ mỗi phút
setInterval(checkInactivity, 60000)

export default axiosInstance
