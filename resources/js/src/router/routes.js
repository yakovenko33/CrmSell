
import {RouteNamesEnum} from "./RouteNamesEnum";
import {RolesEnum} from "../modules/Auth/enum/roles.enum";
const Login = () => import("@/js/src/pages/Login/Login.vue");
const Main = () => import("@/js/src/pages/Main/Main.vue");
const Docs = () => import("@/js/src/pages/Docs/Docs.vue");


const routes = [
    {
        path: '/login',
        name: RouteNamesEnum.USER_LOGIN,
        component: Login,
    },
    {
        path: '/',
        name: RouteNamesEnum.MAIN,
        component: Main,
        meta: {
            sidepanel: 'main',
        },
    },
    {
        path: '/docs',
        name: RouteNamesEnum.DOCS,
        component: Docs,
        meta: {
            accessScopes: [RolesEnum.ADMIN],
            sidepanel: 'main',
        },
    },
    {
        path: '/admin',
        component: Docs,
        meta: {
            accessScopes: [RolesEnum.ADMIN],
            sidepanel: 'admin'
        },
    },
    {
        path: '/users',
        name: 'users-list',
        component: () => import("../modules/Admin/pages/Users/List/UsersList.vue"),
        meta: {
            accessScopes: [RolesEnum.ADMIN],
            sidepanel: 'admin',
        },
    },
    {
        path: '/user/create',
        component: () => import("../modules/Admin/pages/Users/CreateUpdate/Create/UserCreate.vue"),
        meta: {
            accessScopes: [RolesEnum.ADMIN],
            sidepanel: 'admin',
        },
    },
    {
        path: '/user/edit/:recordId',
        component: () => import("../modules/Admin/pages/Users/CreateUpdate/Update/UserUpdate.vue"),
        meta: {
            accessScopes: [RolesEnum.ADMIN],
            sidepanel: 'admin',
        },
    },
    {
        path: '/user/detail/:recordId',
        component: () => import("../modules/Admin/pages/Users/DetailView/UserDetailView.vue"),
        meta: {
            accessScopes: [RolesEnum.ADMIN],
            sidepanel: 'admin',
        },
    },
    {
        path: '/status/:type',
        component: () => import("../modules/Admin/pages/Status/List/StatusList.vue"),
        meta: {
            accessScopes: [RolesEnum.ADMIN],
            sidepanel: 'admin',
        },
    },
    {
        path: '/status/:type/create',
        component: () => import("../modules/Admin/pages/Status/Edit/StatusEdit.vue"),
        meta: {
            accessScopes: [RolesEnum.ADMIN],
            sidepanel: 'admin',
        },
    },
    {
        path: '/status/:type/edit/:recordId',
        component: () => import("../modules/Admin/pages/Status/Edit/StatusEdit.vue"),
        meta: {
            accessScopes: [RolesEnum.ADMIN],
            sidepanel: 'admin',
        },
    },
    {
        path: '/providers',
        name: 'providers',
        component: () => import("../modules/Admin/pages/Providers/List/ProvidersList.vue"),
        meta: {
            accessScopes: [RolesEnum.ADMIN],
            sidepanel: 'admin',
        },
    },
    {
        path: '/provider/create',
        component: () => import("../modules/Admin/pages/Providers/Edit/ProviderEdit.vue"),
        meta: {
            accessScopes: [RolesEnum.ADMIN],
            sidepanel: 'admin',
        },
    },
    {
        path: '/goods',
        name: 'goods-list',
        component: () => import("../modules/Admin/pages/Goods/List/GoodsList.vue"),
        meta: {
            accessScopes: [RolesEnum.ADMIN],
            sidepanel: 'admin',
        },
    },
    {
        path: '/goods/create',
        name: 'goods-add',
        component: () => import("../modules/Admin/pages/Goods/Edit/GoodsEdit.vue"),
        meta: {
            accessScopes: [RolesEnum.ADMIN],
            sidepanel: 'admin',
        },
    },
    {
        path: '/goods/edit/:recordId',
        name: 'goods-edit',
        component: () => import("../modules/Admin/pages/Goods/Edit/GoodsEdit.vue"),
        meta: {
            accessScopes: [RolesEnum.ADMIN],
            sidepanel: 'admin',
        },
    },
    {
        path: '/provider/edit/:recordId',
        component: () => import("../modules/Admin/pages/Providers/Edit/ProviderEdit.vue"),
        meta: {
            accessScopes: [RolesEnum.ADMIN],
            sidepanel: 'admin',
        },
    },
    {
        path: '/orders',
        name: 'orders',
        component: () => import("../modules/Orders/pages/List/OrdersList.vue"),
        meta: {
            sidepanel: 'main',
        },
    },
    {
        path: '/order/add',
        name: 'order-add',
        component: () => import("../modules/Orders/pages/Create/OrderCreate.vue"),
        meta: {
            //accessScopes: [],
            sidepanel: 'main',
        },
    },
    {
        path: '/brands',
        name: 'brands',
        component: () => import("../modules/Admin/pages/Brands/List/BrandsList.vue"),
        meta: {
            accessScopes: [RolesEnum.ADMIN],
            sidepanel: 'admin',
        },
    },
    {
        path: '/brand/create',
        name: 'brand-add',
        component: () => import("../modules/Admin/pages/Brands/Edit/BrandEdit.vue"),
        meta: {
            accessScopes: [RolesEnum.ADMIN],
            sidepanel: 'admin',
        },
    },
    {
        path: '/brand/edit/:recordId',
        name: 'brand-edit',
        component: () => import("../modules/Admin/pages/Brands/Edit/BrandEdit.vue"),
        meta: {
            accessScopes: [RolesEnum.ADMIN],
            sidepanel: 'admin',
        },
    },
    {
        path: '/order/add',
        name: 'order-add',
        component: () => import("../modules/Orders/pages/Create/OrderCreate.vue"),
        meta: {
            //accessScopes: [],
            sidepanel: 'main',
        },
    },
    {
        path: '/access-error',
        name: RouteNamesEnum.ACCESS_ERROR,
        meta: {
            sidepanel: 'main',
        },
        component: () => import("@/js/src/modules/Auth/pages/AccessError.vue"),
    },
    {
        path: '/:catchAll(.*)',
        component: () => import("@/js/src/modules/Auth/pages/PageNotFound.vue"),
    },
];

export default routes;
