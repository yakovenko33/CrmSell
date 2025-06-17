
import {useUserStore} from "../../stores/UserStore";
import axios from "axios";
import {UserInfo} from "../../types/userInfo";


export interface Permission  {
    hasRoles(roles: string[]): boolean;
    hasPermission(permission: string[]): boolean;
    init(): Promise<void>
}

let authService: Permission = null;
let rolesList: Set<string> = null;
let permissionList: Set<string> = null;

const hasRoles = function(roles: string[]): boolean {
    for (const element of roles) {
        if (rolesList.has(element)) {
            return true;
        }
    }
    return false;
}

const hasPermission = function(permission: string[]): boolean {
    for (const element of permission) {
        if (permissionList.has(element)) {
            return true;
        }
    }
    return false;
}

const init = async function(): Promise<void>  {
    const userStore = useUserStore();
    const response = await axios.get('/api/v1/user');

    const data = response.data.data;
    userStore.setUser(data.user as UserInfo);

    rolesList = new Set(data.roles);
    permissionList = new Set(data.permissions);
};

const initAuth = function(): Permission {
    if (authService === null) {
        authService = {
            hasRoles,
            hasPermission,
            init
        } as Permission;
    }
    return authService;
}

export {initAuth};
