
import {useUserStore} from "../../stores/UserStore";
import {RouteNamesEnum} from "../RouteNamesEnum";
import {NavigationGuardNext, RouteLocationNormalized} from "vue-router";


export function userLoginMiddleware(to: RouteLocationNormalized, from: RouteLocationNormalized, next: NavigationGuardNext): any {
    const userStore = useUserStore();
    const token = userStore.isAuthenticated;

    if (!token) {
        if (to.name !== RouteNamesEnum.USER_LOGIN) {
            return next({ name: RouteNamesEnum.USER_LOGIN });
        }
    } else {
        if (to.name === RouteNamesEnum.USER_LOGIN) {
            return next({ name: RouteNamesEnum.MAIN });
        }
    }
    next();
}
