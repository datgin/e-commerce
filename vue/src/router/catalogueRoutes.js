export default [
  {
    path: 'catalogues', // Không cần thêm tiền tố admin
    name: 'catalogues',
    component: () => import('@/views/catalogue/IndexView.vue'),
  },
  {
    path: 'catalogues/create',
    name: 'catalogues.create',
    component: () => import('@/views/catalogue/SaveView.vue'),
  },
  {
    path: 'catalogues/:id/update',
    name: 'catalogues.update',
    component: () => import('@/views/catalogue/SaveView.vue'),
  },
]
