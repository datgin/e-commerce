export function authenticate(to, from, next) {
  const token = localStorage.getItem('token')

  if (!token) {
    return next({ name: 'login' })
  }

  return next()
}
