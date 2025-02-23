import { createRouter, createWebHistory } from 'vue-router'
import { authenticate } from '@/middleware/authenticate'

import catalogueRoutes from './catalogueRoutes'
import productRoutes from './productRoutes'
import brandRoutes from './brandRoutes'
import attributeRoutes from './attributeRoutes'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/admin/login',
      name: 'login',
      component: () => import('@/views/auth/LoginView.vue'),
    },
    {
      path: '/admin',
      beforeEnter: authenticate,
      children: [
        {
          path: '',
          name: 'dashboard',
          component: () => import('@/views/DashboardView.vue'),
        },
        ...productRoutes,
        ...catalogueRoutes,
        ...brandRoutes,
        ...attributeRoutes,
      ],
    },
    {
      path: '/:catchAll(.*)', // Redirect tất cả các route không hợp lệ
      redirect: '/admin',
    },
  ],
})

export default router
