export default [
  {
    path: 'brands',
    name: 'brands',
    component: () => import('@/views/brand/IndexView.vue'),
  },
  {
    path: 'brands/create',
    name: 'brands.create',
    component: () => import('@/views/brand/SaveView.vue'),
  },
  {
    path: 'brands/:id/update',
    name: 'brands.update',
    component: () => import('@/views/brand/SaveView.vue'),
  },
]
