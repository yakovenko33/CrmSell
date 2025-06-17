
import {useUserStore} from "../../stores/UserStore";
import {initAuth} from "../../modules/Auth/auth.service";

async function initUser(): Promise<void> {
    const userStore = useUserStore();
    if (userStore.isAuthenticated === null) {
        return;
    }
    if (userStore.getUserInfo !== null) {
        return;
    }

    await initAuth().init();
}

export {initUser};
