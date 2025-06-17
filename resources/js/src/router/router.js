
import { createRouter, createWebHistory } from 'vue-router';
import routes from './routes.js';
import {userLoginMiddleware} from "./middleware/userLoginMiddleware";
import {initUser} from "./middleware/initUserMiddleware";
import {accessGuardMiddleware} from "./middleware/accessGuardMiddleware";

const router = createRouter({
    history: createWebHistory(),
    linkActiveClass: 'active',
    routes,
});


router.beforeEach(userLoginMiddleware);
router.beforeEach(initUser);
router.beforeEach(accessGuardMiddleware);

export default router;


