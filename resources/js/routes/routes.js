const routes = [
    {
        path: '/',
        redirect: '/clients'
    },
    {
        path: '/clients',
        name: 'clients.index',
        component: () => import('../views/Clients/Clients')
    },
    {
        path: '/clients/:userId',
        name: 'clients.show',
        component: () => import('../views/Client/Client'),
    },
    {
        path: '/login',
        name: 'Login',
        meta: {
            layout: 'login',
            login: true
        },
        component: () => import('../views/Login/Login'),
    },
    {
        path: '/receivables',
        name: 'Receivables',
        component: () => import('../views/Economy/Receivables/Receivables')
    },
    {
        path: '/stats',
        name: 'Stats',
        component: () => import('../views/Economy/Stats/Stats')
    },
    {
        path: '/orders',
        name: 'Orders',
        component: () => import('../views/Control/Orders/Orders')
    },
    {
        path: '/news',
        name: 'News',
        component: () => import('../views/Control/News/News')
    },
    {
        path: '/page-content',
        name: 'PageContent',
        component: () => import('../views/Control/PageContent/PageContent')
    },
    {
        path: '/users',
        name: 'users.index',
        component: () => import('../views/Services/Users/Users'),
    },
    {
        path: '/client-fields',
        name: 'ClientFields',
        component: () => import('../views/Services/ClientFields/ClientFields')
    },
    {
        path: '/mailing',
        name: 'Mailing',
        component: () => import('../views/Services/Mailing/Mailing')
    },
    {
        path: '/prices',
        name: 'Prices',
        component: () => import('../views/Services/Prices/Prices')
    },
   /* {
        path: '*',
        redirect: '/clients'
    }*/

];


export default routes;
