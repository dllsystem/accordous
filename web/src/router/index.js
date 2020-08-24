import Vue from 'vue'
import VueRouter from 'vue-router'
import Home from '../views/Home.vue'

import PropertyIndex from '@/views/Property/Index'
import PropertyCreate from '@/views/Property/Create'
import PropertyEdit from '@/views/Property/Edit'

import ContractIndex from '@/views/Contract/Index'
import ContractCreate from '@/views/Contract/Create'
import ContractEdit from '@/views/Contract/Edit'

Vue.use(VueRouter)

  const routes = [
  {
    path: '/',
    name: 'home',
    component: Home
  },

  {
    path: '/property', 
    name: 'property',
    component: PropertyIndex
  },

  {
    path: '/property/create', 
    name: 'property.create',
    component: PropertyCreate
  },

  {
    path: '/property/:id/edit', 
    name: 'property.edit',
    component: PropertyEdit
  },

  {
    path: '/contract',
    name: 'contract',
    component: ContractIndex
  },

  {
    path: '/contract/create', 
    name: 'contract.create',
    component: ContractCreate
  },

  {
    path: '/contract/:id/edit', 
    name: 'contract.edit',
    component: ContractEdit
  },
  
  {
    path: '/about',
    name: 'About',
    // route level code-splitting
    // this generates a separate chunk (about.[hash].js) for this route
    // which is lazy-loaded when the route is visited.
    component: () => import(/* webpackChunkName: "about" */ '../views/About.vue')
  }
]

const router = new VueRouter({
  mode: 'history',
  base: process.env.BASE_URL,
  routes
})

export default router
