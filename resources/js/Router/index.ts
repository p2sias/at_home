import VueRouter, { RouteConfig } from 'vue-router'
import Login from '../Pages/Login.vue'
import Dashboard from '../Pages/DashBoard.vue'
import Session from '../Pages/Session.vue'
import AdminPanel from '../Pages/admin/AdminPanel.vue'
import PostsPanel from '../Pages/admin/PostsPanel.vue'
import SessionDetails from '../Pages/admin/sessions/SessionDetails.vue'
import ProfilePage from '../Pages/ProfilePage.vue'
import Users from '../Pages/admin/Users.vue';

import Home from '../Pages/site/Home.vue'


const routes: Array<RouteConfig> = [
    {
        path: '/login',
        name: 'Login',
        component: Login
    },
    {
        path: '/dashboard',
        name: 'Dashboard',
        component: Dashboard
    },
    {
        path: '/sessions',
        name: 'Session',
        component: Session
    },
    {
        path: '/admin/panel',
        name: 'Admin',
        component: AdminPanel
    },
    {
        path: '/admin/posts',
        name: 'Posts',
        component: PostsPanel
    },
    {
        path: '/admin/panel/session/:id',
        name: 'AdminSession',
        component: SessionDetails
    },
    {
        path: '/profile',
        name: 'Profile',
        component: ProfilePage
    },
    {
        path: '/admin/users',
        name: 'Users',
        component: Users
    },
    // Site vitrine
    {
        path: '/',
        name: 'home',
        component: Home
    }
]

const router = new VueRouter({
  mode: 'history',
  base: process.env.BASE_URL,
  routes
})

export default router;
