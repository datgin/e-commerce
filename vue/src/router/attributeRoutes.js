export default [
  {
    path: 'attributes',
    name: 'attributes',
    component: () => import('@/views/attribute/IndexView.vue'),
  },
  {
    path: 'attributes/:value',
    name: 'attribute_values',
    component: () => import('@/views/attribute-value/IndexView.vue'),
  },
]
